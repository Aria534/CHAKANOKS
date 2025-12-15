<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routes Management - ChakaNoks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php echo view('templete/sidebar_styles'); ?>
    <style>
        .main-content { margin-left: 220px; padding: 2rem; }
        .page-title { font-size: 1.8rem; margin-bottom: 1.5rem; font-weight: 600; color: white; background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%); padding: 1rem 1.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(183, 90, 3, 0.3); }
        .card { border: none; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e8e8e8; margin-bottom: 2rem; }
        .card h5 { color: #2c3e50; margin-bottom: 1rem; font-weight: 600; }
        .table { margin-bottom: 0; }
        .table th { background-color: #f8f9fa; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e8e8e8; }
        .table td { vertical-align: middle; padding: 1rem; }
        .badge { padding: 0.5rem 1rem; font-weight: 600; }
        .btn-primary { background: #b75a03ff; border-color: #b75a03ff; cursor: pointer !important; }
        .btn-primary:hover { background: #ff9320ff; border-color: #ff9320ff; }
    </style>
</head>
<body>
    <?php echo view('templete/sidebar', ['active' => 'logistics']); ?>

    <div class="main-content">
        <div class="page-title">
            <i class="fas fa-map"></i> Routes Management
        </div>

        <div class="card">
            <h5><i class="fas fa-list"></i> All Routes</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Route ID</th>
                            <th>Driver</th>
                            <th>Shipments</th>
                            <th>Start Location</th>
                            <th>End Location</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($routes)): ?>
                            <?php foreach($routes as $route): ?>
                                <tr>
                                    <td><strong><?php echo esc($route['route_id']); ?></strong></td>
                                    <td><?php echo !empty($route['driver_name']) ? esc($route['driver_name']) : 'Unassigned'; ?></td>
                                    <td><span class="badge bg-info"><?php echo $route['shipment_count'] ?? 0; ?></span></td>
                                    <td><?php echo esc($route['start_location'] ?? '-'); ?></td>
                                    <td><?php echo esc($route['end_location'] ?? '-'); ?></td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($route['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                    <p>No routes found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
