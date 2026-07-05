<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JobCardModel;
use App\Models\UserModel;

class JobsController extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;

    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $jobCardModel = new JobCardModel();
        $userModel = new UserModel();

        $jobCardModel->select('id, job_no, vehicle_id, diagnosis, job_status');

        $search = $this->request->getVar('search');
        if (!empty($search)) {
            $jobCardModel->like('job_no', $search)
                ->orLike('vehicle_id', $search);
        }

        $service_advisors = $userModel->whereIn('role', ['admin', 'receptionist'])->findAll();
        $mechanics = $userModel->getByRole('mechanic');

        $jobs = $jobCardModel->paginate(10);
        $pager = $jobCardModel->pager;

        if ($this->request->isAJAX()) {
            return view('admin/jobs/jobs_list', ['jobs' => $jobs, 'pager' => $pager]);
        }

        return view('job/index', ['jobs' => $jobs, 'pager' => $pager, 'service_advisors' => $service_advisors, 'mechanics' => $mechanics]);
    }

    public function fetchJobs()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $jobCardModel = new JobCardModel();
        $result = $jobCardModel->select('job_cards.id, job_cards.job_no, job_cards.diagnosis, job_cards.job_status, job_cards.date_in, job_cards.start_date, job_cards.end_date, job_cards.created_at, job_cards.updated_at, vehicles.registration_number')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id')
            ->findAll();

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
        $userModel = new UserModel();
        $service_advisors = $userModel->getByRole('mechanic');
        return view('jobs/add', ['service_advisors' => $service_advisors]);
    }

    public function edit($id)
    {
        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($id);

        if (!$job) {
            return redirect()->to('/admin/jobs')->with('error', 'Job not found.');
        }

        return view('jobs/edit', ['job' => $job]);
    }

    public function update($id)
    {
        $rules = [
            'job_name' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        try {
            $jobCardModel = new JobCardModel();
            $jobCardModel->update($id, $data);
            return redirect()->to('/admin/jobs')->with('success', 'Job updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function details($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->getWithDetails($id);

        if (!$job) {
            return $this->respond(['status' => 'error', 'message' => 'Job not found'], 404);
        }

        $customerModel = new \App\Models\CustomerModel();
        $vehicleModel = new \App\Models\VehicleModel();
        $jobCardPartModel = new \App\Models\JobCardPartModel();
        $jobCardLaborModel = new \App\Models\JobCardLaborModel();
        $jobCardPhotoModel = new \App\Models\JobCardPhotoModel();

        $customer = $customerModel->find($job['customer_id']);
        $vehicle = $vehicleModel->find($job['vehicle_id']);
        $parts = $jobCardPartModel->getByJobCard($id);
        $tasks = $jobCardLaborModel->getByJobCard($id);
        $photos = $jobCardPhotoModel->where('job_card_id', $id)->findAll();

        $userModel = new UserModel();
        $mechanics = $userModel->getByRole('mechanic');

        return $this->respond([
            'id' => $job['id'],
            'job_no' => $job['job_no'],
            'job_status' => $job['job_status'],
            'diagnosis' => $job['diagnosis'],
            'initial_damage_notes' => $job['initial_damage_notes'],
            'mileage_in' => $job['mileage_in'],
            'fuel_level' => $job['fuel_level'],
            'date_in' => $job['date_in'],
            'time_in' => $job['time_in'],
            'estimated_labor_hours' => $job['estimated_labor_hours'],
            'quote_status' => $job['quote_status'],
            'quote_amount' => $job['quote_amount'],
            'assigned_service_advisor' => $job['advisor_name'] ?? 'N/A',
            'assigned_mechanic_id' => $job['assigned_mechanic_id'],
            'mechanic_name' => $job['mechanic_name'] ?? null,
            'job_summary' => $job['job_summary'],
            'customer' => $customer ?: [],
            'vehicle' => $vehicle ?: [],
            'parts' => $parts,
            'tasks' => $tasks,
            'photos' => $photos,
            'mechanics' => $mechanics,
        ]);
    }

    public function assign_mechanic($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $mechanic_id = $this->request->getPost('mechanic_id');
        if (empty($mechanic_id)) {
            return $this->respond(['status' => 'error', 'message' => 'Mechanic ID is required'], 400);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($id);

        if (!$job) {
            return $this->respond(['status' => 'error', 'message' => 'Job not found'], 404);
        }

        $updateData = ['assigned_mechanic_id' => (int)$mechanic_id];

        // If job is Awaiting Assignment, move to Awaiting Diagnosis
        if ($job['job_status'] === 'Awaiting Assignment') {
            $updateData['job_status'] = 'Awaiting Diagnosis';
        }

        $jobCardModel->update($id, $updateData);

        return $this->respond(['status' => 'success', 'message' => 'Mechanic assigned successfully']);
    }

    public function delete($id)
    {
        try {
            $jobCardModel = new JobCardModel();
            $jobCardModel->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

            return redirect()->to('/admin/jobs')->with('success', 'Job deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->to('/admin/jobs')->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }
}
