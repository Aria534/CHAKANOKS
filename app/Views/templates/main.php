<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ChakaNoks' ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        .sidebar {
            background-color: #1a1a1a;
            color: #fff;
            width: 250px;
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
            display: flex;
            align-items: center;
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
            margin-left: 250px;
            min-height: 100vh;
        }
        .page-header {
            background-color: #ff6b00;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="flex">
        <!-- Sidebar -->
        <?= view('templete/sidebar', ['active' => $active ?? '']) ?>
        
        <!-- Main Content -->
        <div class="main-content flex-1">
            <!-- Page Header -->
            <header class="page-header">
                <h1 class="text-xl font-semibold"><?= $title ?? 'Dashboard' ?></h1>
                <div class="flex items-center">
                    <span class="mr-4">Welcome, <?= session('username') ?? 'User' ?></span>
                    <a href="<?= site_url('logout') ?>" class="text-white hover:underline">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-6">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Add any global scripts here
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Application initialized');
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
