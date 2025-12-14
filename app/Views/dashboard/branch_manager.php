<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Branch Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .charts-grid { 
            display:grid; 
            grid-template-columns:repeat(2, 1fr); 
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-info { background: #dbeafe; color: #1e40af; }

        .quick-actions-card {
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8;
        }

        .quick-action-link {
            display: block;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #1a1a1a;
            transition: all 0.2s;
            margin-bottom: 0.75rem;
            border: 1px solid #e8e8e8;
        }
        .quick-action-link:hover {
            background: #fff;
            border-color: #b75a03ff;
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(183, 90, 3, 0.15);
        }
        .quick-action-link i {
            color: #b75a03ff;
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        .quick-action-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .quick-action-desc {
            font-size: 0.85rem;
            color: #666;
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

        .status-card {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            border: 2px solid #e8e8e8;
            transition: all 0.2s;
        }
        .status-card:hover {
            border-color: #b75a03ff;
            box-shadow: 0 2px 8px rgba(183, 90, 3, 0.15);
        }
        .status-count {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        .status-label {
            font-size: 0.85rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width:768px){ 
            .metrics-grid, .charts-grid{grid-template-columns:1fr} 
            .main-content { margin-left: 0; padding:1rem; }
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'dashboard']) ?>

    <div class="main-content">
        <div class="page-title">
            🏢 Branch Manager Dashboard
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= esc($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Branch Information -->
        <div class="branch-info-card">
            <h2><i class="fas fa-building me-2"></i><?= esc($branch['branch_name']) ?></h2>
            <div class="info-row">
                <div class="info-item">
                    <i class="fas fa-user-tie"></i>
                    <span><strong>Manager:</strong> <?= esc($branch['manager_name']) ?></span>
                </div>
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
                    <i class="fas fa-clock"></i>
                    Pending Orders
                </div>
                <div class="metric-value"><?= esc($pendingPOs) ?></div>
                <small style="color:#888;">Awaiting approval</small>
            </div>
            <div class="metric-card">
                <div class="metric-label">
                    <i class="fas fa-truck"></i>
                    Delivered Orders
                </div>
                <div class="metric-value" style="color:#10b981;"><?= esc($poCounts['delivered']) ?></div>
                <small style="color:#888;">Successfully received</small>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Purchase Order Status -->
            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-shopping-cart"></i>
                        Purchase Order Status Overview
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="status-card">
                                <div class="status-count" style="color:#FF9800;"><?= esc($poCounts['pending']) ?></div>
                                <div class="status-label">
                                    <i class="fas fa-clock"></i> Pending
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="status-card">
                                <div class="status-count" style="color:#0ea5e9;"><?= esc($poCounts['approved']) ?></div>
                                <div class="status-label">
                                    <i class="fas fa-check-circle"></i> Approved
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="status-card">
                                <div class="status-count" style="color:#10b981;"><?= esc($poCounts['delivered']) ?></div>
                                <div class="status-label">
                                    <i class="fas fa-box-check"></i> Delivered
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <canvas id="poStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="quick-actions-card">
                    <div class="chart-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </div>
                    <a href="<?= site_url('orders/create') ?>" class="quick-action-link">
                        <i class="fas fa-plus-circle"></i>
                        <div>
                            <div class="quick-action-title">Create Order</div>
                            <div class="quick-action-desc">New purchase request</div>
                        </div>
                    </a>
                    <a href="<?= site_url('orders') ?>" class="quick-action-link">
                        <i class="fas fa-list-ul"></i>
                        <div>
                            <div class="quick-action-title">View Orders</div>
                            <div class="quick-action-desc">Track all requests</div>
                        </div>
                    </a>
                    <a href="<?= site_url('inventory') ?>" class="quick-action-link">
                        <i class="fas fa-boxes"></i>
                        <div>
                            <div class="quick-action-title">Manage Inventory</div>
                            <div class="quick-action-desc">Check stock levels</div>
                        </div>
                    </a>
                    <a href="<?= site_url('reports') ?>" class="quick-action-link">
                        <i class="fas fa-chart-line"></i>
                        <div>
                            <div class="quick-action-title">View Reports</div>
                            <div class="quick-action-desc">Branch analytics</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Low Stock Items -->
            <div class="col-lg-6">
                <div class="table-card">
                    <div class="chart-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Low Stock Alert
                    </div>
                    <?php if (!empty($lowStockItems)): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Current</th>
                                        <th class="text-end">Minimum</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowStockItems as $item): ?>
                                    <tr>
                                        <td><?= esc($item['product_name']) ?></td>
                                        <td class="text-end"><strong><?= esc($item['available_stock']) ?></strong></td>
                                        <td class="text-end"><?= esc($item['minimum_stock']) ?></td>
                                        <td>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation-circle"></i> Low Stock
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>All items are well stocked!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Stock Movements -->
            <div class="col-lg-6">
                <div class="table-card">
                    <div class="chart-title">
                        <i class="fas fa-exchange-alt"></i>
                        Recent Stock Movements
                    </div>
                    <?php if (!empty($recentMovements)): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th class="text-end">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMovements as $move): ?>
                                    <tr>
                                        <td>
                                            <small><?= date('M d, Y', strtotime($move['created_at'])) ?></small>
                                            <br>
                                            <small style="color:#888;"><?= date('h:i A', strtotime($move['created_at'])) ?></small>
                                        </td>
                                        <td><?= esc($move['product_name']) ?></td>
                                        <td>
                                            <?php
                                            $typeColors = [
                                                'in' => 'success',
                                                'out' => 'warning',
                                                'adjustment' => 'info'
                                            ];
                                            $typeIcons = [
                                                'in' => 'fa-arrow-down',
                                                'out' => 'fa-arrow-up',
                                                'adjustment' => 'fa-sync'
                                            ];
                                            $type = strtolower($move['movement_type']);
                                            $badgeClass = $typeColors[$type] ?? 'secondary';
                                            $icon = $typeIcons[$type] ?? 'fa-circle';
                                            ?>
                                            <span class="badge badge-<?= $badgeClass ?>">
                                                <i class="fas <?= $icon ?>"></i>
                                                <?= ucfirst($move['movement_type']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <strong><?= esc($move['quantity']) ?></strong>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No recent stock movements</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Purchase Order Status Chart
        const poData = {
            pending: <?= $poCounts['pending'] ?>,
            approved: <?= $poCounts['approved'] ?>,
            delivered: <?= $poCounts['delivered'] ?>
        };

        const ctx = document.getElementById('poStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Delivered'],
                datasets: [{
                    data: [poData.pending, poData.approved, poData.delivered],
                    backgroundColor: ['#fef3c7', '#dbeafe', '#d1fae5'],
                    borderColor: ['#92400e', '#1e40af', '#065f46'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = poData.pending + poData.approved + poData.delivered;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
