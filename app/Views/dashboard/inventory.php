<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        .table-card { background:#fff; border-radius:14px; padding:1.25rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; }
        .table-card table { width:100%; border-collapse:collapse; font-size:14px; }
        .table-card th { text-align:left; padding:.8rem; font-weight:700; color:#666; border-bottom:1px solid #f0f0f0; }
        .table-card td { padding:.8rem; border-bottom:1px solid #f7f7f7; color:#444; }
        .table-card tbody tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'inventory']) ?>

    <div class="main-content">
        <div class="page-title">Inventory Management</div>

        <?php $role = (string)(session('role') ?? ''); ?>

        <!-- Branch Selector for Central Admin -->
        <?php if (in_array($role, ['central_admin','system_admin'])): ?>
            <div style="margin-bottom:1.5rem;">
                <form method="get" action="<?= site_url('inventory') ?>" style="display:flex; gap:1rem; align-items:center;">
                    <label style="font-weight:500; color:#2c3e50;">View by Branch:</label>
                    <select name="branch_id" class="form-select" style="max-width:250px;" onchange="this.form.submit()">
                        <option value="all" <?= (isset($_GET['branch_id']) && $_GET['branch_id'] === 'all') ? 'selected' : '' ?>>All Branches</option>
                        <?php if (!empty($branches ?? [])): ?>
                            <?php foreach($branches as $b): ?>
                                <option value="<?= (int)$b['branch_id'] ?>" <?= (isset($_GET['branch_id']) && (int)$_GET['branch_id'] === (int)$b['branch_id']) ? 'selected' : '' ?>>
                                    <?= esc($b['branch_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </form>
            </div>
        <?php endif; ?>

        <!-- Inventory Table -->
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Branch</th>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventory)): ?>
                        <?php foreach($inventory as $i): ?>
                        <tr>
                            <td><?= $i['inventory_id'] ?></td>
                            <td><?= esc($i['branch_name'] ?? 'N/A') ?></td>
                            <td><?= esc($i['product_name'] ?? 'N/A') ?></td>
                            <td><?= (int)($i['current_stock'] ?? 0) ?></td>
                            <td>₱<?= number_format((float)($i['unit_price'] ?? 0), 2) ?></td>
                            <td>₱<?= number_format((float)($i['stock_value'] ?? 0), 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No inventory data found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
