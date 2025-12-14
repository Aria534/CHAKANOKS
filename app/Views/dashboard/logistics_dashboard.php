<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?? 'Logistics Dashboard' ?> - ChakaNoks</title>
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

        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #dbeafe; color: #1e40af; }
        .badge-ordered { background: #e0e7ff; color: #3730a3; }
        .badge-delivered { background: #d1fae5; color: #065f46; }

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

        .btn-primary {
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(183, 90, 3, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.35);
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
    <?= view('templete/sidebar', ['active' => 'logistics']) ?>

    <div class="main-content">
        <div class="page-title">
            📦 Logistics Dashboard
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">📊 Total Shipments</div>
                <div class="metric-value"><?= $totalShipments ?? 0 ?></div>
                <small style="color:#888;">All tracked shipments</small>
            </div>
            <div class="metric-card">
                <div class="metric-label">⏳ Pending Deliveries</div>
                <div class="metric-value" style="color: #FF9800;"><?= $pendingDeliveries ?? 0 ?></div>
                <small style="color:#888;">Awaiting delivery</small>
            </div>
            <div class="metric-card">
                <div class="metric-label">✅ Delivered This Month</div>
                <div class="metric-value" style="color: #2196F3;"><?= $deliveredThisMonth ?? 0 ?></div>
                <small style="color:#888;"><?= date('F Y') ?></small>
            </div>
            <div class="metric-card">
                <div class="metric-label">📈 On-Time Rate</div>
                <div class="metric-value" style="color: #9C27B0;">
                    <?php
                    $onTimeRate = 95;
                    if ($deliveredThisMonth > 0 && $totalShipments > 0) {
                        $onTimeRate = round(($deliveredThisMonth / $totalShipments) * 100);
                    }
                    echo $onTimeRate . '%';
                    ?>
                </div>
                <small style="color:#888;">Delivery performance</small>
            </div>
        </div>

        <div class="charts-grid">
            <!-- Charts Section -->
            <div class="chart-card">
                <div class="chart-title">📊 Shipment Status Distribution</div>
                <!-- Status Cards -->
                <div class="row g-2 mb-4" style="margin-bottom: 1.5rem;">
                    <div class="col-md-4">
                        <div class="status-card">
                            <div class="status-count" style="color:#FF9800;"><?= $statusCounts['approved'] ?? 0 ?></div>
                            <div class="status-label">
                                <i class="fas fa-clock"></i> Approved
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="status-card">
                            <div class="status-count" style="color:#0ea5e9;"><?= $statusCounts['ordered'] ?? 0 ?></div>
                            <div class="status-label">
                                <i class="fas fa-truck"></i> In Transit
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="status-card">
                            <div class="status-count" style="color:#10b981;"><?= $statusCounts['delivered'] ?? 0 ?></div>
                            <div class="status-label">
                                <i class="fas fa-check-circle"></i> Delivered
                            </div>
                        </div>
                    </div>
                </div>
                <canvas id="statusChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">🏢 Orders by Branch</div>
                <canvas id="branchChart"></canvas>
            </div>
        </div>

        <!-- Recent Shipments and Quick Actions -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
            <!-- Recent Shipments Table -->
            <div class="table-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div class="chart-title">🚚 Recent Shipments</div>
                    <a href="<?= site_url('shipments') ?>" class="btn btn-primary btn-sm">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Status</th>
                            <th>Expected Delivery</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentShipments)): ?>
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <p>No recent shipments</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($recentShipments as $shipment): ?>
                            <tr>
                                <td><strong><?= esc($shipment['po_number'] ?? '') ?></strong></td>
                                <td>
                                    <span class="badge badge-<?= esc($shipment['status'] ?? '') ?>">
                                        <?php
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'approved' => 'Approved',
                                            'ordered' => 'In Transit',
                                            'delivered' => 'Delivered'
                                        ];
                                        echo $statusLabels[$shipment['status']] ?? ucfirst($shipment['status']);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?= !empty($shipment['expected_delivery_date']) 
                                        ? date('M d, Y', strtotime($shipment['expected_delivery_date'])) 
                                        : '<span style="color:#999;">Not set</span>' ?>
                                </td>
                                <td><?= esc($shipment['branch_name'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="<?= site_url('logistics/track/' . $shipment['purchase_order_id']) ?>" 
                                       class="btn btn-primary btn-sm">
                                        Track
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions-card">
                <div class="chart-title">⚡ Quick Actions</div>
                <a href="<?= site_url('shipments') ?>" class="quick-action-link">
                    <i class="fas fa-shipping-fast"></i>
                    <div>
                        <div class="quick-action-title">Manage Shipments</div>
                        <div class="quick-action-desc">View and update all shipments</div>
                    </div>
                </a>
                <a href="<?= site_url('routes') ?>" class="quick-action-link">
                    <i class="fas fa-route"></i>
                    <div>
                        <div class="quick-action-title">Delivery Routes</div>
                        <div class="quick-action-desc">Plan and optimize routes</div>
                    </div>
                </a>
                <a href="<?= site_url('orders') ?>" class="quick-action-link">
                    <i class="fas fa-box"></i>
                    <div>
                        <div class="quick-action-title">Purchase Orders</div>
                        <div class="quick-action-desc">View all orders</div>
                    </div>
                </a>
                <a href="<?= site_url('dashboard/inventory') ?>" class="quick-action-link">
                    <i class="fas fa-warehouse"></i>
                    <div>
                        <div class="quick-action-title">Inventory</div>
                        <div class="quick-action-desc">Check stock levels</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const statusData = <?= json_encode($statusDistribution ?? []) ?>;
        const routeData = <?= json_encode($routeEfficiency ?? []) ?>;

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        if (statusData.length > 0) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(d => {
                        const labels = {
                            'approved': 'Approved',
                            'ordered': 'In Transit',
                            'delivered': 'Delivered'
                        };
                        return labels[d.status] || d.status;
                    }),
                    datasets: [{
                        data: statusData.map(d => d.count),
                        backgroundColor: ['#dbeafe', '#e0e7ff', '#d1fae5'],
                        borderColor: ['#1e40af', '#3730a3', '#065f46'],
                        borderWidth: 2
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            statusCtx.canvas.parentElement.innerHTML = '<div class="empty-state"><p>No data available</p></div>';
        }

        // Branch Distribution Chart
        const branchCtx = document.getElementById('branchChart').getContext('2d');
        if (routeData.length > 0) {
            new Chart(branchCtx, {
                type: 'bar',
                data: {
                    labels: routeData.map(d => d.branch_name || 'Unknown'),
                    datasets: [{
                        label: 'Orders',
                        data: routeData.map(d => d.order_count),
                        backgroundColor: 'rgba(183, 90, 3, 0.8)',
                        borderColor: '#b75a03ff',
                        borderWidth: 1
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
        } else {
            branchCtx.canvas.parentElement.innerHTML = '<div class="empty-state"><p>No data available</p></div>';
        }
    </script>
</body>
</html>




