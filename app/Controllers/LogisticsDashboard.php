<?php

namespace App\Controllers;

class LogisticsDashboard extends BaseController
{
    public function index()
    {
        $role = (string) (session('role') ?? '');
        if ($role !== 'logistics_coordinator') {
            return redirect()->to(site_url('dashboard'));
        }

        $data = [
            'title' => 'Logistics Coordinator Dashboard',
            'active' => 'logistics'
        ];

        return view('dashboard/logistics_coordinator', $data);
    }

    public function shipments()
    {
        $role = (string) (session('role') ?? '');
        if ($role !== 'logistics_coordinator') {
            return redirect()->to(site_url('dashboard'));
        }

        $data = [
            'title' => 'Shipments Management',
            'active' => 'shipments'
        ];

        return view('dashboard/shipments', $data);
    }

    public function routes()
    {
        $role = (string) (session('role') ?? '');
        if ($role !== 'logistics_coordinator') {
            return redirect()->to(site_url('dashboard'));
        }

        $data = [
            'title' => 'Delivery Routes',
            'active' => 'routes'
        ];

        return view('dashboard/routes', $data);
    }

    // NEW FIXED FUNCTION
    public function analytics()
    {
        $role = (string) (session('role') ?? '');
        if ($role !== 'logistics_coordinator') {
            return redirect()->to(site_url('dashboard'));
        }

        try {
            // Example placeholder data (replace with real DB queries)
            $recentShipments = []; 
            $statusDistribution = [];
            $routeEfficiency = [];

            return view('dashboard/logistics_analytics', [
                'title' => 'Logistics Analytics',
                'active' => 'analytics',
                'recentShipments' => $recentShipments,
                'statusDistribution' => $statusDistribution,
                'routeEfficiency' => $routeEfficiency,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Logistics Dashboard Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading logistics data');
        }
    }
}
