<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?? 'Shipments Management' ?> - ChakaNoks</title>
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
        }

        .filter-card {
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8;
            margin-bottom: 1.5rem;
        }

        .table-card { 
            background:#fff; 
            border-radius:14px; 
            padding:0; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
        }

        .table-header {
            padding: 1.25rem;
            border-bottom: 1px solid #e8e8e8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title { 
            font-size:1.05rem; 
            font-weight:600; 
            color:#2c3e50; 
        }

        table { 
            width:100%; 
            border-collapse:collapse; 
            font-size:14px; 
        }

        th { 
            text-align:left; 
            padding:.8rem 1rem; 
            font-weight:700; 
            color:#666; 
            border-bottom:2px solid #f0f0f0; 
            background-color: #fafafa;
        }

        td { 
            padding:.8rem 1rem; 
            border-bottom:1px solid #f7f7f7; 
            color:#444; 
        }

        tbody tr:hover {
            background-color: #f9f9f9;
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
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .form-label { font-weight: 600; color: #666; margin-bottom: 0.5rem; }
        .form-control, .form-select { 
            border: 1px solid #e8e8e8; 
            border-radius: 8px; 
            padding: 0.5rem 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #b75a03ff;
            box-shadow: 0 0 0 0.2rem rgba(183, 90, 3, 0.15);
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

        .btn-info {
            background: #0ea5e9;
            border: none;
            color: #fff;
        }
        .btn-info:hover {
            background: #0284c7;
        }

        .btn-success {
            background: #10b981;
            border: none;
            color: #fff;
        }
        .btn-success:hover {
            background: #059669;
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
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #ddd;
        }

        @media (max-width:768px){ 
            .main-content { margin-left: 0; padding:1rem; }
        }
    </style>
</head>
<body>
    <?= view('templete/sidebar', ['active' => 'shipments']) ?>

    <div class="main-content">
        <div class="page-title">
            🚚 Shipments Management
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

        <!-- Filter Section -->
        <div class="filter-card">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="approved">Approved</option>
                        <option value="ordered">In Transit</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="PO Number, Branch...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Delivery Date</label>
                    <input type="date" id="filterDate" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                        <i class="fas fa-redo me-1"></i>Reset Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Shipments Table -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">📦 All Shipments</div>
                <span style="color:#888;">Total: <strong id="totalCount"><?= count($shipments ?? []) ?></strong> shipments</span>
            </div>
            <div style="overflow-x: auto;">
                <table id="shipmentsTable">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Branch</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Expected Delivery</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($shipments)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>No shipments found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($shipments as $shipment): ?>
                            <tr data-status="<?= esc($shipment['status']) ?>"
                                data-po="<?= esc($shipment['po_number']) ?>"
                                data-branch="<?= esc($shipment['branch_name'] ?? '') ?>"
                                data-supplier="<?= esc($shipment['supplier_name'] ?? '') ?>"
                                data-date="<?= esc($shipment['expected_delivery_date'] ?? '') ?>">
                                <td>
                                    <strong><?= esc($shipment['po_number']) ?></strong>
                                    <br>
                                    <small style="color:#888;">
                                        <?= date('M d, Y', strtotime($shipment['requested_date'])) ?>
                                    </small>
                                </td>
                                <td><?= esc($shipment['branch_name'] ?? 'N/A') ?></td>
                                <td><?= esc($shipment['supplier_name'] ?? 'N/A') ?></td>
                                <td>
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
                                </td>
                                <td>
                                    <?php if (!empty($shipment['expected_delivery_date'])): ?>
                                        <?= date('M d, Y', strtotime($shipment['expected_delivery_date'])) ?>
                                        <?php
                                        $today = strtotime('today');
                                        $expDate = strtotime($shipment['expected_delivery_date']);
                                        if ($expDate < $today && $shipment['status'] !== 'delivered') {
                                            echo '<br><span class="badge badge-danger">Overdue</span>';
                                        }
                                        ?>
                                    <?php else: ?>
                                        <span style="color:#999;">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong>₱<?= number_format((float)$shipment['total_amount'], 2) ?></strong></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('logistics/track/' . $shipment['purchase_order_id']) ?>" 
                                           class="btn btn-info" title="Track on Map">
                                            <i class="fas fa-map-marked-alt"></i> Track
                                        </a>
                                        <?php if ($shipment['status'] !== 'delivered'): ?>
                                            <button class="btn btn-success" 
                                                    onclick="updateStatus(<?= $shipment['purchase_order_id'] ?>, 'delivered')"
                                                    title="Mark as Delivered">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        function filterTable() {
            const status = document.getElementById('filterStatus').value.toLowerCase();
            const search = document.getElementById('searchInput').value.toLowerCase();
            const date = document.getElementById('filterDate').value;
            const rows = document.querySelectorAll('#shipmentsTable tbody tr[data-status]');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status').toLowerCase();
                const rowPO = row.getAttribute('data-po').toLowerCase();
                const rowBranch = row.getAttribute('data-branch').toLowerCase();
                const rowSupplier = row.getAttribute('data-supplier').toLowerCase();
                const rowDate = row.getAttribute('data-date');

                const statusMatch = !status || rowStatus === status;
                const searchMatch = !search || rowPO.includes(search) || rowBranch.includes(search) || rowSupplier.includes(search);
                const dateMatch = !date || rowDate === date;

                if (statusMatch && searchMatch && dateMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('totalCount').textContent = visibleCount;
        }

        function resetFilters() {
            document.getElementById('filterStatus').value = '';
            document.getElementById('searchInput').value = '';
            document.getElementById('filterDate').value = '';
            filterTable();
        }

        document.getElementById('filterStatus').addEventListener('change', filterTable);
        document.getElementById('searchInput').addEventListener('input', filterTable);
        document.getElementById('filterDate').addEventListener('change', filterTable);

        function updateStatus(orderId, status) {
            if (!confirm('Mark this shipment as delivered?')) return;

            fetch(`<?= site_url('logistics/update-status/') ?>${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `status=${status}&actual_delivery_date=${new Date().toISOString().split('T')[0]}`
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
