<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Supplier - ChakaNoks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }
        .form-card {
            background: #fff;
            border-radius: 14px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #e8e8e8;
            max-width: 800px;
        }
        .form-label {
            font-weight: 600;
            color: #503e2cff;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #ff9320ff;
            box-shadow: 0 0 0 0.2rem rgba(255, 147, 32, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(183, 90, 3, 0.35);
        }
        .btn-secondary {
            border-radius: 8px;
            padding: 0.6rem 2rem;
            font-weight: 600;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'suppliers']) ?>

    <div class="main-content">
        <div class="page-title">Create New Supplier</div>

        <div class="form-card">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Validation Errors:</strong>
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('suppliers/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" 
                               value="<?= old('supplier_name') ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" 
                               value="<?= old('contact_person') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?= old('phone') ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email') ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?= old('address') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="payment_terms" class="form-label">Payment Terms <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="payment_terms" name="payment_terms" 
                               value="<?= old('payment_terms', 'Net 30') ?>" placeholder="e.g., Net 30" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="delivery_time" class="form-label">Delivery Time (days) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="delivery_time" name="delivery_time" 
                               value="<?= old('delivery_time', '3') ?>" min="1" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Create Supplier</button>
                    <a href="<?= site_url('suppliers') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

