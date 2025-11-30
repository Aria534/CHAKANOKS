<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Users - ChakaNoks Central Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            color: #503e2cff;
        }
        .sidebar {
            width: 220px;
            background: #1a1a1a;
            color: #b75a03ff;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .sidebar .logo { font-size:1.5rem; font-weight:700; color:#b75a03ff; margin-bottom:2rem; }
        .sidebar nav { display: flex; flex-direction: column; gap: 0.6rem; }
        .sidebar nav a {
            color:#aaa;
            text-decoration:none;
            font-weight:500;
            padding:0.6rem 1rem;
            border-radius:6px;
            transition:0.2s;
        }
        .sidebar nav a:hover { background:#2c2c2c; color:#fff; }
        .sidebar a.active, .sidebar a:hover {
            background: #ff9320ff;
            color: #fff;
        }
        .sidebar nav a.logout { color:#e74c3c !important; margin-top:auto; }
        .main-content { margin-left: 220px; padding: 2rem; }
        .page-title {
            font-size:1.8rem;
            margin-bottom:1.5rem;
            font-weight:600;
            color:#fff;
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(183, 90, 3, 0.3);
        }
        .table-responsive { margin-top: 2rem; }
        .btn-add { margin-bottom: 1rem; }
        .status-active { color: green; }
        .status-inactive { color: red; }
        @media (max-width:768px){ .main-content { margin-left: 0; padding:1rem; } }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="logo">ChakaNoks</div>
    <nav>
        <a href="<?= site_url('dashboard') ?>">Dashboard</a>
        <a href="<?= site_url('users') ?>" class="active">Manage Users</a>
        <a href="<?= site_url('branches') ?>">Branches</a>
        <a href="<?= site_url('products') ?>">Products</a>
        <a href="<?= site_url('orders') ?>">Orders</a>
        <a href="<?= site_url('inventory') ?>">Inventory</a>
        <a href="<?= site_url('logout') ?>" class="logout">Logout</a>
    </nav>
</aside>

<!-- Main content -->
<div class="main-content">
    <div class="page-title">Manage Users</div>

    <a href="<?= site_url('users/create') ?>" class="btn btn-primary btn-add">Add New User</a>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user['username']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td><?= esc($user['first_name']) ?></td>
                            <td><?= esc($user['last_name']) ?></td>
                            <td><?= esc($user['role']) ?></td>
                            <td>
                                <span class="status-<?= esc($user['status']) ?>">
                                    <?= ucfirst(esc($user['status'])) ?>
                                </span>
                            </td>
                            <td><?= esc($user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never') ?></td>
                            <td>
                                <a href="<?= site_url('users/edit/' . $user['user_id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?= site_url('users/delete/' . $user['user_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
