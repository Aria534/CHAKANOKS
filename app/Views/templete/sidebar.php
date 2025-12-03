<?php
// Sidebar partial
// Usage: echo view('templete/sidebar', ['active' => 'dashboard']);
$role = (string)(session('role') ?? '');
$active = $active ?? '';
?>

<aside class="sidebar">
    <div class="logo">ChakaNoks</div>
    <nav>
        <!-- Dashboard -->
        <?php if (in_array($role, ['central_admin','system_admin'])): ?>
            <a href="<?= site_url('dashboard/central') ?>" class="<?= $active==='dashboard'?'active':'' ?>">
                Dashboard
            </a>
        <?php elseif ($role === 'branch_manager'): ?>
            <a href="<?= site_url('dashboard/branch-manager') ?>" class="<?= $active==='dashboard'?'active':'' ?>">
                Dashboard
            </a>
        <?php elseif ($role === 'inventory_staff'): ?>
            <a href="<?= site_url('dashboard/inventory') ?>" class="<?= $active==='inventory'?'active':'' ?>">
                Dashboard
            </a>
        <?php elseif ($role === 'logistics_coordinator'): ?>
            <a href="<?= site_url('dashboard/logistics') ?>" class="<?= $active==='logistics'?'active':'' ?>">
                Dashboard
            </a>
        <?php elseif ($role === 'franchise_manager'): ?>
            <a href="<?= site_url('dashboard/franchise') ?>" class="<?= $active==='franchise'?'active':'' ?>">
                Dashboard
            </a>

            <!-- Franchise Manager Specific Menu Items -->
            <a href="<?= site_url('reports') ?>" class="<?= $active==='reports'?'active':'' ?>">
                Reports
            </a>
            <a href="<?= site_url('products') ?>" class="<?= $active==='products'?'active':'' ?>">
                Products
            </a>
            <a href="<?= site_url('orders') ?>" class="<?= $active==='orders'?'active':'' ?>">
                Orders
            </a>
        <?php endif; ?>

        <!-- Manage Users (Central Admin & System Admin only) -->
        <?php if (in_array($role, ['central_admin','system_admin'])): ?>
            <a href="<?= site_url('users') ?>" class="<?= $active==='users'?'active':'' ?>">
                Manage Users
            </a>
        <?php endif; ?>

        <!-- Branches -->
        <?php if (in_array($role, ['central_admin','system_admin', 'franchise_manager'])): ?>
            <a href="<?= site_url('branches') ?>" class="<?= $active==='branches'?'active':'' ?>">
                Branches
            </a>
        <?php endif; ?>

        <!-- Products -->
        <?php if (in_array($role, ['central_admin','system_admin'])): ?>
            <a href="<?= site_url('products') ?>" class="<?= $active==='products'?'active':'' ?>">
                Products
            </a>
        <?php endif; ?>

        <!-- Orders -->
        <?php if (in_array($role, ['central_admin','system_admin', 'branch_manager', 'inventory_staff', 'logistics_coordinator'])): ?>
            <a href="<?= site_url('orders') ?>" class="<?= $active==='orders'?'active':'' ?>">
                Orders
            </a>
        <?php endif; ?>

        <!-- Inventory -->
        <?php if (in_array($role, ['central_admin','system_admin', 'branch_manager', 'inventory_staff', 'franchise_manager'])): ?>
            <a href="<?= site_url('dashboard/inventory') ?>" class="<?= $active==='inventory'?'active':'' ?>">
                Inventory
            </a>
        <?php endif; ?>

        <!-- Shipments -->
        <?php if ($role === 'logistics_coordinator'): ?>
            <a href="<?= site_url('shipments') ?>" class="<?= $active==='shipments'?'active':'' ?>">
                Shipments
            </a>
        <?php endif; ?>

        <!-- Routes -->
        <?php if ($role === 'logistics_coordinator'): ?>
            <a href="<?= site_url('routes') ?>" class="<?= $active==='routes'?'active':'' ?>">
                Routes
            </a>
        <?php endif; ?>

        <!-- Reports (for other roles) -->
        <?php if ($role !== 'franchise_manager'): ?>
        <a href="<?= site_url('reports') ?>" class="<?= $active==='reports'?'active':'' ?>">
            Reports
        </a>
        <?php endif; ?>

        <!-- Logout -->
        <a href="<?= site_url('logout') ?>" class="logout">
            Logout
        </a>
    </nav>
</aside>