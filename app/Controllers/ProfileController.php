<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        return view('admin/profile', ['user' => $user]);
    }

    public function update()
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'email'      => 'permit_empty|valid_email|max_length[255]',
            'phone_number' => 'permit_empty|max_length[20]',
        ];

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
            $rules['current_password'] = 'required|min_length[6]';
            $rules['new_password'] = 'required|min_length[6]';
            $rules['confirm_password'] = 'required|matches[new_password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (!empty($currentPassword)) {
            if (!password_verify($currentPassword, $user['password'])) {
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }
        }

        $updateData = [
            'first_name'   => $this->request->getPost('first_name'),
            'last_name'    => $this->request->getPost('last_name'),
            'email'        => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
        ];

        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $profilePicture = $this->request->getFile('profile_picture');
        if ($profilePicture && $profilePicture->isValid() && !$profilePicture->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $validMime = $profilePicture->getMimeType();

            if (!in_array($validMime, $allowedTypes)) {
                return redirect()->back()->with('error', 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
            }

            $newName = $profilePicture->getRandomName();
            $profilePicture->move('uploads/users/', $newName);

            if (!empty($user['profile_picture'])) {
                $oldFile = 'uploads/users/' . $user['profile_picture'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $updateData['profile_picture'] = $newName;
        }

        $userModel->update($userId, $updateData);

        session()->set('user_name', $updateData['first_name'] . ' ' . $updateData['last_name']);
        if (isset($updateData['profile_picture'])) {
            session()->set('profile_picture', $updateData['profile_picture']);
        }

        return redirect()->to('/admin/profile')->with('success', 'Profile updated successfully.');
    }
}
