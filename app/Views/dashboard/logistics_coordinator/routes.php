<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?? 'Delivery Routes' ?> - ChakaNoks</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .route-card {
            background:#fff; 
            border-radius:14px; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .route-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .route-header {
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            color: white;
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .route-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.15rem;
        }

        .route-info {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        .route-info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .route-body {
            padding: 1.5rem;
        }

        .branch-details {
            background: #fff3cd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .order-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #e0e7ff;
            transition: all 0.2s;
        }

        .order-item:hover {
            border-left-color: #3730a3;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .badge {
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-approved { background: #dbeafe; color: #1e40af; }
        .badge-ordered { background: #e0e7ff; color: #3730a3; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(183, 90, 3, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.35);
        }

        .btn-outline-primary {
            background: #fff;
            border: 1px solid #b75a03ff;
            color: #b75a03ff;
        }
        .btn-outline-primary:hover {
            background: #b75a03ff;
            color: #fff;
        }

        .btn-success {
            background: #10b981;
            color: #fff;
        }
        .btn-success:hover {
            background: #059669;
        }

        .btn-light {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .btn-light:hover {
            background: rgba(255,255,255,0.3);
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.875rem;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #999;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ddd;
        }

        @media (max-width:768px) { 
            .main-content { margin-left: 0; padding:1rem; }
            .route-info { flex-direction: column; gap: 0.5rem; }
            .page-title { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>
    <?= view('templete/sidebar', ['active' => 'routes']) ?>

    <div class="main-content">
        <div class="page-title">
            <span>🚛 Delivery Routes</span>
            <span class="badge badge-warning">
                <i class="fas fa-map-marked-alt me-1"></i>
                <?= count($routes ?? []) ?> Active Routes
            </span>
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

        <?php if (empty($routes)): ?>
            <div class="route-card">
                <div class="empty-state">
                    <i class="fas fa-route"></i>
                    <h4>No Active Delivery Routes</h4>
                    <p style="color:#888;">All deliveries have been completed or there are no pending orders.</p>
                    <a href="<?= site_url('orders') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-box me-2"></i>View All Orders
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($routes as $route): ?>
            <div class="route-card">
                <div class="route-header" onclick="toggleRoute(<?= $route['branch_id'] ?>)">
                    <div>
                        <h5><i class="fas fa-building me-2"></i><?= esc($route['branch_name']) ?></h5>
                        <div class="route-info">
                            <div class="route-info-item">
                                <i class="fas fa-box"></i>
                                <span><?= $route['pending_orders'] ?> Pending Order<?= $route['pending_orders'] > 1 ? 's' : '' ?></span>
                            </div>
                            <div class="route-info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Earliest: <?= !empty($route['earliest_delivery']) ? date('M d, Y', strtotime($route['earliest_delivery'])) : 'Not set' ?></span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-light btn-sm">
                        <i class="fas fa-chevron-down" id="icon-<?= $route['branch_id'] ?>"></i>
                    </button>
                </div>
                <div class="route-body" id="route-<?= $route['branch_id'] ?>">
                    <!-- Branch Contact Info -->
                    <div class="branch-details">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-map-marker-alt me-2"></i>Address:</strong>
                                <p class="mb-0 ms-4"><?= esc($route['address']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-phone me-2"></i>Contact:</strong>
                                <p class="mb-0 ms-4"><?= esc($route['phone']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Orders for this route -->
                    <h6 class="mb-3"><i class="fas fa-list-ul me-2"></i>Orders to Deliver:</h6>
                    <?php if (!empty($route['orders'])): ?>
                        <?php foreach($route['orders'] as $order): ?>
                        <div class="order-item">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <div>
                                    <strong><?= esc($order['po_number']) ?></strong>
                                    <span class="badge badge-<?= esc($order['status']) ?> ms-2">
                                        <?php
                                        $statusLabels = [
                                            'approved' => 'Approved',
                                            'ordered' => 'In Transit'
                                        ];
                                        echo $statusLabels[$order['status']] ?? ucfirst($order['status']);
                                        ?>
                                    </span>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('logistics/track/' . $order['purchase_order_id']) ?>" 
                                       class="btn btn-outline-primary" title="Track on Map">
                                        <i class="fas fa-map-marked-alt"></i> Track on Map
                                    </a>
                                    <button class="btn btn-success" 
                                            onclick="markDelivered(<?= $order['purchase_order_id'] ?>)"
                                            title="Mark as Delivered">
                                        <i class="fas fa-check"></i> Delivered
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <small style="color:#888;"><i class="fas fa-store me-1"></i>Supplier:</small>
                                    <div><?= esc($order['supplier_name'] ?? 'N/A') ?></div>
                                </div>
                                <div class="col-md-4">
                                    <small style="color:#888;"><i class="fas fa-calendar me-1"></i>Expected Delivery:</small>
                                    <div>
                                        <?php if (!empty($order['expected_delivery_date'])): ?>
                                            <?= date('M d, Y', strtotime($order['expected_delivery_date'])) ?>
                                            <?php
                                            $today = strtotime('today');
                                            $expDate = strtotime($order['expected_delivery_date']);
                                            if ($expDate < $today) {
                                                echo '<span class="badge badge-danger ms-2">Overdue</span>';
                                            }
                                            ?>
                                        <?php else: ?>
                                            <span style="color:#999;">Not set</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <small style="color:#888;"><i class="fas fa-money-bill-wave me-1"></i>Amount:</small>
                                    <div><strong>₱<?= number_format((float)$order['total_amount'], 2) ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center" style="color:#999; padding: 1.5rem;">No orders for this route</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleRoute(branchId) {
            const routeBody = document.getElementById(`route-${branchId}`);
            const icon = document.getElementById(`icon-${branchId}`);
            
            if (routeBody.style.display === 'none') {
                routeBody.style.display = 'block';
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            } else {
                routeBody.style.display = 'none';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            }
        }

        function markDelivered(orderId) {
            if (!confirm('Mark this order as delivered?')) return;

            fetch(`<?= site_url('logistics/update-status/') ?>${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `status=delivered&actual_delivery_date=${new Date().toISOString().split('T')[0]}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status');
            });
        }
    </script>
</body>
</html>
