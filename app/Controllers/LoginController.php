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

        $companyid = $this->request->getPost('company_id');
        $password = $this->request->getPost('password');
        $validation = \Config\Services::validation();
        $validation->setRules([
            'company_id' => 'required',
            'password' => 'required'
        ]);
        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

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
