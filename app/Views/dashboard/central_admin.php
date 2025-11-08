<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Central Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f0f4f8;
    }
    h1 {
      color: #333;
    }
    .metrics {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }
    .metric-box {
      background: white;
      padding: 20px;
      border-radius: 8px;
      flex: 1;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }
    .metric-box h2 {
      margin: 0;
      font-size: 2em;
      color: #007bff;
    }
    .metric-box p {
      margin: 5px 0 0;
      font-weight: bold;
      color: #555;
    }
    .charts {
      display: flex;
      gap: 40px;
      flex-wrap: wrap;
    }
    .chart-container {
      background: white;
      padding: 20px;
      border-radius: 8px;
      flex: 1 1 400px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <?= view('partials/navbar', ['active' => 'dashboard']) ?>

  <div class="container-fluid mt-3">
  <h1>Central Admin Dashboard</h1>
  <div class="metrics">
    <div class="metric-box">
      <h2>₱<?= number_format($stockValue, 2) ?></h2>
      <p>Total Stock Value</p>
    </div>
    <div class="metric-box">
      <h2><?= esc($lowStock) ?></h2>
      <p>Low Stock Alerts</p>
    </div>
    <div class="metric-box">
      <h2><?= esc($pendingOrders) ?></h2>
      <p>Pending Orders</p>
    </div>
  </div>

  <div class="charts">
    <div class="chart-container">
      <h3>Stock per Category</h3>
      <canvas id="categoryChart"></canvas>
    </div>
    <div class="chart-container">
      <h3>Orders Trend (This Year)</h3>
      <canvas id="ordersTrendChart"></canvas>
    </div>
  </div>

  <div class="charts" style="margin-top:24px;">
    <div class="chart-container">
      <h3>Low Stock Items (Top 10)</h3>
      <?php if (!empty($lowStockList)): ?>
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Branch</th>
              <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Product</th>
              <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">Available</th>
              <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">Min</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lowStockList as $row): ?>
              <tr>
                <td style="padding:8px;border-bottom:1px solid #f3f4f6;"><?= esc($row['branch_name']) ?></td>
                <td style="padding:8px;border-bottom:1px solid #f3f4f6;"><?= esc($row['product_name']) ?></td>
                <td style="padding:8px;border-bottom:1px solid #f3f4f6; text-align:right;"><?= (int)$row['available_stock'] ?></td>
                <td style="padding:8px;border-bottom:1px solid #f3f4f6; text-align:right;"><?= (int)$row['minimum_stock'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No low stock items.</p>
      <?php endif; ?>
    </div>
    <div class="chart-container">
      <h3>Pending Purchase Requests (Latest 5)</h3>
      <?php if (!empty($pendingOrdersList)): ?>
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">PO Number</th>
              <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Branch</th>
              <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Requested</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pendingOrdersList as $po): ?>
              <tr>
                <td style="padding:8px;border-bottom:1px solid #f3f4f6;"><?= esc($po['po_number']) ?></td>
                <td style="padding:8px;border-bottom:1px solid #f3f4f6;"><?= esc($po['branch_name']) ?></td>
                <td style="padding:8px;border-bottom:1px solid #f3f4f6;"><?= date('M d, Y H:i', strtotime($po['requested_date'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No pending requests.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    const categoryData = {
      labels: <?= json_encode(array_column($categoryStats, 'category_name')) ?>,
      datasets: [{
        label: 'Stock Quantity',
        data: <?= json_encode(array_column($categoryStats, 'qty')) ?>,
        backgroundColor: [
          '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8'
        ],
      }]
    };

    const categoryConfig = {
      type: 'doughnut',
      data: categoryData,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
          }
        }
      }
    };

    const ordersTrendData = {
      labels: <?= json_encode(array_map(function($m) { return date('M', mktime(0,0,0,$m,1)); }, array_column($ordersTrend, 'month'))) ?>,
      datasets: [{
        label: 'Orders',
        data: <?= json_encode(array_column($ordersTrend, 'total')) ?>,
        fill: false,
        borderColor: '#007bff',
        tension: 0.1
      }]
    };

    const ordersTrendConfig = {
      type: 'line',
      data: ordersTrendData,
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            precision: 0
          }
        }
      }
    };

    new Chart(
      document.getElementById('categoryChart'),
      categoryConfig
    );

    new Chart(
      document.getElementById('ordersTrendChart'),
      ordersTrendConfig
    );
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
