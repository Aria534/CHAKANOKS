<?php?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Central Office Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f9fafb; }
        .wrap { padding:24px; }
        .title { font-size:28px; font-weight:800; color:#111827; margin-bottom:20px; }
        .card { background:#fff; border-radius:12px; padding:18px; box-shadow:0 4px 12px rgba(0,0,0,.08); margin-bottom:20px; }
        .card h3 { margin:0 0 15px 0; font-size:16px; font-weight:600; color:#111827; }
        .stat { font-size:22px; font-weight:bold; color:#111827; margin-bottom:4px; }
        .subtext { font-size:13px; color:#6b7280; }
        /* ✅ smaller donut chart */
        #categoryChart {
            max-width: 250px;
            max-height: 180px;
            margin: auto;
        }
    </style>
</head>
<body>
    <!-- ✅ Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= site_url('dashboard') ?>">ChakaNoks Central Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="<?= site_url('dashboard') ?>">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('branches') ?>">Branches</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('products') ?>">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('orders') ?>">Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('inventory') ?>">Inventory</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="<?= site_url('logout') ?>">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="wrap">
        <div class="title">Central Office Dashboard</div>

        <div class="row">
            <!-- Quick Stats -->
            <div class="col-md-4">
                <div class="card text-center">
                    <h3>Total Stock Value</h3>
                    <div class="stat">₱<?= number_format($stockValue ?? 0, 2) ?></div>
                    <div class="subtext">as of today</div>
                </div>
                <div class="card text-center">
                    <h3>Low Stock Alerts</h3>
                    <div class="stat"><?= $lowStock ?? 0 ?></div>
                    <div class="subtext">items need restock</div>
                </div>
                <div class="card text-center">
                    <h3>Pending Orders</h3>
                    <div class="stat"><?= $pendingOrders ?? 0 ?></div>
                    <div class="subtext">awaiting approval</div>
                </div>
            </div>

            <!-- Graphs -->
            <div class="col-md-8">
                <div class="card text-center">
                    <h3>Stock by Category</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="card">
                    <h3>Orders Trend (This Year)</h3>
                    <canvas id="ordersChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ✅ PHP variables → JS
        const categoryLabels = <?= json_encode(!empty($categoryStats) ? array_column($categoryStats, 'category_name') : []) ?>;
        const categoryDataVals = <?= json_encode(!empty($categoryStats) ? array_column($categoryStats, 'qty') : []) ?>;

        const orderLabels = <?= json_encode(!empty($ordersTrend) ? array_map(fn($o) => date('M', mktime(0,0,0,$o['month'],1)), $ordersTrend) : []) ?>;
        const orderDataVals = <?= json_encode(!empty($ordersTrend) ? array_column($ordersTrend, 'total') : []) ?>;

        // ✅ Category Chart (Donut)
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryDataVals,
                    backgroundColor: ['#f87171','#60a5fa','#34d399','#fbbf24','#a78bfa'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%'
            }
        });

        // ✅ Orders Trend Chart (Line)
        new Chart(document.getElementById('ordersChart'), {
            type: 'line',
            data: {
                labels: orderLabels,
                datasets: [{
                    label: 'Orders',
                    data: orderDataVals,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.3)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });
    </script>
</body>
</html>
