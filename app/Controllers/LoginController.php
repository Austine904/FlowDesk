<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CustomerModel;

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
                    $customerModel = new CustomerModel();
                    $customer = null;
                    if (!empty($user['email'])) {
                        $customer = $customerModel->where('email', $user['email'])->first();
                    }
                    if (!$customer && !empty($user['phone_number'])) {
                        $customer = $customerModel->where('phone', $user['phone_number'])->first();
                    }
                    if ($customer) {
                        $session->set('customer_id', $customer['id']);
                    }
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

    public function resetPassword($token)
    {
        $companyId = base64_decode($token);

        $userModel = new UserModel();
        $user = $userModel->getByCompanyId($companyId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Invalid or expired reset link.');
        }

        return view('reset_password', ['token' => $token]);
    }

    public function processResetPassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (empty($token) || empty($password) || empty($confirmPassword)) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        if ($password !== $confirmPassword) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'Password must be at least 6 characters.');
        }

        $companyId = base64_decode($token);

        $userModel = new UserModel();
        $user = $userModel->getByCompanyId($companyId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Invalid or expired reset link.');
        }

        $userModel->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/login')->with('success', 'Password reset successful. Please sign in with your new password.');
    }
}
