<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit User - ChakaNoks Central Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }
        .form-container { 
            background: #fff; 
            padding: 2rem; 
            border-radius: 14px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.06); 
            border: 1px solid #e8e8e8;
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border: 1px solid #e8e8e8;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #b75a03ff;
            box-shadow: 0 0 0 0.2rem rgba(183, 90, 3, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(183, 90, 3, 0.35);
            color: #fff;
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            color: #fff;
            font-weight: 500;
        }
        .btn-secondary:hover {
            background: #5a6268;
            color: #fff;
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'users']) ?>

    <div class="main-content">
    <div class="page-title">Edit User</div>

    <div class="form-container">
        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php 
                    $errors = session('errors');
                    if (is_array($errors)) {
                        foreach ($errors as $key => $error) {
                            if (is_array($error)) {
                                foreach ($error as $err) {
                                    echo '<li>' . esc($err) . '</li>';
                                }
                            } else {
                                echo '<li>' . esc($error) . '</li>';
                            }
                        }
                    } else {
                        echo '<li>' . esc($errors) . '</li>';
                    }
                    ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('users/edit/' . (is_array($user) ? $user['user_id'] : $user->user_id)) ?>" method="post">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= old('username', is_array($user) ? $user['username'] : $user->username) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', is_array($user) ? $user['email'] : $user->email) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= old('first_name', is_array($user) ? $user['first_name'] : $user->first_name) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= old('last_name', is_array($user) ? $user['last_name'] : $user->last_name) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone', is_array($user) ? $user['phone'] : $user->phone) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <?php $userRole = is_array($user) ? $user['role'] : $user->role; ?>
                        <option value="central_admin" <?= old('role', $userRole) == 'central_admin' ? 'selected' : '' ?>>Central Admin</option>
                        <option value="branch_manager" <?= old('role', $userRole) == 'branch_manager' ? 'selected' : '' ?>>Branch Manager</option>
                        <option value="inventory_staff" <?= old('role', $userRole) == 'inventory_staff' ? 'selected' : '' ?>>Inventory Staff</option>
                        <option value="supplier" <?= old('role', $userRole) == 'supplier' ? 'selected' : '' ?>>Supplier</option>
                        <option value="logistics_coordinator" <?= old('role', $userRole) == 'logistics_coordinator' ? 'selected' : '' ?>>Logistics Coordinator</option>
                        <option value="franchise_manager" <?= old('role', $userRole) == 'franchise_manager' ? 'selected' : '' ?>>Franchise Manager</option>
                        <option value="system_admin" <?= old('role', $userRole) == 'system_admin' ? 'selected' : '' ?>>System Admin</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php $userStatus = is_array($user) ? $user['status'] : $user->status; ?>
                        <option value="active" <?= old('status', $userStatus) == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status', $userStatus) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="<?= site_url('users') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
