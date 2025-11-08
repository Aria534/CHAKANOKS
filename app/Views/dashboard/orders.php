<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f9fafb; }
        .wrap { padding:24px; }
        .title { font-size:24px; font-weight:800; color:#111827; margin-bottom:20px; }
        .card { background:#fff; border-radius:12px; padding:18px; box-shadow:0 4px 12px rgba(0,0,0,.08); margin-bottom:20px; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { color:#374151; font-size:13px; text-transform:uppercase; letter-spacing:.04em; }
    </style>
</head>
<body>
    <?= view('templete/navbar', ['active' => 'orders']) ?>

    <div class="wrap">
        <div class="title">Purchase Orders</div>
        <?php $role = (string)(session('role') ?? ''); ?>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div>
              <?php if (in_array($role, ['branch_manager','central_admin','system_admin'])): ?>
                <a class="btn btn-primary" href="<?= site_url('/orders/create') ?>">Create Purchase Request</a>
              <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <table class="table table-striped">
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
                                      <form method="post" action="<?= site_url('/orders/'.$o['purchase_order_id'].'/approve') ?>" onsubmit="return confirm('Approve this order?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-success">Approve</button>
                                      </form>
                                    <?php endif; ?>
                                    <?php if (in_array($role, ['central_admin','system_admin']) && in_array($o['status'], ['approved','pending'])): ?>
                                      <form method="post" action="<?= site_url('/orders/'.$o['purchase_order_id'].'/send') ?>" onsubmit="return confirm('Mark as sent to supplier?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-warning">Send</button>
                                      </form>
                                    <?php endif; ?>
                                    <?php if (in_array($role, ['central_admin','system_admin','inventory_staff']) && in_array($o['status'], ['ordered','approved','pending'])): ?>
                                      <form method="post" action="<?= site_url('/orders/'.$o['purchase_order_id'].'/receive') ?>" onsubmit="return confirm('Receive and update inventory?')">
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

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
