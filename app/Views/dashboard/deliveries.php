<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deliveries - ChakaNoks</title>
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
                                                <?php if ($delivery['status'] !== 'delivered'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#updateStatusModal" data-id="<?php echo $delivery['purchase_order_id']; ?>" data-status="<?php echo $delivery['status']; ?>">
                                                        <i class="fas fa-edit"></i> Update
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

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%); color: white;">
                    <h5 class="modal-title" id="updateStatusLabel">Update Delivery Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo site_url('deliveries/update-status'); ?>" id="updateStatusForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="purchase_order_id" id="updateId">

                        <div class="mb-3">
                            <label class="form-label fw-bold">New Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="statusSelect" required>
                                <option value="">— select status —</option>
                                <option value="in_transit">In Transit</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>

                        <div class="mb-3" id="dateDiv" style="display: none;">
                            <label class="form-label fw-bold">Delivery Date</label>
                            <input type="date" class="form-control" name="delivery_date" id="deliveryDate">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Add delivery notes..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="updateStatusForm" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const updateStatusModal = document.getElementById('updateStatusModal');
            const statusSelect = document.getElementById('statusSelect');
            const dateDiv = document.getElementById('dateDiv');

            if (updateStatusModal) {
                updateStatusModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const currentStatus = button.getAttribute('data-status');
                    document.getElementById('updateId').value = id;
                    statusSelect.value = '';
                });
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    dateDiv.style.display = this.value === 'delivered' ? 'block' : 'none';
                    if (this.value === 'delivered') {
                        document.getElementById('deliveryDate').required = true;
                    } else {
                        document.getElementById('deliveryDate').required = false;
                    }
                });
            }

            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
