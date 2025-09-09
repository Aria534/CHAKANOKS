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

        // 🔎 Get branch info from user_branches
        $db = db_connect();
        $branch = $db->table('user_branches ub')
            ->select('b.branch_id, b.branch_name, b.manager_name')
            ->join('branches b', 'b.branch_id = ub.branch_id')
            ->where('ub.user_id', $user['user_id'])
            ->where('ub.is_primary', 1) // kunin yung primary branch
            ->get()
            ->getRowArray();

        // Save to session
        session()->set([
            'user_id'   => $user['user_id'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'branch_id' => $branch['branch_id'] ?? null,
            'branch_name' => $branch['branch_name'] ?? null,
            'manager_name' => $branch['manager_name'] ?? null,
            'isLoggedIn' => true,
        ]);

        return redirect()->to(site_url('dashboard'));
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(site_url('home'));
    }
}
