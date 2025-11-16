<?php
use Config\Database;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>ChakaNoks Central Admin - Enhanced</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    background: #ff9320ff; /* Replace with your preferred color */
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
    background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);  /* Gradient */
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(183, 90, 3, 0.3);
}
.metrics-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1rem; margin-bottom:1.5rem; }
.metric-card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; transition:transform .2s; }
.metric-card:hover { transform:translateY(-4px); }
.metric-label { color:#888; font-size:0.85rem; text-transform:uppercase; letter-spacing:.6px; }
.metric-value { font-size:1.8rem; font-weight:700; color:#2c3e50; margin-top:8px; }

.charts-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(360px,1fr)); gap:1rem; margin-bottom:1.5rem; }
.chart-card, .table-card { background:#fff; border-radius:14px; padding:1.25rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; }
.chart-title { font-size:1.05rem; font-weight:600; margin-bottom:1rem; color:#2c3e50; }
.chart-card canvas { height: 260px !important; max-height: 260px !important; }

table { width:100%; border-collapse:collapse; font-size:14px; }
th { text-align:left; padding:.8rem; font-weight:700; color:#666; border-bottom:1px solid #f0f0f0; }
td { padding:.8rem; border-bottom:1px solid #f7f7f7; color:#444; }

.fab { position:fixed; bottom:24px; right:24px; width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:#fff; border:none; font-size:24px; display:flex; align-items:center; justify-content:center; box-shadow:0 6px 20px rgba(102,126,234,0.3); cursor:pointer; }

@media (max-width:768px){ .metrics-grid, .charts-grid{grid-template-columns:1fr} .main-content { margin-left: 0; padding:1rem; } }
</style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="logo">ChakaNoks</div>
    <nav>
        <a href="<?= site_url('dashboard') ?>" class="active">Dashboard</a>
        <a href="<?= site_url('branches') ?>">Branches</a>
        <a href="<?= site_url('products') ?>">Products</a>
        <a href="<?= site_url('orders') ?>">Orders</a>
        <a href="<?= site_url('inventory') ?>">Inventory</a>
        <a href="<?= site_url('logout') ?>" class="logout">Logout</a>
    </nav>
</aside>

<!-- Main content -->
<div class="main-content">
    <div class="page-title">Central Admin Dashboard</div>

<?php
// --- DATA PREPARATION ---
try {
    if (!isset($stockValue) || !isset($categoryStats)) {
        $db = Database::connect();
        $stockValue = 0.00;
        $query = $db->query("SELECT SUM(COALESCE(p.price,0) * COALESCE(s.qty,0)) AS total FROM products p JOIN stocks s ON s.product_id = p.id");
        $res = $query->getRow();
        if ($res && isset($res->total)) $stockValue = (float)$res->total;

        $lowStock = 0;
        $q2 = $db->query("SELECT COUNT(*) AS c FROM stocks s JOIN products p ON p.id = s.product_id WHERE s.qty <= COALESCE(p.minimum_stock,0)");
        $r2 = $q2->getRow();
        if ($r2 && isset($r2->c)) $lowStock = (int)$r2->c;

        $pendingOrders = 0;
        $q3 = $db->query("SELECT COUNT(*) AS c FROM orders WHERE status IN ('pending','for_approval')");
        $r3 = $q3->getRow();
        if ($r3 && isset($r3->c)) $pendingOrders = (int)$r3->c;

        $categoryStats = [];
        $q4 = $db->query("SELECT c.name AS category_name, SUM(s.qty) AS qty
                          FROM categories c
                          LEFT JOIN products p ON p.category_id = c.id
                          LEFT JOIN stocks s ON s.product_id = p.id
                          GROUP BY c.id, c.name
                          ORDER BY qty DESC");
        foreach ($q4->getResult() as $row) $categoryStats[] = ['category_name' => $row->category_name, 'qty' => (int)$row->qty];

        $ordersTrend = [];
        $q5 = $db->query("SELECT MONTH(created_at) AS mm, COUNT(*) AS total
                          FROM orders
                          WHERE YEAR(created_at) = YEAR(CURDATE())
                          GROUP BY MONTH(created_at)
                          ORDER BY mm");
        $trendTemp = array_fill(1, 12, 0);
        foreach ($q5->getResult() as $row) $trendTemp[(int)$row->mm] = (int)$row->total;
        for ($m=1;$m<=12;$m++) $ordersTrend[] = ['month' => $m, 'total' => $trendTemp[$m]];
    }
} catch (\Throwable $e) {
    $stockValue = $stockValue ?? 229435.00;
    $lowStock = $lowStock ?? 0;
    $pendingOrders = $pendingOrders ?? 0;
    $categoryStats = $categoryStats ?? [
        ['category_name' => 'Fresh Produce','qty'=>35],
        ['category_name' => 'Dairy & Frozen','qty'=>25],
        ['category_name' => 'Beverages','qty'=>20],
        ['category_name' => 'Kitchen Supplies','qty'=>20],
    ];
    $ordersTrend = $ordersTrend ?? array_map(function($m){ return ['month'=>$m,'total'=>rand(10,200)]; }, range(1,12));
}
$monthNames = array_map(function($m){ return date('M', mktime(0,0,0,$m,1)); }, range(1,12));
?>

<!-- METRICS GRID -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total Stock Value</div>
        <div class="metric-value">₱<?= number_format((float)$stockValue, 2) ?></div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Low Stock Alerts</div>
        <div class="metric-value"><?= esc($lowStock) ?></div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Pending Orders</div>
        <div class="metric-value"><?= esc($pendingOrders) ?></div>
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

</div>

<!-- FAB Button -->
<button class="fab" title="Quick Actions" onclick="location.href='<?= site_url('purchase/new') ?>'">+</button>

<script>
const categoryLabels = <?= json_encode(array_column($categoryStats, 'category_name')) ?>;
const categoryData = <?= json_encode(array_column($categoryStats, 'qty')) ?>;
const ordersLabels = <?= json_encode($monthNames) ?>;
const ordersData = <?= json_encode(array_map(function($v){return (int)$v['total'];}, $ordersTrend)) ?>;

const stockCtx = document.getElementById('stockChart').getContext('2d');
new Chart(stockCtx, {type:'doughnut', data:{labels:categoryLabels, datasets:[{data:categoryData,borderWidth:0}]}, options:{responsive:true, maintainAspectRatio:false, cutout:'65%', plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:12}}}}});

const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {type:'line', data:{labels:ordersLabels, datasets:[{label:'Orders',data:ordersData,borderColor:'#667eea',backgroundColor:'rgba(102,126,234,0.08)',tension:0.35,fill:true,pointRadius:4}]}, options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}});
</script>

</body>
</html>
