<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Branches</title>
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

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'branches']) ?>

    <!-- Main content -->
    <div class="main-content">
        <div class="page-title">Branches</div>

        <div class="table-card">
            <table>
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
                            <td colspan="8" class="text-center text-muted">No branches found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
