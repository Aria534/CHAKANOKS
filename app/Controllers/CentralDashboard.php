<?php
namespace App\Controllers;

use App\Models\UserModel;

class CentralDashboard extends BaseController {
    public function index() {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }
        
        try {
            $db = \Config\Database::connect();
            if (!is_object($db)) {
                throw new \Exception('Database connection failed');
            }
        } catch (\Exception $e) {
            log_message('error', 'Database connection error in CentralDashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Database connection error');
        }

        // Initialize data arrays
        $stockValue = 0;
        $lowStock = 0;
        $pendingOrders = 0;
        $pendingOrdersList = [];
        $categoryStats = [];
        $ordersTrend = [];
        $lowStockList = [];
        
        try {
            // Total Stock Value
            $result = $db->table('inventory i')
                ->select('SUM(i.current_stock * p.unit_price) as total_value')
                ->join('products p', 'p.product_id = i.product_id')
                ->get()
                ->getRowArray();
            $stockValue = $result['total_value'] ?? 0;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching stock value: ' . $e->getMessage());
        }

        try {
            // Low Stock Alerts (uses available_stock vs product minimum)
            $result = $db->table('inventory i')
                ->select('COUNT(*) as low_count')
                ->join('products p', 'p.product_id = i.product_id')
                ->where('i.available_stock <= p.minimum_stock')
                ->get()
                ->getRowArray();
            $lowStock = $result['low_count'] ?? 0;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching low stock count: ' . $e->getMessage());
        }

        try {
            // Pending Orders
            $pendingOrders = $db->table('purchase_orders')
                ->where('status', 'pending')
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching pending orders count: ' . $e->getMessage());
            $pendingOrders = 0;
        }

        try {
            // Pending Orders List (latest 5)
            $pendingOrdersList = $db->table('purchase_orders po')
                ->select('po.po_number, po.requested_date, b.branch_name')
                ->join('branches b', 'b.branch_id = po.branch_id', 'left')
                ->where('po.status', 'pending')
                ->orderBy('po.requested_date', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching pending orders list: ' . $e->getMessage());
        }

        try {
            // Stock per category (for Donut Chart)
            $categoryStats = $db->table('inventory i')
                ->select('c.category_name, SUM(i.current_stock) as qty')
                ->join('products p', 'p.product_id = i.product_id')
                ->join('categories c', 'c.category_id = p.category_id')
                ->groupBy('c.category_name')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching category stats: ' . $e->getMessage());
        }

        try {
            // Orders trend per month (for Line Chart)
            $ordersTrend = $db->table('purchase_orders')
                ->select("MONTH(created_at) as month, COUNT(*) as total")
                ->where('YEAR(created_at)', date('Y'))
                ->groupBy('MONTH(created_at)')
                ->orderBy('month', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching orders trend: ' . $e->getMessage());
        }

        try {
            // Low stock list across branches (top 10)
            $lowStockList = $db->table('inventory i')
                ->select('b.branch_name, p.product_name, i.available_stock, p.minimum_stock')
                ->join('branches b', 'b.branch_id = i.branch_id')
                ->join('products p', 'p.product_id = i.product_id')
                ->where('i.available_stock <= p.minimum_stock')
                ->orderBy('b.branch_name', 'ASC')
                ->orderBy('p.product_name', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching low stock list: ' . $e->getMessage());
        }
        // Pass data to view
        return view('dashboard/central_admin', [
            'stockValue'    => $stockValue,
            'lowStock'      => $lowStock,
            'pendingOrders' => $pendingOrders,
            'categoryStats' => $categoryStats ?: [],
            'ordersTrend'   => $ordersTrend ?: [],
            'lowStockList'  => $lowStockList ?: [],
            'pendingOrdersList' => $pendingOrdersList ?: [],
        ]);
    }

    public function manageUsers() {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

        return view('dashboard/manage_users', [
            'users' => $users
        ]);
    }

    public function createUser() {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }

        if ($this->request->getMethod() === 'post') {
            $userModel = new UserModel();
            $data = [
                'username' => $this->request->getPost('username'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'phone' => $this->request->getPost('phone'),
                'role' => $this->request->getPost('role'),
                'status' => $this->request->getPost('status'),
            ];

            if ($userModel->insert($data)) {
                return redirect()->to(site_url('users'))->with('success', 'User created successfully.');
            } else {
                return redirect()->back()->withInput()->with('errors', $userModel->errors());
            }
        }

        return view('dashboard/create_user');
    }

    public function editUser($userId) {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to(site_url('users'))->with('error', 'User not found.');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'phone' => $this->request->getPost('phone'),
                'role' => $this->request->getPost('role'),
                'status' => $this->request->getPost('status'),
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($userModel->update($userId, $data)) {
                return redirect()->to(site_url('users'))->with('success', 'User updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('errors', $userModel->errors());
            }
        }

        return view('dashboard/edit_user', ['user' => $user]);
    }

    public function deleteUser($userId) {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }

        $userModel = new UserModel();
        if ($userModel->delete($userId)) {
            return redirect()->to(site_url('users'))->with('success', 'User deleted successfully.');
        } else {
            return redirect()->to(site_url('users'))->with('error', 'Failed to delete user.');
        }
    }
}
