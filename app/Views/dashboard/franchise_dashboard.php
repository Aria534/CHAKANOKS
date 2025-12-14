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
        :root {
            --primary-color: #9C27B0;
            --secondary-color: #2196F3;
            --success-color: #4CAF50;
            --warning-color: #FFC107;
            --danger-color: #F44336;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .main-content { 
            margin-left: 240px; 
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .page-title { 
            font-size: 1.5rem; 
            margin-bottom: 1.5rem; 
            font-weight: 600; 
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        /* Stat Cards */
        .stat-card { 
            background: #fff; 
            border-radius: 10px; 
            padding: 1.25rem; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary-color);
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card h6 { 
            color: #7f8c8d; 
            font-size: 0.8rem; 
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card .number { 
            font-size: 1.5rem; 
            font-weight: 700; 
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Cards */
        .card { 
            background: #fff; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 1.5rem;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .card-header { 
            background: #fff; 
            border-bottom: 1px solid #e9ecef; 
            padding: 1rem 1.25rem;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-header h5 { 
            margin: 0; 
            color: #2c3e50; 
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        /* Tables */
        .table { 
            margin: 0;
            font-size: 0.9rem;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th { 
            background: #f8fafc; 
            border-bottom: 2px solid #e9ecef; 
            color: #7f8c8d; 
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.75rem 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-top: 1px solid #edf2f7;
            color: #4a5568;
        }
        
        /* Chart Containers */
        .chart-container {
            position: relative;
            height: 280px;
            width: 100%;
            min-height: 200px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .page-title {
                font-size: 1.3rem;
                margin-bottom: 1.25rem;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
</head>
<body>
    <?= view('templete/sidebar', ['active' => 'franchise']) ?>

    <div class="main-content">
        <div class="page-title">
            <i class="fas fa-store"></i>
            <span>Franchise Dashboard</span>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card fade-in" style="animation-delay: 0.1s">
                    <h6><i class="fas fa-code-branch me-1"></i> Total Branches</h6>
                    <div class="number"><?= $totalBranches ?? 0 ?></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card fade-in" style="animation-delay: 0.2s">
                    <h6><i class="fas fa-boxes me-1"></i> Inventory Value</h6>
                    <div class="number">₱<?= number_format($totalInventoryValue ?? 0, 0) ?></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card fade-in" style="animation-delay: 0.3s">
                    <h6><i class="fas fa-shopping-cart me-1"></i> Total Orders</h6>
                    <div class="number" style="color: var(--secondary-color);"><?= $totalOrders ?? 0 ?></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card fade-in" style="animation-delay: 0.4s">
                    <h6><i class="fas fa-clock me-1"></i> Pending Orders</h6>
                    <div class="number" style="color: var(--warning-color);"><?= $pendingOrders ?? 0 ?></div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100 fade-in" style="animation-delay: 0.2s">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Category Distribution</h5>
                                <div class="chart-legend"></div>
                            </div>
                            <div class="card-body p-3">
                                <div style="position: relative; height: 250px;">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100 fade-in" style="animation-delay: 0.3s">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>Branch Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card fade-in" style="animation-delay: 0.4s">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-table me-2"></i>Branch Performance Details</h5>
                <button class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download me-1"></i> Export
                </button>
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
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(d => d.category_name),
                datasets: [{
                    data: categoryData.map(d => d.qty),
                    backgroundColor: ['#9C27B0', '#2196F3', '#4CAF50', '#FF9800', '#F44336', '#00BCD4', '#FFC107', '#8BC34A', '#607D8B'],
                    borderWidth: 1,
                    weight: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                layout: {
                    padding: 10
                }
            }
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