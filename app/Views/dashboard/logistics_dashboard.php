<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Logistics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?= view('templete/sidebar_styles') ?>
    <style>
        .main-content { margin-left: 220px; padding: 2rem; }
        .page-title { font-size:2rem; margin-bottom:2rem; font-weight:600; color:#1a1a1a; }
        .stat-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #4CAF50; }
        .stat-card h6 { color: #666; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .stat-card .number { font-size: 2rem; font-weight: 700; color: #4CAF50; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: none; }
        .card-header { background: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 1.5rem; }
        .card-header h5 { margin: 0; color: #1a1a1a; font-weight: 600; }
        .table { margin: 0; }
        .table thead th { background: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #666; font-weight: 600; }
        .badge-pending { background: #FFC107; color: #000; }
        .badge-in_transit { background: #2196F3; color: #fff; }
        .badge-delivered { background: #4CAF50; color: #fff; }
        .badge-approved { background: #FF9800; color: #fff; }
        @media (max-width:768px) { .main-content { margin-left: 0; padding:1rem; } }
    </style>
</head>
<body>
    <?= view('templete/sidebar', ['active' => 'logistics']) ?>

    <div class="main-content">
        <div class="page-title">📦 Logistics Dashboard</div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Total Shipments</h6>
                    <div class="number"><?= $totalShipments ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Pending Deliveries</h6>
                    <div class="number" style="color: #FF9800;"><?= $pendingDeliveries ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Delivered This Month</h6>
                    <div class="number" style="color: #2196F3;"><?= $deliveredThisMonth ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>On-Time Rate</h6>
                    <div class="number" style="color: #9C27B0;">95%</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Shipment Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Deliveries by Branch</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="branchChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Recent Shipments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Status</th>
                                <th>Expected Delivery</th>
                                <th>Branch</th>
                                <th>Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(($recentShipments ?? []) as $shipment): ?>
                            <tr>
                                <td><?= esc($shipment['po_number'] ?? '') ?></td>
                                <td>
                                    <span class="badge badge-<?= $shipment['status'] ?? '' ?>">
                                        <?= ucfirst($shipment['status'] ?? '') ?>
                                    </span>
                                </td>
                                <td><?= !empty($shipment['expected_delivery_date']) ? date('M d, Y', strtotime($shipment['expected_delivery_date'])) : 'N/A' ?></td>
                                <td><?= esc($shipment['branch_name'] ?? 'N/A') ?></td>
                                <td><?= esc($shipment['supplier_name'] ?? 'N/A') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const statusData = <?= json_encode($statusDistribution ?? []) ?>;
        const routeData = <?= json_encode($routeEfficiency ?? []) ?>;

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(d => d.status),
                datasets: [{
                    data: statusData.map(d => d.count),
                    backgroundColor: ['#FFC107', '#2196F3', '#4CAF50', '#FF9800']
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });

        const branchCtx = document.getElementById('branchChart').getContext('2d');
        new Chart(branchCtx, {
            type: 'bar',
            data: {
                labels: routeData.map(d => d.branch_name),
                datasets: [{
                    label: 'Orders',
                    data: routeData.map(d => d.order_count),
                    backgroundColor: '#4CAF50'
                }]
            },
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</body>
</html>