<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function login(): string
    {
        return view('Login');
    }

    public function attemptLogin(): RedirectResponse
    {
        $loginInput = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        if ($loginInput === '' || $password === '') {
            return redirect()->back()->with('error', 'Please enter username/email and password.');
        }

        $userModel = model('App\\Models\\UserModel');

        // Check if login is email or username
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = $userModel->where('email', $loginInput)->first();
        } else {
            $user = $userModel->where('username', $loginInput)->first();
        }

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid username/email or password.');
        }

        // Initialize session data
        $sessionData = [
            'user_id'   => $user['user_id'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'email'     => $user['email'],
            'full_name' => $user['first_name'] . ' ' . $user['last_name']
        ];

        // For central admin, we don't need branch info
        if ($user['role'] === 'central_admin' || $user['role'] === 'system_admin') {
            $sessionData['is_central'] = true;
        } else {
            // For other roles, get branch info
            $db = \Config\Database::connect();
            $branch = $db->table('user_branches ub')
                ->select('b.branch_id, b.branch_name, b.manager_name')
                ->join('branches b', 'b.branch_id = ub.branch_id')
                ->where('ub.user_id', $user['user_id'])
                ->orderBy('ub.user_branch_id', 'ASC')
                ->get()
                ->getRowArray();

            if ($branch) {
                $sessionData['branch_id'] = $branch['branch_id'];
                $sessionData['branch_name'] = $branch['branch_name'];
            }
        }

        // Save to session
        session()->set($sessionData);

        // Update last login
        $userModel->update($user['user_id'], ['last_login' => date('Y-m-d H:i:s')]);

        // Redirect based on role
        $redirectMap = [
            'central_admin' => 'dashboard/central',
            'system_admin'  => 'dashboard/central',
            'branch_manager' => 'dashboard/inventory',
            'inventory_staff' => 'dashboard/inventory',
            'supplier' => 'supplier',
            'logistics_coordinator' => 'logistics',
            'franchise_manager' => 'franchise'
        ];

        $redirectTo = $redirectMap[$user['role']] ?? 'dashboard';
        return redirect()->to(site_url($redirectTo));
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(site_url('home'));
    }
}
