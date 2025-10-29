<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Management</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background: #fef250; /* bright yellow background */
    }

    .container {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
    }

    h1 {
      display: inline-block;
      font-size: 24px;
      margin: 0;
    }

    .back-arrow {
      display: inline-block;
      margin-right: 10px;
      font-size: 30px;
      cursor: pointer;
      color: #000; /* black color */
      font-weight: bold; /* bold */
      transition: color 0.2s;
    }

    .back-arrow:hover {
      color: #333; /* slightly darker on hover */
    }

    .btn-add {
      float: right;
      padding: 8px 15px;
      font-size: 14px;
      border: none;
      background: #0b100bff;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn-add:hover {
      background: #0c150dff;
    }

    .filters {
      margin: 20px 0;
      display: flex;
      gap: 10px;
    }

    .filters input,
    .filters select {
      padding: 7px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    table th, table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    table th {
      background: #f2f2f2;
      color: darkblue; /* 🔹 changed text color to dark blue */
    }

    .actions button {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 16px;
      margin: 0 5px;
    }

    .actions button.edit {
      color: #007BFF;
    }

    .actions button.delete {
      color: #E53935;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>
      <span class="back-arrow" onclick="history.back()">&#8592;</span>
      Inventory Management
    </h1>
    <button class="btn-add">Add New Item</button>

    <div class="filters">
      <input type="text" placeholder="Search Item...">
      <select>
        <option>All Branches</option>
        <option>Davao City</option>
        <option>Tagum City</option>
        <option>General Santos City</option>
        <option>Zamboanga City</option>
        <option>Koronadal City</option>
      </select>
      <select>
        <option>All Status</option>
        <option>In Stock</option>
        <option>Low Stock</option>
        <option>Out of Stock</option>
      </select>
    </div>

    <table>
      <thead>
        <tr>
          <th>Item Name</th>
          <th>Branch</th>
          <th>Stock Level</th>
          <th>Stock Value</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($inventory)): ?>
          <?php foreach ($inventory as $item): ?>
            <tr>
              <td><?= esc($item['product_name']) ?></td>
              <td><?= esc($item['branch_name']) ?></td>
              <td><?= esc($item['current_stock']) ?></td>
              <td>₱<?= number_format($item['stock_value'], 2) ?></td>
              <td>
                <?php
                $status = $item['current_stock'] > 10 ? 'In Stock' : ($item['current_stock'] > 0 ? 'Low Stock' : 'Out of Stock');
                $color = $status == 'In Stock' ? 'green' : ($status == 'Low Stock' ? 'orange' : 'red');
                ?>
                <span style="color: <?= $color ?>; font-weight: bold;"><?= $status ?></span>
              </td>
              <td class="actions">
                <button class="edit">✏️</button>
                <button class="delete">🗑️</button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align:center; color:#999;">No data available</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
