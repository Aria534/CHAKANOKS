<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deliveries - ChakaNoks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
        .status-pending { background-color: #fff3cd; color: #856404; font-weight: 600; }
        .status-approved { background-color: #cfe2ff; color: #084298; font-weight: 600; }
        .status-ordered { background-color: #cfe2ff; color: #084298; font-weight: 600; }
        .status-in_transit { background-color: #cfe2ff; color: #084298; font-weight: 600; }
        .status-delivered { background-color: #d1e7dd; color: #0f5132; font-weight: 600; }
        .action-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .filter-section { background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #e8e8e8; }
        .form-label { font-weight: 600; color: #2c3e50; }
        .form-control, .form-select { border: 1px solid #e8e8e8; border-radius: 8px; }
        .form-control:focus, .form-select:focus { border-color: #b75a03ff; box-shadow: 0 0 0 0.2rem rgba(183,90,3,0.15); }
    </style>
</head>
<body>
    <?php echo view('templete/sidebar', ['active' => 'deliveries']); ?>

    <div class="main-content">
        <div class="page-title"><i class="fas fa-truck"></i> Deliveries</div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo session()->getFlashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="get" action="<?php echo site_url('deliveries'); ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo isset($_GET['status']) && $_GET['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="in_transit" <?php echo isset($_GET['status']) && $_GET['status'] === 'in_transit' ? 'selected' : ''; ?>>In Transit (Ordered)</option>
                        <option value="delivered" <?php echo isset($_GET['status']) && $_GET['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search PO Number</label>
                    <input type="text" class="form-control" name="search" placeholder="Enter PO number..." value="<?php echo isset($_GET['search']) ? esc($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Deliveries Card -->
        <div class="card">
            <h5><i class="fas fa-list"></i> All Deliveries</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Items</th>
                                <th>Expected Delivery</th>
                                <th>Status</th>
                                <th>Received Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($deliveries)): ?>
                                <?php foreach($deliveries as $delivery): ?>
                                    <tr>
                                        <td><strong><?php echo esc($delivery['po_number']); ?></strong></td>
                                        <td><?php echo esc($delivery['supplier_name']); ?></td>
                                        <td>
                                            <small><?php echo esc(substr($delivery['items'] ?? 'N/A', 0, 40)); ?>...</small>
                                        </td>
                                        <td><?php echo !empty($delivery['expected_delivery_date']) ? date('M d, Y', strtotime($delivery['expected_delivery_date'])) : 'N/A'; ?></td>
                                        <td>
                                            <?php 
                                                $statusDisplay = $delivery['status'];
                                                if ($delivery['status'] === 'ordered') {
                                                    $statusDisplay = 'In Transit';
                                                }
                                            ?>
                                            <span class="badge status-<?php echo strtolower($delivery['status']); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $statusDisplay)); ?>
                                            </span>
                                        </td>
                                        <td><?php echo !empty($delivery['actual_delivery_date']) ? date('M d, Y', strtotime($delivery['actual_delivery_date'])) : '-'; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="<?php echo site_url('deliveries/view/' . $delivery['purchase_order_id']); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if (in_array($delivery['status'], ['ordered', 'in_transit'])): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#trackModal" data-id="<?php echo $delivery['purchase_order_id']; ?>">
                                                        <i class="fas fa-map-location-dot"></i> Track
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                        <p>No deliveries found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>

    <!-- Track Modal with Map -->
    <div class="modal fade" id="trackModal" tabindex="-1" aria-labelledby="trackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%); color: white;">
                    <h5 class="modal-title" id="trackModalLabel">Track Delivery</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 1rem;">
                        <h6 style="margin: 0; color: #2c3e50;"><i class="fas fa-map-location-dot"></i> Delivery Tracking Map</h6>
                    </div>
                    <div id="trackingMap" style="width: 100%; height: 450px; border-radius: 8px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; position: relative;">
                        <div class="text-center">
                            <i class="fas fa-map" style="font-size: 3rem; color: #b75a03ff; margin-bottom: 1rem;"></i>
                            <p class="text-muted">Loading map...</p>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; font-size: 0.85rem; color: #666;">
                        <i class="fas fa-info-circle" style="color: #b75a03ff; margin-right: 0.5rem;"></i>
                        <span>Map shows the route from supplier to destination branch. <span style="color: #0066cc;">?</span> Supplier Location <span style="color: #cc0000;">?</span> Branch Destination</span>
                    </div>
                    <div style="margin-top: 1.5rem;">
                        <h6 class="mb-3" style="color: #2c3e50;">Delivery Information</h6>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e8e8e8;">
                                <span class="fw-bold">Status:</span>
                                <span id="trackStatus">-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e8e8e8;">
                                <span class="fw-bold">Expected Delivery:</span>
                                <span id="trackExpectedDate">-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span class="fw-bold">Current Location:</span>
                                <span id="trackLocation">In Transit</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Track Modal Handler
            const trackModal = document.getElementById('trackModal');
            if (trackModal) {
                trackModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const orderId = button.getAttribute('data-id');
                    
                    // Fetch tracking data
                    fetch('<?php echo site_url('deliveries/get-tracking/'); ?>' + orderId)
                        .then(response => response.json())
                        .then(data => {
                            // Update info
                            document.getElementById('trackStatus').textContent = data.status || '-';
                            document.getElementById('trackExpectedDate').textContent = data.expected_date || '-';
                            document.getElementById('trackLocation').textContent = data.location || 'In Transit';
                            
                            // Initialize map
                            setTimeout(() => {
                                initializeDeliveryMap(data);
                            }, 300);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('trackingMap').innerHTML = '<p style="text-align:center; padding:2rem; color:#666;">Unable to load map</p>';
                        });
                });
            }
        });

        function initializeDeliveryMap(data) {
            const container = document.getElementById('trackingMap');
            if (!container) return;

            container.innerHTML = '';
            container.style.height = '400px';
            container.style.backgroundColor = '#f0f0f0';

            // Check if Leaflet is loaded
            if (typeof L === 'undefined') {
                container.innerHTML = '<p style="text-align:center; padding:2rem; color:#666;">Map library not loaded</p>';
                return;
            }

            try {
                // Ensure data is valid
                const supplierLat = parseFloat(data.supplier_lat) || 7.0500;
                const supplierLng = parseFloat(data.supplier_lng) || 125.6000;
                const branchLat = parseFloat(data.latitude) || 7.0731;
                const branchLng = parseFloat(data.longitude) || 125.6121;

                // Calculate center
                const centerLat = (supplierLat + branchLat) / 2;
                const centerLng = (supplierLng + branchLng) / 2;

                // Create map
                const map = L.map(container).setView([centerLat, centerLng], 11);

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                    maxZoom: 19
                }).addTo(map);

                // Add supplier marker
                L.circleMarker([supplierLat, supplierLng], {
                    radius: 10,
                    fillColor: '#0066cc',
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map).bindPopup('<b>Supplier</b><br>' + (data.supplier_name || 'Supplier'));

                // Add branch marker
                L.circleMarker([branchLat, branchLng], {
                    radius: 10,
                    fillColor: '#cc0000',
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map).bindPopup('<b>Delivery</b><br>' + (data.location || 'Branch'));

                // Draw route line
                L.polyline([
                    [supplierLat, supplierLng],
                    [branchLat, branchLng]
                ], {
                    color: '#0066cc',
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '5, 5'
                }).addTo(map);

            } catch (error) {
                console.error('Map error:', error);
                container.innerHTML = '<p style="text-align:center; padding:2rem; color:#666;">Error initializing map</p>';
            }
        }
    </script>
</body>
</html>











