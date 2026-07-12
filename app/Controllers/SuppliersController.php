<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use CodeIgniter\API\ResponseTrait;

class SuppliersController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        return view('admin/suppliers/index');
    }

    public function load()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $supplierModel = new SupplierModel();

        $draw   = $this->request->getPost('draw');
        $start  = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';

        $builder = $supplierModel->builder();
        $totalRecords = $builder->countAllResults(false);

        if (!empty($search)) {
            $builder->like('name', $search);
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

        return view('admin/suppliers/form', ['supplier' => null, 'action' => 'add']);
    }

    public function create()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $rules = [
            'name' => 'required',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->failValidationErrors($this->validator->getErrors());
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supplierModel = new SupplierModel();
        $supplierModel->insert([
            'name' => $this->request->getPost('name'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->respond(['status' => 'success', 'message' => 'Supplier added successfully.']);
        }

        return redirect()->to('/admin/suppliers')->with('success', 'Supplier added successfully.');
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $supplierModel = new SupplierModel();
        $item = $supplierModel->find($id);

        if (!$item) {
            return $this->failNotFound('Supplier not found.');
        }

        if ($this->request->isAJAX()) {
            return $this->respond($item);
        }

        return view('admin/suppliers/form', ['supplier' => $item, 'action' => 'edit']);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $rules = [
            'name' => 'required',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->failValidationErrors($this->validator->getErrors());
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supplierModel = new SupplierModel();
        $supplierModel->update($id, [
            'name' => $this->request->getPost('name'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->respond(['status' => 'success', 'message' => 'Supplier updated successfully.']);
        }

        return redirect()->to('/admin/suppliers')->with('success', 'Supplier updated successfully.');
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failForbidden('Forbidden.');
        }

        $db = \Config\Database::connect();
        $used = $db->table('sublets')
            ->where('sublet_provider_id', $id)
            ->countAllResults();

        if ($used > 0) {
            return $this->fail('Cannot delete this supplier — it is referenced in sublets.');
        }

        $supplierModel = new SupplierModel();
        $supplierModel->delete($id);

        return $this->respondDeleted(['status' => 'success', 'message' => 'Supplier deleted successfully.']);
    }

    public function getAll()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->failUnauthorized('Unauthorized.');
        }

        $supplierModel = new SupplierModel();
        $suppliers = $supplierModel->getAll();

        return $this->respond($suppliers);
    }
}
