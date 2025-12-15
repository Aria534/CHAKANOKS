<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Product - ChakaNoks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f5f5f5; }
        .sidebar { position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 2rem 1rem; overflow-y: auto; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%); color: white; border-radius: 12px 12px 0 0; }
        .form-label { font-weight: 600; color: #2c3e50; }
        .form-control, .form-select { border: 1px solid #e8e8e8; border-radius: 8px; }
        .form-control:focus, .form-select:focus { border-color: #b75a03ff; box-shadow: 0 0 0 0.2rem rgba(183,90,3,0.15); }
        .section-title { font-size: 1.1rem; font-weight: 700; color: #2c3e50; margin-top: 2rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #b75a03ff; }
        .btn-primary { background: #b75a03ff; border-color: #b75a03ff; }
        .btn-primary:hover { background: #ff9320ff; border-color: #ff9320ff; }
        .required { color: #dc3545; }
        .info-panel { background: #e3f2fd; border-left: 4px solid #2196F3; padding: 1.5rem; border-radius: 8px; }
    </style>
</head>
<body>
    <?php echo view('templete/sidebar', ['active' => 'inventory']); ?>

    <div class="main-content">
        <h2 class="mb-4"><i class="fas fa-plus-circle"></i> Create New Product</h2>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-box"></i> Product Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo site_url('product/store'); ?>">
                            <?php echo csrf_field(); ?>

                            <div class="section-title">Basic Information</div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="product_name" placeholder="e.g., Fresh Tomatoes" required value="<?php echo old('product_name'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Code</label>
                                    <input type="text" class="form-control" name="product_code" placeholder="e.g., PROD-001" value="<?php echo old('product_code'); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Product description..."><?php echo old('description'); ?></textarea>
                            </div>

                            <div class="section-title">Classification</div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category <span class="required">*</span></label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">— select category —</option>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['category_id']; ?>" <?php echo old('category_id') == $cat['category_id'] ? 'selected' : ''; ?>>
                                                <?php echo esc($cat['category_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Supplier <span class="required">*</span></label>
                                    <select class="form-select" name="supplier_id" required>
                                        <option value="">— select supplier —</option>
                                        <?php foreach($suppliers as $sup): ?>
                                            <option value="<?php echo $sup['supplier_id']; ?>" <?php echo old('supplier_id') == $sup['supplier_id'] ? 'selected' : ''; ?>>
                                                <?php echo esc($sup['supplier_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="section-title">Pricing & Stock Management</div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Unit Price (₱) <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="unit_price" placeholder="0.00" step="0.01" min="0" required value="<?php echo old('unit_price'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimum Stock Level</label>
                                    <input type="number" class="form-control" name="minimum_stock" placeholder="10" min="0" value="<?php echo old('minimum_stock') ?? 10; ?>">
                                </div>
                            </div>

                            <div class="section-title">Perishable Settings</div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_perishable" id="isPerishable" value="1" <?php echo old('is_perishable') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="isPerishable">This is a perishable product</label>
                                </div>
                            </div>

                            <div class="mb-3" id="shelfLifeDiv" style="display: none;">
                                <label class="form-label">Shelf Life (Days)</label>
                                <input type="number" class="form-control" name="shelf_life_days" placeholder="e.g., 7" min="0" value="<?php echo old('shelf_life_days'); ?>">
                            </div>

                            <div class="section-title">Branch Assignment & Initial Stock</div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Branch <span class="required">*</span></label>
                                    <select class="form-select" name="branch_id" required>
                                        <option value="">— select branch —</option>
                                        <?php foreach($branches as $branch): ?>
                                            <option value="<?php echo $branch['branch_id']; ?>" <?php echo old('branch_id') == $branch['branch_id'] ? 'selected' : ''; ?>>
                                                <?php echo esc($branch['branch_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Initial Stock Quantity</label>
                                    <input type="number" class="form-control" name="initial_stock" placeholder="0" min="0" value="<?php echo old('initial_stock') ?? 0; ?>">
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Create Product
                                </button>
                                <a href="<?php echo site_url('inventory'); ?>" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="info-panel">
                    <h6><i class="fas fa-info-circle"></i> Required Fields</h6>
                    <ul class="small">
                        <li>Product Name</li>
                        <li>Category</li>
                        <li>Supplier</li>
                        <li>Unit Price</li>
                        <li>Branch</li>
                    </ul>

                    <h6 class="mt-3"><i class="fas fa-lightbulb"></i> Tips</h6>
                    <ul class="small">
                        <li>Product Code is optional but recommended</li>
                        <li>Set minimum stock level for low stock alerts</li>
                        <li>Mark as perishable if product has expiry date</li>
                        <li>Initial stock will be recorded as inventory movement</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isPerishableCheckbox = document.getElementById('isPerishable');
            const shelfLifeDiv = document.getElementById('shelfLifeDiv');

            isPerishableCheckbox.addEventListener('change', function() {
                shelfLifeDiv.style.display = this.checked ? 'block' : 'none';
            });

            isPerishableCheckbox.dispatchEvent(new Event('change'));

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
