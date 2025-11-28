<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Inventory Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        /* --- Main content --- */
        .main-content { margin-left: 220px; padding: 2rem; }

        .wrap { padding: 0; }

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

        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .page-header .branch-selector { display:flex; gap:0.5rem; align-items:center; }
        .page-header .form-select { max-width:200px; }

        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:1rem; }
        .card { background:#fff; border-radius:14px; padding:1.25rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; }
        .card h5 { color:#2c3e50; margin-bottom:1rem; font-weight:600; }

        .table-card { grid-column:1 / -1; }
        .table-card table { width:100%; border-collapse:collapse; font-size:14px; }
        .table-card th { text-align:left; padding:.8rem; font-weight:700; color:#666; border-bottom:1px solid #f0f0f0; }
        .table-card td { padding:.8rem; border-bottom:1px solid #f7f7f7; color:#444; }
        .table-card tbody tr:hover { background-color:#f9f9f9; }

        .form-label { font-weight:500; color:#2c3e50; }
        .form-control, .form-select { border:1px solid #e8e8e8; border-radius:8px; }
        .form-control:focus, .form-select:focus { border-color:#b75a03ff; box-shadow:0 0 0 0.2rem rgba(183,90,3,0.15); }

        .btn-primary { background:#b75a03ff; border-color:#b75a03ff; }
        .btn-primary:hover { background:#ff9320ff; border-color:#ff9320ff; }

        .alert { border-radius:8px; border:1px solid #e8e8e8; }

        @media (max-width:768px){ .main-content { margin-left: 0; padding:1rem; } .sidebar { width: 100%; height: auto; position: relative; } .page-header { flex-direction:column; gap:1rem; } }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'inventory']) ?>

    <div class="main-content">
        <div class="wrap">
        <div class="page-title">Inventory Management</div>

        <div class="page-header">
            <span style="font-size:1.1rem; font-weight:600; color:#2c3e50;">Inventory – <?= esc($branchName ?? 'Branch') ?></span>
            <?php if (!empty($canSwitchBranches)): ?>
            <form method="get" action="<?= site_url('inventory') ?>" class="branch-selector">
                <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" <?= (isset($_GET['branch_id']) && $_GET['branch_id'] === 'all') ? 'selected' : '' ?>>All Branches</option>
                    <?php if (!empty($branches)): ?>
                        <?php foreach($branches as $branch): ?>
                            <option value="<?= $branch['branch_id'] ?>" <?= (isset($_GET['branch_id']) && (int)$_GET['branch_id'] === (int)$branch['branch_id']) ? 'selected' : '' ?>>
                                <?= esc($branch['branch_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>
            <?php endif; ?>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <!-- Low Stock Alerts -->
        <?php if (!empty($lowStockItems)): ?>
        <div class="alert alert-warning" style="margin-bottom:1.5rem;">
            <strong>Low Stock Alert:</strong> You have <?= count($lowStockItems) ?> item(s) below minimum stock level
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Main Inventory Table -->
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Current Inventory</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                            <i class="fas fa-plus"></i> Add/Adjust Stock
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">In Stock</th>
                                        <th class="text-end">Available</th>
                                        <th class="text-end">Min Level</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Stock Value</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Debug: Check what's in branchInventory
                                    // print_r($branchInventory);
                                    ?>
                                    
                                    <?php if (!empty($branchInventory)): ?>
                                        <?php foreach($branchInventory as $item): ?>
                                            <?php 
                                                $isLowStock = isset($item['available_stock']) && 
                                                            isset($item['minimum_stock']) && 
                                                            $item['available_stock'] <= $item['minimum_stock'];
                                            ?>
                                            <tr class="<?= $isLowStock ? 'table-warning' : '' ?>">
                                                <td><?= esc($item['product_name'] ?? 'N/A') ?></td>
                                                <td class="text-end"><?= $item['current_stock'] ?? 0 ?></td>
                                                <td class="text-end"><?= $item['available_stock'] ?? 0 ?></td>
                                                <td class="text-end"><?= $item['minimum_stock'] ?? 0 ?></td>
                                                <td class="text-end">₱<?= number_format(($item['unit_price'] ?? 0), 2) ?></td>
                                                <td class="text-end">₱<?= number_format(($item['stock_value'] ?? 0), 2) ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary adjust-stock" 
                                                            data-product-id="<?= $item['product_id'] ?? '' ?>"
                                                            data-product-name="<?= esc($item['product_name'] ?? '') ?>">
                                                        <i class="fas fa-edit"></i> Adjust
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                No inventory data found. 
                                                <?php if (empty($products)): ?>
                                                    <div>No products are available in the system.</div>
                                                <?php else: ?>
                                                    <div>Click 'Add/Adjust Stock' to add inventory items.</div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Receives -->
        <?php if (!empty($pendingReceives)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Pending Receives</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Expected Delivery</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($pendingReceives)): ?>
                                <?php foreach($pendingReceives as $po): ?>
                                <tr>
                                    <td><?= esc($po['po_number'] ?? '') ?></td>
                                    <td><?= esc($po['supplier_name'] ?? 'N/A') ?></td>
                                    <td><span class="badge bg-<?= ($po['status'] ?? '') === 'approved' ? 'warning' : 'info' ?>"><?= ucfirst($po['status'] ?? '') ?></span></td>
                                    <td><?= !empty($po['expected_delivery_date']) ? date('M d, Y', strtotime($po['expected_delivery_date'])) : 'N/A' ?></td>
                                    <td>
                                        <a href="<?= site_url('purchase-orders/view/' . ($po['purchase_order_id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <h5>Adjust Stock</h5>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <form method="post" action="<?= site_url('/inventory/adjust') ?>">
                    <?= csrf_field() ?>
                    <?php if (!empty($selectedBranchId)): ?>
                        <input type="hidden" name="branch_id" value="<?= (int)$selectedBranchId ?>">
                    <?php endif; ?>
                    <div class="mb-2">
                        <label class="form-label">Product</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">— select product —</option>
                            <?php foreach(($products ?? []) as $p): ?>
                                <option value="<?= (int)$p['product_id'] ?>"><?= esc($p['product_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Quantity (use negative to deduct)</label>
                        <input type="number" class="form-control" name="qty" placeholder="e.g., 5 or -2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <input type="text" class="form-control" name="reason" placeholder="e.g., Adjustment, Damaged, Expired" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Apply Adjustment</button>
                </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
