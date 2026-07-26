<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JobCardModel;
use App\Models\UserModel;
use App\Models\JobStatusHistoryModel;
use App\Models\InvoiceModel;
use App\Models\LpoModel;
use Config\JobStatus;

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
        $mechanics = $userModel->getMechanicsWithAvailability();

        $jobs = $jobCardModel->paginate(10);
        $pager = $jobCardModel->pager;

        if ($this->request->isAJAX()) {
            return view('admin/jobs/jobs_list', ['jobs' => $jobs, 'pager' => $pager]);
        }

        // echo'<pre>';
        // print_r("Here is your problem");
        // exit;

        return view('job/index', ['jobs' => $jobs, 'pager' => $pager, 'service_advisors' => $service_advisors, 'mechanics' => $mechanics]);
    }

    public function fetchJobs()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $request = \Config\Services::request();
        $draw = (int) $request->getGet('draw');
        $start = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');
        $searchValue = $request->getGet('search')['value'] ?? '';
        $orderColumn = (int) ($request->getGet('order')[0]['column'] ?? 1);
        $orderDir = $request->getGet('order')[0]['dir'] ?? 'desc';
        $statusFilter = $request->getGet('status');
        $dateFrom = $request->getGet('date_from');
        $dateTo = $request->getGet('date_to');

        $columnMap = [
            0 => null, // checkbox
            1 => 'job_cards.job_no',
            2 => 'customers.name',
            3 => 'vehicles.registration_number',
            4 => null, // mechanic_name (virtual)
            5 => 'job_cards.date_in',
            6 => 'job_cards.diagnosis',
            7 => 'job_cards.job_status',
            8 => null, // progress
            9 => null, // actions
        ];

        $db = \Config\Database::connect();
        $builder = $db->table('job_cards')
            ->select('
                job_cards.id,
                job_cards.job_no,
                job_cards.diagnosis,
                job_cards.job_status,
                job_cards.date_in,
                job_cards.start_date,
                job_cards.end_date,
                job_cards.created_at,
                job_cards.updated_at,
                job_cards.fuel_level,
                job_cards.mileage_in,
                job_cards.assigned_mechanic_id,
                vehicles.registration_number,
                customers.name as customer_name,
                customers.phone as customer_phone,
                assigned_mech.name as mechanic_name
            ')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->join('customers', 'customers.id = job_cards.customer_id', 'left')
            ->join('users as assigned_mech', 'assigned_mech.id = job_cards.assigned_mechanic_id', 'left');

        // Total records before filtering
        $recordsTotal = $builder->countAllResults(false);

        // Apply status filter
        if (!empty($statusFilter)) {
            $builder->where('job_cards.job_status', $statusFilter);
        }

        // Apply date range filter
        if (!empty($dateFrom)) {
            $builder->where('job_cards.date_in >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('job_cards.date_in <=', $dateTo);
        }

        // Apply search filter
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('job_cards.job_no', $searchValue)
                ->orLike('customers.name', $searchValue)
                ->orLike('customers.phone', $searchValue)
                ->orLike('vehicles.registration_number', $searchValue)
                ->orLike('job_cards.diagnosis', $searchValue)
                ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        // Apply ordering
        if (isset($columnMap[$orderColumn])) {
            $builder->orderBy($columnMap[$orderColumn], $orderDir);
        } else {
            $builder->orderBy('job_cards.date_in', 'DESC');
        }

        // Apply pagination
        if ($length > 0) {
            $builder->limit($length, $start);
        }

        $result = $builder->get()->getResultArray();

        $progressMap = [
            'Awaiting Assignment' => 0,
            'Awaiting Diagnosis' => 5,
            'Diagnosis Complete' => 10,
            'Quote Sent' => 15,
            'Approved' => 20,
            'In Progress' => 40,
            'Awaiting Parts' => 45,
            'Quality Check' => 70,
            'Ready for Invoice' => 85,
            'On Hold' => -1,
            'Rework' => -1,
            'Paid' => 95,
            'Completed' => 100,
            'Cancelled' => -1,
        ];

        $jobs = [];
        foreach ($result as $row) {
            $jobs[] = [
                'id' => $row['id'],
                'job_no' => $row['job_no'],
                'customer_name' => $row['customer_name'],
                'customer_phone' => $row['customer_phone'],
                'registration_number' => $row['registration_number'],
                'diagnosis' => $row['diagnosis'],
                'job_status' => $row['job_status'],
                'date_in' => $row['date_in'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'progress' => $progressMap[$row['job_status']] ?? 0,
                'mechanic_name' => $row['mechanic_name'] ?? null,
                'assigned_mechanic_id' => $row['assigned_mechanic_id'] ?? null,
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $jobs,
        ]);
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

    public function view($id)
    {
        return redirect()->to('/admin/jobs');
    }

    public function update($id)
    {
        $jobCardModel = new JobCardModel();

        $job = $jobCardModel->find($id);
        if (!$job) {
            return redirect()->to('/admin/jobs')->with('error', 'Job not found.');
        }

        $rules = [
            'reported_problem'             => 'required|min_length[10]',
            'initial_damage_notes'         => 'permit_empty|max_length[500]',
            'mileage_in'                   => 'required|integer|greater_than_equal_to[0]',
            'fuel_level'                   => 'required|in_list[Empty,1/4,1/2,3/4,Full]',
            'job_status'                   => 'required|in_list[Awaiting Assignment,Awaiting Diagnosis,In Progress,Awaiting Parts,Completed,Cancelled]',
            'assigned_service_advisor_id'  => 'required|integer',
            'assigned_mechanic_id'         => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $assigned_mechanic_id = $this->request->getPost('assigned_mechanic_id');
        $assigned_mechanic_id = !empty($assigned_mechanic_id) ? (int) $assigned_mechanic_id : null;

        $data = [
            'diagnosis'                    => $this->request->getPost('reported_problem'),
            'initial_damage_notes'         => $this->request->getPost('initial_damage_notes'),
            'mileage_in'                   => $this->request->getPost('mileage_in'),
            'fuel_level'                   => $this->request->getPost('fuel_level'),
            'job_status'                   => $this->request->getPost('job_status'),
            'assigned_service_advisor_id'  => (int) $this->request->getPost('assigned_service_advisor_id'),
            'assigned_mechanic_id'         => $assigned_mechanic_id,
        ];

        try {
            $jobCardModel->update($id, $data);
            return redirect()->to(base_url('admin/jobs/view/' . $id))->with('success', 'Job updated successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Job update failed for ID ' . $id . ': ' . $e->getMessage());
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
        $mechanics = $userModel->getMechanicsWithAvailability();

        $invoiceModel = new InvoiceModel();
        $invoice = $invoiceModel->where('job_card_id', $id)->first();

        $config = new JobStatus();
        $role = session()->get('role');
        $validTransitions = $config->getValidTransitions($job['job_status'], $role);

        $lpoModel = new LpoModel();
        $lpos = $lpoModel->builder()
            ->select('lpos.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = lpos.supplier_id', 'LEFT')
            ->where('lpos.job_card_id', $id)
            ->get()
            ->getResultArray();

        return $this->respond([
            'id' => $job['id'],
            'job_no' => $job['job_no'],
            'job_status' => $job['job_status'],
            'invoice' => $invoice ? [
                'invoice_id'    => $invoice['id'],
                'invoice_no'    => $invoice['invoice_no'],
                'grand_total'   => $invoice['grand_total'],
                'balance_due'   => $invoice['balance_due'],
                'status'        => $invoice['status'],
            ] : null,
            'valid_transitions' => $validTransitions,
            'current_role' => $role,
            'diagnosis' => $job['diagnosis'],
            'diagnosis_category' => $job['diagnosis_category'] ?? null,
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
            'lpos' => $lpos,
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

        // Hard block: if already assigned to a different mechanic, require explicit unassign first
        if (!empty($job['assigned_mechanic_id']) && (int) $job['assigned_mechanic_id'] !== (int) $mechanic_id) {
            $currentMechModel = new UserModel();
            $currentMech = $currentMechModel->find($job['assigned_mechanic_id']);
            $currentMechName = $currentMech ? ($currentMech['first_name'] . ' ' . $currentMech['last_name']) : 'Unknown';
            return $this->respond([
                'status' => 'error',
                'message' => "This job is already assigned to {$currentMechName}. Unassign them first before reassigning.",
                'code' => 'assigned_to_another'
            ], 409);
        }

        $updateData = ['assigned_mechanic_id' => (int)$mechanic_id];

        // If job is Awaiting Assignment, move to Awaiting Diagnosis
        if ($job['job_status'] === 'Awaiting Assignment') {
            $updateData['job_status'] = 'Awaiting Diagnosis';
        }

        $jobCardModel->update($id, $updateData);

        // Log status transition in history
        if (isset($updateData['job_status'])) {
            $historyModel = new JobStatusHistoryModel();
            $historyModel->insert([
                'job_card_id' => $id,
                'from_status' => 'Awaiting Assignment',
                'to_status'   => 'Awaiting Diagnosis',
                'changed_by'  => session()->get('user_id'),
                'notes'       => 'Mechanic assigned: ' . $mechanic_id,
            ]);
        }

        // Notify mechanic on new assignment (skip if reassigned to same person)
        if ((int) $mechanic_id !== (int) ($job['assigned_mechanic_id'] ?? 0)) {
            $notificationModel = new \App\Models\NotificationModel();
            $notificationModel->notify(
                (int) $mechanic_id,
                'job_assigned',
                'New Job Assigned',
                "You've been assigned to job {$job['job_no']}",
                'job_card',
                (int) $id
            );
        }

        return $this->respond(['status' => 'success', 'message' => 'Mechanic assigned successfully']);
    }

    public function unassign_mechanic($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($id);

        if (!$job) {
            return $this->respond(['status' => 'error', 'message' => 'Job not found'], 404);
        }

        if (empty($job['assigned_mechanic_id'])) {
            return $this->respond(['status' => 'error', 'message' => 'No mechanic is currently assigned to this job.'], 400);
        }

        $oldMechanicId = (int) $job['assigned_mechanic_id'];
        $jobCardModel->update($id, ['assigned_mechanic_id' => null]);

        // If job was in a flowing status, move back to Awaiting Assignment
        $autoUnassignStatuses = ['Awaiting Diagnosis'];
        if (in_array($job['job_status'], $autoUnassignStatuses)) {
            $jobCardModel->update($id, ['job_status' => 'Awaiting Assignment']);
            $historyModel = new JobStatusHistoryModel();
            $historyModel->insert([
                'job_card_id' => $id,
                'from_status' => $job['job_status'],
                'to_status'   => 'Awaiting Assignment',
                'changed_by'  => session()->get('user_id'),
                'notes'       => 'Mechanic unassigned',
            ]);
        }

        $notificationModel = new \App\Models\NotificationModel();
        $notificationModel->notify(
            $oldMechanicId,
            'job_unassigned',
            'Job Unassigned',
            "You have been unassigned from job {$job['job_no']}",
            'job_card',
            (int) $id
        );

        log_activity('mechanic_unassigned', 'job_card', (int) $id, "Mechanic unassigned from job {$job['job_no']}");

        return $this->respond(['status' => 'success', 'message' => 'Mechanic unassigned successfully']);
    }

    public function delete($id)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $user_role = $this->session->get('role');
        if ($user_role !== 'admin' && $user_role !== 'receptionist') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Forbidden: Insufficient permissions.'])->setStatusCode(403);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($id);

        if (!$job) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Job not found.'])->setStatusCode(404);
        }

        try {
            $jobCardModel->delete($id);

            log_activity('job_deleted', 'job_card', $id, "Job card {$job['job_no']} deleted");

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Job card {$job['job_no']} deleted successfully."
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Job delete failed for ID ' . $id . ': ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()])->setStatusCode(500);
        }
    }

    public function update_status($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $role = session()->get('role');
        $userId = session()->get('user_id');
        $newStatus = $this->request->getPost('new_status');
        $notes = $this->request->getPost('notes');

        if (empty($newStatus)) {
            return $this->respond(['status' => 'error', 'message' => 'New status is required'], 400);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($id);

        if (!$job) {
            return $this->respond(['status' => 'error', 'message' => 'Job not found'], 404);
        }

        $currentStatus = $job['job_status'];

        $config = new JobStatus();
        $validTransitions = $config->getValidTransitions($currentStatus, $role);

        if (!in_array($newStatus, $validTransitions)) {
            return $this->respond(['status' => 'error', 'message' => 'This status transition is not allowed for your role'], 403);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $updateData = ['job_status' => $newStatus];

            // Set completed_at only on FIRST transition to Completed
            if ($newStatus === 'Completed' && $job['completed_at'] === null) {
                $updateData['completed_at'] = date('Y-m-d H:i:s');
            }

            $jobCardModel->update($id, $updateData);

            $historyModel = new JobStatusHistoryModel();
            $historyModel->insert([
                'job_card_id' => $id,
                'from_status' => $currentStatus,
                'to_status'   => $newStatus,
                'changed_by'  => $userId,
                'notes'       => $notes,
            ]);

            // Auto-generate invoice when job reaches Ready for Invoice
            if ($newStatus === 'Ready for Invoice') {
                $discount = (float) ($this->request->getPost('discount') ?? 0);
                $otherCharges = (float) ($this->request->getPost('other_charges') ?? 0);
                $otherChargesDesc = $this->request->getPost('other_charges_description') ?? '';
                $invoiceModel = new InvoiceModel();
                $invoiceModel->generateFromJobCard($id, $userId, $discount, $otherCharges, $otherChargesDesc);
            }

            $db->transCommit();

            log_activity('status_change', 'job_card', $id, "Status changed from {$currentStatus} to {$newStatus}");

            // Notify the assigned mechanic if someone else changed the status
            if (!empty($job['assigned_mechanic_id']) && (int) $job['assigned_mechanic_id'] !== (int) $userId) {
                $notificationModel = new \App\Models\NotificationModel();
                $notificationModel->notify(
                    (int) $job['assigned_mechanic_id'],
                    'status_changed',
                    'Job Status Updated',
                    "Job {$job['job_no']} status changed to {$newStatus}",
                    'job_card',
                    (int) $id
                );
            }

            $config = new JobStatus();
            $nextTransitions = $config->getValidTransitions($newStatus, $role);

            return $this->respond([
                'status' => 'success',
                'message' => 'Status updated successfully',
                'new_status' => $newStatus,
                'valid_transitions' => $nextTransitions,
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->respond(['status' => 'error', 'message' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }

    public function status_history($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $historyModel = new JobStatusHistoryModel();
        $history = $historyModel->getByJobCard($id);

        return $this->respond(['data' => $history]);
    }
}
