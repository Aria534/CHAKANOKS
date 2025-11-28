<?php
// Role-aware sidebar partial
// Usage: echo view('templete/sidebar', ['active' => 'dashboard']);
$role = (string)(session('role') ?? '');
$active = $active ?? '';
?>

<aside class="sidebar">
    <div class="logo">ChakaNoks</div>
    <nav>
        <?php if (in_array($role, ['central_admin','system_admin'])): ?>
            <!-- Central Admin Sidebar -->
            <a href="<?= site_url('dashboard/central') ?>" class="<?= $active==='dashboard'?'active':'' ?>">Dashboard</a>
            <a href="<?= site_url('branches') ?>" class="<?= $active==='branches'?'active':'' ?>">Branches</a>
            <a href="<?= site_url('products') ?>" class="<?= $active==='products'?'active':'' ?>">Products</a>
            <a href="<?= site_url('orders') ?>" class="<?= $active==='orders'?'active':'' ?>">Orders</a>
            <a href="<?= site_url('dashboard/inventory') ?>" class="<?= $active==='inventory'?'active':'' ?>">Inventory</a>
        <?php elseif ($role === 'branch_manager'): ?>
            <!-- Branch Manager Sidebar -->
            <a href="<?= site_url('dashboard/inventory') ?>" class="<?= $active==='dashboard'?'active':'' ?>">Dashboard</a>
            <a href="<?= site_url('dashboard/inventory') ?>" class="<?= $active==='inventory'?'active':'' ?>">Inventory</a>
            <a href="<?= site_url('orders') ?>" class="<?= $active==='orders'?'active':'' ?>">Orders</a>
        <?php elseif ($role === 'inventory_staff'): ?>
            <!-- Inventory Staff Sidebar -->
            <a href="<?= site_url('dashboard/inventory') ?>" class="<?= $active==='inventory'?'active':'' ?>">Inventory</a>
            <a href="<?= site_url('orders') ?>" class="<?= $active==='orders'?'active':'' ?>">Orders</a>
        <?php elseif ($role === 'logistics_coordinator'): ?>
            <!-- Logistics Coordinator Sidebar -->
            <a href="<?= site_url('dashboard/logistics') ?>" class="<?= $active==='logistics'?'active':'' ?>">Dashboard</a>
            <a href="<?= site_url('orders') ?>" class="<?= $active==='orders'?'active':'' ?>">Orders</a>
            <a href="<?= site_url('shipments') ?>" class="<?= $active==='shipments'?'active':'' ?>">Shipments</a>
            <a href="<?= site_url('routes') ?>" class="<?= $active==='routes'?'active':'' ?>">Routes</a>
        <?php elseif ($role === 'franchise_manager'): ?>
            <!-- Franchise Manager Sidebar -->
            <a href="<?= site_url('dashboard/franchise') ?>" class="<?= $active==='franchise'?'active':'' ?>">Dashboard</a>
            <a href="<?= site_url('branches') ?>" class="<?= $active==='branches'?'active':'' ?>">Branches</a>
            <a href="<?= site_url('dashboard/inventory') ?>" class="<?= $active==='inventory'?'active':'' ?>">Inventory</a>
            <a href="<?= site_url('reports') ?>" class="<?= $active==='reports'?'active':'' ?>">Reports</a>
        <?php endif; ?>
        <a href="<?= site_url('logout') ?>" class="logout">Logout</a>
    </nav>
</aside>