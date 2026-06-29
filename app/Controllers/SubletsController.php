<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;

class SubletsController extends BaseController
{
    use ResponseTrait;

    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
    }

    /**
     * Displays the main sublet management page.
     * Accessible by Admin and Receptionist.
     */
    public function index()
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return redirect()->to('/login')->with('error', 'You do not have permission to access this page.');
        }
        return view('sublets/index');
    }

    /**
     * AJAX endpoint to load sublet data for DataTables.
     */
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

        // Map DataTables column index to database column name
        $orderableColumns = [
            0 => 'sublets.id', // Checkbox column, not directly orderable by DB, but safe placeholder
            1 => 'sublets.id',
            2 => 'job_cards.job_no',
            3 => 'sublets.description',
            4 => 'suppliers.name',
            5 => 'sublets.cost',
            6 => 'sublets.status',
            7 => 'sublets.date_sent',
            8 => 'sublets.date_returned',
            9 => 'sublets.id', // Actions column
        ];
        $order_by = $orderableColumns[$order_column] ?? 'sublets.id'; // Default to ID if column not found

        try {
            $builder = $this->db->table('sublets')
                                ->select('
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

            // Apply search filter
            if (!empty($search_value)) {
                $builder->groupStart()
                        ->like('sublets.description', $search_value)
                        ->orLike('sublets.status', $search_value)
                        ->orLike('job_cards.job_no', $search_value)
                        ->orLike('suppliers.name', $search_value)
                        ->groupEnd();
            }

            // Apply status filter from JS
            $statusFilter = $request->getPost('status_filter');
            if (!empty($statusFilter)) {
                $builder->where('sublets.status', $statusFilter);
            }

            // Get total count (before pagination and filtering)
            $totalRecords = $builder->countAllResults(false); // Pass false to keep the WHERE/JOIN clauses

            // Apply ordering
            $builder->orderBy($order_by, $order_dir);

            // Apply pagination
            $query = $builder->limit($length, $start)->get();
            $data = $query->getResultArray();

            $response = [
                "draw" => intval($draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords, // For simplicity, recordsFiltered is same as totalRecords after filtering
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

    /**
     * Displays the add/edit sublet form (loaded into a modal).
     * @param int|null $id Sublet ID to edit, or null for new.
     */
    public function add($id = null)
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }

        $data['sublet'] = null;
        if ($id) {
            $data['sublet'] = $this->db->table('sublets')
                                       ->where('id', $id)
                                       ->get()
                                       ->getRowArray();
            if (!$data['sublet']) {
                return $this->failNotFound('Sublet not found.');
            }
        }

        // Fetch necessary data for dropdowns using direct DB calls
        $data['job_cards'] = $this->db->table('job_cards')
                                     ->select('job_cards.id, job_cards.job_no, vehicles.registration_number')
                                     ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
                                     ->get()
                                     ->getResultArray();

        // Fetch suppliers using direct DB calls
        $data['sublet_providers'] = $this->db->table('suppliers')
                                            ->select('id, name')
                                            ->get()
                                            ->getResultArray();

        return view('sublets/form', $data);
    }

    /**
     * Handles saving (add/edit) a sublet record via AJAX.
     */
    public function save()
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return $this->failForbidden('Forbidden: Insufficient permissions.');
        }

        $sublet_id = $this->request->getPost('id'); // Will be null for new sublets
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

        // Custom validation for date_returned to be after date_sent
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
            if ($sublet_id) {
                // Update existing record
                $this->db->table('sublets')->where('id', $sublet_id)->update($data);
                if ($this->db->affectedRows() > 0) {
                    return $this->respond(['status' => 'success', 'message' => 'Sublet updated successfully!']);
                } else {
                    return $this->respond(['status' => 'info', 'message' => 'No changes were made to the sublet.']);
                }
            } else {
                // Insert new record
                $this->db->table('sublets')->insert($data);
                $newId = $this->db->insertID();
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
        //error in console
        
    }

    /**
     * Fetches details of a single sublet for display in a modal.
     * @param int $id The ID of the sublet.
     */
    public function details($id)
    {
        if (!$this->session->get('isLoggedIn') || (!in_array($this->session->get('role'), ['admin', 'receptionist']))) {
            return $this->failUnauthorized('Unauthorized access.');
        }

        try {
            $sublet = $this->db->table('sublets')
                               ->select('
                                   sublets.*,
                                   job_cards.job_no,
                                   vehicles.registration_number,
                                   suppliers.name as provider_name
                               ')
                               ->join('job_cards', 'job_cards.id = sublets.job_card_id', 'left')
                               ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
                               ->join('suppliers', 'suppliers.id = sublets.sublet_provider_id', 'left')
                               ->where('sublets.id', $id)
                               ->get()
                               ->getRowArray();

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

    /**
     * Deletes a single sublet record.
     * @param int $id The ID of the sublet to delete.
     */
    public function delete($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admins can delete sublets.');
        }

        try {
            $this->db->table('sublets')->where('id', $id)->delete();
            if ($this->db->affectedRows() > 0) {
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

    /**
     * Handles bulk actions for sublets (e.g., bulk delete).
     */
    public function bulkAction()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admins can perform bulk actions.');
        }

        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids'); // Array of IDs

        if (empty($ids) || !is_array($ids)) {
            return $this->failValidationErrors('No items selected for bulk action.');
        }

        try {
            if ($action === 'delete') {
                $deletedCount = 0;
                foreach ($ids as $id) {
                    $this->db->table('sublets')->where('id', $id)->delete();
                    $deletedCount += $this->db->affectedRows();
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
