<?php /** Shipments Management */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Shipments' ?> - ChakaNoks</title>
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
    <?= view('templete/sidebar', ['active' => 'shipments']) ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Shipments Management</h1>
            <div class="user-info flex items-center">
                <span class="mr-4">Welcome, <?= session('username') ?? 'User' ?></span>
                <a href="<?= site_url('logout') ?>" class="text-white hover:underline">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="content-wrapper">
            <!-- Filters and Actions -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search shipments..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>In Transit</option>
                        <option>Delivered</option>
                        <option>Cancelled</option>
                    </select>
                    <select class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option>All Dates</option>
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                    </select>
                </div>
                <a href="/shipments/create" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> New Shipment
                </a>
            </div>

            <!-- Shipments Table -->
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h3>All Shipments</h3>
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">1-10</span> of <span class="font-medium">48</span> shipments
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="w-40">Shipment ID</th>
                                    <th>Order #</th>
                                    <th>Origin</th>
                                    <th>Destination</th>
                                    <th>Status</th>
                                    <th>ETA</th>
                                    <th class="w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-gray-50">
                                    <td class="font-medium">#SH-1001</td>
                                    <td>PO-2023-001</td>
                                    <td>Main Warehouse</td>
                                    <td>Downtown Branch</td>
                                    <td><span class="status-badge status-processing">In Transit</span></td>
                                    <td>Today, 3:00 PM</td>
                                    <td>
                                        <a href="#" class="action-btn" title="Track"><i class="fas fa-map-marker-alt"></i></a>
                                        <a href="#" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="action-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="font-medium">#SH-1000</td>
                                    <td>PO-2023-000</td>
                                    <td>Main Warehouse</td>
                                    <td>Uptown Branch</td>
                                    <td><span class="status-badge status-completed">Delivered</span></td>
                                    <td>Today, 11:30 AM</td>
                                    <td>
                                        <a href="#" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="action-btn" title="Print"><i class="fas fa-print"></i></a>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="font-medium">#SH-999</td>
                                    <td>PO-2022-999</td>
                                    <td>Main Warehouse</td>
                                    <td>Westside Branch</td>
                                    <td><span class="status-badge status-pending">Scheduled</span></td>
                                    <td>Tomorrow, 9:00 AM</td>
                                    <td>
                                        <a href="#" class="action-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="action-btn text-red-500 hover:text-red-700" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="font-medium">#SH-998</td>
                                    <td>PO-2022-998</td>
                                    <td>Main Warehouse</td>
                                    <td>East End Branch</td>
                                    <td><span class="status-badge status-cancelled">Delayed</span></td>
                                    <td>Yesterday</td>
                                    <td>
                                        <a href="#" class="action-btn" title="Reschedule"><i class="fas fa-calendar-alt"></i></a>
                                        <a href="#" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-6 py-4 border-t flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">48</span> entries
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="px-3 py-1 border rounded bg-orange-100 text-orange-600 font-medium">
                            1
                        </button>
                        <button class="px-3 py-1 border rounded hover:bg-gray-50">
                            2
                        </button>
                        <button class="px-3 py-1 border rounded hover:bg-gray-50">
                            3
                        </button>
                        <span class="px-3 py-1">...</span>
                        <button class="px-3 py-1 border rounded hover:bg-gray-50">
                            10
                        </button>
                        <button class="px-3 py-1 border rounded hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any interactive elements here
            console.log('Shipments page initialized');
        });
    </script>
</body>
</html>
                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="#" class="text-green-600 hover:text-green-900">Update</a>
                            </td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="flex items-center justify-between mt-6">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">24</span> results
                </div>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 border rounded-md bg-white text-gray-700 hover:bg-gray-50">
                        Previous
                    </button>
                    <button class="px-4 py-2 border rounded-md bg-blue-600 text-white hover:bg-blue-700">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
