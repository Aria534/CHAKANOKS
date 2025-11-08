<?php
// Role-aware navbar partial (templete)
// Usage: echo view('templete/navbar', ['active' => 'orders']);
// Supported $active: 'dashboard', 'branches', 'products', 'orders', 'inventory', 'scan'
$role = (string)(session('role') ?? '');
$active = $active ?? '';

// Determine brand label and dashboard URL per role
$brand = 'ChakaNoks';
$dashboardUrl = site_url('dashboard');
if (in_array($role, ['central_admin','system_admin'])) {
  $brand = 'ChakaNoks Central Admin';
  $dashboardUrl = site_url('dashboard/central');
} elseif ($role === 'branch_manager') {
  $brand = 'ChakaNoks Branch Manager';
  $dashboardUrl = site_url('dashboard/branch-manager');
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= $dashboardUrl ?>"><?= esc($brand) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (in_array($role, ['central_admin','system_admin'])): ?>
          <li class="nav-item"><a class="nav-link <?= $active==='dashboard'?'active':'' ?>" href="<?= site_url('dashboard/central') ?>">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='branches'?'active':'' ?>" href="<?= site_url('branches') ?>">Branches</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='products'?'active':'' ?>" href="<?= site_url('products') ?>">Products</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='orders'?'active':'' ?>" href="<?= site_url('orders') ?>">Orders</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='inventory'?'active':'' ?>" href="<?= site_url('inventory') ?>">Inventory</a></li>
        <?php elseif ($role === 'branch_manager'): ?>
          <li class="nav-item"><a class="nav-link <?= $active==='dashboard'?'active':'' ?>" href="<?= site_url('dashboard/branch-manager') ?>">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='inventory'?'active':'' ?>" href="<?= site_url('inventory') ?>">Inventory</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='orders'?'active':'' ?>" href="<?= site_url('orders') ?>">Orders</a></li>
        <?php else: ?>
          <!-- inventory_staff -->
          <li class="nav-item"><a class="nav-link <?= $active==='orders'?'active':'' ?>" href="<?= site_url('orders') ?>">Orders</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='inventory'?'active':'' ?>" href="<?= site_url('inventory') ?>">Inventory</a></li>
          <li class="nav-item"><a class="nav-link <?= $active==='scan'?'active':'' ?>" href="<?= site_url('inventory/scan') ?>">Scan</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link text-danger" href="<?= site_url('logout') ?>">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
