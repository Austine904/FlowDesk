<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\Exceptions\DataException;

class VehicleController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $vehicles = $db->table('vehicles')->get()->getResultArray();
        return view('vehicles/index', ['vehicles' => $vehicles]);
    }

    public function fetchVehicles()
    {

        $db = \Config\Database::connect();
        $builder = $db->table('vehicles');
        $query = $builder->get();
        $result = $query->getResultArray();

        $vehicles = [];
        foreach ($result as $row) {
            $vehicles[] = [
                'id' => $row['id'],
                'registration_number' => $row['registration_number'],
                'owner_id' => $row['owner_id'],
                'vehicle' => $row['make'] . ' ' . $row['model'],
                'color' => $row['color'],
                'status' => $row['status']
            ];
        }

        return $this->response->setJSON(['data' => $vehicles]);
    }
    public function add()
    {
        $vehicleData = [
            'registration_number' => $this->request->getPost('registration_number'),
            'make' => $this->request->getPost('make'),
            'model' => $this->request->getPost('model'),
            'year' => $this->request->getPost('year'),
            'color' => $this->request->getPost('color'),
        ];
        $db = \Config\Database::connect();
        $db->table('vehicles')->insert($vehicleData);
        return view('vehicles/add');
    }

    public function store()
    {
        $data = $this->request->getPost();
        $db = \Config\Database::connect();
        $db->table('vehicles')->insert($data);

        return $this->response->setJSON(['status' => 'success']);
    }
    public function edit($id)
    {
        $db = \Config\Database::connect();
        $vehicle = $db->table('vehicles')->where('id', $id)->get()->getRowArray();

        if ($vehicle) {
            return view('vehicles/edit', ['vehicle' => $vehicle]);
        } else {
            return redirect()->to('/vehicles')->with('error', 'Vehicle not found');
        }
    }
    
    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->table('vehicles')->where('id', $id)->delete();

        return $this->response->setJSON(['status' => 'success']);
    }

    public function details($id)
    {
        $db = db_connect();
        $query = $db->query("SELECT * FROM vehicles WHERE id = ?", [$id]);
        $vehicle = $query->getRowArray();

        if ($vehicle) {
            return $this->response->setJSON($vehicle);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Vehicle not found']);
        }
    }

    public function get($id)
{
    $db = \Config\Database::connect();
    $builder = $db->table('vehicles');
    $vehicle = $builder->where('id', $id)->get()->getRowArray();

    return $this->response->setJSON($vehicle);
}

public function update($id) 
{
    $data = $this->request->getPost();

    $db = \Config\Database::connect();
    $builder = $db->table('vehicles');
    $builder->where('id', $id)->update($data);

    return $this->response->setJSON(['status' => 'success']);
}

}
