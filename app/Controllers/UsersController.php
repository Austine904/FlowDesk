<?php

namespace App\Controllers;

use Config\Services;

use CodeIgniter\Controller;
use CodeIgniter\Database\Query;


class UsersController extends BaseController
{
    protected $session; // Define the $session property
    // Display all users
    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $role = $this->request->getVar('role');

        $search = $this->request->getVar('search');

        if (!empty($search)) {
            $userModel->like('name', $search)
                ->orLike('phone', $search)
                ->orLike('role', $search)
                ->orLike('company_id', $search);
        }

        $data['users'] = $userModel->paginate(10);
        $data['pager'] = $userModel->pager;
        $total = $data['pager']->getTotal();

        if ($this->request->isAJAX()) {
            return view('admin/users/user_list', $data + ['total' => $total]);
        }

        return view('admin/users', $data + ['total' => $total]);
    }

    // Show the add user form
    public function add()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        return view('admin/add_user');
    }

    public function __construct()
    {
        $this->session = Services::session();
    }

    // Handle editing a user (Form)
    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $user = $builder->where('id', $id)->get()->getRowArray();

        return view('admin/edit_user', ['user' => $user]);
    }

    // Handle updating a user
    public function update($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $role = $this->request->getPost('role');

        $data = [
            'name' => $name,
            'phone' => $phone,
            'role' => $role
        ];

        if ($this->request->getPost('password')) {
            $password = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            $data['password'] = $password;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('id', $id)->update($data);

        return redirect()->to('/admin/users')->with('success', 'User updated successfully.');
    }

    // Handle deleting a user
    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('id', $id)->delete();

        return redirect()->to('/admin/users')->with('success', 'User deleted successfully.');
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


    public function deleteMultiple()
    {
        $userIds = $this->request->getPost('user_ids');

        if (!is_array($userIds) || empty($userIds)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No users selected.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $builder->whereIn('id', $userIds)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Users deleted successfully.']);
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
            'first_name' => '',
            'last_name' => '',
            'relationship' => '',
            'phone_number' => ''
        ];

        return $this->response->setJSON($result);
    }

    // Handle adding a new user
    public function addStep1()
    {
        return view('user/add_step1');
    }

    public function add_step1()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'profile_picture'     => 'uploaded[profile_picture]|max_size[profile_picture,2048]|is_image[profile_picture]',
            'role'               => 'required',
            'company_id'         => 'required',
            'date_of_employment' => 'required'
        ];

        // if (in_array($this->request->getPost('role'), ['admin', 'receptionist'])) {
        //     $rules['password'] = 'required|min_length[6]';
        // }

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $profileImage = $this->request->getFile('profile_picture');
        $imagePath = null; // Initialize the path as null

        if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
            $newName = $profileImage->getRandomName();
            $profileImage->move('uploads/users', $newName);
            $imagePath = 'uploads/users/' . $newName;
        }

        // Store in session
        session()->set('step1_data', [
            'profile_picture'     => $imagePath,
            'role'               => $this->request->getPost('role'),
            'company_id'         => $this->request->getPost('company_id'),
            'date_of_employment' => $this->request->getPost('date_of_employment'),


            // 'password'           => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Step 1 completed successfully!'
        ]);

        return redirect()->to(base_url('user/add_step2'));
    }



    public function addStep2()
    {
        return view('user/add_step2');
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
        return view('user/add_step3');
    }
    public function addUserStep3()
    {
        // Validate incoming data
        $validation = \Config\Services::validation();
        $validation->setRules([
            'kin_first_name'     => 'required',
            'kin_last_name'      => 'required',
            'relationship'       => 'required',
            'kin_phone_number'   => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // If validation fails, redirect back with errors
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Store Step 3 data in the session
        session()->set('step3_data', [
            'kin_first_name'   => $this->request->getPost('kin_first_name'),
            'kin_last_name'    => $this->request->getPost('kin_last_name'),
            'relationship'     => $this->request->getPost('relationship'),
            'kin_phone_number' => $this->request->getPost('kin_phone_number')
        ]);


        // For example, you can return a success message
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Step 3 completed successfully!'
        ]);

        // Load the view for preview and return it as the AJAX response
        return redirect()->to(base_url('user/preview'));
    }

    public function getLastId()
    {
        $role = $this->request->getVar('role');

        // Define prefixes for each role
        $rolePrefixes = [
            'admin' => 'ADM',
            'mechanic' => 'MECH',
            'receptionist' => 'RP'
        ];

        // Get the appropriate prefix based on the role
        $prefix = $rolePrefixes[$role] ?? '';

        if ($prefix === '') {
            return $this->response->setJSON(['result' => 0]);
        }

        // Connect to the database and fetch the last company_id for this role
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->selectMax('company_id');
        $builder->like('company_id', $prefix, 'after');
        $query = $builder->get();
        $result = $query->getRow();

        // Extract the last 3 digits and increment
        $lastId = 0;
        if ($result && !empty($result->company_id)) {
            $lastId = (int)substr($result->company_id, -3);
        }

        return $this->response->setJSON(['result' => $lastId]);
    }

    public function preview()
    {
        return view('user/preview');
    }

    public function saveUser()
    {
        // Retrieve all the session data
        $step1Data = session('step1_data');
        $step2Data = session('step2_data');
        $step3Data = session('step3_data');

        // Prepare the data for database insertion
        $userData = array_merge($step1Data, $step2Data);

        // Insert into the database (you can use Query Builder here)
        $db = \Config\Database::connect();
        $db->table('users')->insert($userData);
        $db->table('next_of_kin')->insert([
            'user_id'       => $db->insertID(),
            'kin_first_name'    => $step3Data['kin_first_name'],
            'kin_last_name'     => $step3Data['kin_last_name'],
            'relationship'  => $step3Data['relationship'],
            'kin_phone_number'  => $step3Data['kin_phone_number']
        ]);
        // $db->table('next_of_kin')->insert($userData);

        // Clear the session data
        session()->remove('step1_data');
        session()->remove('step2_data');
        session()->remove('step3_data');

        // Redirect to a success page
        return redirect()->to(base_url('user/success'))->with('message', 'User added successfully!');
    }
    public function success()
    {
        return view('user/success');
    }

    public function failure()
    {
        return view('user/failure');
    }


    public function fetchUsers()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $builder->where('deleted_at', null);

        $query = $builder->get();
        $result = $query->getResultArray();

        $users = [];
        foreach ($result as $user) {
            $users[] = [
                'id' => $user['id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'phone' => $user['phone_number'],
                'role' => $user['role'],
                'company_id' => $user['company_id'],
            ];
        }

        return $this->response->setJSON(['data' => $users]);
    }
}
