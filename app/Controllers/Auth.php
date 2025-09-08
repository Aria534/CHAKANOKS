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
        $user_id = (string) $this->request->getPost('user_id');
        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');
        if ($username !== '' && $password !== '') {
            return redirect()->to(site_url('dashboard'));
        }
        return redirect()->back()->with('error', 'Please enter username and password.');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(site_url('home'));
    }

    // Registration removed as requested
}
