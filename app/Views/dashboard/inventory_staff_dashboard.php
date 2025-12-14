<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Inventory Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?= view('templete/sidebar_styles') ?>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            color: #503e2cff;
        }

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

        .branch-info-card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.5rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
            margin-bottom:1.5rem;
            border-left: 4px solid #b75a03ff;
        }
        .branch-info-card h2 { 
            color:#2c3e50; 
            font-size:1.3rem; 
            margin-bottom:0.75rem;
            font-weight: 600;
        }
        .branch-info-card .info-row {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
        }
        .branch-info-card .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
        }
        .branch-info-card .info-item i {
            color: #b75a03ff;
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .metric-value { 
            font-size:1.8rem; 
            font-weight:700; 
            color:#2c3e50; 
        }
        .metric-card.warning .metric-value { color: #FF9800; }
        .metric-card.success .metric-value { color: #10b981; }
        .metric-card.info .metric-value { color: #0ea5e9; }

        .card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
            margin-bottom:1.5rem;
        }
        .card h5 { 
            color:#2c3e50; 
            margin-bottom:1rem; 
            font-weight:600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table { 
            width:100%; 
            border-collapse:collapse; 
            font-size:14px; 
        }
        .table th { 
            text-align:left; 
            padding:.8rem; 
            font-weight:700; 
            color:#666; 
            border-bottom:2px solid #e8e8e8; 
            background: #f8f9fa;
        }
        .table td { 
            padding:.8rem; 
            border-bottom:1px solid #f0f0f0; 
            color:#444; 
        }
        .table tbody tr:hover { 
            background-color:#f9f9f9; 
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-danger {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #999;
        }
        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            opacity: 0.5;
        }

        .btn-primary {
            background: #b75a03ff;
            border-color: #b75a03ff;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #ff9320ff;
            border-color: #ff9320ff;
            color: white;
        }

        @media (max-width:768px){ 
            .metrics-grid{grid-template-columns:1fr} 
            .main-content { margin-left: 0; padding:1rem; }
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'dashboard']) ?>

    <div class="main-content">
        <div class="page-title">
            📦 Inventory Staff Dashboard
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <!-- Branch Information -->
        <div class="branch-info-card">
            <h2><i class="fas fa-building me-2"></i><?= esc($branchName) ?></h2>
            <div class="info-row">
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span><strong>Today:</strong> <?= date('F d, Y') ?></span>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="metrics-grid">
            <div class="metric-card success">
                <div class="metric-label">
                    <i class="fas fa-dollar-sign"></i>
                    Total Stock Value
                </div>
                <div class="metric-value">₱<?= number_format($summary['stock_value'] ?? 0, 2) ?></div>
                <small style="color:#888;">Current inventory value</small>
            </div>
            <div class="metric-card warning">
                <div class="metric-label">
                    <i class="fas fa-exclamation-triangle"></i>
                    Low Stock Items
                </div>
                <div class="metric-value"><?= esc($summary['low_stock_items'] ?? 0) ?></div>
                <small style="color:#888;">Items need restocking</small>
            </div>
            <div class="metric-card info">
                <div class="metric-label">
                    <i class="fas fa-boxes"></i>
                    Total Products
                </div>
                <div class="metric-value"><?= esc($summary['total_products'] ?? 0) ?></div>
                <small style="color:#888;">Products in inventory</small>
            </div>
            <div class="metric-card">
                <div class="metric-label">
                    <i class="fas fa-clock"></i>
                    Pending Receives
                </div>
                <div class="metric-value"><?= esc($pendingReceives ?? 0) ?></div>
                <small style="color:#888;">Awaiting delivery</small>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?= site_url('inventory') ?>" class="btn-primary">
                    <i class="fas fa-boxes me-1"></i> Manage Inventory
                </a>
                <a href="<?= site_url('inventory?mode=add') ?>" class="btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Stock
                </a>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="card">
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Items</h5>
            <?php if (empty($lowStockItems)): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No low stock items</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Product</th>
                            <th>Available</th>
                            <th>Min Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($lowStockItems as $item): ?>
                        <tr>
                            <td><strong><?= esc($item['branch_name'] ?? 'N/A') ?></strong></td>
                            <td><?= esc($item['product_name']) ?></td>
                            <td><?= esc($item['available_stock']) ?></td>
                            <td><?= esc($item['minimum_stock']) ?></td>
                            <td>
                                <span style="color: #FF9800; font-weight: 600;">
                                    <i class="fas fa-exclamation-circle"></i> Low
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Recent Stock Movements -->
        <div class="card">
            <h5><i class="fas fa-history me-2"></i>Recent Stock Movements</h5>
            <?php if (empty($recentMovements)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No recent movements</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentMovements as $movement): ?>
                        <tr>
                            <td><?= date('M d, Y H:i', strtotime($movement['created_at'])) ?></td>
                            <td><strong><?= esc($movement['branch_name'] ?? 'N/A') ?></strong></td>
                            <td><?= esc($movement['product_name']) ?></td>
                            <td>
                                <?php 
                                $type = $movement['movement_type'];
                                $badgeColor = ($type === 'in') ? 'success' : (($type === 'out') ? 'danger' : 'info');
                                $typeLabel = ucfirst($type);
                                ?>
                                <span style="padding: 0.25rem 0.5rem; border-radius: 4px; background: <?= $badgeColor === 'success' ? '#d4edda' : ($badgeColor === 'danger' ? '#f8d7da' : '#d1ecf1') ?>; color: <?= $badgeColor === 'success' ? '#155724' : ($badgeColor === 'danger' ? '#721c24' : '#0c5460') ?>;">
                                    <?= $typeLabel ?>
                                </span>
                            </td>
                            <td>
                                <strong style="color: <?= $movement['quantity'] > 0 ? '#10b981' : '#ef4444' ?>;">
                                    <?= $movement['quantity'] > 0 ? '+' : '' ?><?= esc($movement['quantity']) ?>
                                </strong>
                            </td>
                            <td><?= esc($movement['notes'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

