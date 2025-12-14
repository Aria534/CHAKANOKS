<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drivers Management - ChakaNoks</title>
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
        .status-active { background-color: #d1e7dd; color: #0f5132; }
        .status-inactive { background-color: #f8d7da; color: #842029; }
        .btn-primary { background: #b75a03ff; border-color: #b75a03ff; cursor: pointer !important; }
        .btn-primary:hover { background: #ff9320ff; border-color: #ff9320ff; }
        .driver-card { background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid #e8e8e8; margin-bottom: 1rem; }
        .driver-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .driver-info { display: flex; justify-content: space-between; align-items: center; }
        .driver-stats { display: flex; gap: 2rem; }
        .stat { text-align: center; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #b75a03ff; }
        .stat-label { font-size: 0.85rem; color: #888; text-transform: uppercase; }
    </style>
</head>
<body>
    <?php echo view('templete/sidebar', ['active' => 'logistics']); ?>

    <div class="main-content">
        <div class="page-title">
            <i class="fas fa-users"></i> Drivers Management
        </div>

        <div class="card">
            <h5><i class="fas fa-list"></i> Active Drivers</h5>
            
            <?php if (!empty($drivers)): ?>
                <div class="row">
                    <?php foreach($drivers as $driver): ?>
                        <div class="col-md-6">
                            <div class="driver-card">
                                <div class="driver-info">
                                    <div>
                                        <h6 class="mb-2"><?php echo esc($driver['driver_name']); ?></h6>
                                        <p class="mb-1"><small><i class="fas fa-phone"></i> <?php echo esc($driver['phone_number']); ?></small></p>
                                        <p class="mb-0"><small><i class="fas fa-id-card"></i> <?php echo esc($driver['license_number'] ?? 'N/A'); ?></small></p>
                                    </div>
                                    <div class="driver-stats">
                                        <div class="stat">
                                            <div class="stat-value"><?php echo $driver['total_shipments'] ?? 0; ?></div>
                                            <div class="stat-label">Total</div>
                                        </div>
                                        <div class="stat">
                                            <div class="stat-value"><?php echo $driver['completed_shipments'] ?? 0; ?></div>
                                            <div class="stat-label">Completed</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>No drivers available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
