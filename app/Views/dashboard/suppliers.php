<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Suppliers - ChakaNoks Central Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }
        .table-card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
            margin-top: 1.5rem;
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
        .btn-add { 
            margin-bottom: 1.5rem; 
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.25);
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(183, 90, 3, 0.35);
            color: #fff;
        }
        .badge {
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success {
            background-color: #28a745;
            color: #fff;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
            margin-right: 0.25rem;
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'suppliers']) ?>

    <div class="main-content">
        <div class="page-title">Suppliers Management</div>

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

        <a href="<?= site_url('suppliers/create') ?>" class="btn btn-primary btn-add">+ Add New Supplier</a>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Payment Terms</th>
                        <th>Delivery Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($suppliers)): ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?= esc($supplier['supplier_id']) ?></td>
                                <td><strong><?= esc($supplier['supplier_name']) ?></strong></td>
                                <td><?= esc($supplier['contact_person']) ?></td>
                                <td><?= esc($supplier['phone']) ?></td>
                                <td><?= esc($supplier['email']) ?></td>
                                <td><?= esc($supplier['payment_terms']) ?></td>
                                <td><?= esc($supplier['delivery_time']) ?> days</td>
                                <td>
                                    <span class="badge badge-<?= $supplier['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst(esc($supplier['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= site_url('suppliers/edit/' . $supplier['supplier_id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="<?= site_url('suppliers/delete/' . $supplier['supplier_id']) ?>" method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No suppliers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

