<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Details - ChakaNoks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php echo view('templete/sidebar_styles'); ?>
    <style>
        .main-content { margin-left: 220px; padding: 2rem; }
        .page-title { font-size: 1.8rem; margin-bottom: 1.5rem; font-weight: 600; color: white; background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%); padding: 1rem 1.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(183, 90, 3, 0.3); }
        .card { border: none; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e8e8e8; margin-bottom: 2rem; }
        .card h5 { color: #2c3e50; margin-bottom: 1rem; font-weight: 600; }
        .info-section { background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #e8e8e8; }
        .info-row { display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e8e8e8; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #2c3e50; }
        .info-value { color: #555; }
        .badge { padding: 0.5rem 1rem; font-weight: 600; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-in_transit { background-color: #cfe2ff; color: #084298; }
        .status-delivered { background-color: #d1e7dd; color: #0f5132; }
        .table { margin-bottom: 0; }
        .table th { background-color: #f8f9fa; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e8e8e8; }
        .table td { vertical-align: middle; padding: 1rem; }
        .btn-primary { background: #b75a03ff; border-color: #b75a03ff; cursor: pointer !important; }
        .btn-primary:hover { background: #ff9320ff; border-color: #ff9320ff; }
    </style>
</head>
<body>
    <?php echo view('templete/sidebar', ['active' => 'deliveries']); ?>

    <div class="main-content">
        <div class="page-title">
            <i class="fas fa-truck"></i> Delivery Details
        </div>

        <a href="<?php echo site_url('deliveries'); ?>" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Deliveries
        </a>

        <?php if (!empty($purchaseOrder)): ?>
            <!-- Delivery Information -->
            <div class="card">
                <h5><i class="fas fa-info-circle"></i> Delivery Information</h5>
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">PO Number:</span>
                        <span class="info-value"><strong><?php echo esc($purchaseOrder['po_number']); ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="badge status-<?php echo strtolower($purchaseOrder['status']); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $purchaseOrder['status'])); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Expected Delivery:</span>
                        <span class="info-value"><?php echo !empty($purchaseOrder['expected_delivery_date']) ? date('M d, Y', strtotime($purchaseOrder['expected_delivery_date'])) : 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Actual Delivery:</span>
                        <span class="info-value"><?php echo !empty($purchaseOrder['actual_delivery_date']) ? date('M d, Y', strtotime($purchaseOrder['actual_delivery_date'])) : 'Pending'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Supplier Information -->
            <div class="card">
                <h5><i class="fas fa-building"></i> Supplier Information</h5>
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">Supplier Name:</span>
                        <span class="info-value"><?php echo esc($purchaseOrder['supplier_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact Person:</span>
                        <span class="info-value"><?php echo !empty($purchaseOrder['contact_person']) ? esc($purchaseOrder['contact_person']) : 'N/A'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Delivery Items -->
            <div class="card">
                <h5><i class="fas fa-boxes"></i> Delivery Items</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th class="text-end">Quantity Requested</th>
                                <th class="text-end">Quantity Delivered</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach($items as $item): ?>
                                    <tr>
                                        <td><?php echo esc($item['product_code'] ?? 'N/A'); ?></td>
                                        <td><?php echo esc($item['product_name']); ?></td>
                                        <td class="text-end"><?php echo $item['quantity_requested']; ?></td>
                                        <td class="text-end"><?php echo $item['quantity_delivered'] ?? 0; ?></td>
                                        <td class="text-end">₱<?php echo number_format($item['unit_price'] ?? 0, 2); ?></td>
                                        <td class="text-end">₱<?php echo number_format(($item['unit_price'] ?? 0) * ($item['quantity_delivered'] ?? 0), 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No items found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <?php if (!empty($purchaseOrder['notes'])): ?>
                <div class="card">
                    <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                    <div class="info-section">
                        <p><?php echo esc($purchaseOrder['notes']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Delivery not found
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
