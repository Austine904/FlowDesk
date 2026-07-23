<?php

namespace App\Controllers;

use Config\Services;

use CodeIgniter\Controller;
use CodeIgniter\Database\Query;


class UsersController extends BaseController
{
    protected $session; // Define the $session property
    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        return view('admin/users');
    }

    // Start the multi-step user creation wizard
    public function add()
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'receptionist'])) {
            return redirect()->to('/login');
        }

        // Clear any existing step session data to start fresh
        session()->remove(['step1_data', 'step2_data', 'step3_data']);
        return redirect()->to(base_url('user/add_step1'));
    }

    public function __construct()
    {
        $this->session = Services::session();
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        return view('admin/edit_user', ['user' => $user]);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();

        $data = [
            'first_name'   => $this->request->getPost('first_name'),
            'last_name'    => $this->request->getPost('last_name'),
            'email'        => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'role'         => $this->request->getPost('role'),
            'gender'       => $this->request->getPost('gender'),
            'dob'          => $this->request->getPost('dob'),
            'national_id'  => $this->request->getPost('national_id'),
            'date_of_employment' => $this->request->getPost('date_of_employment'),
            'address'      => $this->request->getPost('address'),
        ];

        // Handle profile picture upload
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($file->getMimeType(), $allowedMimes)) {
                $currentUser = $userModel->find($id);
                if ($currentUser && !empty($currentUser['profile_picture'])) {
                    $oldFile = FCPATH . $currentUser['profile_picture'];
                    if (file_exists($oldFile)) {
                        @unlink($oldFile);
                    }
                }
                $newName = $file->getRandomName();
                $file->move('uploads/users', $newName);
                $data['profile_picture'] = 'uploads/users/' . $newName;
            }
        }

        // Remove empty values
        $data = array_filter($data, function ($v) { return $v !== null && $v !== ''; });

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);

        // Log role change if role changed
        $currentUser = $userModel->find($id);
        $newRole = $this->request->getPost('role');
        if ($currentUser && $newRole && $currentUser['role'] !== $newRole) {
            log_activity('role_changed', 'user', $id,
                "Role changed from {$currentUser['role']} to {$newRole}");
        }

        // Handle next-of-kin update
        $kinData = [
            'kin_first_name'   => $this->request->getPost('kin_first_name'),
            'kin_last_name'    => $this->request->getPost('kin_last_name'),
            'relationship'     => $this->request->getPost('relationship'),
            'kin_phone_number' => $this->request->getPost('kin_phone_number'),
        ];
        $hasKinData = array_filter($kinData, function ($v) { return $v !== null && $v !== ''; });
        if (!empty($hasKinData)) {
            $kinModel = new \App\Models\NextOfKinModel();
            $existing = $kinModel->getByUserId($id);
            if ($existing) {
                $kinModel->update($existing['id'], $kinData);
            } else {
                $kinData['user_id'] = $id;
                $kinModel->insert($kinData);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'User updated successfully.']);
        }

        return redirect()->to('/admin/users')->with('success', 'User updated successfully.');
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $userModel = new \App\Models\UserModel();
        $userModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'User deleted successfully.']);
    }

    public function restore($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $userModel = new \App\Models\UserModel();
        $userModel->update($id, ['deleted_at' => null]);
        log_activity('user_restored', 'user', $id, "User ID {$id} restored from soft delete");
        return $this->response->setJSON(['status' => 'success', 'message' => 'User restored successfully.']);
    }

    // Handle bulk actions (delete)
    // public function bulk_action()
    // {
    //     if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
    //         return redirect()->to('/login');
    //     }

    //     $userIds = $this->request->getPost('users');

    //     if ($userIds) {
    //         $db = \Config\Database::connect();
    //         $builder = $db->table('users');
    //         $builder->whereIn('id', $userIds)->delete();

    //         return redirect()->to('/admin/users')->with('success', 'Selected users deleted successfully.');
    //     } else {
    //         return redirect()->to('/admin/users')->with('error', 'No users selected for deletion.');
    //     }
    // }


    public function bulk_action()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $action = $this->request->getPost('action');
        $userIds = $this->request->getPost('users');

        if ($action === 'delete' && is_array($userIds) && !empty($userIds)) {
            $userModel = new \App\Models\UserModel();
            $userModel->whereIn('id', $userIds)->delete();
            return $this->response->setJSON(['status' => 'success', 'message' => count($userIds) . ' user(s) deleted.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid action or no users selected.']);
    }

    public function details($id)
    {
        $db = \Config\Database::connect();

        // Fetch user
        $query = $db->query("SELECT * FROM users WHERE id = ?", [$id]);
        $result = $query->getRowArray();

        if (!$result) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        // Fetch next of kin
        $kinQuery = $db->query("SELECT * FROM next_of_kin WHERE user_id = ?", [$id]);
        $kinResult = $kinQuery->getRowArray();

        // Append kin details to user result
        $result['next_of_kin'] = $kinResult ?? [
            'kin_first_name' => '',
            'kin_last_name' => '',
            'relationship' => '',
            'kin_phone_number' => ''
        ];

        return $this->response->setJSON($result);
    }

    // Handle adding a new user
    public function addStep1()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'receptionist'])) {
            return redirect()->to('/unauthorized');
        }
        $step1Data = session()->get('step1_data') ?? [];
        return view('user/add_step1', ['step1Data' => $step1Data]);
    }

    public function add_step1()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'profile_picture'     => 'uploaded[profile_picture]|max_size[profile_picture,2048]|is_image[profile_picture]',
            'role'               => 'required',
            'company_id'         => 'required',
            'date_of_employment' => 'required',
            'password'           => 'required|min_length[8]',
            'confirm_password'   => 'required|matches[password]',
        ];

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $profileImage = $this->request->getFile('profile_picture');
        $imagePath = null;

        if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
            $newName = $profileImage->getRandomName();
            $profileImage->move('uploads/users', $newName);
            $imagePath = 'uploads/users/' . $newName;
        }

        session()->set('step1_data', [
            'profile_picture'     => $imagePath,
            'role'               => $this->request->getPost('role'),
            'company_id'         => $this->request->getPost('company_id'),
            'date_of_employment' => $this->request->getPost('date_of_employment'),
            'password'           => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Step 1 completed successfully!'
        ]);
    }



    public function addStep2()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'receptionist'])) {
            return redirect()->to('/unauthorized');
        }
        $step2Data = session()->get('step2_data') ?? [];
        return view('user/add_step2', ['step2Data' => $step2Data]);
    }

    public function add_step2()
    {
        // Validate incoming data
        $validation = \Config\Services::validation();
        $validation->setRules([
            'first_name'   => 'required',
            'last_name'    => 'required',
            'dob'          => 'required|valid_date',
            'national_id'  => 'required',
            'gender'       => 'required',
            'phone_number' => 'required',
            'address'      => 'required',
            'email'        => 'required|valid_email'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation errors occurred!',
                'errors'  => $validation->getErrors()
            ]);
        }

        // Store Step 2 data in the session
        session()->set('step2_data', [
            'first_name'   => $this->request->getPost('first_name'),
            'last_name'    => $this->request->getPost('last_name'),
            'dob'          => $this->request->getPost('dob'),
            'national_id'  => $this->request->getPost('national_id'),
            'gender'       => $this->request->getPost('gender'),
            'address'      => $this->request->getPost('address'),
            'phone_number' => $this->request->getPost('phone_number'),
            'email'        => $this->request->getPost('email')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Step 2 completed successfully!'
        ]);
    }

    public function addStep3()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'receptionist'])) {
            return redirect()->to('/unauthorized');
        }
        $step3Data = session()->get('step3_data') ?? [];
        return view('user/add_step3', ['step3Data' => $step3Data]);
    }
    public function addUserStep3()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'kin_first_name'     => 'required',
            'kin_last_name'      => 'required',
            'relationship'       => 'required',
            'kin_phone_number'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        session()->set('step3_data', [
            'kin_first_name'   => $this->request->getPost('kin_first_name'),
            'kin_last_name'    => $this->request->getPost('kin_last_name'),
            'relationship'     => $this->request->getPost('relationship'),
            'kin_phone_number' => $this->request->getPost('kin_phone_number'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Step 3 completed successfully!'
        ]);
    }

    public function getLastId()
    {
        $role = $this->request->getGet('role');
        $prefixes = [
            'admin'        => 'ADM',
            'mechanic'     => 'MCH',
            'receptionist' => 'RCP',
            'customer'     => 'CST',
        ];
        $prefix = $prefixes[$role] ?? 'USR';
        $year = date('y');
        $fullPrefix = $prefix . $year;

        $userModel = new \App\Models\UserModel();
        $lastNum = $userModel->getLastCompanyIdNumber($fullPrefix);
        $nextNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        $newId = $fullPrefix . $nextNum;

        return $this->response->setJSON(['company_id' => $newId]);
    }

    public function preview()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'receptionist'])) {
            return redirect()->to('/unauthorized');
        }
        return view('user/preview');
    }

    public function saveUser()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'receptionist'])) {
            return redirect()->to('/unauthorized');
        }
        $step1Data = session('step1_data');
        $step2Data = session('step2_data');
        $step3Data = session('step3_data');

        if (!$step1Data || !$step2Data || !$step3Data) {
            return redirect()->to(base_url('user/add_step1'))->with('error', 'Session expired. Please start again.');
        }

        if (!isset($step1Data['password']) || empty($step1Data['password'])) {
            return redirect()->to(base_url('user/add_step1'))->with('error', 'Session expired. Password data missing. Please start again.');
        }

        $userData = array_merge($step1Data, $step2Data);

        $db = \Config\Database::connect();
        $db->table('users')->insert($userData);
        $userId = $db->insertID();

        $db->table('next_of_kin')->insert([
            'user_id'          => $userId,
            'kin_first_name'   => $step3Data['kin_first_name'],
            'kin_last_name'    => $step3Data['kin_last_name'],
            'relationship'     => $step3Data['relationship'],
            'kin_phone_number' => $step3Data['kin_phone_number'],
        ]);

        log_activity('user_created', 'user', $userId, "New user {$userData['company_id']} ({$userData['role']}) created");

        session()->remove(['step1_data', 'step2_data', 'step3_data']);

        return redirect()->to(base_url('admin/users'))->with('success', 'User created successfully.');
    }
    public function saveUserFromAdmin()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'company_id'         => 'required|is_unique[users.company_id]',
            'first_name'         => 'required|min_length[2]',
            'last_name'          => 'required|min_length[2]',
            'role'               => 'required|in_list[admin,mechanic,receptionist,customer]',
            'password'           => 'required|min_length[8]',
            'phone_number'       => 'permit_empty',
            'email'              => 'permit_empty|valid_email',
            'date_of_employment' => 'permit_empty|valid_date',
            'dob'                => 'permit_empty|valid_date',
            'national_id'        => 'permit_empty',
            'gender'             => 'permit_empty|in_list[Male,Female,Other]',
            'address'            => 'permit_empty',
            'kin_first_name'     => 'permit_empty',
            'kin_last_name'      => 'permit_empty',
            'relationship'       => 'permit_empty',
            'kin_phone_number'   => 'permit_empty',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $profilePicture = null;
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'errors' => ['profile_picture' => 'Only JPG, PNG, and WebP images are allowed.'],
                ]);
            }
            $newName = $file->getRandomName();
            $file->move('uploads/users', $newName);
            $profilePicture = 'uploads/users/' . $newName;
        }

        $password = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        $userData = [
            'company_id'         => $this->request->getPost('company_id'),
            'first_name'         => $this->request->getPost('first_name'),
            'last_name'          => $this->request->getPost('last_name'),
            'email'              => $this->request->getPost('email'),
            'phone_number'       => $this->request->getPost('phone_number'),
            'password'           => $password,
            'role'               => $this->request->getPost('role'),
            'date_of_employment' => $this->request->getPost('date_of_employment'),
            'dob'                => $this->request->getPost('dob'),
            'national_id'        => $this->request->getPost('national_id'),
            'gender'             => $this->request->getPost('gender'),
            'address'            => $this->request->getPost('address'),
        ];

        if ($profilePicture) {
            $userData['profile_picture'] = $profilePicture;
        }

        $userData = array_filter($userData, function ($v) {
            return $v !== null && $v !== '';
        });

        $db = \Config\Database::connect();
        $db->table('users')->insert($userData);
        $userId = $db->insertID();

        $kinFirstName = $this->request->getPost('kin_first_name');
        $kinLastName = $this->request->getPost('kin_last_name');
        $relationship = $this->request->getPost('relationship');
        $kinPhone = $this->request->getPost('kin_phone_number');

        if ($kinFirstName || $kinLastName || $relationship || $kinPhone) {
            $db->table('next_of_kin')->insert([
                'user_id'          => $userId,
                'kin_first_name'   => $kinFirstName ?? '',
                'kin_last_name'    => $kinLastName ?? '',
                'relationship'     => $relationship ?? '',
                'kin_phone_number' => $kinPhone ?? '',
            ]);
        }

        log_activity('user_created', 'user', $userId, "User {$userData['company_id']} ({$userData['role']}) created via admin modal");

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'User created successfully',
            'user_id' => $userId,
        ]);
    }

    public function success()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'receptionist'])) {
            return redirect()->to('/unauthorized');
        }
        return view('user/success');
    }

    public function failure()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'receptionist'])) {
            return redirect()->to('/unauthorized');
        }
        return view('user/failure');
    }


    public function fetchUsers()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // DataTable request params
        $draw    = (int) $this->request->getVar('draw');
        $start   = (int) $this->request->getVar('start');
        $length  = (int) $this->request->getVar('length');
        $search  = $this->request->getVar('search')['value'] ?? '';
        $order   = $this->request->getVar('order');
        $columns = $this->request->getVar('columns');
        $roleFilter = $this->request->getVar('role_filter');
        $showDeleted = $this->request->getVar('show_deleted');

        // Base: exclude soft-deleted by default
        if ($showDeleted === '1') {
            $builder->where('users.deleted_at IS NOT NULL');
        } else {
            $builder->where('users.deleted_at', null);
        }

        // Total unfiltered count (deleted_at filter only)
        $totalRecords = $builder->countAllResults(false);

        // Role filter
        if (!empty($roleFilter)) {
            $builder->where('users.role', $roleFilter);
        }

        // Search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.phone_number', $search)
                ->orLike('users.company_id', $search)
                ->orLike('users.role', $search)
                ->groupEnd();
        }

        // Filtered count (includes role filter + search)
        $filteredRecords = $builder->countAllResults(false);

        // Sorting
        $sortCol = 'users.id';
        $sortDir = 'DESC';
        if ($order && isset($columns[$order[0]['column']])) {
            $colName = $columns[$order[0]['column']]['data'] ?? '';
            $colMap = [
                'id'    => 'users.id',
                'name'  => 'users.first_name',
                'phone' => 'users.phone_number',
                'role'  => 'users.role',
            ];
            if (isset($colMap[$colName])) {
                $sortCol = $colMap[$colName];
                $sortDir = strtoupper($order[0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            }
        }
        $builder->orderBy($sortCol, $sortDir);

        // Pagination
        $data = $builder->limit($length, $start)->get()->getResultArray();

        // Format response
        $users = [];
        foreach ($data as $user) {
            $users[] = [
                'id'              => (int) $user['id'],
                'name'            => ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
                'phone'           => $user['phone_number'] ?? '',
                'role'            => $user['role'] ?? '',
                'company_id'      => $user['company_id'] ?? '',
                'profile_picture' => $user['profile_picture'] ?? '',
                'deleted_at'      => $user['deleted_at'] ?? null,
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $users,
        ]);
    }
}
