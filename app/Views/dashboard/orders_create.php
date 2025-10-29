<?php /* Simple PR create form using existing purchase_orders + purchase_order_items */ ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Create Purchase Request</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    label { display:block; margin: 8px 0 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f5f5f5; text-align: left; }
    .row { display:flex; gap:16px; align-items:center; }
    .row > div { flex:1; }
    .actions { margin-top: 16px; }
    .btn { padding: 8px 12px; background: #2563eb; color:#fff; border:none; cursor:pointer; }
    .btn.secondary { background:#64748b; }
  </style>
</head>
<body>
  <h2>Create Purchase Request</h2>

  <?php if(session()->getFlashdata('error')): ?>
    <div style="color:#b91c1c;"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>
  <?php if(session()->getFlashdata('success')): ?>
    <div style="color:#166534;"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>

  <form method="post" action="<?= site_url('/orders') ?>">
    <?= csrf_field() ?>

    <div class="row">
      <div>
        <label>Branch</label>
        <select name="branch_id" required>
          <option value="">-- select branch --</option>
          <?php foreach(($branches ?? []) as $b): ?>
            <option value="<?= (int)$b['branch_id'] ?>"><?= esc($b['branch_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Supplier</label>
        <select name="supplier_id" required>
          <option value="">-- select supplier --</option>
          <?php foreach(($suppliers ?? []) as $s): ?>
            <option value="<?= (int)$s['supplier_id'] ?>"><?= esc($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <table id="itemsTable">
      <thead>
        <tr>
          <th style="width:60%">Product</th>
          <th style="width:20%">Qty</th>
          <th style="width:20%">Action</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <div class="actions">
      <button type="button" class="btn secondary" onclick="addRow()">Add Item</button>
      <button type="submit" class="btn">Submit Request</button>
      <a href="<?= site_url('/orders') ?>" class="btn secondary" style="text-decoration:none;">Back</a>
    </div>
  </form>

  <template id="rowTpl">
    <tr>
      <td>
        <select name="items[][product_id]" required>
          <option value="">-- select product --</option>
          <?php foreach(($products ?? []) as $p): ?>
            <option value="<?= (int)$p['product_id'] ?>">
              <?= esc($p['product_name']) ?> (₱<?= number_format((float)$p['unit_price'], 2) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </td>
      <td>
        <input type="number" min="1" name="items[][qty]" value="1" required />
      </td>
      <td>
        <button type="button" class="btn secondary" onclick="this.closest('tr').remove()">Remove</button>
      </td>
    </tr>
  </template>

  <script>
    function addRow() {
      const tpl = document.getElementById('rowTpl');
      const tbody = document.querySelector('#itemsTable tbody');
      tbody.appendChild(tpl.content.cloneNode(true));
    }
    // Add initial row
    addRow();
  </script>
</body>
</html>
