<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Products</title>
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
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'products']) ?>

    <!-- Main content -->
    <div class="main-content">
        <div class="page-title">Products</div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td><?= esc($p['product_id']) ?></td>
                            <td><?= esc($p['product_name']) ?></td>
                            <td><?= esc($p['category_name']) ?></td>
                            <td>₱<?= number_format($p['unit_price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
