<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>ChakaNoks - Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .table-card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
        }
        
        .table-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .table-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .button-group {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        
        .button-group a {
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        .table-card table { 
            width:100%; 
            border-collapse:collapse; 
            font-size:14px; 
        }
        .table-card th { 
            text-align:left; 
            padding:.8rem; 
            font-weight:700; 
            color:#666; 
            border-bottom:2px solid #f0f0f0; 
            background-color: #fafafa;
        }
        .table-card td { 
            padding:.8rem; 
            border-bottom:1px solid #f7f7f7; 
            color:#444; 
        }
        .table-card tbody tr:hover { 
            background-color: #f9f9f9; 
        }

        .form-select {
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }
        .form-select:focus {
            border-color: #b75a03ff;
            box-shadow: 0 0 0 0.2rem rgba(183, 90, 3, 0.15);
        }

        .btn-add-stock {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: #fff !important;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25);
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .btn-add-stock:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.35);
            color: #fff !important;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-adjust-stock {
            display: inline-block;
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff !important;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.25);
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .btn-adjust-stock:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(183, 90, 3, 0.35);
            color: #fff !important;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-adjust {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
            background-color: #0d6efd;
            color: white !important;
            border: none;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .btn-adjust:hover {
            background-color: #0b5ed7;
            color: white !important;
            transform: translateY(-1px);
            cursor: pointer;
            text-decoration: none;
        }

        .badge-low-stock {
            background-color: #dc3545;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        .badge-ok {
            background-color: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        .modal-header {
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            color: white;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'inventory']) ?>

    <div class="main-content">
        <div class="page-title">Inventory Management</div>

        <?php $role = (string)(session('role') ?? ''); ?>

        <!-- Branch Selector for Central Admin -->
        <?php if (in_array($role, ['central_admin','system_admin'])): ?>
            <div style="margin-bottom:1.5rem;">
                <form method="get" action="<?= site_url('inventory') ?>" style="display:flex; gap:1rem; align-items:center;">
                    <label style="font-weight:500; color:#2c3e50;">View by Branch:</label>
                    <select name="branch_id" class="form-select" style="max-width:250px;" onchange="this.form.submit()">
                        <option value="all" <?= (isset($_GET['branch_id']) && $_GET['branch_id'] === 'all') ? 'selected' : '' ?>>All Branches</option>
                        <?php if (!empty($branches ?? [])): ?>
                            <?php foreach($branches as $b): ?>
                                <option value="<?= (int)$b['branch_id'] ?>" <?= (isset($_GET['branch_id']) && (int)$_GET['branch_id'] === (int)$b['branch_id']) ? 'selected' : '' ?>>
                                    <?= esc($b['branch_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </form>
            </div>
        <?php endif; ?>

        <?php
        // Calculate low stock count
        $lowStockCount = 0;
        if (!empty($inventory)) {
            foreach($inventory as $item) {
                $currentStock = (int)($item['current_stock'] ?? $item['available_stock'] ?? 0);
                $minStock = (int)($item['minimum_stock'] ?? 0);
                if ($currentStock <= $minStock) {
                    $lowStockCount++;
                }
            }
        }
        ?>

        <!-- Low Stock Alert -->
        <?php if ($lowStockCount > 0): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Low Stock Alert:</strong> You have <?= $lowStockCount ?> item(s) below minimum stock level.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Success/Error Messages -->
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Inventory Table -->
        <div class="table-card">
            <div class="table-card-header">
                <h3 class="table-card-title">Current Inventory</h3>
                <div class="button-group" style="display: flex; gap: 0.75rem;">
                    <a href="<?= site_url('inventory?mode=add' . (isset($_GET['branch_id']) ? '&branch_id=' . $_GET['branch_id'] : '')) ?>" 
                       class="btn btn-primary btn-add-stock"
                       style="display: inline-block; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white !important; text-decoration: none; padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 600;">
                        Add Stock
                    </a>
                    <a href="<?= site_url('inventory?mode=adjust' . (isset($_GET['branch_id']) ? '&branch_id=' . $_GET['branch_id'] : '')) ?>" 
                       class="btn btn-secondary btn-adjust-stock"
                       style="display: inline-block; background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%); color: white !important; text-decoration: none; padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 600;">
                        Adjust Stock
                    </a>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>In Stock</th>
                        <th>Available</th>
                        <th>Min Level</th>
                        <th>Unit Price</th>
                        <th>Stock Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventory)): ?>
                        <?php foreach($inventory as $i): ?>
                        <?php
                            $currentStock = (int)($i['current_stock'] ?? $i['available_stock'] ?? 0);
                            $availableStock = (int)($i['available_stock'] ?? $currentStock);
                            $minStock = (int)($i['minimum_stock'] ?? 0);
                            $isLowStock = $currentStock <= $minStock;
                        ?>
                        <tr>
                            <td>
                                <strong><?= esc($i['product_name'] ?? 'N/A') ?></strong>
                                <?php if (isset($i['branch_name']) && $i['branch_name']): ?>
                                    <br><small class="text-muted"><?= esc($i['branch_name']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $currentStock ?>
                                <?php if ($isLowStock): ?>
                                    <span class="badge-low-stock">Low</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $availableStock ?></td>
                            <td><?= $minStock ?></td>
                            <td>₱<?= number_format((float)($i['unit_price'] ?? 0), 2) ?></td>
                            <td>₱<?= number_format((float)($i['stock_value'] ?? 0), 2) ?></td>
                            <td>
                                <a href="<?= site_url('inventory?mode=adjust&product_id=' . ($i['product_id'] ?? '') . (isset($_GET['branch_id']) ? '&branch_id=' . $_GET['branch_id'] : ($i['branch_id'] ? '&branch_id=' . $i['branch_id'] : ''))) ?>" 
                                   class="btn btn-adjust"
                                   style="display: inline-block; padding: 0.25rem 0.75rem; font-size: 0.875rem; border-radius: 6px; background-color: #0d6efd; color: white !important; text-decoration: none; cursor: pointer;">
                                    Adjust
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No inventory data found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Adjust Stock Form -->
        <?php 
        $request = \Config\Services::request();
        $mode = $request->getGet('mode') ?? ''; 
        $showForm = in_array($mode, ['add', 'adjust']);
        $isAddMode = $mode === 'add';
        ?>
        
        <?php if ($showForm): ?>
        <div class="table-card" style="margin-top: 1.5rem;">
            <div class="table-card-header">
                <h3 class="table-card-title"><?= $isAddMode ? 'Add Stock' : 'Adjust Stock' ?></h3>
            </div>
            
            <?php 
            $currentBranchId = (int)($request->getGet('branch_id') ?? 0);
            $viewingAllBranches = !$currentBranchId || (isset($_GET['branch_id']) && $_GET['branch_id'] === 'all');
            ?>
            
            <?php if ($viewingAllBranches && in_array($role, ['central_admin','system_admin'])): ?>
            <div class="alert alert-info" style="margin: 1rem;">
                <strong>Note:</strong> Please select a specific branch from the dropdown above to add or adjust stock. Stock adjustments must be made for a specific branch.
            </div>
            <?php else: ?>
            <form method="post" action="<?= site_url('inventory/adjust') ?>" style="padding: 1rem;">
                <?= csrf_field() ?>
                
                <?php 
                $branchId = (int)($request->getGet('branch_id') ?? 0);
                if ($branchId > 0): 
                ?>
                    <input type="hidden" name="branch_id" value="<?= $branchId ?>">
                <?php endif; ?>
                
                <input type="hidden" name="mode" value="<?= esc($mode) ?>">
                
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #2c3e50;">Product</label>
                    <?php $selectedProductId = (int)($request->getGet('product_id') ?? 0); ?>
                    <?php if (!empty($products)): ?>
                        <select name="product_id" class="form-select" required>
                            <option value="">— select product —</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?= (int)$p['product_id'] ?>" <?= $selectedProductId === (int)$p['product_id'] ? 'selected' : '' ?>>
                                    <?= esc($p['product_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            No products available. Please add products first in the Products section.
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($products)): ?>
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #2c3e50;">
                        Quantity <?= $isAddMode ? '' : '(use negative to deduct)' ?>
                    </label>
                    <input type="number" 
                           class="form-control" 
                           name="qty" 
                           placeholder="<?= $isAddMode ? 'e.g., 100' : 'e.g., 5 or -2' ?>" 
                           <?= $isAddMode ? 'min="1"' : '' ?>
                           required>
                    <?php if ($isAddMode): ?>
                        <small class="text-muted">Enter the quantity to add to inventory</small>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #2c3e50;">
                        Reason <span class="text-muted" style="font-weight: 400;">(Optional)</span>
                    </label>
                    <select name="reason" class="form-select">
                        <option value="">— select reason —</option>
                        <?php if ($isAddMode): ?>
                            <option value="New stock delivery">New stock delivery</option>
                            <option value="Restocking">Restocking</option>
                            <option value="Transfer from another branch">Transfer from another branch</option>
                            <option value="Return from customer">Return from customer</option>
                            <option value="Initial stock">Initial stock</option>
                        <?php else: ?>
                            <option value="Manual adjustment">Manual adjustment</option>
                            <option value="Stock count correction">Stock count correction</option>
                            <option value="Damaged goods">Damaged goods</option>
                            <option value="Expired products">Expired products</option>
                            <option value="Spoilage">Spoilage</option>
                            <option value="Theft/Loss">Theft/Loss</option>
                            <option value="Transfer to another branch">Transfer to another branch</option>
                            <option value="Customer return">Customer return</option>
                            <option value="Quality control rejection">Quality control rejection</option>
                        <?php endif; ?>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <?php endif; ?>
                
                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" 
                            class="btn btn-primary" 
                            style="background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%); border: none; color: white; font-weight: 600; padding: 0.6rem 1.5rem; border-radius: 8px; flex: 1;"
                            <?= empty($products) ? 'disabled' : '' ?>>
                        <?= $isAddMode ? 'Add Stock' : 'Apply Adjustment' ?>
                    </button>
                    <a href="<?= site_url('inventory' . (isset($_GET['branch_id']) ? '?branch_id=' . $_GET['branch_id'] : '')) ?>" 
                       class="btn btn-secondary" 
                       style="padding: 0.6rem 1.5rem; border-radius: 8px;">
                        Cancel
                    </a>
                </div>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // If product is pre-selected, scroll to the adjustment form and focus quantity field
            const productSelect = document.querySelector('select[name="product_id"]');
            const qtyInput = document.querySelector('input[name="qty"]');
            
            if (productSelect && productSelect.value) {
                // Scroll to the adjustment form
                const adjustForm = productSelect.closest('form');
                if (adjustForm) {
                    adjustForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Focus the quantity input for immediate entry
                if (qtyInput) {
                    setTimeout(() => {
                        qtyInput.focus();
                    }, 500);
                }
            }
            
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
