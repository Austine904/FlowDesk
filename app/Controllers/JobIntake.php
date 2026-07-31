<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\VehicleModel;
use App\Models\JobCardModel;
use App\Models\JobCardPhotoModel;
use App\Models\JobCardPartModel;
use App\Models\JobCardLaborModel;
use App\Models\InventoryModel;
use App\Models\UserModel;
use App\Models\JobStatusHistoryModel;
use App\Models\JobTimeLogModel;
use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class JobIntake extends BaseController
{
    use ResponseTrait;

    protected $db;
    protected $session;
    protected $validation;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $userModel = new \App\Models\UserModel();
        $serviceAdvisors = $userModel->whereIn('role', ['admin', 'receptionist'])->findAll();
        $mechanics = $userModel->getMechanicsWithAvailability();
        return view('job_intake_form', [
            'service_advisors' => $serviceAdvisors,
            'mechanics' => $mechanics,
        ]);
    }

    public function search()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $query = $this->request->getVar('query', FILTER_SANITIZE_SPECIAL_CHARS);

        $results = [
            'customers' => [],
            'vehicles' => []
        ];

        $sanitizedQuery = (string)$query;

        if (!empty($sanitizedQuery)) {
            $customerModel = new CustomerModel();
            $customers = $customerModel->searchByPhoneOrName($sanitizedQuery);

            foreach ($customers as &$customer) {
                $customer['name'] = $customer['name'] ?? '';
                $customer['phone'] = $customer['phone'] ?? '';
                $customer['email'] = $customer['email'] ?? '';
                $customer['address'] = $customer['address'] ?? '';
            }
            $results['customers'] = $customers;

            $vehicleModel = new VehicleModel();
            $vehicles = $vehicleModel->searchByTerm($sanitizedQuery);

            foreach ($vehicles as &$vehicle) {
                $owner = $customerModel->find($vehicle['owner_id']);

                $processedOwner = [
                    'id'      => $owner['id'] ?? null,
                    'name'    => $owner['name'] ?? '',
                    'phone'   => $owner['phone'] ?? '',
                    'email'   => $owner['email'] ?? '',
                    'address' => $owner['address'] ?? '',
                ];

                $vehicle['owner_name'] = $processedOwner['name'];
                $vehicle['owner'] = $processedOwner;

                $vehicle['registration_number'] = $vehicle['registration_number'] ?? '';
                $vehicle['vin'] = $vehicle['vin'] ?? '';
                $vehicle['make'] = $vehicle['make'] ?? '';
                $vehicle['model'] = $vehicle['model'] ?? '';
                $vehicle['color'] = $vehicle['color'] ?? '';
                $vehicle['reported_problem'] = $vehicle['reported_problem'] ?? '';
                $vehicle['engine_number'] = $vehicle['engine_number'] ?? '';
                $vehicle['chassis_number'] = $vehicle['chassis_number'] ?? '';
                $vehicle['fuel_type'] = $vehicle['fuel_type'] ?? '';
                $vehicle['transmission'] = $vehicle['transmission'] ?? '';
            }
            $results['vehicles'] = $vehicles;
        }

        return $this->respond($results);
    }

    public function create_job_card()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $user_role = $this->session->get('role');
        if ($user_role !== 'admin' && $user_role !== 'receptionist') {
            return $this->respond(['status' => 'error', 'message' => 'Forbidden: Insufficient permissions.'], 403);
        }

        $rules = [
            'reported_problem' => 'required|min_length[10]',
            'mileage_in' => 'required|integer|greater_than_equal_to[0]',
            'fuel_level' => 'required|in_list[Empty,1/4,1/2,3/4,Full]',
            'initial_damage_notes' => 'permit_empty|max_length[500]',
            'assigned_service_advisor_id' => 'required|integer',
        ];

        if ($this->request->getPost('customer_id') === 'new') {
            $rules = array_merge($rules, [
                'new_customer_first_name' => 'required|max_length[50]',
                'new_customer_last_name' => 'required|max_length[50]',
                'new_customer_phone_number' => 'required|max_length[15]|is_unique[customers.phone]',
                'new_customer_email' => 'permit_empty|valid_email|max_length[255]',
                'new_customer_address' => 'permit_empty',
            ]);
        } else {
            $rules['customer_id'] = 'required|integer';
        }

        if ($this->request->getPost('vehicle_id') === 'new') {
            $rules = array_merge($rules, [
                'new_vehicle_license_plate' => 'required|max_length[20]|is_unique[vehicles.registration_number]',
                'new_vehicle_vin' => 'required|exact_length[17]|is_unique[vehicles.vin]',
                'new_vehicle_make' => 'required|max_length[50]',
                'new_vehicle_model' => 'required|max_length[50]',
                'new_vehicle_year' => 'required|integer|exact_length[4]|greater_than_equal_to[1900]|less_than_equal_to[' . (date('Y') + 1) . ']',
                'new_vehicle_engine_number' => 'required|max_length[50]|is_unique[vehicles.engine_number]',
                'new_vehicle_chassis_number' => 'required|max_length[50]|is_unique[vehicles.chassis_number]',
                'new_vehicle_fuel_type' => 'required|in_list[Petrol,Diesel,Electric,Hybrid]',
                'new_vehicle_transmission' => 'required|in_list[Manual,Automatic,CVT]',
                'new_vehicle_color' => 'permit_empty|max_length[30]',
            ]);
        } else {
            $rules['vehicle_id'] = 'required|integer';
        }
        

        $this->validation->setRules($rules);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->fail(['message' => 'Validation failed', 'errors' => $this->validation->getErrors()], 400);
        }
        

        $this->db->transStart();
        

        try {
            $customerModel = new CustomerModel();
            $vehicleModel = new VehicleModel();
            $jobCardModel = new JobCardModel();
            $jobCardPhotoModel = new JobCardPhotoModel();

            $customer_id = $this->request->getPost('customer_id');
            $vehicle_id = $this->request->getPost('vehicle_id');

            if ($customer_id === 'new') {
                $customer_data = [
                    'name' => $this->request->getPost('new_customer_first_name') . ' ' . $this->request->getPost('new_customer_last_name'),
                    'phone' => $this->request->getPost('new_customer_phone_number'),
                    'email' => $this->request->getPost('new_customer_email'),
                    'address' => $this->request->getPost('new_customer_address')
                ];
                $customerModel->insert($customer_data);
                $customer_id = $customerModel->insertID();
                if (!$customer_id) {
                    throw new Exception('Failed to create new customer.');
                }
            } else {
                $customer_id = (int)$customer_id;
            }

            if ($vehicle_id === 'new') {
                $registration_number = $this->request->getPost('new_vehicle_license_plate');
                $vin = $this->request->getPost('new_vehicle_vin');

                $existingVehicle = $vehicleModel->groupStart()
                    ->where('registration_number', $registration_number)
                    ->orWhere('vin', $vin)
                    ->groupEnd()
                    ->first();

                if ($existingVehicle) {
                    throw new \Exception("A vehicle with the same registration number or VIN already exists.");
                }

                $vehicle_data = [
                    'owner_id' => $customer_id,
                    'registration_number' => $registration_number,
                    'vin' => $vin,
                    'make' => $this->request->getPost('new_vehicle_make'),
                    'model' => $this->request->getPost('new_vehicle_model'),
                    'year_of_manufacture' => $this->request->getPost('new_vehicle_year'),
                    'engine_number' => $this->request->getPost('new_vehicle_engine_number'),
                    'chassis_number' => $this->request->getPost('new_vehicle_chassis_number'),
                    'fuel_type' => $this->request->getPost('new_vehicle_fuel_type'),
                    'transmission' => $this->request->getPost('new_vehicle_transmission'),
                    'color' => $this->request->getPost('new_vehicle_color'),
                    'status' => 'On Job',
                    'mileage' => $this->request->getPost('mileage_in'),
                    'reported_problem' => $this->request->getPost('reported_problem'),
                ];

                $vehicleModel->insert($vehicle_data);
                $vehicle_id = $vehicleModel->insertID();

                if (!$vehicle_id) {
                    throw new \Exception('Failed to create new vehicle.');
                }
            } else {
                $vehicle_id = (int)$vehicle_id;

                $vehicleExists = $vehicleModel->find($vehicle_id);

                if (!$vehicleExists) {
                    throw new \Exception("Vehicle with ID $vehicle_id not found.");
                }

                $vehicleModel->update($vehicle_id, [
                    'mileage' => $this->request->getPost('mileage_in'),
                    'reported_problem' => $this->request->getPost('reported_problem')
                ]);
            }

            $assigned_mechanic_id = $this->request->getPost('assigned_mechanic_id');
            $assigned_mechanic_id = !empty($assigned_mechanic_id) ? (int)$assigned_mechanic_id : null;

            $job_no = $jobCardModel->generateJobNo();
            $job_card_data = [
                'job_no' => $job_no,
                'customer_id' => $customer_id,
                'vehicle_id' => $vehicle_id,
                'date_in' => date('Y-m-d'),
                'time_in' => date('H:i:s'),
                'diagnosis' => $this->request->getPost('reported_problem'),
                'initial_damage_notes' => $this->request->getPost('initial_damage_notes'),
                'assigned_service_advisor_id' => (int)$this->request->getPost('assigned_service_advisor_id'),
                'assigned_mechanic_id' => $assigned_mechanic_id,
                'job_status' => $assigned_mechanic_id ? 'Awaiting Diagnosis' : 'Awaiting Assignment',
                'mileage_in' => $this->request->getPost('mileage_in'),
                'fuel_level' => $this->request->getPost('fuel_level')
            ];

            $jobCardModel->insert($job_card_data);
            $job_card_id = $jobCardModel->insertID();

            if (!$job_card_id) {
                log_message('critical', 'DB Error on job card insert: ' . var_export($this->db->error(), true));
                throw new Exception('Failed to create job card. Database insert failed.');
            }

            $files = $this->request->getFiles();
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (isset($files['job_card_photos'])) {
                foreach ($files['job_card_photos'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $ext = strtolower($file->getExtension());
                        $mime = $file->getMimeType();
                        $finfo = new \finfo(FILEINFO_MIME_TYPE);
                        $actualMime = $finfo->file($file->getTempName());

                        if (!in_array($ext, $allowedExts) || !in_array($mime, $allowedMimes) || !in_array($actualMime, $allowedMimes)) {
                            log_message('warning', 'Photo upload rejected for job ID ' . $job_card_id . ': invalid file type (ext=' . $ext . ', mime=' . $mime . ', actual=' . $actualMime . ')');
                            continue;
                        }

                        $newName = bin2hex(random_bytes(16)) . '.' . $ext;
                        $uploadPath = ROOTPATH . 'uploads/job_card_photos/';
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }
                        $file->move($uploadPath, $newName);

                        $photo_data = [
                            'job_card_id' => $job_card_id,
                            'file_path' => 'uploads/job_card_photos/' . $newName,
                            'file_name' => $file->getClientName(),
                            'photo_type' => 'Intake',
                        ];
                        $jobCardPhotoModel->insert($photo_data);
                    } elseif ($file->getError() !== 4) {
                        log_message('error', 'Photo upload failed for job ID ' . $job_card_id . ': ' . $file->getErrorString());
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new Exception('Transaction failed, job card not fully created.');
            } else {
                $reg_number = ($vehicle_id !== 'new' && isset($vehicleExists)) ? ($vehicleExists['registration_number'] ?? '') : ($registration_number ?? '');
                log_activity('job_created', 'job_card', $job_card_id, "Job card {$job_no} created for vehicle registration {$reg_number}");
                return $this->respond(['status' => 'success', 'message' => 'Job Card created successfully!', 'job_id' => $job_card_id, 'job_no' => $job_no]);
            }
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->fail(['message' => $e->getMessage()], 500);
        }
    }

    public function mechanic_jobs()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Unauthorized');
        }

        $mechanic_id = $this->session->get('user_id');
        $jobCardModel = new JobCardModel();
        $data['jobs'] = $jobCardModel->getAssignedToMechanic($mechanic_id);
        $data['name'] = $this->session->get('user_name');

        return view('technician/jobs', $data);
    }

    public function mechanic_view($job_id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Unauthorized');
        }

        $jobCardModel = new JobCardModel();
        $customerModel = new CustomerModel();
        $vehicleModel = new VehicleModel();
        $jobCardPartModel = new JobCardPartModel();
        $jobCardLaborModel = new JobCardLaborModel();

        $data['job'] = $jobCardModel->find($job_id);

        if (!$data['job']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found.');
        }

        if ($data['job']['assigned_mechanic_id'] != session()->get('user_id')) {
            return redirect()->to(base_url('mechanic/jobs'))
                ->with('error', 'You are not assigned to this job.');
        }

        $data['customer'] = $customerModel->find($data['job']['customer_id']);
        $data['vehicle'] = $vehicleModel->find($data['job']['vehicle_id']);

        $oldParts = session()->getFlashdata('old_parts');
        if (is_array($oldParts)) {
            $inventoryModel = new InventoryModel();
            $normalized = [];
            foreach ($oldParts as $part) {
                $part['unit_price_at_estimate'] = (float)($part['unit_price'] ?? 0.00);
                unset($part['unit_price']);
                $invId = (int)($part['inventory_id'] ?? 0);
                if ($invId > 0) {
                    $invItem = $inventoryModel->find($invId);
                    if ($invItem) {
                        $part['name'] = $invItem['name'];
                        $part['part_number'] = $invItem['part_number'];
                    }
                }
                if (!isset($part['name'])) {
                    $part['name'] = '';
                }
                if (!isset($part['part_number'])) {
                    $part['part_number'] = '';
                }
                $normalized[] = $part;
            }
            $data['job_parts'] = $normalized;
        } else {
            $data['job_parts'] = $jobCardPartModel->getByJobCard($job_id);
        }

        $oldTasks = session()->getFlashdata('old_tasks');
        $data['job_tasks'] = is_array($oldTasks) ? $oldTasks : $jobCardLaborModel->getByJobCard($job_id);

        $config = new \Config\JobStatus();
        $data['valid_transitions'] = $config->getValidTransitions($data['job']['job_status'], 'mechanic');

        return view('technician/job_detail', $data);
    }

    public function search_parts()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $query = $this->request->getVar('query', FILTER_SANITIZE_SPECIAL_CHARS);
        $results = [];

        if (!empty($query)) {
            $inventoryModel = new InventoryModel();
            $results = $inventoryModel->search($query);
        }

        return $this->respond($results);
    }

    public function save_diagnosis()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return redirect()->to('/login');
        }

        $job_id = $this->request->getVar('job_id', FILTER_SANITIZE_NUMBER_INT);
        $redirectUrl = base_url('mechanic/jobs/' . $job_id);

        $rules = [
            'diagnosis' => 'required|min_length[10]',
        ];

        $messages = [
            'diagnosis' => [
                'required' => 'Please provide a diagnosis description.',
                'min_length' => 'Diagnosis must be at least 10 characters.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            session()->setFlashdata('old_parts', $this->request->getPost('parts'));
            session()->setFlashdata('old_tasks', $this->request->getPost('tasks'));
            session()->setFlashdata('old_diagnosis_category', $this->request->getPost('diagnosis_category'));
            return redirect()->to($redirectUrl)->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate parts: each row must have a valid inventory_id
        $parts = $this->request->getPost('parts');
        if ($parts && is_array($parts)) {
            $partErrors = [];
            $idx = 0;
            foreach ($parts as $part) {
                $idx++;
                if (empty($part['inventory_id']) || (int)$part['inventory_id'] <= 0) {
                    $partErrors[] = "Part row {$idx}: Select a valid part from the search results.";
                }
            }
            if (!empty($partErrors)) {
                session()->setFlashdata('old_parts', $this->request->getPost('parts'));
                session()->setFlashdata('old_tasks', $this->request->getPost('tasks'));
                session()->setFlashdata('old_diagnosis_category', $this->request->getPost('diagnosis_category'));
                return redirect()->to($redirectUrl)->withInput()->with('errors', $partErrors);
            }
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($job_id);
        if (!$job || $job['assigned_mechanic_id'] != session()->get('user_id')) {
            return redirect()->to($redirectUrl)->with('error', 'You are not authorized to submit diagnosis for this job.');
        }

        $this->db->transStart();

        try {
            $jobCardPartModel = new JobCardPartModel();
            $jobCardLaborModel = new JobCardLaborModel();
            $fromStatus = $job ? $job['job_status'] : 'Awaiting Diagnosis';

            $diagnosisCategory = $this->request->getVar('diagnosis_category');

            $totalHours = 0;
            $tasks = $this->request->getVar('tasks');
            if ($tasks && is_array($tasks)) {
                foreach ($tasks as $task) {
                    $totalHours += (float)($task['estimated_hours'] ?? 0);
                }
            }

            $update_data = [
                'diagnosis' => $this->request->getVar('diagnosis', FILTER_SANITIZE_SPECIAL_CHARS),
                'diagnosis_category' => !empty($diagnosisCategory) ? $diagnosisCategory : null,
                'estimated_labor_hours' => $totalHours,
                'job_status' => 'Diagnosis Complete'
            ];
            $jobCardModel->update($job_id, $update_data);

            $historyModel = new JobStatusHistoryModel();
            $historyModel->insert([
                'job_card_id' => $job_id,
                'from_status' => $fromStatus,
                'to_status'   => 'Diagnosis Complete',
                'changed_by'  => $this->session->get('user_id'),
                'notes'       => 'Diagnosis submitted by mechanic',
            ]);

            $jobCardPartModel->deleteByJobCard($job_id);
            $parts = $this->request->getVar('parts');
            if ($parts && is_array($parts)) {
                $batchInsertParts = [];
                foreach ($parts as $part) {
                    $batchInsertParts[] = [
                        'job_card_id' => $job_id,
                        'inventory_id' => (int)($part['inventory_id'] ?? 0),
                        'quantity_required' => (int)($part['quantity_required'] ?? 0),
                        'unit_price_at_estimate' => (float)($part['unit_price'] ?? 0.00),
                    ];
                }
                if (!empty($batchInsertParts)) {
                    $jobCardPartModel->insertBatch($batchInsertParts);
                }

                $inventoryModel = new InventoryModel();
                foreach ($parts as $part) {
                    $invItem = $inventoryModel->find((int)($part['inventory_id'] ?? 0));
                    if ($invItem && !empty($invItem['is_stocked'])) {
                        $inventoryModel->decrementStock($invItem['id'], (int)($part['quantity_required'] ?? 0));
                    }
                }
            }

            $jobCardLaborModel->deleteByJobCard($job_id);
            $tasks = $this->request->getVar('tasks');
            if ($tasks && is_array($tasks)) {
                $batchInsertTasks = [];
                foreach ($tasks as $task) {
                    $batchInsertTasks[] = [
                        'job_card_id' => $job_id,
                        'task_name' => $task['task_name'] ?? '',
                        'estimated_hours' => (float)($task['estimated_hours'] ?? 0.00),
                        'rate_per_hour' => (float)($task['rate_per_hour'] ?? 0.00),
                        'notes' => $task['notes'] ?? ''
                    ];
                }
                if (!empty($batchInsertTasks)) {
                    $jobCardLaborModel->insertBatch($batchInsertTasks);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new Exception('Transaction failed, diagnosis not saved.');
            } else {
                log_activity('diagnosis_saved', 'job_card', (int)$job_id, "Diagnosis saved for job card ID {$job_id}");
                return redirect()->to($redirectUrl)->with('success', 'Diagnosis and estimate saved successfully!');
            }
        } catch (Exception $e) {
            $this->db->transRollback();
            return redirect()->to($redirectUrl)->with('error', 'Failed to save diagnosis: ' . $e->getMessage());
        }
    }

    public function request_part($jobCardId)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($jobCardId);
        if (!$job || $job['assigned_mechanic_id'] != session()->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'You are not assigned to this job.'
            ]);
        }

        $inventoryId = (int) $this->request->getPost('inventory_id');
        $note = $this->request->getPost('note');

        if (!$inventoryId) {
            return $this->respond(['status' => 'error', 'message' => 'Inventory ID is required'], 400);
        }

        $partModel = new JobCardPartModel();

        $existingPart = $partModel->where('job_card_id', $jobCardId)
            ->where('inventory_id', $inventoryId)
            ->where('status', 'Pending')
            ->first();

        if ($existingPart) {
            return $this->respond(['status' => 'error', 'message' => 'This part is already requested for this job.'], 409);
        }

        $partId = $partModel->insert([
            'job_card_id'          => (int) $jobCardId,
            'inventory_id'         => $inventoryId,
            'quantity_required'    => 1,
            'unit_price_at_estimate' => 0.00,
            'status'               => 'Pending',
            'requested_at'         => date('Y-m-d H:i:s'),
            'requested_note'       => $note ?: null,
            'requested_by'         => session()->get('user_id'),
        ]);

        $inventoryModel = new InventoryModel();
        $invItem = $inventoryModel->find($inventoryId);

        $notificationModel = new NotificationModel();
        $userModel = new UserModel();
        $adminUsers = $userModel->whereIn('role', ['admin'])->findAll();
        foreach ($adminUsers as $admin) {
            $notificationModel->notify(
                (int) $admin['id'],
                'part_requested',
                'Part Requested',
                "Part \"{$invItem['name']}\" requested for job {$job['job_no']}" . ($note ? ": {$note}" : ''),
                'job_card',
                (int) $jobCardId
            );
        }

        log_activity('part_requested', 'job_card', (int) $jobCardId, "Part \"{$invItem['name']}\" requested for job {$job['job_no']}");

        return $this->respond([
            'status'  => 'success',
            'message' => 'Part request submitted. Admin will be notified.',
            'part_id' => $partId,
        ]);
    }

    public function mechanic_history()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Unauthorized');
        }

        $mechanic_id = $this->session->get('user_id');
        $jobCardModel = new JobCardModel();
        $data['completed_jobs'] = $jobCardModel->getCompletedForMechanic($mechanic_id);

        return view('technician/history', $data);
    }

    public function mechanic_notifications()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Unauthorized');
        }

        $notificationModel = new NotificationModel();
        $data['notifications'] = $notificationModel->getForUser($this->session->get('user_id'));

        return view('technician/notifications', $data);
    }

    public function notifications_unread_count()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->respond(['count' => 0]);
        }

        $notificationModel = new NotificationModel();
        $count = $notificationModel->getUnreadCount($this->session->get('user_id'));

        return $this->respond(['count' => $count]);
    }

    public function notifications_mark_read($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $notificationModel = new NotificationModel();
        $notification = $notificationModel->find($id);

        if (!$notification) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Notification not found']);
        }

        if ((int) $notification['user_id'] !== (int) $this->session->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $notificationModel->markRead($id);

        return $this->respond(['status' => 'success', 'message' => 'Marked as read']);
    }

    public function notifications_mark_all_read()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $notificationModel = new NotificationModel();
        $notificationModel->markAllRead($this->session->get('user_id'));

        return $this->respond(['status' => 'success', 'message' => 'All notifications marked as read']);
    }

    public function upload_photo($jobCardId)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($jobCardId);
        if (!$job || $job['assigned_mechanic_id'] != session()->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'You are not assigned to this job.'
            ]);
        }

        $photoType = $this->request->getPost('photo_type');
        if (!in_array($photoType, ['Progress', 'Completion'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Photo type must be Progress or Completion.'
            ]);
        }

        $files = $this->request->getFiles();
        if (!isset($files['photo']) || !$files['photo']->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No valid photo file provided.'
            ]);
        }

        $file = $files['photo'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower($file->getExtension());
        $mime = $file->getMimeType();
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $actualMime = $finfo->file($file->getTempName());

        if (!in_array($ext, $allowedExts) || !in_array($mime, $allowedMimes) || !in_array($actualMime, $allowedMimes)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Invalid file type. Allowed: jpg, jpeg, png, webp, gif.'
            ]);
        }

        $newName = $file->getRandomName();
        $uploadPath = ROOTPATH . 'uploads/job_card_photos/';
        $file->move($uploadPath, $newName);

        $jobCardPhotoModel = new JobCardPhotoModel();
        $photoId = $jobCardPhotoModel->insert([
            'job_card_id' => (int) $jobCardId,
            'file_path'   => 'uploads/job_card_photos/' . $newName,
            'file_name'   => $file->getClientName(),
            'uploaded_by' => session()->get('user_id'),
            'photo_type'  => $photoType,
            'caption'     => $this->request->getPost('caption'),
        ]);

        return $this->respond([
            'status'    => 'success',
            'message'   => 'Photo uploaded successfully.',
            'photo'     => [
                'id'         => $photoId,
                'file_path'  => 'uploads/job_card_photos/' . $newName,
                'photo_type' => $photoType,
                'caption'    => $this->request->getPost('caption'),
            ],
        ]);
    }

    public function clock_in($jobCardId)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($jobCardId);
        if (!$job || $job['assigned_mechanic_id'] != session()->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'You are not assigned to this job.'
            ]);
        }

        $timeLogModel = new JobTimeLogModel();
        $existing = $timeLogModel->getOpenLogForMechanic((int) $jobCardId, session()->get('user_id'));
        if ($existing) {
            return $this->response->setStatusCode(409)->setJSON([
                'status' => 'error',
                'message' => 'Already clocked in.',
                'log_id' => (int) $existing['id'],
            ]);
        }

        $logId = $timeLogModel->clockIn((int) $jobCardId, session()->get('user_id'));

        return $this->respond([
            'status'     => 'success',
            'message'    => 'Clocked in successfully.',
            'log_id'     => $logId,
            'clock_in'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function clock_out($logId)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $timeLogModel = new JobTimeLogModel();
        $log = $timeLogModel->find($logId);
        if (!$log) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Time log not found.'
            ]);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($log['job_card_id']);
        if (!$job || $job['assigned_mechanic_id'] != session()->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'You are not assigned to this job.'
            ]);
        }

        $result = $timeLogModel->clockOut((int) $logId);
        if (!$result) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Already clocked out or log not found.'
            ]);
        }

        $updated = $timeLogModel->find($logId);

        return $this->respond([
            'status'           => 'success',
            'message'          => 'Clocked out successfully.',
            'duration_minutes' => (int) ($updated['duration_minutes'] ?? 0),
        ]);
    }

    public function get_time_logs($jobCardId)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'mechanic') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $jobCardModel = new JobCardModel();
        $job = $jobCardModel->find($jobCardId);
        if (!$job || $job['assigned_mechanic_id'] != session()->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'You are not assigned to this job.'
            ]);
        }

        $timeLogModel = new JobTimeLogModel();
        $logs = $timeLogModel->getLogsForJobCard((int) $jobCardId);

        return $this->respond([
            'status' => 'success',
            'logs'   => $logs,
        ]);
    }
}
