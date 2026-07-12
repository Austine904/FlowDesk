<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VehicleModel;
use App\Models\CustomerModel;

class VehicleController extends BaseController
{
    public function index()
    {
        $vehicleModel = new VehicleModel();
        $vehicles = $vehicleModel->paginate(20);
        $pager = $vehicleModel->pager;
        return view('vehicles/index', ['vehicles' => $vehicles, 'pager' => $pager]);
    }

    public function fetchVehicles()
    {
        $vehicleModel = new VehicleModel();
        $result = $vehicleModel->findAll();

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
        $vehicleModel = new VehicleModel();
        $vehicleModel->insert($vehicleData);
        return view('vehicles/add');
    }

    public function store()
    {
        if (!$this->validate([
            'registration_number' => 'required|min_length[3]|max_length[20]|is_unique[vehicles.registration_number]',
            'make'               => 'required',
            'model'              => 'required',
            'owner_id'           => 'required|integer',
        ])) {
            return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
        }

        $data = $this->request->getPost();
        $vehicleModel = new VehicleModel();
        $vehicleModel->insert($data);

        return $this->response->setJSON(['status' => 'success']);
    }
    public function edit($id)
    {
        $vehicleModel = new VehicleModel();
        $vehicle = $vehicleModel->find($id);

        if ($vehicle) {
            return view('vehicles/edit', ['vehicle' => $vehicle]);
        } else {
            return redirect()->to('/vehicles')->with('error', 'Vehicle not found');
        }
    }

    public function delete($id)
    {
        $vehicleModel = new VehicleModel();
        $vehicleModel->delete($id);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function details($id)
    {
        $vehicleModel = new VehicleModel();
        $vehicle = $vehicleModel->find($id);

        if ($vehicle) {
            return $this->response->setJSON($vehicle);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Vehicle not found']);
        }
    }

    public function get($id)
    {
        $vehicleModel = new VehicleModel();
        $vehicle = $vehicleModel->find($id);

        return $this->response->setJSON($vehicle);
    }

    public function update($id)
    {
        $data = $this->request->getPost();

        $vehicleModel = new VehicleModel();
        $vehicleModel->update($id, $data);

        return $this->response->setJSON(['status' => 'success']);
    }

}
