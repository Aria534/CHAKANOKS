<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Branch Manager Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f0f4f8;
    }
    h1 {
      color: #333;
    }
    .branch-info {
      background: white;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    /* Navbar link styling */
    .navbar .nav-link { text-decoration: none !important; }
    .navbar .nav-link:hover, .navbar .nav-link.active { text-decoration: none !important; }
    .summary {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }
    .summary-box {
      background: white;
      padding: 20px;
      border-radius: 8px;
      flex: 1;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }
    .summary-box h2 {
      margin: 0;
      font-size: 2em;
      color: #007bff;
    }
    .summary-box p {
      margin: 5px 0 0;
      font-weight: bold;
      color: #555;
    }
    .po-counts {
      background: white;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .po-counts h3 {
      margin-top: 0;
    }
    .po-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 10px;
    }
    .po-item {
      text-align: center;
      padding: 10px;
      background: #f8f9fa;
      border-radius: 5px;
    }
    .lists {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    .list-container {
      background: white;
      padding: 20px;
      border-radius: 8px;
      flex: 1 1 300px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .list-container h3 {
      margin-top: 0;
    }
    ul {
      list-style: none;
      padding: 0;
    }
    li {
      padding: 5px 0;
      border-bottom: 1px solid #eee;
    }
  </style>
</head>
<body>
  <?= view('partials/navbar', ['active' => 'dashboard']) ?>
  <div class="container-fluid mt-3">
  <h1>Branch Manager Dashboard</h1>
  <div class="branch-info">
    <h2>Branch: <?= esc($branch['branch_name']) ?></h2>
    <p>Manager: <?= esc($branch['manager_name']) ?></p>
  </div>

  <div class="summary">
    <div class="summary-box">
      <h2>₱<?= number_format($summary['stock_value'] ?? 0, 2) ?></h2>
      <p>Total Stock Value</p>
    </div>
    <div class="summary-box">
      <h2><?= esc($summary['low_stock_items'] ?? 0) ?></h2>
      <p>Low Stock Items</p>
    </div>
    <div class="summary-box">
      <h2><?= esc($pendingPOs) ?></h2>
      <p>Pending Purchase Orders</p>
    </div>
  </div>

  <div class="po-counts">
    <h3>Purchase Order Status Counts</h3>
    <div class="po-grid">
      <div class="po-item">
        <strong>Pending:</strong> <?= esc($poCounts['pending']) ?>
      </div>
      <div class="po-item">
        <strong>Approved:</strong> <?= esc($poCounts['approved']) ?>
      </div>
      <div class="po-item">
        <strong>Delivered:</strong> <?= esc($poCounts['delivered']) ?>
      </div>
    </div>
  </div>

  <div class="lists">
    <div class="list-container">
      <h3>Low Stock Items</h3>
      <ul>
        <?php if (!empty($lowStockItems)): ?>
          <?php foreach ($lowStockItems as $item): ?>
            <li><?= esc($item['product_name']) ?> (Stock: <?= esc($item['available_stock']) ?>, Min: <?= esc($item['minimum_stock']) ?>)</li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>No low stock items.</li>
        <?php endif; ?>
      </ul>
    </div>
    <div class="list-container">
      <h3>Recent Stock Movements</h3>
      <ul>
        <?php if (!empty($recentMovements)): ?>
          <?php foreach ($recentMovements as $move): ?>
            <li>
              <?= date('M d, Y H:i', strtotime($move['created_at'])) ?> - <?= esc($move['movement_type']) ?> <?= esc($move['quantity']) ?> of <?= esc($move['product_name']) ?>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>No recent movements.</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
