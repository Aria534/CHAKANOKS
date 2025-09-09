<?php /** Inventory Staff Dashboard */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Staff Dashboard</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f3f4f6; }
        .wrap { padding:24px; }
        .title { font-size:26px; font-weight:800; color:#111827; margin-bottom:14px; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:20px; }
        .card { background:#fff; border-radius:10px; padding:18px; box-shadow:0 10px 24px rgba(0,0,0,.08); }
        .card h3 { margin:0 0 10px 0; font-size:18px; color:#111827; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { color:#374151; font-size:13px; text-transform:uppercase; letter-spacing:.04em; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="title">Inventory Staff Dashboard</div>
        <div class="grid">
            <div class="card">
                <h3>My Tasks</h3>
                <table>
                    <tr><th>Task</th><th>Status</th></tr>
                    <tr><td>Receive Deliveries</td><td>—</td></tr>
                    <tr><td>Update Stocks</td><td>—</td></tr>
                    <tr><td>Report Damaged/Expired</td><td>—</td></tr>
                </table>
            </div>
            <div class="card">
                <h3>Low Stock Items</h3>
                <table>
                    <tr><th>Item</th><th>Qty</th><th>Branch</th></tr>
                    <tr><td>—</td><td>—</td><td>—</td></tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>


