<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Accounts Payable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #e8e8e8;
        }

        .stat-card.danger {
            border-left: 4px solid #dc3545;
        }

        .stat-card.warning {
            border-left: 4px solid #ffc107;
        }

        .stat-card.success {
            border-left: 4px solid #28a745;
        }

        .stat-card.info {
            border-left: 4px solid #17a2b8;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-card.danger .stat-value {
            color: #dc3545;
        }

        .stat-card.success .stat-value {
            color: #28a745;
        }

        .filter-card {
            background: #fff;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #e8e8e8;
            margin-bottom: 1.5rem;
        }

        .table-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #e8e8e8;
        }

        .table-card table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table-card th {
            text-align: left;
            padding: 0.8rem;
            font-weight: 700;
            color: #666;
            border-bottom: 2px solid #f0f0f0;
            background-color: #fafafa;
        }

        .table-card td {
            padding: 0.8rem;
            border-bottom: 1px solid #f7f7f7;
            color: #444;
        }

        .table-card tbody tr:hover {
            background-color: #f9f9f9;
        }

        .badge-status {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-paid {
            background: #d4edda;
            color: #155724;
        }

        .badge-unpaid {
            background: #fff3cd;
            color: #856404;
        }

        .badge-partial {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-overdue {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-action {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view {
            background: #e3f2fd;
            color: #1565c0;
        }

        .btn-view:hover {
            background: #bbdefb;
            color: #0d47a1;
        }

        .btn-pay {
            background: #28a745;
            color: #fff;
        }

        .btn-pay:hover {
            background: #218838;
            color: #fff;
        }

        .overdue-highlight {
            background-color: #fff5f5 !important;
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'accounts_payable']) ?>

    <div class="main-content">
        <div class="page-title">Accounts Payable</div>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card danger">
                <div class="stat-label">Total Payable</div>
                <div class="stat-value">₱<?= number_format($totalPayable, 2) ?></div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Total Paid</div>
                <div class="stat-value">₱<?= number_format($totalPaid, 2) ?></div>
            </div>
            <div class="stat-card warning">
                <div class="stat-label">Overdue Amount</div>
                <div class="stat-value">₱<?= number_format($totalOverdue, 2) ?></div>
            </div>
            <div class="stat-card info">
                <div class="stat-label">Overdue Invoices</div>
                <div class="stat-value"><?= $overdueCount ?></div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="get" action="<?= site_url('accounts-payable') ?>" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $filterStatus === 'all' ? 'selected' : '' ?>>All Status</option>
                        <option value="unpaid" <?= $filterStatus === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                        <option value="paid" <?= $filterStatus === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="partial" <?= $filterStatus === 'partial' ? 'selected' : '' ?>>Partial</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier" class="form-select" onchange="this.form.submit()">
                        <option value="all">All Suppliers</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['supplier_id'] ?>" <?= $filterSupplier == $sup['supplier_id'] ? 'selected' : '' ?>>
                                <?= esc($sup['supplier_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Branch</label>
                    <select name="branch" class="form-select" onchange="this.form.submit()">
                        <option value="all">All Branches</option>
                        <?php foreach ($branches as $br): ?>
                            <option value="<?= $br['branch_id'] ?>" <?= $filterBranch == $br['branch_id'] ? 'selected' : '' ?>>
                                <?= esc($br['branch_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="<?= site_url('accounts-payable') ?>" class="btn btn-outline-secondary w-100">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Payables Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Branch</th>
                            <th>Delivery Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payables)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No payables found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payables as $payable): ?>
                                <?php
                                $isOverdue = ($payable['payment_status'] !== 'paid' && !empty($payable['days_overdue']) && $payable['days_overdue'] > 0);
                                $rowClass = $isOverdue ? 'overdue-highlight' : '';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td>
                                        <strong><?= esc($payable['po_number']) ?></strong>
                                        <?php if ($isOverdue): ?>
                                            <br><small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> <?= (int)$payable['days_overdue'] ?> days overdue
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= esc($payable['supplier_name']) ?>
                                        <?php if (!empty($payable['contact_person'])): ?>
                                            <br><small class="text-muted"><?= esc($payable['contact_person']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($payable['branch_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($payable['actual_delivery_date'])) ?></td>
                                    <td>
                                        <?= !empty($payable['payment_due_date']) ? date('M d, Y', strtotime($payable['payment_due_date'])) : 'Not set' ?>
                                    </td>
                                    <td class="text-end">
                                        <strong>₱<?= number_format($payable['total_amount'], 2) ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-unpaid';
                                        $statusText = 'Unpaid';
                                        
                                        if ($payable['payment_status'] === 'paid') {
                                            $statusClass = 'badge-paid';
                                            $statusText = 'Paid';
                                        } elseif ($isOverdue) {
                                            $statusClass = 'badge-overdue';
                                            $statusText = 'Overdue';
                                        } elseif ($payable['payment_status'] === 'partial') {
                                            $statusClass = 'badge-partial';
                                            $statusText = 'Partial';
                                        }
                                        ?>
                                        <span class="badge-status <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('accounts-payable/view/' . $payable['purchase_order_id']) ?>" 
                                           class="btn-action btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php if ($payable['payment_status'] !== 'paid'): ?>
                                            <button onclick="openPaymentModal(<?= $payable['purchase_order_id'] ?>, '<?= esc($payable['po_number']) ?>', <?= $payable['total_amount'] ?>)" 
                                                    class="btn-action btn-pay ms-1">
                                                <i class="fas fa-check"></i> Pay
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm">
                        <input type="hidden" id="paymentPOId" name="po_id">
                        
                        <div class="mb-3">
                            <label class="form-label">PO Number</label>
                            <input type="text" id="paymentPONumber" class="form-control" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" id="paymentAmount" class="form-control" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                                <option value="cash">Cash</option>
                                <option value="online_payment">Online Payment</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="Transaction/Check number">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitPayment()">
                        <i class="fas fa-check"></i> Record Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let paymentModal;

        document.addEventListener('DOMContentLoaded', function() {
            paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        });

        function openPaymentModal(poId, poNumber, amount) {
            document.getElementById('paymentPOId').value = poId;
            document.getElementById('paymentPONumber').value = poNumber;
            document.getElementById('paymentAmount').value = '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            paymentModal.show();
        }

        function submitPayment() {
            const poId = document.getElementById('paymentPOId').value;
            const formData = new FormData(document.getElementById('paymentForm'));
            
            const submitBtn = event.target;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            
            fetch(`<?= site_url('accounts-payable/mark-paid/') ?>${poId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    paymentModal.hide();
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to record payment'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check"></i> Record Payment';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error recording payment. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Record Payment';
            });
        }
    </script>
</body>
</html>

