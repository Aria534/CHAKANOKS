<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            color: #503e2cff;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 220px;
            background: #1a1a1a;
            color: #b75a03ff;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .sidebar .logo { font-size:1.5rem; font-weight:700; color:#b75a03ff; margin-bottom:2rem; }
        .sidebar nav { display: flex; flex-direction: column; gap: 0.6rem; }
        .sidebar nav a {
            color:#aaa;
            text-decoration:none;
            font-weight:500;
            padding:0.6rem 1rem;
            border-radius:6px;
            transition:0.2s;
        }
        .sidebar nav a:hover { background:#2c2c2c; color:#fff; }
        .sidebar a.active, .sidebar a:hover {
            background: #ff9320ff;
            color: #fff;
        }
        .sidebar nav a.logout { color:#e74c3c !important; margin-top:auto; }

        /* --- Main content --- */
        .main-content { margin-left: 220px; padding: 2rem; }

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

        .table-card { background:#fff; border-radius:14px; padding:1.25rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; }
        .table-card table { width:100%; border-collapse:collapse; font-size:14px; }
        .table-card th { text-align:left; padding:.8rem; font-weight:700; color:#666; border-bottom:1px solid #f0f0f0; }
        .table-card td { padding:.8rem; border-bottom:1px solid #f7f7f7; color:#444; }
        .table-card tbody tr:hover { background-color: #f9f9f9; }

        @media (max-width:768px){ .main-content { margin-left: 0; padding:1rem; } .sidebar { width: 100%; height: auto; position: relative; } }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">ChakaNoks</div>
        <nav>
            <a href="<?= site_url('dashboard') ?>">Dashboard</a>
            <a href="<?= site_url('branches') ?>">Branches</a>
            <a href="<?= site_url('products') ?>" class="active">Products</a>
            <a href="<?= site_url('orders') ?>">Orders</a>
            <a href="<?= site_url('inventory') ?>">Inventory</a>
            <a href="<?= site_url('logout') ?>" class="logout">Logout</a>
        </nav>
    </aside>

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
