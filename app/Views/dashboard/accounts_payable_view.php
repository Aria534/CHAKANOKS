<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Payable Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }

        .info-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #e8e8e8;
            margin-bottom: 1.5rem;
        }

        .info-card h5 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .info-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f7f7f7;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            width: 200px;
            flex-shrink: 0;
        }

        .info-value {
            color: #2c3e50;
            flex: 1;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .items-table th {
            text-align: left;
            padding: 0.8rem;
            font-weight: 700;
            color: #666;
            border-bottom: 2px solid #f0f0f0;
            background-color: #fafafa;
        }

        .items-table td {
            padding: 0.8rem;
            border-bottom: 1px solid #f7f7f7;
            color: #444;
        }

        .items-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .total-row {
            font-weight: 700;
            font-size: 1.1rem;
            background-color: #f9f9f9;
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
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

        .badge-overdue {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'accounts_payable']) ?>

    <div class="main-content">
        <div class="page-title">Payable Details</div>

        <a href="<?= site_url('accounts-payable') ?>" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Accounts Payable
        </a>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= esc(session('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Payment Information -->
        <div class="info-card">
            <h5>Payment Information</h5>
            <div class="info-row">
                <div class="info-label">PO Number:</div>
                <div class="info-value"><strong><?= esc($payable['po_number']) ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Status:</div>
                <div class="info-value">
                    <?php
                    $status = $payable['payment_status'] ?? 'unpaid';
                    $isOverdue = ($status !== 'paid' && !empty($payable['payment_due_date']) && 
                                 strtotime($payable['payment_due_date']) < strtotime('now'));
                    
                    if ($status === 'paid') {
                        $badgeClass = 'badge-paid';
                        $statusText = 'Paid';
                    } elseif ($isOverdue) {
                        $badgeClass = 'badge-overdue';
                        $statusText = 'Overdue';
                    } else {
                        $badgeClass = 'badge-unpaid';
                        $statusText = 'Unpaid';
                    }
                    ?>
                    <span class="status-badge <?= $badgeClass ?>"><?= $statusText ?></span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Amount:</div>
                <div class="info-value"><strong class="text-danger">₱<?= number_format($payable['total_amount'], 2) ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Delivery Date:</div>
                <div class="info-value">
                    <?= !empty($payable['actual_delivery_date']) ? date('M d, Y', strtotime($payable['actual_delivery_date'])) : 'N/A' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Due Date:</div>
                <div class="info-value">
                    <?= !empty($payable['payment_due_date']) ? date('M d, Y', strtotime($payable['payment_due_date'])) : 'Not set' ?>
                    <?php if ($isOverdue): ?>
                        <span class="text-danger ms-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= floor((strtotime('now') - strtotime($payable['payment_due_date'])) / 86400) ?> days overdue
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($status === 'paid'): ?>
            <div class="info-row">
                <div class="info-label">Payment Date:</div>
                <div class="info-value">
                    <?= !empty($payable['payment_date']) ? date('M d, Y', strtotime($payable['payment_date'])) : 'N/A' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Method:</div>
                <div class="info-value"><?= ucwords(str_replace('_', ' ', $payable['payment_method'] ?? 'N/A')) ?></div>
            </div>
            <?php if (!empty($payable['payment_reference'])): ?>
            <div class="info-row">
                <div class="info-label">Reference Number:</div>
                <div class="info-value"><?= esc($payable['payment_reference']) ?></div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Supplier & Branch Information -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-card">
                    <h5>Supplier Information</h5>
                    <div class="info-row">
                        <div class="info-label">Supplier:</div>
                        <div class="info-value"><?= esc($payable['supplier_name']) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Contact Person:</div>
                        <div class="info-value"><?= esc($payable['contact_person'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value"><?= esc($payable['phone'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value"><?= esc($payable['email'] ?? 'N/A') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <h5>Branch Information</h5>
                    <div class="info-row">
                        <div class="info-label">Branch:</div>
                        <div class="info-value"><?= esc($payable['branch_name']) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Requested By:</div>
                        <div class="info-value"><?= esc($payable['requested_by_name'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Request Date:</div>
                        <div class="info-value">
                            <?= !empty($payable['requested_date']) ? date('M d, Y', strtotime($payable['requested_date'])) : 'N/A' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="info-card">
            <h5>Order Items</h5>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th class="text-end">Qty Ordered</th>
                        <th class="text-end">Qty Delivered</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td><?= esc($item['product_code'] ?? 'N/A') ?></td>
                            <td><?= esc($item['product_name'] ?? 'N/A') ?></td>
                            <td class="text-end"><?= (int)($item['quantity_requested'] ?? 0) ?></td>
                            <td class="text-end"><?= (int)($item['quantity_delivered'] ?? 0) ?></td>
                            <td class="text-end">₱<?= number_format((float)($item['unit_price'] ?? 0), 2) ?></td>
                            <td class="text-end">₱<?= number_format((float)($item['total_price'] ?? 0), 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="5" class="text-end">Total:</td>
                            <td class="text-end">₱<?= number_format((float)($payable['total_amount'] ?? 0), 2) ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Payment History -->
        <?php if (!empty($paymentHistory)): ?>
        <div class="info-card">
            <h5>Payment History</h5>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($paymentHistory as $payment): ?>
                    <tr>
                        <td><?= date('M d, Y h:i A', strtotime($payment['payment_date'])) ?></td>
                        <td>₱<?= number_format($payment['payment_amount'], 2) ?></td>
                        <td><?= ucwords(str_replace('_', ' ', $payment['payment_method'])) ?></td>
                        <td><?= esc($payment['reference_number'] ?? 'N/A') ?></td>
                        <td><?= esc($payment['processed_by_name'] ?? 'N/A') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <?php if (($payable['payment_status'] ?? 'unpaid') !== 'paid'): ?>
        <div class="d-flex gap-2">
            <button onclick="openPaymentModal()" class="btn btn-success">
                <i class="fas fa-check"></i> Record Payment
            </button>
        </div>
        <?php endif; ?>
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
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" value="₱<?= number_format($payable['total_amount'], 2) ?>" class="form-control" readonly>
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

        function openPaymentModal() {
            paymentModal.show();
        }

        function submitPayment() {
            const formData = new FormData(document.getElementById('paymentForm'));
            const poId = <?= $payable['purchase_order_id'] ?>;
            
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

