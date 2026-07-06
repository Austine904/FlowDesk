<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PettyCashModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;

class PettyCashController extends BaseController
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
            return redirect()->to('/login');
        }

        $pettyCashModel = new PettyCashModel();
        $summary       = $pettyCashModel->getSummary();
        $transactions  = $pettyCashModel->getWithDetails(50);
        $byCategory    = $pettyCashModel->getByCategory();

        return view('admin/petty_cash/index', compact('summary', 'transactions', 'byCategory'));
    }

    public function load()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized access.');
        }

        $request = service('request');
        $draw    = $request->getPost('draw');
        $start   = $request->getPost('start');
        $length  = $request->getPost('length');
        $search_value = $request->getPost('search')['value'];
        $order_column = $request->getPost('order')[0]['column'];
        $order_dir    = $request->getPost('order')[0]['dir'];

        $orderableColumns = [
            0 => 'petty_cash.transaction_date',
            1 => 'petty_cash.type',
            2 => 'petty_cash.category',
            3 => 'petty_cash.description',
            4 => 'petty_cash.amount',
            5 => 'petty_cash.reference_no',
            6 => 'recorded_by_name',
            7 => 'petty_cash.id',
        ];
        $order_by = $orderableColumns[$order_column] ?? 'petty_cash.id';

        try {
            $pettyCashModel = new PettyCashModel();

            $totalRecords = $pettyCashModel->countAllResults();

            $builder = $pettyCashModel->builder();
            $builder->select('
                petty_cash.id,
                petty_cash.transaction_date,
                petty_cash.type,
                petty_cash.category,
                petty_cash.description,
                petty_cash.amount,
                petty_cash.reference_no,
                CONCAT(users.first_name, " ", users.last_name) as recorded_by_name
            ');
            $builder->join('users', 'users.id = petty_cash.recorded_by', 'left');

            if (!empty($search_value)) {
                $builder->groupStart()
                    ->like('petty_cash.description', $search_value)
                    ->orLike('petty_cash.category', $search_value)
                    ->orLike('petty_cash.reference_no', $search_value)
                    ->orLike('CONCAT(users.first_name, " ", users.last_name)', $search_value)
                    ->groupEnd();
            }
            $filteredRecords = $builder->countAllResults(true);

            $builder = $pettyCashModel->builder();
            $builder->select('
                petty_cash.id,
                petty_cash.transaction_date,
                petty_cash.type,
                petty_cash.category,
                petty_cash.description,
                petty_cash.amount,
                petty_cash.reference_no,
                CONCAT(users.first_name, " ", users.last_name) as recorded_by_name
            ');
            $builder->join('users', 'users.id = petty_cash.recorded_by', 'left');

            if (!empty($search_value)) {
                $builder->groupStart()
                    ->like('petty_cash.description', $search_value)
                    ->orLike('petty_cash.category', $search_value)
                    ->orLike('petty_cash.reference_no', $search_value)
                    ->orLike('CONCAT(users.first_name, " ", users.last_name)', $search_value)
                    ->groupEnd();
            }
            $builder->orderBy($order_by, $order_dir);
            $data = $builder->limit($length, $start)->get()->getResultArray();

            $response = [
                "draw"            => intval($draw),
                "recordsTotal"    => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data"            => $data,
            ];

            return $this->respond($response);
        } catch (DatabaseException $e) {
            log_message('error', 'Database error loading petty cash: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not load petty cash data.');
        } catch (Exception $e) {
            log_message('error', 'Error loading petty cash: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred while loading petty cash data.');
        }
    }

    public function add()
    {
        return view('admin/petty_cash/form', ['transaction' => null, 'action' => 'add']);
    }

    public function create()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'transaction_date' => 'required|valid_date',
            'type'             => 'required|in_list[Income,Expense]',
            'category'         => 'required',
            'description'      => 'required',
            'amount'           => 'required|numeric|greater_than[0]',
            'reference_no'     => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $data = [
            'transaction_date' => $this->request->getPost('transaction_date'),
            'type'             => $this->request->getPost('type'),
            'category'         => $this->request->getPost('category'),
            'description'      => $this->request->getPost('description'),
            'amount'           => $this->request->getPost('amount'),
            'reference_no'     => !empty($this->request->getPost('reference_no')) ? $this->request->getPost('reference_no') : null,
            'recorded_by'      => $this->session->get('user_id'),
        ];

        try {
            $pettyCashModel = new PettyCashModel();
            $pettyCashModel->insert($data);
            $id = $pettyCashModel->insertID();
            log_activity('petty_cash_entry', 'petty_cash', $id, "{$data['type']} of KSh {$data['amount']} recorded — {$data['category']}");
            return redirect()->to(base_url('admin/pettycash'))->with('success', 'Transaction added successfully.');
        } catch (DatabaseException $e) {
            log_message('error', 'Database error creating petty cash: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Database error: Could not save transaction.')->withInput();
        } catch (Exception $e) {
            log_message('error', 'Error creating petty cash: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred.')->withInput();
        }
    }

    public function edit($id)
    {
        $pettyCashModel = new PettyCashModel();
        $item = $pettyCashModel->find($id);
        if (!$item) {
            return redirect()->to(base_url('admin/pettycash'))->with('error', 'Transaction not found.');
        }
        return view('admin/petty_cash/form', ['transaction' => $item, 'action' => 'edit']);
    }

    public function update($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'transaction_date' => 'required|valid_date',
            'type'             => 'required|in_list[Income,Expense]',
            'category'         => 'required',
            'description'      => 'required',
            'amount'           => 'required|numeric|greater_than[0]',
            'reference_no'     => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $data = [
            'transaction_date' => $this->request->getPost('transaction_date'),
            'type'             => $this->request->getPost('type'),
            'category'         => $this->request->getPost('category'),
            'description'      => $this->request->getPost('description'),
            'amount'           => $this->request->getPost('amount'),
            'reference_no'     => !empty($this->request->getPost('reference_no')) ? $this->request->getPost('reference_no') : null,
        ];

        try {
            $pettyCashModel = new PettyCashModel();
            $pettyCashModel->update($id, $data);
            return redirect()->to(base_url('admin/pettycash'))->with('success', 'Transaction updated successfully.');
        } catch (DatabaseException $e) {
            log_message('error', 'Database error updating petty cash: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Database error: Could not update transaction.')->withInput();
        } catch (Exception $e) {
            log_message('error', 'Error updating petty cash: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred.')->withInput();
        }
    }

    public function delete($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }

        try {
            $pettyCashModel = new PettyCashModel();
            $pettyCashModel->delete($id);
            if ($pettyCashModel->db->affectedRows() > 0) {
                return $this->respondDeleted(['status' => 'success', 'message' => 'Transaction deleted successfully.']);
            } else {
                return $this->failNotFound('Transaction not found or already deleted.');
            }
        } catch (DatabaseException $e) {
            log_message('error', 'Database error deleting petty cash: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not delete transaction.');
        } catch (Exception $e) {
            log_message('error', 'Error deleting petty cash: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred during deletion.');
        }
    }

    public function ledger()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $pettyCashModel = new PettyCashModel();
        $transactions = $pettyCashModel->getRunningBalance();

        return view('admin/petty_cash/ledger', ['transactions' => $transactions]);
    }

    public function filter()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized access.');
        }

        $start_date = $this->request->getPost('start_date');
        $end_date   = $this->request->getPost('end_date');

        if (empty($start_date) || empty($end_date)) {
            return $this->failValidationErrors('Both start_date and end_date are required.');
        }

        try {
            $pettyCashModel = new PettyCashModel();
            $summary    = $pettyCashModel->getSummaryByPeriod($start_date, $end_date);
            $byCategory = $pettyCashModel->getByCategory();

            return $this->respond([
                'status'      => 'success',
                'summary'     => $summary,
                'by_category' => $byCategory,
            ]);
        } catch (DatabaseException $e) {
            log_message('error', 'Database error filtering petty cash: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not filter petty cash data.');
        } catch (Exception $e) {
            log_message('error', 'Error filtering petty cash: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred.');
        }
    }
}
