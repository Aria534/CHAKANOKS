<?php

namespace App\Controllers;
use CodeIgniter\Controller;
class LogisticsDashboard extends Controller
{
    public function index()
    {
        return view('dashboard/logistics_dashboard');
    }
}
