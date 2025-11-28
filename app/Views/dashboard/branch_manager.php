<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Branch Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            color: #503e2cff;
        }

        /* --- Main content --- */
        .main-content { margin-left: 220px; padding: 2rem; }

        .container-fluid { padding: 0; }

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

        .branch-info { background:#fff; border-radius:14px; padding:1.25rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; margin-bottom:1.5rem; }
        .branch-info h2 { color:#2c3e50; font-size:1.3rem; margin-bottom:0.5rem; }
        .branch-info p { color:#666; margin:0; }

        .metrics-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1rem; margin-bottom:1.5rem; }
        .metric-card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; text-align:center; transition:transform .2s; }
        .metric-card:hover { transform:translateY(-4px); }
        .metric-label { color:#888; font-size:0.85rem; text-transform:uppercase; letter-spacing:.6px; }
        .metric-value { font-size:1.8rem; font-weight:700; color:#b75a03ff; margin-top:8px; }

        .po-counts { background:#fff; border-radius:14px; padding:1.25rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; margin-bottom:1.5rem; }
        .po-counts h3 { margin-top:0; color:#2c3e50; }
        .po-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:1rem; }
        .po-item { text-align:center; padding:1rem; background:#f9f9f9; border-radius:8px; border-left:4px solid #b75a03ff; }
        .po-item strong { color:#2c3e50; }

        .lists { display:flex; gap:1.5rem; flex-wrap:wrap; margin-bottom:1.5rem; }
        .list-container { background:#fff; border-radius:14px; padding:1.25rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; flex:1 1 300px; }
        .list-container h3 { margin-top:0; color:#2c3e50; }
        ul { list-style:none; padding:0; }
        li { padding:0.6rem 0; border-bottom:1px solid #f0f0f0; color:#555; font-size:0.95rem; }
        li:last-child { border-bottom:none; }

        @media (max-width:768px){ .main-content { margin-left: 0; padding:1rem; } .sidebar { width: 100%; height: auto; position: relative; } }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'dashboard']) ?>

    <div class="main-content">
        <div class="container-fluid">
        <div class="page-title">Branch Manager Dashboard</div>

        <div class="branch-info">
            <h2>Branch: <?= esc($branch['branch_name']) ?></h2>
            <p>Manager: <?= esc($branch['manager_name']) ?></p>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Total Stock Value</div>
                <div class="metric-value">₱<?= number_format($summary['stock_value'] ?? 0, 2) ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Low Stock Items</div>
                <div class="metric-value"><?= esc($summary['low_stock_items'] ?? 0) ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Pending POs</div>
                <div class="metric-value"><?= esc($pendingPOs) ?></div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
