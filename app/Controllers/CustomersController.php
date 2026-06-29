<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;
use CodeIgniter\Exceptions\PageNotFoundException; // Import for 404 handling

class CustomersController extends BaseController
{
    use ResponseTrait;

    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
    }


    public function index()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('You are not authorized to view this page.');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('customers')
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

        // If the request is AJAX, return a partial view for DataTables
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

    /**
     * AJAX endpoint for DataTables to load customer data.
     * Handles pagination, searching, and sorting.
     */
    public function load()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized access.');
        }

        $request = $this->request;
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';
        $order = $request->getPost('order');
        $columns = $request->getPost('columns');

        $builder = $this->db->table('customers');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        // Get total records count without filtering
        $totalRecords = $builder->countAllResults(false); // `false` to keep the WHERE clause for filtered count

        // Get filtered records count
        $filteredRecords = $builder->countAllResults(false);

        // Subquery to count vehicles for each customer
        $subQuery = $this->db->table('vehicles')
            ->select('COUNT(id)')
            ->where('vehicles.owner_id = customers.id')
            ->getCompiledSelect();

        // Select main customer columns and the vehicle count
        $builder->select('customers.id, customers.name, customers.phone, customers.email, customers.address, customers.created_at');
        $builder->select("({$subQuery}) as vehicle_count"); // Add vehicle count

        // Apply ordering
        if ($order) {
            $columnName = $columns[$order[0]['column']]['data'];
            $columnDir = $order[0]['dir'];
            // Map DataTables column names to actual database column names if necessary
            // For 'vehicle_count', we order by the alias
            if ($columnName === 'vehicle_count') {
                $builder->orderBy('vehicle_count', $columnDir);
            } else {
                $builder->orderBy($columnName, $columnDir);
            }
        }

        // Apply pagination
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

    /**
     * AJAX endpoint to fetch details for a specific customer, including their vehicles.
     * @param int $id Customer ID
     */

    public function details($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized access.');
        }

        if (!is_numeric($id)) {
            return $this->failValidationErrors('Invalid customer ID.');
        }

        try {
            $customer = $this->db->table('customers')->where('id', $id)->get()->getRowArray();

            if (!$customer) {
                return $this->failNotFound('Customer not found.');
            }

            $vehicles = $this->db->table('vehicles')->where('owner_id', $id)->get()->getResultArray();
            $customer['vehicles'] = $vehicles;

            $jobs = $this->db->table('job_cards')->where('customer_id', $id)->get()->getResultArray();
            //fetch vehicle registration numbers for each job
            foreach ($jobs as &$job) {
                $vehicle = $this->db->table('vehicles')
                    ->select('registration_number')
                    ->where('id', $job['vehicle_id'])
                    ->get()
                    ->getRowArray();
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
        // You would load your add customer form view here
        return view('admin/forms/add_customer_form');
    }

    /**
     * Placeholder for loading the 'Edit Customer' form.
     * This method would typically return a view segment for a modal, pre-filled with customer data.
     * @param int $id Customer ID
     */
    public function edit($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }
        // Fetch customer data and load edit form view
        $customer = $this->db->table('customers')->where('id', $id)->get()->getRowArray();
        if (!$customer) {
            return $this->failNotFound('Customer not found for editing.');
        }
        return view('admin/forms/edit_customer_form', ['customer' => $customer]); // Create this view file
    }

    /**
     * Placeholder for handling bulk actions (e.g., delete selected customers).
     */
    public function bulk_action()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }

        $customer_ids = $this->request->getPost('customers'); // Array of IDs from checkboxes

        if (empty($customer_ids)) {
            session()->setFlashdata('error', 'No customers selected for deletion.');
            return redirect()->back();
        }

        try {
            // Start a transaction for bulk deletion
            $this->db->transStart();

            // NOTE: Vehicle cascade delete removed for safety — vehicles.owner_id has FK constraint.
            // If deleting a customer, ensure vehicles are reassigned or handled separately.

            $this->db->table('customers')->whereIn('id', $customer_ids)->delete();

            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                throw new Exception('Transaction failed during bulk customer deletion.');
            }

            session()->setFlashdata('success', count($customer_ids) . ' customer(s) deleted successfully.');
        } catch (Exception $e) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Failed to delete customers: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
