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

        .btn-primary { background:#b75a03ff; border-color:#b75a03ff; cursor: pointer !important; }
        .btn-primary:hover { background:#ff9320ff; border-color:#ff9320ff; }
        
        .btn-success { cursor: pointer !important; }
        .btn-outline-primary { cursor: pointer !important; }
        
        a.btn { 
            pointer-events: auto !important; 
            cursor: pointer !important; 
            text-decoration: none !important;
        }
        
        a.btn:hover {
            text-decoration: none !important;
        }

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
                        <div style="display: flex; gap: 0.5rem;">
                            <?php $role = (string)(session('role') ?? ''); ?>
                            <?php if (in_array($role, ['inventory_staff','central_admin','system_admin'])): ?>
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createProductModal">
                                    <i class="fas fa-plus-circle"></i> Create Product
                                </button>
                                <a href="<?= site_url('inventory?mode=add' . (isset($_GET['branch_id']) ? '&branch_id=' . $_GET['branch_id'] : '')) ?>" 
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-plus"></i> Add Stock
                                </a>
                            <?php endif; ?>
                            <a href="<?= site_url('inventory?mode=adjust' . (isset($_GET['branch_id']) ? '&branch_id=' . $_GET['branch_id'] : '')) ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Adjust Stock
                            </a>
                        </div>
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
                                                $isOutOfStock = isset($item['available_stock']) && $item['available_stock'] == 0;
                                                $isLowStock = isset($item['available_stock']) && 
                                                            isset($item['minimum_stock']) && 
                                                            $item['available_stock'] <= $item['minimum_stock'] &&
                                                            $item['available_stock'] > 0;
                                                $rowClass = $isOutOfStock ? 'table-danger' : ($isLowStock ? 'table-warning' : '');
                                            ?>
                                            <tr class="<?= $rowClass ?>">
                                                <td><?= esc($item['product_name'] ?? 'N/A') ?><?php if ($isOutOfStock): ?> <span class="badge bg-danger">Out of Stock</span><?php endif; ?></td>
                                                <td class="text-end"><?= $item['current_stock'] ?? 0 ?></td>
                                                <td class="text-end" style="<?= $isOutOfStock ? 'color: #dc3545; font-weight: bold;' : '' ?>"><?= $item['available_stock'] ?? 0 ?></td>
                                                <td class="text-end"><?= $item['minimum_stock'] ?? 0 ?></td>
                                                <td class="text-end">₱<?= number_format(($item['unit_price'] ?? 0), 2) ?></td>
                                                <td class="text-end">₱<?= number_format(($item['stock_value'] ?? 0), 2) ?></td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('inventory?mode=adjust&product_id=' . ($item['product_id'] ?? '') . (isset($_GET['branch_id']) ? '&branch_id=' . $_GET['branch_id'] : '')) ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Adjust
                                                    </a>
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

        <?php 
        $request = \Config\Services::request();
        $mode = $request->getGet('mode') ?? ''; 
        $showForm = in_array($mode, ['add', 'adjust']);
        $isAddMode = $mode === 'add';
        ?>
        
        <?php if ($showForm): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><?= $isAddMode ? 'Add Stock' : 'Adjust Stock' ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= site_url('inventory/adjust') ?>">
                            <?= csrf_field() ?>
                            <?php if (!empty($selectedBranchId)): ?>
                                <input type="hidden" name="branch_id" value="<?= (int)$selectedBranchId ?>">
                            <?php endif; ?>
                            <input type="hidden" name="mode" value="<?= esc($mode) ?>">
                            
                            <!-- Product Selection -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Product <span class="text-danger">*</span></label>
                                <?php $selectedProductId = (int)($request->getGet('product_id') ?? 0); ?>
                                <select name="product_id" class="form-select" required id="productSelect">
                                    <option value="">— select product —</option>
                                    <?php foreach(($products ?? []) as $p): ?>
                                        <option value="<?= (int)$p['product_id'] ?>" 
                                                data-category="<?= esc($p['category_name'] ?? 'N/A') ?>"
                                                data-supplier="<?= esc($p['supplier_name'] ?? 'N/A') ?>"
                                                data-price="<?= (float)($p['unit_price'] ?? 0) ?>"
                                                data-code="<?= esc($p['product_code'] ?? '') ?>"
                                                <?= $selectedProductId === (int)$p['product_id'] ? 'selected' : '' ?>>
                                            <?= esc($p['product_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Product Details Display -->
                            <div class="row mb-3" id="productDetails" style="display: none;">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <small class="text-muted">Product Code</small>
                                            <p class="mb-0 fw-bold" id="productCode">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <small class="text-muted">Category</small>
                                            <p class="mb-0 fw-bold" id="productCategory">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <small class="text-muted">Supplier</small>
                                            <p class="mb-0 fw-bold" id="productSupplier">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <small class="text-muted">Unit Price</small>
                                            <p class="mb-0 fw-bold" id="productPrice">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quantity Input -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           name="qty" 
                                           id="qtyInput"
                                           placeholder="<?= $isAddMode ? 'e.g., 100' : 'e.g., 5 or -2' ?>" 
                                           <?= $isAddMode ? 'min="1"' : '' ?>
                                           required>
                                    <span class="input-group-text" id="totalValue">₱0.00</span>
                                </div>
                                <?php if ($isAddMode): ?>
                                    <small class="text-muted">Enter the quantity to add to inventory</small>
                                <?php else: ?>
                                    <small class="text-muted">Use negative numbers to deduct stock</small>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Reason -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Reason <span class="text-muted">(Optional)</span></label>
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
                            
                            <!-- Action Buttons -->
                            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                                <button type="submit" class="btn btn-success" style="flex: 1;">
                                    <i class="fas fa-check"></i> <?= $isAddMode ? 'Add Stock' : 'Apply Adjustment' ?>
                                </button>
                                <a href="<?= site_url('inventory' . (isset($_GET['branch_id']) ? '?branch_id=' . $_GET['branch_id'] : '')) ?>" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.querySelector('select[name="product_id"]');
            const qtyInput = document.querySelector('input[name="qty"]');
            const productDetails = document.querySelector('#productDetails');
            const productCode = document.querySelector('#productCode');
            const productCategory = document.querySelector('#productCategory');
            const productSupplier = document.querySelector('#productSupplier');
            const productPrice = document.querySelector('#productPrice');
            const totalValue = document.querySelector('#totalValue');
            
            // Update product details when product is selected
            if (productSelect) {
                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (this.value) {
                        const code = selectedOption.getAttribute('data-code');
                        const category = selectedOption.getAttribute('data-category');
                        const supplier = selectedOption.getAttribute('data-supplier');
                        const price = parseFloat(selectedOption.getAttribute('data-price'));
                        
                        productCode.textContent = code || '-';
                        productCategory.textContent = category || '-';
                        productSupplier.textContent = supplier || '-';
                        productPrice.textContent = '₱' + price.toFixed(2);
                        
                        if (productDetails) {
                            productDetails.style.display = 'flex';
                        }
                        
                        // Update total value when quantity changes
                        if (qtyInput) {
                            updateTotalValue(price);
                        }
                    } else {
                        if (productDetails) {
                            productDetails.style.display = 'none';
                        }
                    }
                });
                
                // Trigger change event if product is pre-selected
                if (productSelect.value) {
                    productSelect.dispatchEvent(new Event('change'));
                }
            }
            
            // Update total value on quantity input
            if (qtyInput && productSelect) {
                qtyInput.addEventListener('input', function() {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                    updateTotalValue(price);
                });
            }
            
            function updateTotalValue(unitPrice) {
                if (qtyInput && totalValue) {
                    const qty = parseInt(qtyInput.value) || 0;
                    const total = qty * unitPrice;
                    totalValue.textContent = '₱' + total.toFixed(2);
                }
            }
            
            // If product is pre-selected, scroll to the adjustment form and focus quantity field
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

    <!-- Create Product Modal -->
    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%); color: white;">
                    <h5 class="modal-title" id="createProductLabel"><i class="fas fa-plus-circle"></i> Create New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form method="post" action="<?= site_url('product/store') ?>" id="createProductForm">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" placeholder="e.g., Fresh Tomatoes" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Product Code</label>
                                <input type="text" class="form-control" name="product_code" placeholder="e.g., PROD-001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Unit Price (₱) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="unit_price" placeholder="0.00" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Product description..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">— select category —</option>
                                    <?php if (!empty($products)): ?>
                                        <?php $db = db_connect(); $categories = $db->table('categories')->where('status', 'active')->get()->getResultArray(); ?>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?= $cat['category_id'] ?>"><?= esc($cat['category_name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select" name="supplier_id" required>
                                    <option value="">— select supplier —</option>
                                    <?php if (!empty($products)): ?>
                                        <?php $suppliers = $db->table('suppliers')->where('status', 'active')->get()->getResultArray(); ?>
                                        <?php foreach($suppliers as $sup): ?>
                                            <option value="<?= $sup['supplier_id'] ?>"><?= esc($sup['supplier_name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Branch <span class="text-danger">*</span></label>
                                <select class="form-select" name="branch_id" required>
                                    <option value="">— select branch —</option>
                                    <?php if (!empty($branches)): ?>
                                        <?php foreach($branches as $branch): ?>
                                            <?php if (strpos(strtolower($branch['branch_name']), 'central office') === false): ?>
                                                <option value="<?= $branch['branch_id'] ?>"><?= esc($branch['branch_name']) ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Minimum Stock Level</label>
                                <input type="number" class="form-control" name="minimum_stock" placeholder="10" min="0" value="10">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Initial Stock Quantity</label>
                            <input type="number" class="form-control" name="initial_stock" placeholder="0" min="0" value="0">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_perishable" id="isPerishableModal" value="1">
                                <label class="form-check-label" for="isPerishableModal">This is a perishable product</label>
                            </div>
                        </div>

                        <div class="mb-3" id="shelfLifeDivModal" style="display: none;">
                            <label class="form-label fw-bold">Shelf Life (Days)</label>
                            <input type="number" class="form-control" name="shelf_life_days" placeholder="e.g., 7" min="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="createProductForm" class="btn btn-success">
                        <i class="fas fa-save"></i> Create Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isPerishableCheckbox = document.getElementById('isPerishableModal');
            const shelfLifeDiv = document.getElementById('shelfLifeDivModal');
            const productNameInput = document.querySelector('input[name=product_name]');
            const productCodeInput = document.querySelector('input[name=product_code]');
            const categorySelect = document.querySelector('select[name=category_id]');
            const supplierSelect = document.querySelector('select[name=supplier_id]');

            // Category to Supplier mapping
            const categorySupplierMap = {
                '1': '1',  // Vegetables -> Fresh Produce Supply Co.
                '2': '2',  // Beverages -> Beverage Solutions Inc.
                '3': '3',  // Condiments -> Kitchen Essentials Supply
                '4': '4',  // Spices -> Kitchen Essentials Supply
                '5': '5',  // Dairy -> Dairy & Frozen Foods Co.
                '6': '5',  // Frozen Foods -> Dairy & Frozen Foods Co.
                '7': '2',  // Meat & Poultry -> Meat & Poultry Distributors
            };

            // Auto-generate product code based on category
            if (categorySelect && productCodeInput) {
                categorySelect.addEventListener('change', function() {
                    const categoryId = this.value;
                    const categoryName = this.options[this.selectedIndex].text;
                    
                    if (categoryId) {
                        // Get current count of products in this category
                        const categoryPrefix = categoryName.substring(0, 3).toUpperCase();
                        const timestamp = Date.now().toString().slice(-4);
                        const autoCode = categoryPrefix + '-' + timestamp;
                        productCodeInput.value = autoCode;
                        
                        // Auto-select supplier based on category
                        if (categorySupplierMap[categoryId]) {
                            supplierSelect.value = categorySupplierMap[categoryId];
                        }
                    }
                });
            }

            // Perishable checkbox handler
            if (isPerishableCheckbox) {
                isPerishableCheckbox.addEventListener('change', function() {
                    shelfLifeDiv.style.display = this.checked ? 'block' : 'none';
                });
            }
        });
    </script>
</body>
</html>

