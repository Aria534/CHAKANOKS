<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inventory Scan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .wrap { padding:24px; max-width:760px; margin:0 auto; }
    .title { font-size:26px; font-weight:800; color:#111827; margin-bottom:14px; }
  </style>
</head>
<body>
  <nav class="navbar navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= site_url('dashboard') ?>">ChakaNoks</a>
      <div>
        <a class="nav-link d-inline text-white" href="<?= site_url('orders') ?>">Orders</a>
        <a class="nav-link d-inline text-white" href="<?= site_url('inventory') ?>">Inventory</a>
        <a class="nav-link d-inline text-danger" href="<?= site_url('logout') ?>">Logout</a>
      </div>
    </div>
  </nav>

  <div class="wrap">
    <div class="title">Barcode Scan</div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <div class="card p-3 mb-3">
      <p class="text-muted">Use your USB barcode scanner to focus the barcode field and scan. Quantity: positive to receive, negative to deduct.</p>
      <form method="post" action="<?= site_url('/inventory/scan') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label">Barcode / Product Code</label>
          <input type="text" class="form-control" name="barcode" placeholder="Scan or type barcode" autofocus required />
        </div>
        <div class="mb-3">
          <label class="form-label">Quantity (use negative to deduct)</label>
          <input type="number" class="form-control" name="qty" value="1" required />
        </div>
        <button type="submit" class="btn btn-primary">Process Scan</button>
        <a href="<?= site_url('dashboard/inventory') ?>" class="btn btn-secondary">Back</a>
      </form>
    </div>

    <div class="card p-3">
      <h5>Tips</h5>
      <ul class="mb-0">
        <li>Ensure the correct branch is set as your primary in your user profile.</li>
        <li>For perishable products, the system logs an estimated expiry based on <em>shelf_life_days</em>.</li>
      </ul>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
