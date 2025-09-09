<?php /** Logistics Coordinator Dashboard */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logistics Coordinator Dashboard</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f3f4f6; }
        .wrap { padding:24px; }
        .title { font-size:26px; font-weight:800; color:#111827; margin-bottom:14px; }
        .card { background:#fff; border-radius:10px; padding:18px; box-shadow:0 10px 24px rgba(0,0,0,.08); margin-bottom:16px; }
        .card h3 { margin:0 0 10px 0; font-size:18px; color:#111827; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { color:#374151; font-size:13px; text-transform:uppercase; letter-spacing:.04em; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="title">Logistics Coordinator Dashboard</div>
        <div class="card">
            <h3>Delivery Schedule</h3>
            <table>
                <tr><th>Route</th><th>Vehicle</th><th>Departure</th><th>Status</th></tr>
                <tr><td>—</td><td>—</td><td>—</td><td>—</td></tr>
            </table>
        </div>
        <div class="card">
            <h3>Pending Transfers</h3>
            <table>
                <tr><th>From</th><th>To</th><th>Item</th><th>Qty</th></tr>
                <tr><td>—</td><td>—</td><td>—</td><td>—</td></tr>
            </table>
        </div>
    </div>
</body>
</html>


