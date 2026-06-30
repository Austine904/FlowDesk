<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SubletModel;
use App\Models\SupplierModel;
use App\Models\JobCardModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;

class SubletsController extends BaseController
{
    use ResponseTrait;

    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return redirect()->to('/login')->with('error', 'You do not have permission to access this page.');
        }
        return view('sublets/index');
    }

    public function load()
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return $this->failUnauthorized('Unauthorized access.');
        }

        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $search_value = $request->getPost('search')['value'];
        $order_column = $request->getPost('order')[0]['column'];
        $order_dir = $request->getPost('order')[0]['dir'];
        $columns = $request->getPost('columns');

        $orderableColumns = [
            0 => 'sublets.id',
            1 => 'sublets.id',
            2 => 'job_cards.job_no',
            3 => 'sublets.description',
            4 => 'suppliers.name',
            5 => 'sublets.cost',
            6 => 'sublets.status',
            7 => 'sublets.date_sent',
            8 => 'sublets.date_returned',
            9 => 'sublets.id',
        ];
        $order_by = $orderableColumns[$order_column] ?? 'sublets.id';

        try {
            $subletModel = new SubletModel();

            // Step 1 — total unfiltered count
            $totalRecords = $subletModel->countAllResults();

            // Step 2 — filtered count (fresh builder)
            $builder = $subletModel->builder();
            $builder->join('job_cards', 'job_cards.id = sublets.job_card_id', 'left');
            $builder->join('suppliers', 'suppliers.id = sublets.sublet_provider_id', 'left');
            if (!empty($search_value)) {
                $builder->groupStart()
                    ->like('job_cards.job_no', $search_value)
                    ->orLike('suppliers.name', $search_value)
                    ->orLike('sublets.description', $search_value)
                    ->orLike('sublets.status', $search_value)
                    ->groupEnd();
            }
            $statusFilter = $request->getPost('status_filter');
            if (!empty($statusFilter)) {
                $builder->where('sublets.status', $statusFilter);
            }
            $filteredRecords = $builder->countAllResults(true);

            // Step 3 — data query (fresh builder again)
            $builder = $subletModel->builder();
            $builder->select('
                sublets.id,
                sublets.description,
                sublets.cost,
                sublets.status,
                sublets.date_sent,
                sublets.date_returned,
                job_cards.job_no,
                job_cards.id as job_card_id,
                suppliers.name as provider_name
            ');
            $builder->join('job_cards', 'job_cards.id = sublets.job_card_id', 'left');
            $builder->join('suppliers', 'suppliers.id = sublets.sublet_provider_id', 'left');
            if (!empty($search_value)) {
                $builder->groupStart()
                    ->like('job_cards.job_no', $search_value)
                    ->orLike('suppliers.name', $search_value)
                    ->orLike('sublets.description', $search_value)
                    ->orLike('sublets.status', $search_value)
                    ->groupEnd();
            }
            $statusFilter = $request->getPost('status_filter');
            if (!empty($statusFilter)) {
                $builder->where('sublets.status', $statusFilter);
            }
            $builder->orderBy($order_by, $order_dir);
            $data = $builder->limit($length, $start)->get()->getResultArray();

            $response = [
                "draw" => intval($draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $data
            ];

            return $this->respond($response);

        } catch (DatabaseException $e) {
            log_message('error', 'Database error loading sublets: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not load sublet data.');
        } catch (Exception $e) {
            log_message('error', 'Error loading sublets: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred while loading sublet data.');
        }
    }

    public function add($id = null)
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }

        $data['sublet'] = null;
        if ($id) {
            $subletModel = new SubletModel();
            $data['sublet'] = $subletModel->find($id);
            if (!$data['sublet']) {
                return $this->failNotFound('Sublet not found.');
            }
        }

        $jobCardModel = new JobCardModel();
        $data['job_cards'] = $jobCardModel->select('job_cards.id, job_cards.job_no, vehicles.registration_number')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->findAll();

        $supplierModel = new SupplierModel();
        $data['sublet_providers'] = $supplierModel->getAll();

        return view('sublets/form', $data);
    }

    public function save()
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }

        $sublet_id = $this->request->getPost('id');
        $rules = [
            'job_card_id'        => 'required|integer',
            'sublet_provider_id' => 'required|integer',
            'description'        => 'required|min_length[5]|max_length[500]',
            'cost'               => 'required|numeric|greater_than_equal_to[0]',
            'status'             => 'required|in_list[Pending,In Progress,Completed,Invoiced,Paid,Cancelled]',
            'date_sent'          => 'required|valid_date',
            'date_returned'      => 'permit_empty|valid_date',
            'notes'              => 'permit_empty|max_length[1000]',
        ];

        if (!empty($this->request->getPost('date_returned')) && !empty($this->request->getPost('date_sent'))) {
            if (strtotime($this->request->getPost('date_returned')) < strtotime($this->request->getPost('date_sent'))) {
                $this->validator->setError('date_returned', 'Date Returned cannot be earlier than Date Sent.');
            }
        }

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'job_card_id'        => $this->request->getPost('job_card_id'),
            'sublet_provider_id' => $this->request->getPost('sublet_provider_id'),
            'description'        => $this->request->getPost('description'),
            'cost'               => $this->request->getPost('cost'),
            'status'             => $this->request->getPost('status'),
            'date_sent'          => $this->request->getPost('date_sent'),
            'date_returned'      => !empty($this->request->getPost('date_returned')) ? $this->request->getPost('date_returned') : null,
            'notes'              => !empty($this->request->getPost('notes')) ? $this->request->getPost('notes') : null,
        ];

        try {
            $subletModel = new SubletModel();
            if ($sublet_id) {
                $subletModel->update($sublet_id, $data);
                if ($subletModel->db->affectedRows() > 0) {
                    return $this->respond(['status' => 'success', 'message' => 'Sublet updated successfully!']);
                } else {
                    return $this->respond(['status' => 'info', 'message' => 'No changes were made to the sublet.']);
                }
            } else {
                $subletModel->insert($data);
                $newId = $subletModel->insertID();
                if ($newId) {
                    return $this->respondCreated(['status' => 'success', 'message' => 'Sublet added successfully!', 'id' => $newId]);
                } else {
                    return $this->failServerError('Failed to add sublet.');
                }
            }
        } catch (DatabaseException $e) {
            log_message('error', 'Database error saving sublet: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not save sublet. ' . $e->getMessage());
        } catch (Exception $e) {
            log_message('error', 'Error saving sublet: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred while saving the sublet.');
        }
    }

    public function details($id)
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return $this->failUnauthorized('Unauthorized access.');
        }

        try {
            $subletModel = new SubletModel();
            $result = $subletModel->getWithDetails($id);
            $sublet = $result ? $result[0] : null;

            if (!$sublet) {
                return $this->failNotFound('Sublet not found.');
            }

            return view('sublets/_details', ['sublet' => $sublet]);

        } catch (DatabaseException $e) {
            log_message('error', 'Database error fetching sublet details: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not retrieve sublet details.');
        } catch (Exception $e) {
            log_message('error', 'Error fetching sublet details: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred while fetching sublet details.');
        }
    }

    public function delete($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admins can delete sublets.');
        }

        try {
            $subletModel = new SubletModel();
            $subletModel->delete($id);
            if ($subletModel->db->affectedRows() > 0) {
                return $this->respondDeleted(['status' => 'success', 'message' => 'Sublet deleted successfully.']);
            } else {
                return $this->failNotFound('Sublet not found or already deleted.');
            }
        } catch (DatabaseException $e) {
            log_message('error', 'Database error deleting sublet: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not delete sublet.');
        } catch (Exception $e) {
            log_message('error', 'Error deleting sublet: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred during deletion.');
        }
    }

    public function bulkAction()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admins can perform bulk actions.');
        }

        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->failValidationErrors('No items selected for bulk action.');
        }

        try {
            if ($action === 'delete') {
                $deletedCount = 0;
                $subletModel = new SubletModel();
                foreach ($ids as $id) {
                    $subletModel->delete($id);
                    $deletedCount += $subletModel->db->affectedRows();
                }
                if ($deletedCount > 0) {
                    return $this->respondDeleted(['status' => 'success', 'message' => "Successfully deleted {$deletedCount} sublet(s)."]);
                } else {
                    return $this->failNotFound('No sublets found or deleted for the provided IDs.');
                }
            } else {
                return $this->failValidationErrors('Invalid bulk action specified.');
            }
        } catch (DatabaseException $e) {
            log_message('error', 'Database error during bulk sublet action: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not perform bulk action on sublets.');
        } catch (Exception $e) {
            log_message('error', 'Error during bulk sublet action: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred during bulk action.');
        }
    }
}
