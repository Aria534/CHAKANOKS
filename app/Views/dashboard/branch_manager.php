<?php /** Branch Manager Dashboard */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Branch Manager Dashboard</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f3f4f6; }
        .wrap { padding:24px; }
        .title { font-size:26px; font-weight:800; color:#111827; margin-bottom:8px; }
        .subtitle { font-size:16px; color:#374151; margin-bottom:20px; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:20px; }
        .card { background:#fff; border-radius:10px; padding:18px; box-shadow:0 10px 24px rgba(0,0,0,.08); }
        .card h3 { margin:0 0 10px 0; font-size:18px; color:#111827; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { color:#374151; font-size:13px; text-transform:uppercase; letter-spacing:.04em; }
        .highlight { color:#dc2626; font-weight:bold; }
        .empty { text-align:center; color:#6b7280; font-style:italic; }
    </style>
</head>
<body>
    <div class="wrap">
        <!-- Header -->
        <div class="title">Branch Manager Dashboard</div>
        <div class="subtitle">
            <?= session('branch_name') ?? 'Unknown Branch' ?> — 
            Manager: <?= session('manager_name') ?? 'N/A' ?>
        </div>

        <!-- Summary + PO Overview -->
        <div class="grid">
            <!-- Branch Summary -->
            <div class="card">
                <h3>My Branch Summary</h3>
                <table>
                    <tr><th>Metric</th><th>Value</th></tr>
                    <tr>
                        <td>Current Stock Value</td>
                        <td><?= number_format($summary['stock_value'] ?? 0, 2) ?> PHP</td>
                    </tr>
                    <tr>
                        <td>Low Stock Items</td>
                        <td class="<?= ($summary['low_stock_items'] ?? 0) > 0 ? 'highlight' : '' ?>">
                            <?= $summary['low_stock_items'] ?? 0 ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Pending Purchase Requests</td>
                        <td><?= $pendingPOs ?? 0 ?></td>
                    </tr>
                </table>
            </div>

            <!-- Purchase Orders -->
            <div class="card">
                <h3>Purchase Orders Overview</h3>
                <table>
                    <tr><th>Status</th><th>Count</th></tr>
                    <tr><td>Pending</td><td><?= $poCounts['pending'] ?? 0 ?></td></tr>
                    <tr><td>Approved</td><td><?= $poCounts['approved'] ?? 0 ?></td></tr>
                    <tr><td>Delivered</td><td><?= $poCounts['delivered'] ?? 0 ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Low Stock + Recent Movements -->
        <div class="grid" style="margin-top:20px;">
            <!-- Low Stock Items -->
            <div class="card">
                <h3>Low Stock Items</h3>
                <table>
                    <tr><th>Product</th><th>Available</th><th>Minimum</th></tr>
                    <?php if (!empty($lowStockItems)): ?>
                        <?php foreach($lowStockItems as $item): ?>
                            <tr>
                                <td><?= esc($item['product_name']) ?></td>
                                <td class="highlight"><?= $item['available_stock'] ?></td>
                                <td><?= $item['minimum_stock'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="empty">All items above minimum stock</td></tr>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Recent Movements -->
            <div class="card">
                <h3>Recent Stock Movements</h3>
                <table>
                    <tr><th>Date</th><th>Type</th><th>Item</th><th>Qty</th></tr>
                    <?php if (!empty($recentMovements)): ?>
                        <?php foreach($recentMovements as $m): ?>
                            <tr>
                                <td><?= date('Y-m-d', strtotime($m['created_at'])) ?></td>
                                <td><?= ucfirst($m['movement_type']) ?></td>
                                <td><?= $m['product_name'] ?></td>
                                <td><?= $m['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="empty">No stock movements found</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
