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
        .form-control.is-invalid, .form-select.is-invalid { border-color:#dc3545; }
        .invalid-feedback { display:block; width:100%; margin-top:0.25rem; font-size:0.875em; color:#dc3545; }

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

        <form method="post" action="<?= site_url('/orders/store') ?>" class="form-card" id="purchaseRequestForm">
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
                    <select name="supplier_id" id="supplierSelect" class="form-select" required>
                        <option value="">— select supplier —</option>
                        <?php foreach(($suppliers ?? []) as $s): ?>
                            <option value="<?= (int)$s['supplier_id'] ?>"><?= esc($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Select a supplier to filter available products</small>
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
                    <select name="items[][product_id]" class="form-select product-select" required>
                        <option value="">— select product —</option>
                        <?php foreach(($products ?? []) as $p): ?>
                            <option value="<?= (int)$p['product_id'] ?>" 
                                    data-supplier-id="<?= (int)$p['supplier_id'] ?>">
                                <?= esc($p['product_name']) ?> (₱<?= number_format((float)$p['unit_price'], 2) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"></div>
                </td>
                <td>
                    <input type="number" min="1" step="1" name="items[][qty]" value="1" class="form-control text-end" required />
                    <div class="invalid-feedback"></div>
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="this.closest('tr').remove(); updateProductOptions();">Remove</button>
                </td>
            </tr>
        </template>

    <script>
      // Store all products for filtering
      const allProducts = <?= json_encode($products ?? []) ?>;
      
      function updateProductOptions() {
        const supplierId = document.getElementById('supplierSelect')?.value;
        const productSelects = document.querySelectorAll('.product-select');
        
        productSelects.forEach(select => {
          const currentValue = select.value;
          const options = select.querySelectorAll('option');
          let hasVisibleOptions = false;
          
          // Show/hide options based on supplier
          options.forEach(option => {
            if (option.value === '') {
              option.style.display = 'block'; // Always show placeholder
              hasVisibleOptions = true;
            } else {
              const optionSupplierId = option.getAttribute('data-supplier-id');
              // Show if no supplier selected (show all) OR if supplier matches
              if (!supplierId || optionSupplierId === supplierId) {
                option.style.display = 'block';
                hasVisibleOptions = true;
              } else {
                option.style.display = 'none';
                // Clear selection if current product doesn't match supplier
                if (option.value === currentValue) {
                  select.value = '';
                }
              }
            }
          });
          
          // If no valid options, show message
          if (!hasVisibleOptions && supplierId) {
            const placeholder = select.querySelector('option[value=""]');
            if (placeholder) {
              placeholder.textContent = '— No products for this supplier —';
            }
          } else {
            const placeholder = select.querySelector('option[value=""]');
            if (placeholder) {
              placeholder.textContent = '— select product —';
            }
          }
        });
      }
      
      function removeRow(btn) {
        const row = btn.closest('tr');
        const tbody = document.querySelector('#itemsTable tbody');
        if (tbody.children.length > 1) {
          row.remove();
          updateProductOptions();
        } else {
          alert('You must have at least one item row.');
        }
      }
      
      function addRow() {
        const tpl = document.getElementById('rowTpl');
        const tbody = document.querySelector('#itemsTable tbody');
        const newRow = tpl.content.cloneNode(true);
        tbody.appendChild(newRow);
        updateProductOptions(); // Apply supplier filter to new row
        
        // Add real-time validation to new quantity input
        const qtyInput = tbody.lastElementChild.querySelector('input[name*="[qty]"]');
        if (qtyInput) {
          qtyInput.addEventListener('input', function() {
            const value = parseInt(this.value) || 0;
            if (value <= 0) {
              this.classList.add('is-invalid');
            } else {
              this.classList.remove('is-invalid');
            }
          });
          
          qtyInput.addEventListener('blur', function() {
            const value = parseInt(this.value) || 0;
            if (value <= 0) {
              this.value = 1; // Reset to default
              this.classList.remove('is-invalid');
            }
          });
        }
        
        // Add validation to product select
        const productSelect = tbody.lastElementChild.querySelector('select[name*="[product_id]"]');
        if (productSelect) {
          productSelect.addEventListener('change', function() {
            if (this.value) {
              this.classList.remove('is-invalid');
            }
          });
        }
      }

      // Add initial row
      addRow();
      
      // Initialize product options on page load (show all products initially)
      updateProductOptions();
      
      // Add real-time validation to existing quantity inputs
      document.querySelectorAll('input[name*="[qty]"]').forEach(input => {
        input.addEventListener('input', function() {
          const value = parseInt(this.value) || 0;
          if (value <= 0) {
            this.classList.add('is-invalid');
          } else {
            this.classList.remove('is-invalid');
          }
        });
      });

      // Update product options when supplier changes
      document.getElementById('supplierSelect')?.addEventListener('change', function() {
        const supplierId = this.value;
        const productSelects = document.querySelectorAll('.product-select');
        
        // Clear products that don't match the new supplier
        productSelects.forEach(select => {
          const selectedProductId = select.value;
          if (selectedProductId) {
            const selectedOption = select.querySelector(`option[value="${selectedProductId}"]`);
            const optionSupplierId = selectedOption?.getAttribute('data-supplier-id');
            if (optionSupplierId !== supplierId) {
              select.value = ''; // Clear if doesn't match
            }
          }
        });
        
        updateProductOptions();
      });

      // Form validation before submit
      document.getElementById('purchaseRequestForm').addEventListener('submit', function(e) {
        // Get form values
        const branchId = document.querySelector('select[name="branch_id"]')?.value || 
                        document.querySelector('input[name="branch_id"]')?.value;
        const supplierId = document.querySelector('select[name="supplier_id"]')?.value;
        const itemRows = document.querySelectorAll('#itemsTable tbody tr');
        
        // Clear previous validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        let isValid = true;
        let errorMessages = [];
        
        // Validate branch
        if (!branchId || branchId === '') {
          isValid = false;
          const branchSelect = document.querySelector('select[name="branch_id"]');
          if (branchSelect) {
            branchSelect.classList.add('is-invalid');
          }
          errorMessages.push('Please select a branch.');
        }
        
        // Validate supplier
        if (!supplierId || supplierId === '') {
          isValid = false;
          const supplierSelect = document.querySelector('select[name="supplier_id"]');
          if (supplierSelect) {
            supplierSelect.classList.add('is-invalid');
          }
          errorMessages.push('Please select a supplier.');
        }
        
        // Validate items - FIRST remove empty rows, THEN validate
        const rowsToRemove = [];
        itemRows.forEach((row) => {
          const productSelect = row.querySelector('select[name*="[product_id]"]');
          if (productSelect) {
            const productId = productSelect.value.trim();
            // Remove rows with no product selected (but keep at least one row)
            if ((!productId || productId === '' || productId === '0') && itemRows.length > 1) {
              rowsToRemove.push(row);
            }
          }
        });
        
        // Remove empty rows BEFORE validation
        rowsToRemove.forEach(row => row.remove());
        
        // Now validate remaining rows
        const remainingRows = document.querySelectorAll('#itemsTable tbody tr');
        const validItems = [];
        
        remainingRows.forEach((row, index) => {
          const productSelect = row.querySelector('select[name*="[product_id]"]');
          const qtyInput = row.querySelector('input[name*="[qty]"]');
          
          if (!productSelect || !qtyInput) {
            return; // Skip invalid row structure
          }
          
          const productId = productSelect.value.trim();
          const qty = qtyInput.value.trim();
          const qtyNum = parseInt(qty);
          
          // Validate product selection
          if (!productId || productId === '' || productId === '0') {
            productSelect.classList.add('is-invalid');
            isValid = false;
            if (!errorMessages.some(msg => msg.includes('product'))) {
              errorMessages.push('Please select at least one product.');
            }
          } 
          // Validate quantity
          else if (!qty || isNaN(qtyNum) || qtyNum <= 0) {
            qtyInput.classList.add('is-invalid');
            isValid = false;
            const productName = productSelect.options[productSelect.selectedIndex]?.text?.split('(')[0]?.trim() || 'Product';
            errorMessages.push(`${productName}: Please enter a valid quantity (must be greater than 0).`);
          } 
          // Valid item
          else {
            validItems.push({ productId, qty: qtyNum });
          }
        });
        
        // Final check - must have at least one valid item
        if (validItems.length === 0) {
          isValid = false;
          if (!errorMessages.some(msg => msg.includes('product') || msg.includes('quantity'))) {
            errorMessages.push('Please select at least one product and enter a quantity greater than 0.');
          }
        }
        
        // Show errors or allow submit
        if (!isValid) {
          e.preventDefault();
          const errorMsg = errorMessages.length > 0 ? errorMessages.join('\n') : 'Please fill in all required fields correctly.';
          alert(errorMsg);
          
          // Scroll to first error
          const firstError = document.querySelector('.is-invalid');
          if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => firstError.focus(), 300);
          }
          
          return false;
        }
        
        // Debug: Log form data before submission
        console.log('✓ Form validation passed');
        console.log('Branch ID:', branchId);
        console.log('Supplier ID:', supplierId);
        console.log('Valid Items Count:', validItems.length);
        console.log('Valid Items:', validItems);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = 'Submitting...';
        }
        
        // Form is valid - allow submission
        return true;
      });
    </script>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
