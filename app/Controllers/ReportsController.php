<?php

namespace App\Controllers;

class ReportsController extends BaseController
{
    public function index()
    {
        $role = (string)(session('role') ?? '');
        if (empty($role)) {
            return redirect()->to(site_url('login'));
        }

        $data = [
            'title' => 'Reports',
            'active' => 'reports'
        ];

        return view('dashboard/reports', $data);
    }

    public function generate($type = 'inventory')
    {
        $role = (string)(session('role') ?? '');
        if (empty($role)) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        // Get date range from request
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        
        // In a real application, you would fetch data from your models here
        $reportData = [
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('Y-m-d H:i:s'),
            'data' => []
        ];

        // Return JSON response for AJAX requests
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($reportData);
        }

        // For direct access, render a view
        return view('dashboard/report_view', [
            'report' => $reportData,
            'title' => ucfirst($type) . ' Report',
            'active' => 'reports'
        ]);
    }
}
