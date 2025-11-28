<?php /** Delivery Routes */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Delivery Routes' ?> - ChakaNoks</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
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
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background-color: #e2e3e5;
            color: #383d41;
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
        #map {
            height: 100%;
            min-height: 400px;
            border-radius: 8px;
            z-index: 1;
        }
        .route-card {
            border: 1px solid #eee;
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .route-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .route-card.active {
            border-color: #ff6b00;
            background-color: #fff8f2;
        }
    </style>
</head>
<body>
    <?= view('templete/sidebar', ['active' => 'routes']) ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Delivery Routes</h1>
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
                        <input type="text" placeholder="Search routes..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Planned</option>
                        <option>Completed</option>
                    </select>
                    <select class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <a href="/routes/create" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> New Route
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Routes List -->
                <div class="lg:col-span-1 space-y-4">
                    <!-- Route Card 1 -->
                    <div class="route-card active p-4 bg-white rounded-lg shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg">Downtown Delivery</h3>
                                <p class="text-sm text-gray-500">5 stops • 12.5 km • 45 min</p>
                            </div>
                            <span class="status-badge status-active">Active</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <i class="fas fa-user text-gray-400 w-5 mr-2"></i>
                                <span>Driver: John D.</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <i class="fas fa-truck text-gray-400 w-5 mr-2"></i>
                                <span>Vehicle: Truck #A-1234</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="far fa-clock text-gray-400 w-5 mr-2"></i>
                                <span>ETA: Today, 3:00 PM</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between">
                            <button class="text-xs text-orange-600 hover:text-orange-800">
                                <i class="fas fa-map-marker-alt mr-1"></i> Track
                            </button>
                            <button class="text-xs text-blue-600 hover:text-blue-800">
                                <i class="fas fa-route mr-1"></i> Optimize
                            </button>
                            <button class="text-xs text-green-600 hover:text-green-800">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <button class="text-xs text-red-600 hover:text-red-800">
                                <i class="fas fa-stop-circle mr-1"></i> End
                            </button>
                        </div>
                    </div>

                    <!-- Route Card 2 -->
                    <div class="route-card p-4 bg-white rounded-lg shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg">Uptown Delivery</h3>
                                <p class="text-sm text-gray-500">3 stops • 8.2 km • 30 min</p>
                            </div>
                            <span class="status-badge status-pending">Planned</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <i class="fas fa-user text-gray-400 w-5 mr-2"></i>
                                <span>Driver: Sarah M.</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <i class="fas fa-truck text-gray-400 w-5 mr-2"></i>
                                <span>Vehicle: Van #B-5678</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="far fa-calendar-alt text-gray-400 w-5 mr-2"></i>
                                <span>Scheduled: Tomorrow, 9:00 AM</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between">
                            <button class="text-xs text-blue-600 hover:text-blue-800">
                                <i class="fas fa-route mr-1"></i> Optimize
                            </button>
                            <button class="text-xs text-green-600 hover:text-green-800">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <button class="text-xs text-red-600 hover:text-red-800">
                                <i class="fas fa-trash-alt mr-1"></i> Delete
                            </button>
                        </div>
                    </div>

                    <!-- Route Card 3 -->
                    <div class="route-card p-4 bg-white rounded-lg shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg">Westside Delivery</h3>
                                <p class="text-sm text-gray-500">7 stops • 15.8 km • 1h 15min</p>
                            </div>
                            <span class="status-badge status-completed">Completed</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <i class="fas fa-user text-gray-400 w-5 mr-2"></i>
                                <span>Driver: Mike R.</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <i class="fas fa-truck text-gray-400 w-5 mr-2"></i>
                                <span>Vehicle: Truck #C-9012</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="far fa-check-circle text-green-500 w-5 mr-2"></i>
                                <span>Completed: Today, 2:15 PM</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between">
                            <button class="text-xs text-blue-600 hover:text-blue-800">
                                <i class="fas fa-redo mr-1"></i> Reuse
                            </button>
                            <button class="text-xs text-gray-600 hover:text-gray-800">
                                <i class="fas fa-file-export mr-1"></i> Export
                            </button>
                            <button class="text-xs text-gray-600 hover:text-gray-800">
                                <i class="fas fa-print mr-1"></i> Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Map View -->
                <div class="lg:col-span-2">
                    <div class="card h-full">
                        <div class="card-header flex justify-between items-center">
                            <h3>Route Map</h3>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
                                    <i class="fas fa-layer-group mr-1"></i> Layers
                                </button>
                                <button class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
                                    <i class="fas fa-directions mr-1"></i> Directions
                                </button>
                                <button class="px-3 py-1 text-sm border rounded bg-orange-100 text-orange-600">
                                    <i class="fas fa-sync-alt mr-1"></i> Live Update
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div id="map" class="rounded-lg"></div>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm text-gray-500">Total Distance</div>
                                    <div class="font-semibold">12.5 km</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm text-gray-500">Estimated Time</div>
                                    <div class="font-semibold">45 min</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm text-gray-500">Stops</div>
                                    <div class="font-semibold">5/5 completed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stops List -->
            <div class="card mt-6">
                <div class="card-header">
                    <h3>Stops List</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">1</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Main Warehouse</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1234 Logistics Way, City</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">(555) 123-4567</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="#" class="text-green-600 hover:text-green-900">Call</a>
                                </td>
                            </tr>
                            <!-- More stop rows... -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('map').setView([51.505, -0.09], 13);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add a marker
            L.marker([51.5, -0.09]).addTo(map)
                .bindPopup('Sample Location')
                .openPopup();

            // Add route line (sample data)
            const route = L.polyline([
                [51.5, -0.09],
                [51.51, -0.1],
                [51.52, -0.08]
            ]).addTo(map);

            // Fit bounds to show the entire route
            map.fitBounds(route.getBounds());

            // Handle route card clicks
            document.querySelectorAll('.route-card').forEach(card => {
                card.addEventListener('click', function() {
                    // Remove active class from all cards
                    document.querySelectorAll('.route-card').forEach(c => {
                        c.classList.remove('active');
                    });
                    // Add active class to clicked card
                    this.classList.add('active');
                    
                    // Here you would update the map based on the selected route
                    console.log('Selected route:', this.querySelector('h3').textContent);
                });
            });

            console.log('Routes page initialized');
        });
    </script>
</body>
</html>
            
            <!-- Map View -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div id="map" class="h-96 w-full"></div>
                    <div class="p-4">
                        <h3 class="font-semibold mb-2">Route Details</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                                <span>Main Warehouse</span>
                            </div>
                            <div class="flex items-center ml-4">
                                <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                <span>1. Downtown Branch (2:00 PM)</span>
                            </div>
                            <div class="flex items-center ml-4">
                                <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                <span>2. Westside Branch (2:30 PM)</span>
                            </div>
                            <div class="flex items-center ml-4">
                                <div class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></div>
                                <span>3. Uptown Branch (3:00 PM)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const map = L.map('map').setView([14.5995, 120.9842], 13); // Default to Manila coordinates
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Sample route points (replace with actual coordinates)
        const routePoints = [
            [14.5995, 120.9842], // Start point
            [14.6042, 120.9932], // Point 1
            [14.6100, 120.9800], // Point 2
            [14.6050, 120.9700]  // End point
        ];
        
        // Draw the route line
        L.polyline(routePoints, {color: 'blue'}).addTo(map);
        
        // Add markers for each point
        routePoints.forEach((point, index) => {
            L.marker(point).addTo(map)
                .bindPopup(`Stop ${index + 1}`)
                .openPopup();
        });
        
        // Fit map to route bounds
        map.fitBounds(L.latLngBounds(routePoints));
    </script>
</body>
</html>
