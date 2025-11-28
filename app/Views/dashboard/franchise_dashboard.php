<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Franchise Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?= view('templete/sidebar_styles') ?>
    <style>
        .main-content { margin-left: 220px; padding: 2rem; }
        .page-title { font-size:2rem; margin-bottom:2rem; font-weight:600; color:#1a1a1a; }
        .stat-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #9C27B0; }
        .stat-card h6 { color: #666; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .stat-card .number { font-size: 2rem; font-weight: 700; color: #9C27B0; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: none; }
        .card-header { background: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 1.5rem; }
        .card-header h5 { margin: 0; color: #1a1a1a; font-weight: 600; }
        .table { margin: 0; }
        .table thead th { background: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #666; font-weight: 600; }
        .badge-pending { background: #FFC107; color: #000; }
        .badge-approved { background: #4CAF50; color: #fff; }
        @media (max-width:768px) { .main-content { margin-left: 0; padding:1rem; } }
    </style>
</head>
<body>
    <?= view('templete/sidebar', ['active' => 'franchise']) ?>

    <div class="main-content">
        <div class="page-title">🏪 Franchise Dashboard</div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Total Branches</h6>
                    <div class="number"><?= $totalBranches ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Total Inventory Value</h6>
                    <div class="number" style="font-size: 1.5rem;">₱<?= number_format($totalInventoryValue ?? 0, 0) ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Total Orders</h6>
                    <div class="number" style="color: #2196F3;"><?= $totalOrders ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Pending Orders</h6>
                    <div class="number" style="color: #FF9800;"><?= $pendingOrders ?? 0 ?></div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Category Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Branch Performance</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Branch Performance Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Branch Name</th>
                                <th>Orders</th>
                                <th>Inventory Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(($branchPerformance ?? []) as $branch): ?>
                            <tr>
                                <td><?= esc($branch['branch_name'] ?? 'N/A') ?></td>
                                <td><?= $branch['order_count'] ?? 0 ?></td>
                                <td>₱<?= number_format($branch['inventory_value'] ?? 0, 2) ?></td>
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
        const categoryData = <?= json_encode($categoryDistribution ?? []) ?>;
        const branchData = <?= json_encode($branchPerformance ?? []) ?>;

        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: categoryData.map(d => d.category_name),
                datasets: [{
                    data: categoryData.map(d => d.qty),
                    backgroundColor: ['#9C27B0', '#2196F3', '#4CAF50', '#FF9800', '#F44336']
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });

        const perfCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(perfCtx, {
            type: 'bar',
            data: {
                labels: branchData.map(d => d.branch_name),
                datasets: [{
                    label: 'Orders',
                    data: branchData.map(d => d.order_count),
                    backgroundColor: '#9C27B0'
                }]
            },
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</body>
</html>