<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Create Purchase Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>

        .form-card { background:#fff; border-radius:14px; padding:1.25rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #e8e8e8; }
        .form-label { font-weight:500; color:#2c3e50; }
        .form-control, .form-select { border:1px solid #e8e8e8; border-radius:8px; }
        .form-control:focus, .form-select:focus { border-color:#b75a03ff; box-shadow:0 0 0 0.2rem rgba(183,90,3,0.15); }

        .table th { text-transform:uppercase; font-size:12px; color:#374151; letter-spacing:.04em; }
        .table td { padding:.8rem; border-bottom:1px solid #f7f7f7; color:#444; }
        .table tbody tr:hover { background-color:#f9f9f9; }

        .btn-primary {
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(183, 90, 3, 0.35);
            color: #fff;
        }

        .btn-outline-secondary {
            border-color: #e8e8e8;
            color: #666;
        }
        .btn-outline-secondary:hover {
            background: #f9f9f9;
            border-color: #b75a03ff;
            color: #b75a03ff;
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            color: #fff;
            font-weight: 500;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }

        @media (max-width:768px){ .main-content { margin-left: 0; padding:1rem; } .sidebar { width: 100%; height: auto; position: relative; } }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'orders']) ?>

    <!-- Main content -->
    <div class="main-content">
        <div class="page-title">Create Purchase Request</div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('/orders/store') ?>" class="form-card">
            <?= csrf_field() ?>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Branch</label>
                    <?php $lockBranchId = (int)($selectedBranchId ?? 0); ?>
                    <?php if ($lockBranchId > 0): ?>
                        <select class="form-select" disabled>
                            <?php foreach(($branches ?? []) as $b): if ((int)$b['branch_id'] === $lockBranchId): ?>
                                <option selected><?= esc($b['branch_name']) ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                        <input type="hidden" name="branch_id" value="<?= $lockBranchId ?>" />
                    <?php else: ?>
                        <select name="branch_id" class="form-select" required>
                            <option value="">— select branch —</option>
                            <?php foreach(($branches ?? []) as $b): ?>
                                <option value="<?= (int)$b['branch_id'] ?>"><?= esc($b['branch_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">— select supplier —</option>
                        <?php foreach(($suppliers ?? []) as $s): ?>
                            <option value="<?= (int)$s['supplier_id'] ?>"><?= esc($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="itemsTable" class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="width:60%">Product</th>
                            <th class="text-end" style="width:20%">Qty</th>
                            <th class="text-end" style="width:20%">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="d-flex gap-2 justify-content-between mt-2">
                <div>
                    <button type="button" class="btn btn-secondary" onclick="addRow()">Add Item</button>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= site_url('/orders') ?>" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </div>
        </form>

        <template id="rowTpl">
            <tr>
                <td>
                    <select name="items[][product_id]" class="form-select" required>
                        <option value="">— select product —</option>
                        <?php foreach(($products ?? []) as $p): ?>
                            <option value="<?= (int)$p['product_id'] ?>">
                                <?= esc($p['product_name']) ?> (₱<?= number_format((float)$p['unit_price'], 2) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="number" min="1" name="items[][qty]" value="1" class="form-control text-end" required />
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="this.closest('tr').remove()">Remove</button>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
