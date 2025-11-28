<?php /** Logistics Coordinator Dashboard */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Logistics Dashboard' ?> - ChakaNoks</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background-color: #1a1a1a;
            color: #fff;
            width: 220px;
            position: fixed;
            height: 100%;
            padding: 20px 0;
        }
        .sidebar .logo {
            color: #ff6b00;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0 20px;
            margin-bottom: 30px;
        }
        .sidebar nav a {
            display: block;
            color: #ccc;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar nav a:hover {
            background-color: #333;
            color: #fff;
        }
        .sidebar nav a.active {
            background-color: #ff6b00;
            color: white;
        }
        .main-content {
            margin-left: 220px;
            padding: 0;
        }
        .page-header {
            background-color: #ff6b00;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .content-wrapper {
            padding: 25px 30px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            background-color: #f9f9f9;
        }
        .card-body {
            padding: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            text-align: left;
            padding: 12px 15px;
            background-color: #f5f5f5;
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
            border-bottom: 1px solid #eee;
        }
        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .table tr:last-child td {
            border-bottom: none;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        .btn-primary {
            background-color: #ff6b00;
            color: white;
            border: 1px solid #e05d00;
        }
        .btn-primary:hover {
            background-color: #e05d00;
        }
        .btn-sm {
            padding: 4px 10px;
            font-size: 0.85rem;
        }
        .action-btn {
            color: #666;
            margin: 0 5px;
            font-size: 1.1rem;
            transition: color 0.3s;
        }
        .action-btn:hover {
            color: #ff6b00;
        }
    </style>
</head>
<body>
    <?= view('templete/sidebar', ['active' => 'logistics']) ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Logistics Dashboard</h1>
            <div class="user-info flex items-center">
                <span class="mr-4">Welcome, <?= session('username') ?? 'User' ?></span>
                <a href="<?= site_url('logout') ?>" class="text-white hover:underline">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="content-wrapper">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="card">
                    <div class="p-5">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500 text-sm font-medium mb-1">Total Shipments</p>
                                <h3 class="text-2xl font-bold">1,248</h3>
                            </div>
                            <div class="p-3 bg-orange-100 rounded-full text-orange-600">
                                <i class="fas fa-truck text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-green-600">
                            <i class="fas fa-arrow-up"></i> 12.5% from last month
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="p-5">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500 text-sm font-medium mb-1">In Transit</p>
                                <h3 class="text-2xl font-bold">48</h3>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                                <i class="fas fa-shipping-fast text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-red-600">
                            <i class="fas fa-arrow-down"></i> 2.3% from yesterday
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="p-5">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500 text-sm font-medium mb-1">Delivered Today</p>
                                <h3 class="text-2xl font-bold">32</h3>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full text-green-600">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-green-600">
                            <i class="fas fa-arrow-up"></i> 8.1% from yesterday
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="p-5">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500 text-sm font-medium mb-1">On-Time Delivery</p>
                                <h3 class="text-2xl font-bold">94%</h3>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-full text-purple-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-green-600">
                            <i class="fas fa-arrow-up"></i> 3.2% from last week
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Shipments Table -->
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h3>Recent Shipments</h3>
                    <a href="/shipments" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> New Shipment
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Shipment ID</th>
                                <th>Destination</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                                <th>ETA</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#SH-1001</td>
                                <td>Downtown Branch</td>
                                <td>Truck #A-1234</td>
                                <td><span class="status-badge status-processing">In Transit</span></td>
                                <td>Today, 3:00 PM</td>
                                <td>
                                    <a href="#" class="action-btn" title="Track"><i class="fas fa-map-marker-alt"></i></a>
                                    <a href="#" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="#" class="action-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#SH-1000</td>
                                <td>Uptown Branch</td>
                                <td>Van #B-5678</td>
                                <td><span class="status-badge status-completed">Delivered</span></td>
                                <td>Today, 11:30 AM</td>
                                <td>
                                    <a href="#" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="#" class="action-btn" title="Print"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#SH-999</td>
                                <td>Westside Branch</td>
                                <td>Truck #C-9012</td>
                                <td><span class="status-badge status-pending">Scheduled</span></td>
                                <td>Tomorrow, 9:00 AM</td>
                                <td>
                                    <a href="#" class="action-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="action-btn text-red-500 hover:text-red-700" title="Cancel"><i class="fas fa-times"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Stats & Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <a href="/shipments/create" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mr-3">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span>Create New Shipment</span>
                        </a>
                        <a href="/routes" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                <i class="fas fa-route"></i>
                            </div>
                            <span>Plan Delivery Route</span>
                        </a>
                        <a href="/inventory" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <span>Check Inventory</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-header">
                            <h3>Recent Activity</h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-1">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">New shipment created</p>
                                        <p class="text-sm text-gray-600">Shipment #SH-1001 to Downtown Branch has been created and is ready for processing.</p>
                                        <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Delivery completed</p>
                                        <p class="text-sm text-gray-600">Shipment #SH-1000 has been successfully delivered to Uptown Branch.</p>
                                        <p class="text-xs text-gray-400 mt-1">5 hours ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mr-3 mt-1">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Delivery delayed</p>
                                        <p class="text-sm text-gray-600">Shipment #SH-998 is experiencing a delay due to traffic conditions.</p>
                                        <p class="text-xs text-gray-400 mt-1">1 day ago</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <a href="#" class="text-sm text-orange-600 hover:underline">View all activity</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('-translate-x-full');
                });
            }
            
            // Initialize any other interactive elements here
            console.log('Logistics dashboard initialized');
        });
    </script>
</body>
</html>


