<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?? 'Track Shipment' ?> - ChakaNoks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
        .info-card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8;
            margin-bottom: 1.5rem;
        }
        .info-card h5 {
            font-size:1.05rem; 
            font-weight:600; 
            margin-bottom:1rem; 
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f3f4f6;
            color:#2c3e50; 
        }
        .info-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            width: 180px;
            flex-shrink: 0;
        }
        .info-value {
            color: #1f2937;
        }
        .status-timeline {
            position: relative;
            padding-left: 2rem;
        }
        .timeline-item {
            position: relative;
            padding: 1rem 0 1rem 2rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 0;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: #d1d5db;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #d1d5db;
        }
        .timeline-item.active::before {
            background: #10b981;
            box-shadow: 0 0 0 2px #10b981;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: -0.125rem;
            top: 1rem;
            width: 2px;
            height: calc(100% - 1rem);
            background: #d1d5db;
        }
        .timeline-item:last-child::after {
            display: none;
        }
        .timeline-item.active::after {
            background: #10b981;
        }
        .timeline-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        .timeline-item.active .timeline-title {
            color: #10b981;
        }
        .timeline-date {
            font-size: 0.875rem;
            color: #6b7280;
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
        
        .table { 
            width:100%; 
            border-collapse:collapse; 
            font-size:14px; 
        }
        .table thead th { 
            text-align:left; 
            padding:.8rem; 
            font-weight:700; 
            color:#666; 
            border-bottom:2px solid #f0f0f0; 
            background-color: #fafafa;
        }
        .table td {
            padding:.8rem; 
            border-bottom:1px solid #f7f7f7; 
            color:#444; 
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary { 
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            box-shadow: 0 2px 8px rgba(183, 90, 3, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.35);
        }
        .btn-outline-secondary {
            border: 1px solid #e8e8e8;
            color: #666;
            background: #fff;
        }
        .btn-outline-secondary:hover {
            background: #f8f9fa;
            border-color: #b75a03ff;
            color: #b75a03ff;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .map-info {
            padding: 0.5rem;
            background: white;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .map-info strong {
            display: block;
            margin-bottom: 0.25rem;
            color: #1f2937;
        }
        @media (max-width:768px) { 
            .main-content { margin-left: 0; padding:1rem; }
            .info-row { flex-direction: column; }
            .info-label { width: 100%; margin-bottom: 0.25rem; }
            #map { height: 300px; }
        }
    </style>
</head>
<body>
    <?= view('templete/sidebar', ['active' => 'shipments']) ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div class="page-title" style="margin-bottom: 0;">
                📍 Track Shipment - <?= esc($shipment['po_number'] ?? '') ?>
            </div>
            <a href="<?= site_url('shipments') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Map Section -->
            <div class="col-12 mb-4">
                <div class="info-card">
                    <h5><i class="fas fa-map-marked-alt me-2"></i>Delivery Tracking Map</h5>
                    <div id="map"></div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Map shows the route from supplier to destination branch. 
                            <strong class="text-primary">Blue marker</strong> = Supplier Location, 
                            <strong class="text-danger">Red marker</strong> = Branch Destination
                        </small>
                    </div>
                </div>
            </div>

            <!-- Shipment Info -->
            <div class="col-lg-8">
                <div class="info-card">
                    <h5><i class="fas fa-box me-2"></i>Shipment Information</h5>
                    <div class="info-row">
                        <div class="info-label">PO Number:</div>
                        <div class="info-value"><strong><?= esc($shipment['po_number']) ?></strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            <span class="badge badge-<?= esc($shipment['status']) ?>">
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
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Requested Date:</div>
                        <div class="info-value"><?= date('F d, Y', strtotime($shipment['requested_date'])) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Expected Delivery:</div>
                        <div class="info-value">
                            <?= !empty($shipment['expected_delivery_date']) 
                                ? date('F d, Y', strtotime($shipment['expected_delivery_date'])) 
                                : '<span class="text-muted">Not set</span>' ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Actual Delivery:</div>
                        <div class="info-value">
                            <?= !empty($shipment['actual_delivery_date']) 
                                ? date('F d, Y', strtotime($shipment['actual_delivery_date'])) 
                                : '<span class="text-muted">Not delivered yet</span>' ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Amount:</div>
                        <div class="info-value"><strong>₱<?= number_format((float)$shipment['total_amount'], 2) ?></strong></div>
                    </div>
                </div>

                <div class="info-card">
                    <h5><i class="fas fa-building me-2"></i>Destination - Branch Information</h5>
                    <div class="info-row">
                        <div class="info-label">Branch Name:</div>
                        <div class="info-value"><?= esc($shipment['branch_name'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Address:</div>
                        <div class="info-value"><?= esc($shipment['address'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Contact Number:</div>
                        <div class="info-value"><?= esc($shipment['contact_number'] ?? 'N/A') ?></div>
                    </div>
                </div>

                <div class="info-card">
                    <h5><i class="fas fa-truck me-2"></i>Supplier Information</h5>
                    <div class="info-row">
                        <div class="info-label">Supplier Name:</div>
                        <div class="info-value"><?= esc($shipment['supplier_name'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Contact Person:</div>
                        <div class="info-value"><?= esc($shipment['contact_person'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value"><?= esc($shipment['supplier_phone'] ?? 'N/A') ?></div>
                    </div>
                </div>

                <div class="info-card">
                    <h5><i class="fas fa-list me-2"></i>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                                <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach($items as $item): ?>
                                    <tr>
                                        <td><?= esc($item['product_name']) ?></td>
                                        <td class="text-end"><?= number_format($item['quantity_requested']) ?></td>
                                        <td class="text-end">₱<?= number_format((float)$item['unit_price'], 2) ?></td>
                                        <td class="text-end">₱<?= number_format((float)$item['total_price'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total:</td>
                                        <td class="text-end">₱<?= number_format((float)$shipment['total_amount'], 2) ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No items found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="col-lg-4">
                <div class="info-card">
                    <h5><i class="fas fa-history me-2"></i>Delivery Status Timeline</h5>
                    <div class="status-timeline">
                        <div class="timeline-item <?= in_array($shipment['status'], ['pending', 'approved', 'ordered', 'delivered']) ? 'active' : '' ?>">
                            <div class="timeline-title">Request Created</div>
                            <div class="timeline-date">
                                <?= date('M d, Y h:i A', strtotime($shipment['requested_date'])) ?>
                            </div>
                        </div>
                        <div class="timeline-item <?= in_array($shipment['status'], ['approved', 'ordered', 'delivered']) ? 'active' : '' ?>">
                            <div class="timeline-title">Approved</div>
                            <div class="timeline-date">
                                <?= !empty($shipment['approved_date']) 
                                    ? date('M d, Y h:i A', strtotime($shipment['approved_date'])) 
                                    : 'Pending approval' ?>
                            </div>
                        </div>
                        <div class="timeline-item <?= in_array($shipment['status'], ['ordered', 'delivered']) ? 'active' : '' ?>">
                            <div class="timeline-title">In Transit</div>
                            <div class="timeline-date">
                                <?= $shipment['status'] === 'ordered' || $shipment['status'] === 'delivered' 
                                    ? 'On the way' 
                                    : 'Not dispatched yet' ?>
                            </div>
                        </div>
                        <div class="timeline-item <?= $shipment['status'] === 'delivered' ? 'active' : '' ?>">
                            <div class="timeline-title">Delivered</div>
                            <div class="timeline-date">
                                <?= !empty($shipment['actual_delivery_date']) 
                                    ? date('M d, Y', strtotime($shipment['actual_delivery_date'])) 
                                    : 'Not delivered yet' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($shipment['status'] !== 'delivered'): ?>
                <div class="info-card">
                    <h5><i class="fas fa-tools me-2"></i>Update Delivery Status</h5>
                    <form id="updateStatusForm">
                        <input type="hidden" id="orderId" value="<?= $shipment['purchase_order_id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="statusSelect" class="form-select" required>
                                <option value="">Select status</option>
                                <option value="ordered" <?= $shipment['status'] === 'ordered' ? 'selected' : '' ?>>In Transit</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expected Delivery Date</label>
                            <input type="date" id="expectedDate" class="form-control" 
                                   value="<?= $shipment['expected_delivery_date'] ?? '' ?>">
                        </div>

                        <div class="mb-3" id="actualDateGroup" style="display: none;">
                            <label class="form-label">Actual Delivery Date</label>
                            <input type="date" id="actualDate" class="form-control" 
                                   value="<?= date('Y-m-d') ?>">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <?php if (!empty($shipment['notes'])): ?>
                <div class="info-card">
                    <h5><i class="fas fa-sticky-note me-2"></i>Notes</h5>
                    <p class="text-muted mb-0"><?= nl2br(esc($shipment['notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const branchAddress = "<?= addslashes($shipment['address'] ?? 'Davao City, Philippines') ?>";
        const branchName = "<?= addslashes($shipment['branch_name'] ?? 'Branch') ?>";
        const supplierName = "<?= addslashes($shipment['supplier_name'] ?? 'Supplier') ?>";
        const poNumber = "<?= addslashes($shipment['po_number'] ?? '') ?>";
        const status = "<?= addslashes($shipment['status'] ?? '') ?>";

        // Davao City coordinates as default center
        let mapCenter = [7.1907, 125.4553];
        
        // Initialize the map
        const map = L.map('map').setView(mapCenter, 12);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Define some known locations in Davao City (you can expand this)
        const davaoCityLocations = {
            'SM Lanang Premier': [7.0989, 125.6269],
            'Abreeza Mall': [7.0709, 125.6122],
            'Victoria Plaza': [7.0731, 125.6123],
            'NCCC Mall': [7.0644, 125.6089],
            'Gaisano Mall': [7.0644, 125.6089],
            'Davao City': [7.1907, 125.4553],
            'Downtown Davao': [7.0731, 125.6123]
        };

        // Try to find coordinates based on branch address
        let branchCoords = mapCenter;
        let supplierCoords = [7.0644, 125.6089]; // Default to city center

        // Search for known locations in the address
        for (const [location, coords] of Object.entries(davaoCityLocations)) {
            if (branchAddress.toLowerCase().includes(location.toLowerCase())) {
                branchCoords = coords;
                break;
            }
        }

        // Create custom icons
        const supplierIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const branchIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Add supplier marker
        const supplierMarker = L.marker(supplierCoords, { icon: supplierIcon }).addTo(map);
        supplierMarker.bindPopup(`
            <div class="map-info">
                <strong><i class="fas fa-store"></i> ${supplierName}</strong>
                <p class="mb-0 small">Supplier Location</p>
            </div>
        `);

        // Add branch marker
        const branchMarker = L.marker(branchCoords, { icon: branchIcon }).addTo(map);
        branchMarker.bindPopup(`
            <div class="map-info">
                <strong><i class="fas fa-building"></i> ${branchName}</strong>
                <p class="mb-0 small">${branchAddress}</p>
                <span class="badge bg-info mt-1">${poNumber}</span>
            </div>
        `).openPopup();

        // Draw a line between supplier and branch
        const routeLine = L.polyline([supplierCoords, branchCoords], {
            color: status === 'delivered' ? '#10b981' : '#3730a3',
            weight: 3,
            opacity: 0.7,
            dashArray: status === 'delivered' ? '0' : '10, 10'
        }).addTo(map);

        // Add a moving marker if in transit
        if (status === 'ordered') {
            const midPoint = [
                (supplierCoords[0] + branchCoords[0]) / 2,
                (supplierCoords[1] + branchCoords[1]) / 2
            ];
            
            const truckIcon = L.divIcon({
                html: '<i class="fas fa-truck fa-2x" style="color: #ff6b00;"></i>',
                iconSize: [30, 30],
                className: 'truck-marker'
            });
            
            const truckMarker = L.marker(midPoint, { icon: truckIcon }).addTo(map);
            truckMarker.bindPopup(`
                <div class="map-info">
                    <strong><i class="fas fa-shipping-fast"></i> In Transit</strong>
                    <p class="mb-0 small">Order ${poNumber}</p>
                </div>
            `);
        }

        // Fit map to show all markers
        const group = L.featureGroup([supplierMarker, branchMarker]);
        map.fitBounds(group.getBounds().pad(0.2));

        // Show/hide actual date field based on status
        document.getElementById('statusSelect').addEventListener('change', function() {
            const actualDateGroup = document.getElementById('actualDateGroup');
            if (this.value === 'delivered') {
                actualDateGroup.style.display = 'block';
            } else {
                actualDateGroup.style.display = 'none';
            }
        });

        // Handle form submission
        document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const orderId = document.getElementById('orderId').value;
            const status = document.getElementById('statusSelect').value;
            const expectedDate = document.getElementById('expectedDate').value;
            const actualDate = document.getElementById('actualDate').value;

            if (!status) {
                alert('Please select a status');
                return;
            }

            let formData = `status=${status}`;
            if (expectedDate) {
                formData += `&expected_delivery_date=${expectedDate}`;
            }
            if (status === 'delivered' && actualDate) {
                formData += `&actual_delivery_date=${actualDate}`;
            }

            fetch(`<?= site_url('logistics/update-status/') ?>${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
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
        });

        // Trigger change event on page load to show/hide actual date field
        document.getElementById('statusSelect').dispatchEvent(new Event('change'));
    </script>
</body>
</html>

