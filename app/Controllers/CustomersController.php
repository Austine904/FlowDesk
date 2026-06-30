<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\VehicleModel;
use App\Models\JobCardModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;

class CustomersController extends BaseController
{
    use ResponseTrait;

    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('You are not authorized to view this page.');
        }

        $customerModel = new CustomerModel();
        $builder = $customerModel->builder()
            ->select('id, name, phone, email, address, created_at');
        $search = $this->request->getVar('search');
        if (!empty($search)) {
            $builder->like('name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search);
        }

        $perpage = 10;
        $currentPage = $this->request->getVar('page') ?? 1;
        $total = $builder->countAllResults(false);
        $customers = $builder->limit($perpage, ($currentPage - 1) * $perpage)->get()->getResultArray();
        $pager = \Config\Services::pager();

        if ($this->request->isAJAX()) {
            return view('customers/customers_list', ['customers' => $customers, 'pager' => $pager]);
        }
        return view('customers/customers', [
            'customers' => $customers,
            'pager' => $pager,
            'total' => $total,
            'currentPage' => $currentPage,
        ]);
    }

    public function load()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized access.');
        }

        $customerModel = new CustomerModel();

        $request = $this->request;
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';
        $order = $request->getPost('order');
        $columns = $request->getPost('columns');

        // Total unfiltered count (fresh builder)
        $totalRecords = $customerModel->countAllResults();

        // Filtered count
        $builder = $customerModel->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(true);

        // Data query (fresh builder since countAllResults(true) resets)
        $builder = $customerModel->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        $subQuery = $customerModel->builder('vehicles')
            ->select('COUNT(id)')
            ->where('vehicles.owner_id = customers.id')
            ->getCompiledSelect();

        $builder->select('customers.id, customers.name, customers.phone, customers.email, customers.address, customers.created_at');
        $builder->select("({$subQuery}) as vehicle_count");

        if ($order) {
            $columnName = $columns[$order[0]['column']]['data'];
            $columnDir = $order[0]['dir'];
            if ($columnName === 'vehicle_count') {
                $builder->orderBy('vehicle_count', $columnDir);
            } else {
                $builder->orderBy($columnName, $columnDir);
            }
        }

        $builder->limit($length, $start);

        $data = $builder->get()->getResultArray();

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];

        return $this->respond($response);
    }

    public function details($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized access.');
        }

        if (!is_numeric($id)) {
            return $this->failValidationErrors('Invalid customer ID.');
        }

        try {
            $customerModel = new CustomerModel();
            $vehicleModel = new VehicleModel();
            $jobCardModel = new JobCardModel();

            $customer = $customerModel->find($id);

            if (!$customer) {
                return $this->failNotFound('Customer not found.');
            }

            $vehicles = $vehicleModel->getByOwner($id);
            $customer['vehicles'] = $vehicles;

            $jobs = $jobCardModel->where('customer_id', $id)->findAll();
            foreach ($jobs as &$job) {
                $vehicle = $vehicleModel->select('registration_number')
                    ->where('id', $job['vehicle_id'])
                    ->first();
                $job['registration_number'] = $vehicle ? $vehicle['registration_number'] : 'Unknown';
            }

            $customer['jobs'] = $jobs;

            return $this->respond($customer);
        } catch (DatabaseException $e) {
            log_message('error', 'Database error: ' . $e->getMessage());
            return $this->failServerError('Database error.');
        } catch (\Exception $e) {
            log_message('error', 'Unexpected error: ' . $e->getMessage());
            return $this->failServerError('Unexpected error occurred.');
        }
    }

    public function add()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }
        return view('admin/forms/add_customer_form');
    }

    public function edit($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }

        $customerModel = new CustomerModel();
        $customer = $customerModel->find($id);

        if (!$customer) {
            return $this->failNotFound('Customer not found for editing.');
        }
        return view('admin/forms/edit_customer_form', ['customer' => $customer]);
    }

    public function bulk_action()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }

        $customer_ids = $this->request->getPost('customers');

        if (empty($customer_ids)) {
            session()->setFlashdata('error', 'No customers selected for deletion.');
            return redirect()->back();
        }

        try {
            $customerModel = new CustomerModel();
            $db = \Config\Database::connect();
            $db->transStart();

            $customerModel->whereIn('id', $customer_ids)->delete();

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new Exception('Transaction failed during bulk customer deletion.');
            }

            session()->setFlashdata('success', count($customer_ids) . ' customer(s) deleted successfully.');
        } catch (Exception $e) {
            $db->transRollback();
            session()->setFlashdata('error', 'Failed to delete customers: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
