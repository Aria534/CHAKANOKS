<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Users - ChakaNoks Central Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }
        .table-card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
            margin-top: 1.5rem;
        }
        .table-card table { 
            width:100%; 
            border-collapse:collapse; 
            font-size:14px; 
        }
        .table-card th { 
            text-align:left; 
            padding:.8rem; 
            font-weight:700; 
            color:#666; 
            border-bottom:2px solid #f0f0f0; 
            background-color: #fafafa;
        }
        .table-card td { 
            padding:.8rem; 
            border-bottom:1px solid #f7f7f7; 
            color:#444; 
        }
        .table-card tbody tr:hover { 
            background-color: #f9f9f9; 
        }
        .btn-add { 
            margin-bottom: 1.5rem; 
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.25);
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(183, 90, 3, 0.35);
            color: #fff;
        }
        .status-active { color: green; }
        .status-inactive { color: red; }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'users']) ?>

    <div class="main-content">
        <div class="page-title">Manage Users</div>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <a href="<?= site_url('users/create') ?>" class="btn btn-primary btn-add">+ Add New User</a>

        <div class="table-card">
            <table>
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
                        <?php 
                        // Handle both array and object formats
                        $isArray = is_array($user);
                        $username = $isArray ? ($user['username'] ?? '') : ($user->username ?? '');
                        $email = $isArray ? ($user['email'] ?? '') : ($user->email ?? '');
                        $firstName = $isArray ? ($user['first_name'] ?? '') : ($user->first_name ?? '');
                        $lastName = $isArray ? ($user['last_name'] ?? '') : ($user->last_name ?? '');
                        $role = $isArray ? ($user['role'] ?? '') : ($user->role ?? '');
                        $status = $isArray ? ($user['status'] ?? '') : ($user->status ?? '');
                        $lastLogin = $isArray ? ($user['last_login'] ?? null) : ($user->last_login ?? null);
                        $userId = $isArray ? ($user['user_id'] ?? null) : ($user->user_id ?? null);
                        ?>
                        <tr>
                            <td><?= esc($username) ?></td>
                            <td><?= esc($email) ?></td>
                            <td><?= esc($firstName) ?></td>
                            <td><?= esc($lastName) ?></td>
                            <td><?= esc($role) ?></td>
                            <td>
                                <span class="status-<?= esc($status) ?>">
                                    <?= ucfirst(esc($status)) ?>
                                </span>
                            </td>
                            <td><?= esc($lastLogin ? date('Y-m-d H:i', strtotime($lastLogin)) : 'Never') ?></td>
                            <td>
                                <?php if (isset($userId) && !empty($userId)): ?>
                                    <a href="<?= site_url('users/edit/' . $userId) ?>" class="btn btn-sm btn-warning" title="Edit User ID: <?= $userId ?>">Edit</a>
                                    <form action="<?= site_url('users/delete/' . $userId) ?>" method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete User ID: <?= $userId ?>">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-danger">Invalid User ID</span>
                                <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
