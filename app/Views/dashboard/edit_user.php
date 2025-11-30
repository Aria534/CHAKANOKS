<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit User - ChakaNoks Central Admin</title>
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
        .form-container { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
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
    <div class="page-title">Edit User</div>

    <div class="form-container">
        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('users/edit/' . $user['user_id']) ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= old('username', $user['username']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= old('first_name', $user['first_name']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= old('last_name', $user['last_name']) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone', $user['phone']) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="central_admin" <?= old('role', $user['role']) == 'central_admin' ? 'selected' : '' ?>>Central Admin</option>
                        <option value="branch_manager" <?= old('role', $user['role']) == 'branch_manager' ? 'selected' : '' ?>>Branch Manager</option>
                        <option value="inventory_staff" <?= old('role', $user['role']) == 'inventory_staff' ? 'selected' : '' ?>>Inventory Staff</option>
                        <option value="supplier" <?= old('role', $user['role']) == 'supplier' ? 'selected' : '' ?>>Supplier</option>
                        <option value="logistics_coordinator" <?= old('role', $user['role']) == 'logistics_coordinator' ? 'selected' : '' ?>>Logistics Coordinator</option>
                        <option value="franchise_manager" <?= old('role', $user['role']) == 'franchise_manager' ? 'selected' : '' ?>>Franchise Manager</option>
                        <option value="system_admin" <?= old('role', $user['role']) == 'system_admin' ? 'selected' : '' ?>>System Admin</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" <?= old('status', $user['status']) == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status', $user['status']) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="<?= site_url('users') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>
