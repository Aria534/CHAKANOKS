<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics Orders - ChakaNoks</title>
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
        .status-approved { background-color: #cfe2ff; color: #084298; }
        .status-in_transit { background-color: #cfe2ff; color: #084298; }
        .status-delivered { background-color: #d1e7dd; color: #0f5132; }
        .btn-primary { background: #b75a03ff; border-color: #b75a03ff; cursor: pointer !important; }
        .btn-primary:hover { background: #ff9320ff; border-color: #ff9320ff; }
        .action-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .tabs-section { margin-bottom: 2rem; }
        .nav-tabs .nav-link { color: #2c3e50; border-color: #e8e8e8; }
        .nav-tabs .nav-link.active { background: #b75a03ff; color: white; border-color: #b75a03ff; }
    </style>
</head>
<body>
    <?php echo view('templete/sidebar', ['active' => 'logistics']); ?>

    <div class="main-content">
        <div class="page-title">
            <i class="fas fa-truck"></i> Logistics Management
        </div>

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

        <!-- Tabs -->
        <div class="tabs-section">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                        <i class="fas fa-list"></i> Approved Orders
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipments-tab" data-bs-toggle="tab" data-bs-target="#shipments" type="button" role="tab">
                        <i class="fas fa-box"></i> Shipments
                    </button>
                </li>
            </ul>
        </div>

        <!-- Orders Tab -->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="orders" role="tabpanel">
                <div class="card">
                    <h5><i class="fas fa-shopping-cart"></i> Approved Orders Ready for Assignment</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Items</th>
                                    <th>Branch</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($approvedOrders)): ?>
                                    <?php foreach($approvedOrders as $order): ?>
                                        <tr>
                                            <td><strong><?php echo esc($order['po_number']); ?></strong></td>
                                            <td><?php echo esc($order['supplier_name']); ?></td>
                                            <td><small><?php echo esc(substr($order['items'] ?? 'N/A', 0, 40)); ?>...</small></td>
                                            <td><?php echo esc($order['branch_id']); ?></td>
                                            <td>
                                                <span class="badge status-<?php echo strtolower($order['status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignDriverModal" data-order-id="<?php echo $order['purchase_order_id']; ?>" data-po-number="<?php echo esc($order['po_number']); ?>">
                                                    <i class="fas fa-user-plus"></i> Assign Driver
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                            <p>No approved orders available</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipments Tab -->
            <div class="tab-pane fade" id="shipments" role="tabpanel">
                <div class="card">
                    <h5><i class="fas fa-truck"></i> Active Shipments</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Driver</th>
                                    <th>Vehicle</th>
                                    <th>Status</th>
                                    <th>Est. Delivery</th>
                                    <th>Branch</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($shipments)): ?>
                                    <?php foreach($shipments as $shipment): ?>
                                        <tr>
                                            <td><strong><?php echo esc($shipment['po_number']); ?></strong></td>
                                            <td><?php echo !empty($shipment['driver_name']) ? esc($shipment['driver_name']) : 'Unassigned'; ?></td>
                                            <td><?php echo !empty($shipment['vehicle_number']) ? esc($shipment['vehicle_number']) : '-'; ?></td>
                                            <td>
                                                <span class="badge status-<?php echo strtolower($shipment['status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $shipment['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo !empty($shipment['estimated_delivery_date']) ? date('M d, Y', strtotime($shipment['estimated_delivery_date'])) : '-'; ?></td>
                                            <td><?php echo esc($shipment['branch_name']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateShipmentModal" data-shipment-id="<?php echo $shipment['shipment_id']; ?>" data-status="<?php echo $shipment['status']; ?>">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                            <p>No shipments found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Driver Modal -->
    <div class="modal fade" id="assignDriverModal" tabindex="-1" aria-labelledby="assignDriverLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignDriverLabel">Assign Driver to Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?php echo site_url('logistics/assign-driver'); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="hidden" name="purchase_order_id" id="orderId">
                        
                        <div class="mb-3">
                            <label class="form-label">PO Number</label>
                            <input type="text" class="form-control" id="poNumber" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Driver <span class="text-danger">*</span></label>
                            <select name="driver_id" class="form-select" required>
                                <option value="">-- Select Driver --</option>
                                <?php foreach($drivers as $driver): ?>
                                    <option value="<?php echo $driver['driver_id']; ?>">
                                        <?php echo esc($driver['driver_name']); ?> - <?php echo esc($driver['phone_number']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Estimated Delivery Date <span class="text-danger">*</span></label>
                            <input type="date" name="estimated_delivery" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Driver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Shipment Modal -->
    <div class="modal fade" id="updateShipmentModal" tabindex="-1" aria-labelledby="updateShipmentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateShipmentLabel">Update Shipment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?php echo site_url('logistics/update-shipment'); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="hidden" name="shipment_id" id="shipmentId">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Select Status --</option>
                                <option value="in_transit">In Transit</option>
                                <option value="delivered">Delivered</option>
                                <option value="delayed">Delayed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this shipment..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Assign Driver Modal
        const assignDriverModal = document.getElementById('assignDriverModal');
        assignDriverModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            const poNumber = button.getAttribute('data-po-number');
            
            document.getElementById('orderId').value = orderId;
            document.getElementById('poNumber').value = poNumber;
        });

        // Update Shipment Modal
        const updateShipmentModal = document.getElementById('updateShipmentModal');
        updateShipmentModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const shipmentId = button.getAttribute('data-shipment-id');
            
            document.getElementById('shipmentId').value = shipmentId;
        });
    </script>
</body>
</html>
