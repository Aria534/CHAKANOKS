<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = (string) (session('role') ?? '');

        switch ($role) {
            case 'central_admin':
            case 'system_admin':
                // Data-backed central dashboard
                return redirect()->to(site_url('dashboard/central'));
            case 'branch_manager':
                // Data-backed branch dashboard
                return redirect()->to(site_url('dashboard/branch-manager'));
            case 'inventory_staff':
                // Inventory module landing
                return redirect()->to(site_url('dashboard/inventory'));
            case 'logistics_coordinator':
                return redirect()->to(site_url('dashboard/logistics'));
            case 'franchise_manager':
                return redirect()->to(site_url('dashboard/franchise'));
            case 'supplier':
                // For now send suppliers to orders list
                return redirect()->to(site_url('orders'));
            default:
                return redirect()->to(site_url('login'));
        }
    }
}


