<?php

namespace App\Controllers;

use App\Models\UserModel;

class ForgotPasswordController extends BaseController
{
    public function index()
    {
        return view('forgot_password');
    }

    public function sendResetLink()
    {
        $companyId = $this->request->getPost('company_id');

        if (empty($companyId)) {
            return redirect()->back()->with('error', 'Please enter your Company ID.');
        }

        $userModel = new UserModel();
        $user = $userModel->getByCompanyId($companyId);

        if ($user) {
            $token = base64_encode($user['company_id']);
            $resetUrl = base_url('reset-password/' . $token);
            log_message('info', 'Password reset link for ' . $user['company_id'] . ': ' . $resetUrl);
        }

        return redirect()->to('/login')->with('success', 'If that account exists, a password reset link has been sent.');
    }
}
