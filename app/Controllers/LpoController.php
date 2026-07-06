<?php

namespace App\Controllers;

use App\Models\LpoModel;
use App\Models\LpoItemModel;
use App\Models\InventoryModel;
use App\Models\SupplierModel;
use App\Models\JobCardModel;
use CodeIgniter\API\ResponseTrait;

class LpoController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        return view('admin/lpos/index');
    }

    public function load()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $lpoModel = new LpoModel();

        $draw   = $this->request->getPost('draw');
        $start  = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';

        $builder = $lpoModel->builder();
        $builder->select('lpos.*, suppliers.name as supplier_name, job_cards.job_no')
            ->join('suppliers', 'suppliers.id = lpos.supplier_id', 'LEFT')
            ->join('job_cards', 'job_cards.id = lpos.job_card_id', 'LEFT');

        $totalRecords = $builder->countAllResults(false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('lpos.lpo_no', $search)
                ->orLike('suppliers.name', $search)
                ->orLike('job_cards.job_no', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('lpos.id', 'DESC');
        $data = $builder->limit($length, $start)->get()->getResultArray();

        return $this->respond([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }

    public function add()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $supplierModel = new SupplierModel();
        $jobCardModel = new JobCardModel();
        $inventoryModel = new InventoryModel();

        $suppliers = $supplierModel->findAll();
        $job_cards = $jobCardModel->whereNotIn('job_status', ['Completed', 'Cancelled', 'Paid'])->findAll();
        $inventory = $inventoryModel->findAll();

        return view('admin/lpos/form', [
            'lpo' => null,
            'action' => 'add',
            'suppliers' => $suppliers,
            'job_cards' => $job_cards,
            'inventory' => $inventory,
        ]);
    }

    public function create()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'supplier_id' => 'required|numeric',
            'lpo_date'    => 'required|valid_date',
            'items'       => 'required|is_array',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items');
        if (empty($items) || !is_array($items) || count($items) < 1) {
            return redirect()->back()->withInput()->with('errors', ['items' => 'At least one line item is required.']);
        }

        $lpoModel = new LpoModel();
        $lpoItemModel = new LpoItemModel();

        $lpoId = $lpoModel->insert([
            'lpo_no'         => $lpoModel->generateLpoNo(),
            'supplier_id'    => $this->request->getPost('supplier_id'),
            'job_card_id'    => $this->request->getPost('job_card_id') ?: null,
            'raised_by'      => session()->get('user_id'),
            'lpo_date'       => $this->request->getPost('lpo_date'),
            'expected_date'  => $this->request->getPost('expected_date') ?: null,
            'notes'          => $this->request->getPost('notes'),
            'status'         => 'Draft',
            'total_amount'   => 0,
        ]);

        if (!$lpoId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create LPO.');
        }

        foreach ($items as $item) {
            if (empty($item['inventory_id']) || empty($item['quantity_ordered'])) continue;
            $lpoItemModel->insert([
                'lpo_id'           => $lpoId,
                'inventory_id'     => $item['inventory_id'],
                'quantity_ordered' => (float) ($item['quantity_ordered'] ?? 1),
                'unit_price'       => (float) ($item['unit_price'] ?? 0),
            ]);
        }

        $lpoModel->recalculateTotal($lpoId);

        $lpo = $lpoModel->find($lpoId);
        $supplierModel = new SupplierModel();
        $supplier = $supplierModel->find($lpo['supplier_id'] ?? 0);
        $supplierName = $supplier['name'] ?? 'Unknown';
        $lpoNo = $lpo['lpo_no'] ?? '';
        log_activity('lpo_created', 'lpo', $lpoId, "LPO {$lpoNo} raised for supplier {$supplierName}");

        return redirect()->to('/admin/lpos')->with('success', 'LPO created successfully.');
    }

    public function view($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $lpoModel = new LpoModel();
        $lpoItemModel = new LpoItemModel();

        $lpo = $lpoModel->getWithDetails($id);
        if (!$lpo) {
            return redirect()->to('/admin/lpos')->with('error', 'LPO not found.');
        }

        $items = $lpoItemModel->getByLpo($id);

        return view('admin/lpos/view', compact('lpo', 'items'));
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $lpoModel = new LpoModel();
        $lpo = $lpoModel->find($id);

        if (!$lpo) {
            return redirect()->to('/admin/lpos')->with('error', 'LPO not found.');
        }

        if ($lpo['status'] !== 'Draft') {
            return redirect()->to('/admin/lpos')->with('error', 'Only Draft LPOs can be edited.');
        }

        $supplierModel = new SupplierModel();
        $jobCardModel = new JobCardModel();
        $inventoryModel = new InventoryModel();
        $lpoItemModel = new LpoItemModel();

        $suppliers = $supplierModel->findAll();
        $job_cards = $jobCardModel->whereNotIn('job_status', ['Completed', 'Cancelled', 'Paid'])->findAll();
        $inventory = $inventoryModel->findAll();
        $items = $lpoItemModel->getByLpo($id);

        return view('admin/lpos/form', [
            'lpo' => $lpo,
            'items' => $items,
            'action' => 'edit',
            'suppliers' => $suppliers,
            'job_cards' => $job_cards,
            'inventory' => $inventory,
        ]);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $lpoModel = new LpoModel();
        $lpo = $lpoModel->find($id);

        if (!$lpo || $lpo['status'] !== 'Draft') {
            return redirect()->to('/admin/lpos')->with('error', 'Only Draft LPOs can be edited.');
        }

        $rules = [
            'supplier_id' => 'required|numeric',
            'lpo_date'    => 'required|valid_date',
            'items'       => 'required|is_array',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items');
        if (empty($items) || !is_array($items) || count($items) < 1) {
            return redirect()->back()->withInput()->with('errors', ['items' => 'At least one line item is required.']);
        }

        $lpoItemModel = new LpoItemModel();

        $lpoModel->update($id, [
            'supplier_id'   => $this->request->getPost('supplier_id'),
            'job_card_id'   => $this->request->getPost('job_card_id') ?: null,
            'lpo_date'      => $this->request->getPost('lpo_date'),
            'expected_date' => $this->request->getPost('expected_date') ?: null,
            'notes'         => $this->request->getPost('notes'),
        ]);

        $lpoItemModel->deleteByLpo($id);

        foreach ($items as $item) {
            if (empty($item['inventory_id']) || empty($item['quantity_ordered'])) continue;
            $lpoItemModel->insert([
                'lpo_id'           => $id,
                'inventory_id'     => $item['inventory_id'],
                'quantity_ordered' => (float) ($item['quantity_ordered'] ?? 1),
                'unit_price'       => (float) ($item['unit_price'] ?? 0),
            ]);
        }

        $lpoModel->recalculateTotal($id);

        return redirect()->to('/admin/lpos')->with('success', 'LPO updated successfully.');
    }

    public function updateStatus($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $lpoModel = new LpoModel();
        $lpo = $lpoModel->find($id);

        if (!$lpo) {
            return $this->failNotFound('LPO not found.');
        }

        $newStatus = $this->request->getPost('new_status');
        $validTransitions = [
            'Draft' => ['Sent', 'Cancelled'],
            'Sent' => ['Partially Received', 'Received', 'Cancelled'],
            'Partially Received' => ['Received', 'Cancelled'],
        ];

        $currentStatus = $lpo['status'];
        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            return $this->fail('Invalid status transition.', 403);
        }

        $lpoModel->update($id, ['status' => $newStatus]);

        return $this->respond(['status' => 'success', 'message' => 'LPO status updated successfully.', 'new_status' => $newStatus]);
    }

    public function receive($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $lpoModel = new LpoModel();
        $lpoItemModel = new LpoItemModel();

        $lpo = $lpoModel->getWithDetails($id);
        if (!$lpo) {
            return redirect()->to('/admin/lpos')->with('error', 'LPO not found.');
        }

        if (!in_array($lpo['status'], ['Sent', 'Partially Received'])) {
            return redirect()->to('/admin/lpos')->with('error', 'Only Sent or Partially Received LPOs can receive items.');
        }

        $items = $lpoItemModel->getByLpo($id);

        return view('admin/lpos/receive', compact('lpo', 'items'));
    }

    public function processReceive($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $lpoModel = new LpoModel();
        $lpoItemModel = new LpoItemModel();
        $inventoryModel = new InventoryModel();

        $lpo = $lpoModel->find($id);
        if (!$lpo) {
            return redirect()->to('/admin/lpos')->with('error', 'LPO not found.');
        }

        if (!in_array($lpo['status'], ['Sent', 'Partially Received'])) {
            return redirect()->to('/admin/lpos')->with('error', 'Only Sent or Partially Received LPOs can receive items.');
        }

        $items = $lpoItemModel->getByLpo($id);
        $receivedQuantities = $this->request->getPost('receive_qty') ?? [];

        $allFullyReceived = true;
        $anyReceived = false;

        foreach ($items as $item) {
            $itemId = $item['id'];
            $qtyNow = (float) ($receivedQuantities[$itemId] ?? 0);

            if ($qtyNow <= 0) {
                if (($item['quantity_received'] ?? 0) < $item['quantity_ordered']) {
                    $allFullyReceived = false;
                }
                continue;
            }

            $maxQty = $item['quantity_ordered'] - ($item['quantity_received'] ?? 0);
            if ($qtyNow > $maxQty) {
                $qtyNow = $maxQty;
            }

            $newReceived = ($item['quantity_received'] ?? 0) + $qtyNow;
            $lpoItemModel->update($itemId, ['quantity_received' => $newReceived]);
            $anyReceived = true;

            if ($newReceived < $item['quantity_ordered']) {
                $allFullyReceived = false;
            }

            $invItem = $inventoryModel->find($item['inventory_id']);
            if ($invItem && $invItem['is_stocked']) {
                $inventoryModel->incrementStock($item['inventory_id'], $qtyNow);
            }
        }

        if (!$anyReceived) {
            return redirect()->back()->with('error', 'No quantities entered for receiving.');
        }

        $newStatus = $allFullyReceived ? 'Received' : 'Partially Received';
        $lpoModel->update($id, ['status' => $newStatus]);

        $lpoRecord = $lpoModel->find($id);
        $lpoNo = $lpoRecord['lpo_no'] ?? '';
        log_activity('lpo_received', 'lpo', $id, "Items received for LPO {$lpoNo}");

        return redirect()->to('/admin/lpos/view/' . $id)->with('success', 'Items received successfully.');
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $lpoModel = new LpoModel();
        $lpo = $lpoModel->find($id);

        if (!$lpo) {
            return $this->failNotFound('LPO not found.');
        }

        if ($lpo['status'] !== 'Draft') {
            return $this->fail('Only Draft LPOs can be deleted.', 403);
        }

        $lpoModel->delete($id);

        return $this->respondDeleted(['status' => 'success', 'message' => 'LPO deleted successfully.']);
    }
}
