<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $role = (string) (session('role') ?? '');

        switch ($role) {
            case 'central_admin':
                return view('dashboard/central_admin');
            case 'branch_manager':
                return view('dashboard/branch_manager');
            case 'franchise_manager':
                return view('dashboard/franchise_manager');
            case 'inventory_staff':
                return view('dashboard/inventory_staff');
            case 'logistics_coordinator':
                return view('dashboard/logistics_coordinator');
            case 'system_admin':
                // Super admin sees central dashboard for now
                return view('dashboard/central_admin');
            case 'supplier':
                // Placeholder – could add supplier portal view later
                return view('dashboard/central_admin');
            default:
                return redirect()->to(site_url('login'));
        }
    }
}


