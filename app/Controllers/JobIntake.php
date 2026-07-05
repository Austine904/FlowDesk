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
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('auth/login');
        }

        $user_role = $this->session->get('role');
        if ($user_role !== 'admin' && $user_role !== 'receptionist') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('You are not authorized to access this page.');
        }

        $userModel = new UserModel();
        $service_advisors = $userModel->whereIn('role', ['admin', 'receptionist'])->findAll();
        $mechanics = $userModel->getByRole('mechanic');
        $data['service_advisors'] = $service_advisors;
        $data['mechanics'] = $mechanics;
        return view('job_intake_form', $data);
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

            $customerModel = new CustomerModel();
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

            if (isset($files['job_card_photos'])) {
                foreach ($files['job_card_photos'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $uploadPath = ROOTPATH . 'public/uploads/job_card_photos/';
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }
                        $file->move($uploadPath, $newName);

                        $photo_data = [
                            'job_card_id' => $job_card_id,
                            'file_path' => 'uploads/job_card_photos/' . $newName,
                            'file_name' => $file->getClientName()
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

        return view('mechanic/jobs', $data);
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

        $data['customer'] = $customerModel->find($data['job']['customer_id']);
        $data['vehicle'] = $vehicleModel->find($data['job']['vehicle_id']);

        $data['job_parts'] = $jobCardPartModel->getByJobCard($job_id);

        $data['job_tasks'] = $jobCardLaborModel->getByJobCard($job_id);

        return view('mechanic_diagnosis_form', $data);
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
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $job_id = $this->request->getVar('job_id', FILTER_SANITIZE_NUMBER_INT);

        $rules = [
            'diagnosis' => 'required|min_length[10]',
            'estimated_labor_hours' => 'numeric|greater_than_equal_to[0]',
            'parts' => 'permit_empty|array',
            'tasks' => 'permit_empty|array',
        ];

        $this->validation->setRules($rules);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->fail(['message' => 'Validation failed', 'errors' => $this->validation->getErrors()], 400);
        }

        $this->db->transStart();

        try {
            $jobCardModel = new JobCardModel();
            $jobCardPartModel = new JobCardPartModel();
            $jobCardLaborModel = new JobCardLaborModel();

            $update_data = [
                'diagnosis' => $this->request->getVar('diagnosis', FILTER_SANITIZE_SPECIAL_CHARS),
                'estimated_labor_hours' => $this->request->getVar('estimated_labor_hours', FILTER_SANITIZE_NUMBER_FLOAT),
                'job_status' => 'Diagnosis Complete'
            ];
            $jobCardModel->update($job_id, $update_data);

            $jobCardPartModel->deleteByJobCard($job_id);
            $parts = $this->request->getVar('parts');
            if ($parts && is_array($parts)) {
                $batchInsertParts = [];
                foreach ($parts as $part) {
                    $batchInsertParts[] = [
                        'job_card_id' => $job_id,
                        'inventory_id' => (int)($part['inventory_id'] ?? 0),
                        'quantity_required' => (int)($part['quantity_required'] ?? 0),
                        'unit_price_at_estimate' => (float)($part['unit_price'] ?? 0.00)
                    ];
                }
                if (!empty($batchInsertParts)) {
                    $jobCardPartModel->insertBatch($batchInsertParts);
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
                return $this->respond(['status' => 'success', 'message' => 'Diagnosis and estimate saved successfully!']);
            }
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->fail(['message' => $e->getMessage()], 500);
        }
    }
}
