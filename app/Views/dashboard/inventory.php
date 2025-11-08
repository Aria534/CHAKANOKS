<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f9fafb; }
        .wrap { padding:24px; }
        .title { font-size:24px; font-weight:800; color:#111827; margin-bottom:20px; }
        .card { background:#fff; border-radius:12px; padding:18px; box-shadow:0 4px 12px rgba(0,0,0,.08); margin-bottom:20px; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { color:#374151; font-size:13px; text-transform:uppercase; letter-spacing:.04em; }
    </style>
</head>
<body>
    <!-- ✅ Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= site_url('dashboard') ?>">ChakaNoks Central Admin</a>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="<?= site_url('dashboard') ?>">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('branches') ?>">Branches</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('products') ?>">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('orders') ?>">Orders</a></li>
            <li class="nav-item"><a class="nav-link active" href="<?= site_url('inventory') ?>">Inventory</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="<?= site_url('logout') ?>">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="wrap">
        <div class="title">Inventory Management</div>
        <div class="card">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Branch</th>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($inventory as $i): ?>
                    <tr>
                        <td><?= $i['inventory_id'] ?></td>
                        <td><?= esc($i['branch_name']) ?></td>
                        <td><?= esc($i['product_name']) ?></td>
                        <td><?= $i['current_stock'] ?></td>
                        <td>₱<?= number_format($i['unit_price'], 2) ?></td>
                        <td>₱<?= number_format($i['stock_value'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
