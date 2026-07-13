<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function auth()
    {
        $session = session();

        if (!$this->validate([
            'company_id' => 'required|min_length[3]',
            'password'   => 'required|min_length[6]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $companyid = $this->request->getPost('company_id');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->getByCompanyId($companyid);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $sessionData = [
                    'user_id' => $user['id'],
                    'user_name' => $user['first_name'] . ' ' . $user['last_name'],
                    'role' => $user['role'],
                    'company_id' => $user['company_id'],
                    'profile_picture' => $user['profile_picture'],

                    'isLoggedIn' => true
                ];
                $session->set($sessionData);

                if ($user['role'] === 'admin') {
                    return redirect()->to('/admin/dashboard');
                } elseif ($user['role'] === 'receptionist') {
                    return redirect()->to('/receptionist/dashboard');
                } elseif ($user['role'] === 'mechanic') {
                    return redirect()->to('/mechanic/dashboard');
                } elseif ($user['role'] === 'customer') {
                    return redirect()->to('/customer/dashboard');
                } else {
                    return redirect()->back()->with('error', 'Unauthorized role.');
                }
            } else {
                return redirect()->back()->with('error', 'Wrong password');
            }
        } else {
            return redirect()->back()->with('error', 'User not found');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
