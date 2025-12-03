<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }

        .table-card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
        }
        .table-card table { 
            width:100%; 
            border-collapse:collapse; 
            font-size:14px; 
        }
        .table-card th { 
            text-align:left; 
            padding:.8rem; 
            font-weight:700; 
            color:#666; 
            border-bottom:2px solid #f0f0f0; 
            background-color: #fafafa;
        }
        .table-card td { 
            padding:.8rem; 
            border-bottom:1px solid #f7f7f7; 
            color:#444; 
        }
        .table-card tbody tr:hover { 
            background-color: #f9f9f9; 
        }

        .action-buttons { 
            margin-bottom: 1.5rem; 
        }
        .action-buttons .btn {
            margin-right: 0.5rem;
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.25);
            text-decoration: none;
            display: inline-block;
        }
        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(183, 90, 3, 0.35);
            color: #fff;
        }
        .action-buttons .btn:active {
            transform: translateY(0);
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'orders']) ?>

    <div class="main-content">
        <div class="page-title">Purchase Orders</div>
        
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="action-buttons">
            <?php $role = (string)(session('role') ?? ''); ?>
            <?php if (in_array($role, ['branch_manager','central_admin','system_admin'])): ?>
                <a class="btn" href="<?= site_url('/orders/create') ?>">+ Create Purchase Request</a>
            <?php endif; ?>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PO Number</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Requested Date</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td><?= esc($o['purchase_order_id']) ?></td>
                            <td><?= esc($o['po_number']) ?></td>
                            <td><?= esc($o['branch_name']) ?></td>
                            <td>
                              <?php
                                $badge = 'secondary';
                                if ($o['status']==='pending') $badge='warning';
                                elseif ($o['status']==='approved') $badge='info';
                                elseif ($o['status']==='ordered') $badge='primary';
                                elseif ($o['status']==='delivered') $badge='success';
                                elseif ($o['status']==='cancelled' || $o['status']==='rejected') $badge='danger';
                              ?>
                              <span class="badge bg-<?= $badge ?> text-uppercase"><?= esc($o['status']) ?></span>
                            </td>
                            <td>₱<?= number_format($o['total_amount'], 2) ?></td>
                            <td><?= $o['requested_date'] ? date('Y-m-d', strtotime($o['requested_date'])) : '-' ?></td>
                            <td><?= $o['created_at'] ? date('Y-m-d', strtotime($o['created_at'])) : '-' ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php if (in_array($role, ['central_admin','system_admin']) && in_array($o['status'], ['pending'])): ?>
                                      <form method="post" action="<?= site_url('/orders/'.$o['purchase_order_id'].'/approve') ?>" onsubmit="return confirm('Approve this order?')" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-success">Approve</button>
                                      </form>
                                    <?php endif; ?>
                                    <?php if (in_array($role, ['central_admin','system_admin']) && in_array($o['status'], ['approved','pending'])): ?>
                                      <form method="post" action="<?= site_url('/orders/'.$o['purchase_order_id'].'/send') ?>" onsubmit="return confirm('Mark as sent to supplier?')" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-warning">Send</button>
                                      </form>
                                    <?php endif; ?>
                                    <?php if (in_array($role, ['central_admin','system_admin','inventory_staff']) && in_array($o['status'], ['ordered','approved','pending'])): ?>
                                      <form method="post" action="<?= site_url('/orders/'.$o['purchase_order_id'].'/receive') ?>" onsubmit="return confirm('Receive and update inventory?')" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-primary">Receive</button>
                                      </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
