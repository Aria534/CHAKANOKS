<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Purchase Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }

        .page-title { 
            font-size:1.8rem; 
            margin-bottom:1.5rem; 
            font-weight:600; 
            color:#fff;
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(183, 90, 3, 0.3);
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

        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending { background-color: #ffc107; color: #000; }
        .status-approved { background-color: #17a2b8; color: #fff; }
        .status-ordered { background-color: #007bff; color: #fff; }
        .status-delivered { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }

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

        .btn-back {
            background: #6c757d;
            border: none;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .btn-back:hover {
            background: #5a6268;
            color: #fff;
        }

        .action-buttons {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-approve {
            background: linear-gradient(135deg, #17a2b8 0%, #20c9e0 100%);
            color: white;
        }

        .btn-send {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .btn-receive {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            color: white;
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'orders']) ?>

    <div class="main-content">
        <div class="page-title">Purchase Order Details</div>

        <a href="<?= site_url('orders') ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?= esc(session('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <?= esc(session('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Order Information -->
        <div class="info-card">
            <h5>Order Information</h5>
            <div class="info-row">
                <div class="info-label">PO Number:</div>
                <div class="info-value"><strong><?= esc($po['po_number'] ?? 'N/A') ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-<?= strtolower($po['status'] ?? 'pending') ?>">
                        <?= ucfirst($po['status'] ?? 'Pending') ?>
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Branch:</div>
                <div class="info-value"><?= esc($po['branch_name'] ?? 'N/A') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Supplier:</div>
                <div class="info-value"><?= esc($po['supplier_name'] ?? 'N/A') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Amount:</div>
                <div class="info-value"><strong>₱<?= number_format((float)($po['total_amount'] ?? 0), 2) ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Requested By:</div>
                <div class="info-value"><?= esc($po['requested_by_name'] ?? 'N/A') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Requested Date:</div>
                <div class="info-value">
                    <?= !empty($po['requested_date']) ? date('M d, Y h:i A', strtotime($po['requested_date'])) : 'N/A' ?>
                </div>
            </div>
            <?php if (!empty($po['approved_by_name'])): ?>
            <div class="info-row">
                <div class="info-label">Approved By:</div>
                <div class="info-value"><?= esc($po['approved_by_name']) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($po['approved_date'])): ?>
            <div class="info-row">
                <div class="info-label">Approved Date:</div>
                <div class="info-value"><?= date('M d, Y h:i A', strtotime($po['approved_date'])) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($po['expected_delivery_date'])): ?>
            <div class="info-row">
                <div class="info-label">Expected Delivery:</div>
                <div class="info-value"><?= date('M d, Y', strtotime($po['expected_delivery_date'])) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($po['actual_delivery_date'])): ?>
            <div class="info-row">
                <div class="info-label">Actual Delivery:</div>
                <div class="info-value"><?= date('M d, Y', strtotime($po['actual_delivery_date'])) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Order Items -->
        <div class="info-card">
            <h5>Order Items</h5>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th class="text-end">Qty Requested</th>
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
                            <td class="text-end">₱<?= number_format((float)($po['total_amount'] ?? 0), 2) ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Action Buttons -->
        <?php $status = strtolower($po['status'] ?? ''); ?>
        <div class="action-buttons">
            <?php if ($status === 'pending' && in_array($userRole, ['central_admin','system_admin'])): ?>
                <form method="post" action="<?= site_url('orders/' . $po['purchase_order_id'] . '/approve') ?>" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-action btn-approve" onclick="return confirm('Approve this purchase order?')">
                        <i class="fas fa-check"></i> Approve Order
                    </button>
                </form>
            <?php endif; ?>

            <?php if (in_array($status, ['approved', 'pending']) && in_array($userRole, ['central_admin','system_admin'])): ?>
                <form method="post" action="<?= site_url('orders/' . $po['purchase_order_id'] . '/send') ?>" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-action btn-send" onclick="return confirm('Send this order to supplier?')">
                        <i class="fas fa-paper-plane"></i> Send to Supplier
                    </button>
                </form>
            <?php endif; ?>

            <?php if (in_array($status, ['ordered', 'approved']) && in_array($userRole, ['central_admin','system_admin','inventory_staff','branch_manager'])): ?>
                <form method="post" action="<?= site_url('orders/' . $po['purchase_order_id'] . '/receive') ?>" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-action btn-receive" onclick="return confirm('Mark this order as received? This will update inventory.')">
                        <i class="fas fa-box"></i> Receive Order
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

