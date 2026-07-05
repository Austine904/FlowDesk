<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use CodeIgniter\API\ResponseTrait;

class InventoryController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        return view('admin/inventory/index');
    }

    public function load()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $inventoryModel = new InventoryModel();

        $draw   = $this->request->getPost('draw');
        $start  = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';

        $builder = $inventoryModel->builder();
        $totalRecords = $builder->countAllResults(false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('part_number', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('name', 'ASC');
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

        return view('admin/inventory/form', ['inventory' => null, 'action' => 'add']);
    }

    public function create()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'name'       => 'required',
            'part_number' => 'permit_empty',
            'unit_price'  => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $inventoryModel = new InventoryModel();
        $inventoryModel->insert([
            'name'        => $this->request->getPost('name'),
            'part_number' => $this->request->getPost('part_number'),
            'unit_price'  => $this->request->getPost('unit_price'),
        ]);

        return redirect()->to('/admin/inventory')->with('success', 'Part added successfully.');
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $inventoryModel = new InventoryModel();
        $item = $inventoryModel->find($id);

        if (!$item) {
            return redirect()->to('/admin/inventory')->with('error', 'Part not found.');
        }

        return view('admin/inventory/form', ['inventory' => $item, 'action' => 'edit']);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'name'       => 'required',
            'part_number' => 'permit_empty',
            'unit_price'  => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $inventoryModel = new InventoryModel();
        $inventoryModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'part_number' => $this->request->getPost('part_number'),
            'unit_price'  => $this->request->getPost('unit_price'),
        ]);

        return redirect()->to('/admin/inventory')->with('success', 'Part updated successfully.');
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden.');
        }

        $db = \Config\Database::connect();
        $used = $db->table('job_card_parts_required')
            ->where('inventory_id', $id)
            ->countAllResults();

        if ($used > 0) {
            return $this->fail('Cannot delete this part — it is referenced in job card parts.');
        }

        $inventoryModel = new InventoryModel();
        $inventoryModel->delete($id);

        return $this->respondDeleted(['status' => 'success', 'message' => 'Part deleted successfully.']);
    }

    public function fetch($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->failUnauthorized('Unauthorized.');
        }

        $inventoryModel = new InventoryModel();
        $item = $inventoryModel->find($id);

        if (!$item) {
            return $this->failNotFound('Part not found.');
        }

        return $this->respond($item);
    }

    public function search()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->failUnauthorized('Unauthorized.');
        }

        $query = $this->request->getGet('query');
        if (empty($query)) {
            return $this->respond([]);
        }

        $inventoryModel = new InventoryModel();
        $results = $inventoryModel->search($query);

        return $this->respond($results);
    }
}
