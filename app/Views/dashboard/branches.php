<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Branches</title>
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
    <?= view('partials/navbar', ['active' => 'branches']) ?>

    <div class="wrap">
        <div class="title">Branches</div>
        <div class="card">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Branch</th>
                        <th>Manager</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($branches)): ?>
                        <?php foreach($branches as $b): ?>
                        <tr>
                            <td><?= esc($b['branch_id']) ?></td>
                            <td><?= esc($b['branch_name']) ?></td>
                            <td><?= esc($b['manager_name'] ?? 'N/A') ?></td>
                            <td><?= esc($b['address']) ?></td>
                            <td><?= esc($b['phone']) ?></td>
                            <td><?= esc($b['email']) ?></td>
                            <td><?= esc($b['status']) ?></td>
                            <td>
                              <a class="btn btn-sm btn-primary" href="<?= site_url('inventory') ?>?branch_id=<?= (int)$b['branch_id'] ?>">View Inventory</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No branches found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
