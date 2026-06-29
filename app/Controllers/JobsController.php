<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class JobsController extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('job_cards')
            ->select('id, job_no, vehicle_id, diagnosis, job_status');

        $search = $this->request->getVar('search');
        if (!empty($search)) {
            $builder->like('job_no', $search)
                ->orLike('vehicle_id', $search);
        }

        $service_advisors = $db->table('users')
            ->where('role', 'mechanic')
            ->select('id, company_id, first_name, last_name')
            ->orderBy('first_name', 'ASC')
            ->get()
            ->getResultArray();


        $perPage = 10;
        $currentPage = $this->request->getVar('page') ?? 1;

        $total = $builder->countAllResults(false);
        $jobs = $builder->limit($perPage, ($currentPage - 1) * $perPage)->get()->getResultArray();
        $pager = \Config\Services::pager();

        if ($this->request->isAJAX()) {
            return view('admin/jobs/jobs_list', ['jobs' => $jobs, 'pager' => $pager,]);
        }

        return view('job/index', ['jobs' => $jobs, 'pager' => $pager, 'service_advisors' => $service_advisors]);
    }

    public function fetchJobs()
    {
        // Check for 'isLoggedIn' if this endpoint requires authentication
        if (!session()->get('isLoggedIn')) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // Join job_cards with vehicles table to get the registration_number
        $builder = $this->db->table('job_cards')
            ->select('job_cards.id, job_cards.job_no, job_cards.diagnosis, job_cards.job_status, job_cards.date_in, job_cards.start_date, job_cards.end_date, job_cards.created_at, job_cards.updated_at, vehicles.registration_number')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id');


        $query = $builder->get();
        $result = $query->getResultArray();

        $jobs = [];
        foreach ($result as $row) {
            $jobs[] = [
                'id' => $row['id'],
                'job_no' => $row['job_no'],
                'registration_number' => $row['registration_number'], 
                'diagnosis' => $row['diagnosis'],
                'job_status' => $row['job_status'],
                'date_in' => $row['date_in'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ];
        }

        return $this->response->setJSON(['data' => $jobs]);
    }

    public function add()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        $db = \Config\Database::connect();
        $service_advisors = $db->table('users')
            ->where('role', 'mechanic')
            ->select('id, company_id, first_name, last_name')
            ->orderBy('first_name', 'ASC')
            ->get()
            ->getResultArray();
        return view('jobs/add', ['service_advisors' => $service_advisors]);
    }

    // public function store()
    // {
    //     $rules = [
    //         'job_name' => 'required|min_length[3]',
    //         'description' => 'permit_empty|max_length[255]',
    //         'status' => 'required|in_list[pending,completed,cancelled]', // Example statuses
    //         'assigned_to' => 'permit_empty|integer', // This is a user ID
    //         'created_at' => 'required|valid_date',
    //         'updated_at' => 'permit_empty|valid_date',
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('errors', $this->validator->getErrors());
    //     }

    //     $data = $this->request->getPost();

    //     try {
    //         $this->db->table('jobs')->insert($data);
    //         return redirect()->to('/admin/jobs')->with('success', 'Job added successfully!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    //     }
    // }

    public function edit($id)
    {
        $job = $this->db->table('job_cards')->where('id', $id)->get()->getRowArray();

        if (!$job) {
            return redirect()->to('/admin/jobs')->with('error', 'Job not found.');
        }

        return view('jobs/edit', ['job' => $job]);
    }

    public function update($id)
    {
        $rules = [
            'job_name' => 'required|min_length[3]',
            // Add other validation rules here
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        try {
            $this->db->table('job_cards')->update($data, ['id' => $id]);
            return redirect()->to('/admin/jobs')->with('success', 'Job updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            // Soft delete
            $this->db->table('job_cards')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

            return redirect()->to('/admin/jobs')->with('success', 'Job deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->to('/admin/jobs')->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }
}
