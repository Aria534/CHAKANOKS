<?php
namespace App\Controllers;

class BranchController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        // ✅ No join needed, since manager_name is stored directly in branches
        $branches = $db->table('branches b')
            ->select('b.branch_id, b.branch_name, b.manager_name, b.address, b.phone, b.email, b.status')
            ->get()
            ->getResultArray();

        return view('dashboard/branches', ['branches' => $branches]);
    }
}
