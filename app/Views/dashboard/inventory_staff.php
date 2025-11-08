<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inventory – <?= esc($branchName ?? 'Branch') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .wrap { padding:24px; }
    .title { font-size:26px; font-weight:800; color:#111827; margin-bottom:14px; }
    .grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(320px,1fr)); gap:16px; }
    .card { background:#fff; border-radius:12px; padding:18px; box-shadow:0 6px 18px rgba(0,0,0,.06); }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
    th { color:#374151; font-size:13px; text-transform:uppercase; letter-spacing:.04em; }
  </style>
</head>
<body>
  <?= view('templete/navbar', ['active' => 'inventory']) ?>
  <div class="wrap">
    <div class="title d-flex justify-content-between align-items-center">
      <span>Inventory – <?= esc($branchName ?? 'Branch') ?></span>
      <?php if (!empty($canSwitchBranches)): ?>
      <form method="get" action="<?= site_url('inventory') ?>" class="d-flex align-items-center" style="gap:8px;">
        <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="all">All Branches</option>
          <?php foreach(($branches ?? []) as $b): ?>
            <option value="<?= (int)$b['branch_id'] ?>" <?= ((int)($selectedBranchId ?? 0) === (int)$b['branch_id']) ? 'selected' : '' ?>>
              <?= esc($b['branch_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <noscript><button class="btn btn-sm btn-primary">View</button></noscript>
      </form>
      <?php endif; ?>
    </div>
    <div class="grid">
      <div class="card" style="grid-column: 1 / -1;">
        <h5 class="mb-3">All Products – <?= esc($branchName ?? 'Branch') ?></h5>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Product</th>
                <th class="text-end">Current</th>
                <th class="text-end">Available</th>
                <th class="text-end">Min</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Stock Value</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($branchInventory)): foreach($branchInventory as $row): ?>
                <?php $isLow = ((int)($row['available_stock'] ?? 0) <= (int)($row['minimum_stock'] ?? 0)); ?>
                <tr class="<?= $isLow ? 'table-warning' : '' ?>">
                  <td><?= esc($row['product_name']) ?></td>
                  <td class="text-end"><?= (int)$row['current_stock'] ?></td>
                  <td class="text-end"><?= (int)$row['available_stock'] ?></td>
                  <td class="text-end"><?= (int)($row['minimum_stock'] ?? 0) ?></td>
                  <td class="text-end">₱<?= number_format((float)($row['unit_price'] ?? 0), 2) ?></td>
                  <td class="text-end">₱<?= number_format((float)($row['stock_value'] ?? 0), 2) ?></td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="6" class="text-muted">No products found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card">
        <h5 class="mb-3">Low Stock Items</h5>
        <table>
          <thead><tr><th>Item</th><th>Qty</th><th>Branch</th></tr></thead>
          <tbody>
            <?php if (!empty($lowStockItems)): foreach ($lowStockItems as $item): ?>
              <tr>
                <td><?= esc($item['product_name']) ?></td>
                <td><?= esc($item['available_stock']) ?></td>
                <td><?= esc($item['branch_name'] ?? 'N/A') ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="3" class="text-muted">No low stock items.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card">
        <h5 class="mb-3">Pending Receives</h5>
        <table>
          <thead><tr><th>PO</th><th>Supplier</th><th>ETA</th><th>Action</th></tr></thead>
          <tbody>
            <?php if (!empty($pendingReceives)): foreach ($pendingReceives as $row): ?>
              <tr>
                <td><?= esc($row['po_number']) ?></td>
                <td><?= esc($row['supplier_name'] ?? '-') ?></td>
                <td><?= esc($row['expected_delivery_date'] ?? '-') ?></td>
                <td>
                  <form method="post" action="<?= site_url('/orders/'.$row['purchase_order_id'].'/receive') ?>" onsubmit="return confirm('Receive this order?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-primary">Receive</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="4" class="text-muted">No pending receives.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card">
        <h5 class="mb-3">Adjust Stock</h5>
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= site_url('/inventory/adjust') ?>">
          <?= csrf_field() ?>
          <?php if (!empty($selectedBranchId)): ?>
            <input type="hidden" name="branch_id" value="<?= (int)$selectedBranchId ?>">
          <?php endif; ?>
          <div class="mb-2">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
              <option value="">— select product —</option>
              <?php foreach(($products ?? []) as $p): ?>
                <option value="<?= (int)$p['product_id'] ?>"><?= esc($p['product_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Quantity (use negative to deduct)</label>
            <input type="number" class="form-control" name="qty" placeholder="e.g., 5 or -2" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason</label>
            <input type="text" class="form-control" name="reason" placeholder="e.g., Adjustment, Damaged, Expired" required>
          </div>
          <button type="submit" class="btn btn-dark w-100">Apply Adjustment</button>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
