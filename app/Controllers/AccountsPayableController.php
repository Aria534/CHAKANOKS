<?php

namespace App\Controllers;

class AccountsPayableController extends BaseController
{
    public function index()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        $userId = (int)($session->get('user_id') ?? 0);
        
        // Only central admin and system admin can access
        if (!in_array($role, ['central_admin', 'system_admin'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Unauthorized access');
        }

        $db = \Config\Database::connect();
        
        // Get filter parameters
        $status = $this->request->getGet('status') ?? 'all';
        $supplier = $this->request->getGet('supplier') ?? 'all';
        $branch = $this->request->getGet('branch') ?? 'all';
        
        // Build query for payables (approved and beyond purchase orders)
        $builder = $db->table('purchase_orders po')
            ->select('po.purchase_order_id, po.po_number, po.total_amount, po.status,
                     po.requested_date, po.actual_delivery_date,
                     po.payment_status, po.payment_date, po.payment_due_date,
                     b.branch_name, s.supplier_name, s.contact_person, s.phone,
                     DATEDIFF(CURDATE(), po.payment_due_date) as days_overdue')
            ->join('branches b', 'b.branch_id = po.branch_id', 'left')
            ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
            ->whereIn('po.status', ['approved', 'ordered', 'delivered']);
        
        // Apply filters
        if ($status !== 'all') {
            $builder->where('po.payment_status', $status);
        }
        
        if ($supplier !== 'all' && is_numeric($supplier)) {
            $builder->where('po.supplier_id', (int)$supplier);
        }
        
        if ($branch !== 'all' && is_numeric($branch)) {
            $builder->where('po.branch_id', (int)$branch);
        }
        
        $payables = $builder->orderBy('po.payment_due_date', 'ASC')
            ->get()
            ->getResultArray();
        
        // Calculate totals
        $totalPayable = 0;
        $totalPaid = 0;
        $totalOverdue = 0;
        $overdueCount = 0;
        
        foreach ($payables as &$payable) {
            // Set default payment status if not set
            if (empty($payable['payment_status'])) {
                $payable['payment_status'] = 'unpaid';
            }
            
            // Calculate payment due date if not set (30 days from delivery)
            if (empty($payable['payment_due_date']) && !empty($payable['actual_delivery_date'])) {
                $payable['payment_due_date'] = date('Y-m-d', strtotime($payable['actual_delivery_date'] . ' +30 days'));
                $payable['days_overdue'] = max(0, (strtotime('now') - strtotime($payable['payment_due_date'])) / 86400);
            }
            
            if ($payable['payment_status'] === 'paid') {
                $totalPaid += (float)$payable['total_amount'];
            } else {
                $totalPayable += (float)$payable['total_amount'];
                
                if ($payable['days_overdue'] > 0) {
                    $totalOverdue += (float)$payable['total_amount'];
                    $overdueCount++;
                }
            }
        }
        
        // Get suppliers for filter
        $suppliers = $db->table('suppliers')
            ->select('supplier_id, supplier_name')
            ->where('status', 'active')
            ->orderBy('supplier_name', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get branches for filter
        $branches = $db->table('branches')
            ->select('branch_id, branch_name')
            ->where('status', 'active')
            ->orderBy('branch_name', 'ASC')
            ->get()
            ->getResultArray();
        
        return view('dashboard/central_office/accounts_payable', [
            'payables' => $payables,
            'suppliers' => $suppliers,
            'branches' => $branches,
            'totalPayable' => $totalPayable,
            'totalPaid' => $totalPaid,
            'totalOverdue' => $totalOverdue,
            'overdueCount' => $overdueCount,
            'filterStatus' => $status,
            'filterSupplier' => $supplier,
            'filterBranch' => $branch
        ]);
    }

    public function markAsPaid($id)
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        $userId = (int)($session->get('user_id') ?? 0);
        
        if (!in_array($role, ['central_admin', 'system_admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ])->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        
        // Get payment details from request
        $paymentDate = $this->request->getPost('payment_date') ?? date('Y-m-d');
        $paymentMethod = $this->request->getPost('payment_method') ?? 'bank_transfer';
        $referenceNumber = $this->request->getPost('reference_number') ?? '';
        $notes = $this->request->getPost('notes') ?? '';
        
        try {
            // Get purchase order
            $po = $db->table('purchase_orders')
                ->where('purchase_order_id', $id)
                ->get()
                ->getRowArray();
            
            if (!$po) {
                throw new \Exception('Purchase order not found');
            }
            
            if (!in_array($po['status'], ['approved', 'ordered', 'delivered'])) {
                throw new \Exception('Only approved, ordered, or delivered orders can be marked as paid');
            }
            
            // Update payment status
            $updateData = [
                'payment_status' => 'paid',
                'payment_date' => $paymentDate,
                'payment_method' => $paymentMethod,
                'payment_reference' => $referenceNumber,
                'payment_notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('purchase_orders')
                ->where('purchase_order_id', $id)
                ->update($updateData);
            
            // Log the payment
            $now = date('Y-m-d H:i:s');
            $db->table('payment_logs')->insert([
                'purchase_order_id' => $id,
                'payment_amount' => $po['total_amount'],
                'payment_date' => $paymentDate,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'notes' => $notes,
                'processed_by' => $userId,
                'created_at' => $now
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment recorded successfully'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error marking payment: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function view($id)
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        
        if (!in_array($role, ['central_admin', 'system_admin'])) {
            return redirect()->to(site_url('accounts-payable'))->with('error', 'Unauthorized access');
        }

        $db = \Config\Database::connect();
        
        // Get payable details
        $payable = $db->table('purchase_orders po')
            ->select('po.*, b.branch_name, b.address as branch_address,
                     s.supplier_name, s.contact_person, s.phone, s.email, s.address as supplier_address,
                     u1.username as requested_by_name, u2.username as approved_by_name')
            ->join('branches b', 'b.branch_id = po.branch_id', 'left')
            ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
            ->join('users u1', 'u1.user_id = po.requested_by', 'left')
            ->join('users u2', 'u2.user_id = po.approved_by', 'left')
            ->where('po.purchase_order_id', $id)
            ->get()
            ->getRowArray();
        
        if (!$payable) {
            return redirect()->to(site_url('accounts-payable'))->with('error', 'Payable not found');
        }
        
        // Set default payment status if not set
        if (empty($payable['payment_status'])) {
            $payable['payment_status'] = 'unpaid';
        }
        
        // Calculate payment due date if not set
        if (empty($payable['payment_due_date']) && !empty($payable['actual_delivery_date'])) {
            $payable['payment_due_date'] = date('Y-m-d', strtotime($payable['actual_delivery_date'] . ' +30 days'));
        }
        
        // Get order items
        $items = $db->table('purchase_order_items poi')
            ->select('poi.*, p.product_name, p.product_code')
            ->join('products p', 'p.product_id = poi.product_id', 'left')
            ->where('poi.purchase_order_id', $id)
            ->orderBy('p.product_name', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get payment history
        $paymentHistory = $db->table('payment_logs pl')
            ->select('pl.*, u.username as processed_by_name')
            ->join('users u', 'u.user_id = pl.processed_by', 'left')
            ->where('pl.purchase_order_id', $id)
            ->orderBy('pl.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        return view('dashboard/central_office/accounts_payable_view', [
            'payable' => $payable,
            'items' => $items,
            'paymentHistory' => $paymentHistory
        ]);
    }

    public function updateDueDate($id)
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        
        if (!in_array($role, ['central_admin', 'system_admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ])->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        
        $newDueDate = $this->request->getPost('due_date');
        
        if (empty($newDueDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Due date is required'
            ])->setStatusCode(400);
        }
        
        try {
            $db->table('purchase_orders')
                ->where('purchase_order_id', $id)
                ->update([
                    'payment_due_date' => $newDueDate,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Due date updated successfully'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error updating due date: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating due date'
            ])->setStatusCode(500);
        }
    }
}

