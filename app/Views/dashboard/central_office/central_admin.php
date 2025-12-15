<?php
// Use data from controller, with fallbacks
$stockValue = $stockValue ?? 0;
$lowStock = $lowStock ?? 0;
$pendingOrders = $pendingOrders ?? 0;
$categoryStats = $categoryStats ?? [];
$ordersTrend = $ordersTrend ?? [];
$lowStockList = $lowStockList ?? [];
$pendingOrdersList = $pendingOrdersList ?? [];

// Prepare month names for chart
$monthNames = array_map(function($m){ return date('M', mktime(0,0,0,$m,1)); }, range(1,12));

// Prepare orders trend data - ensure all 12 months are present
$ordersTrendData = array_fill(0, 12, 0);
foreach ($ordersTrend as $trend) {
    $monthIndex = (int)($trend['month'] ?? 0) - 1;
    if ($monthIndex >= 0 && $monthIndex < 12) {
        $ordersTrendData[$monthIndex] = (int)($trend['total'] ?? 0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>ChakaNoks Central Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?= view('templete/sidebar_styles') ?>

<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    background: #f5f5f5;
    min-height: 100vh;
    color: #503e2cff;
}

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

.metrics-grid { 
    display:grid; 
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); 
    gap:1rem; 
    margin-bottom:1.5rem; 
}

.metric-card { 
    background:#fff; 
    border-radius:12px; 
    padding:1.2rem 1.5rem; 
    box-shadow:0 2px 10px rgba(0,0,0,0.06); 
    border:1px solid #e8e8e8; 
    transition:transform .2s, box-shadow .2s;
    cursor: pointer;
}
.metric-card:hover { 
    transform:translateY(-4px); 
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
}
.metric-label { 
    color:#888; 
    font-size:0.85rem; 
    text-transform:uppercase; 
    letter-spacing:.6px; 
    margin-bottom:0.5rem;
}
.metric-value { 
    font-size:1.8rem; 
    font-weight:700; 
    color:#2c3e50; 
}

.charts-grid { 
    display:grid; 
    grid-template-columns:repeat(auto-fit,minmax(360px,1fr)); 
    gap:1rem; 
    margin-bottom:1.5rem; 
}

.chart-card, .table-card { 
    background:#fff; 
    border-radius:14px; 
    padding:1.25rem; 
    box-shadow:0 2px 10px rgba(0,0,0,0.06); 
    border:1px solid #e8e8e8; 
}

.chart-title { 
    font-size:1.05rem; 
    font-weight:600; 
    margin-bottom:1rem; 
    color:#2c3e50; 
}

.chart-card canvas { 
    height: 260px !important; 
    max-height: 260px !important; 
}

table { 
    width:100%; 
    border-collapse:collapse; 
    font-size:14px; 
}

th { 
    text-align:left; 
    padding:.8rem; 
    font-weight:700; 
    color:#666; 
    border-bottom:2px solid #f0f0f0; 
    background-color: #fafafa;
}

td { 
    padding:.8rem; 
    border-bottom:1px solid #f7f7f7; 
    color:#444; 
}

tbody tr:hover {
    background-color: #f9f9f9;
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-warning {
    background-color: #ffc107;
    color: #000;
}

.badge-danger {
    background-color: #dc3545;
    color: #fff;
}

.badge-info {
    background-color: #0dcaf0;
    color: #000;
}

.fab { 
    position:fixed; 
    bottom:24px; 
    right:24px; 
    width:56px; 
    height:56px; 
    border-radius:50%; 
    background:linear-gradient(135deg,#b75a03ff 0%,#ff9320ff 100%); 
    color:#fff; 
    border:none; 
    font-size:24px; 
    display:flex; 
    align-items:center; 
    justify-content:center; 
    box-shadow:0 6px 20px rgba(183, 90, 3, 0.3); 
    cursor:pointer;
    transition: transform 0.2s;
    z-index: 1000;
}

.fab:hover {
    transform: scale(1.1);
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #999;
}

@media (max-width:768px){ 
    .metrics-grid, .charts-grid{grid-template-columns:1fr} 
    .main-content { margin-left: 0; padding:1rem; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<?= view('templete/sidebar', ['active' => 'dashboard']) ?>

<!-- Main content -->
<div class="main-content">
    <div class="page-title">Central Admin Dashboard</div>

    <!-- METRICS GRID -->
    <div class="metrics-grid">
        <div class="metric-card" onclick="window.location.href='<?= site_url('inventory') ?>'">
            <div class="metric-label">Total Stock Value</div>
            <div class="metric-value">₱<?= number_format((float)$stockValue, 2) ?></div>
        </div>
        <div class="metric-card" onclick="window.location.href='<?= site_url('inventory') ?>?filter=low_stock'">
            <div class="metric-label">Low Stock Alerts</div>
            <div class="metric-value"><?= esc($lowStock) ?></div>
        </div>
        <div class="metric-card" onclick="window.location.href='<?= site_url('orders') ?>?status=pending'">
            <div class="metric-label">Pending Orders</div>
            <div class="metric-value"><?= esc($pendingOrders) ?></div>
        </div>
        <div class="metric-card" onclick="window.location.href='<?= site_url('branches') ?>'">
            <div class="metric-label">Total Branches</div>
            <div class="metric-value"><?= esc($totalBranches ?? 0) ?></div>
        </div>
        <div class="metric-card" onclick="window.location.href='<?= site_url('users') ?>'">
            <div class="metric-label">Total Users</div>
            <div class="metric-value"><?= esc($totalUsers ?? 0) ?></div>
        </div>
        <div class="metric-card" onclick="window.location.href='<?= site_url('products') ?>'">
            <div class="metric-label">Total Products</div>
            <div class="metric-value"><?= esc($totalProducts ?? 0) ?></div>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">Stock Distribution by Category</div>
            <canvas id="stockChart" height="220"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-title">Orders Trend (This Year)</div>
            <canvas id="ordersChart" height="220"></canvas>
        </div>
    </div>

    <!-- DATA TABLES -->
    <div class="charts-grid">
        <!-- Low Stock List -->
        <div class="table-card">
            <div class="chart-title">Low Stock Items</div>
            <?php if (!empty($lowStockList)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Product</th>
                            <th>Available</th>
                            <th>Minimum</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockList as $item): ?>
                            <tr>
                                <td><?= esc($item['branch_name'] ?? 'N/A') ?></td>
                                <td><?= esc($item['product_name'] ?? 'N/A') ?></td>
                                <td><?= esc($item['available_stock'] ?? 0) ?></td>
                                <td><?= esc($item['minimum_stock'] ?? 0) ?></td>
                                <td>
                                    <span class="badge badge-danger">Low Stock</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">No low stock items</div>
            <?php endif; ?>
        </div>

        <!-- Pending Orders List -->
        <div class="table-card">
            <div class="chart-title">Recent Pending Orders</div>
            <?php if (!empty($pendingOrdersList)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Branch</th>
                            <th>Requested Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingOrdersList as $order): ?>
                            <tr>
                                <td><?= esc($order['po_number'] ?? 'N/A') ?></td>
                                <td><?= esc($order['branch_name'] ?? 'N/A') ?></td>
                                <td><?= esc($order['requested_date'] ? date('Y-m-d', strtotime($order['requested_date'])) : 'N/A') ?></td>
                                <td>
                                    <span class="badge badge-warning">Pending</span>
                                </td>
                                <td>
                                    <a href="<?= site_url('orders') ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">No pending orders</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- FAB Button -->
<button class="fab" title="Create New Order" onclick="window.location.href='<?= site_url('orders/create') ?>'">+</button>

<script>
// Prepare chart data
const categoryLabels = <?= json_encode(array_column($categoryStats, 'category_name')) ?>;
const categoryData = <?= json_encode(array_column($categoryStats, 'qty')) ?>;
const ordersLabels = <?= json_encode($monthNames) ?>;
const ordersData = <?= json_encode($ordersTrendData) ?>;

// Stock Distribution Chart (Doughnut)
const stockCtx = document.getElementById('stockChart');
if (stockCtx) {
    if (categoryLabels.length > 0 && categoryData.some(d => d > 0)) {
        new Chart(stockCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    borderWidth: 0,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 12
                        }
                    }
                }
            }
        });
    } else {
        stockCtx.parentElement.innerHTML = '<div class="empty-state">No stock data available</div>';
    }
}

// Orders Trend Chart (Line)
const ordersCtx = document.getElementById('ordersChart');
if (ordersCtx) {
    new Chart(ordersCtx.getContext('2d'), {
        type: 'line',
        data: {
            labels: ordersLabels,
            datasets: [{
                label: 'Orders',
                data: ordersData,
                borderColor: '#b75a03ff',
                backgroundColor: 'rgba(183, 90, 3, 0.08)',
                tension: 0.35,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
</script>

</body>
</html>
