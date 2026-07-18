<?php

namespace App\Controllers;

use App\Models\JobCardModel;
use App\Models\CustomerModel;
use App\Models\VehicleModel;

class SearchController extends BaseController
{
    public function index()
    {
        $query = $this->request->getGet('q');
        if (!$query || strlen(trim($query)) < 2) {
            return $this->response->setJSON([]);
        }

        $term = trim($query);

        $results = [];

        $jobModel = new JobCardModel();
        $jobs = $jobModel->select('job_cards.id, job_cards.job_no, customers.name as customer_name, vehicles.registration_number')
            ->join('customers', 'customers.id = job_cards.customer_id', 'left')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->like('job_cards.job_no', $term)
            ->orLike('vehicles.registration_number', $term)
            ->orLike('customers.name', $term)
            ->findAll(5);

        foreach ($jobs as $j) {
            $results[] = [
                'url'     => 'admin/jobs/' . $j['id'],
                'icon'    => '&#x1f4cb;',
                'label'   => $j['job_no'],
                'subtext' => ($j['customer_name'] ?? '') . ' — ' . ($j['registration_number'] ?? ''),
            ];
        }

        $customerModel = new CustomerModel();
        $customers = $customerModel->searchByPhoneOrName($term);
        foreach (array_slice($customers, 0, 5) as $c) {
            $results[] = [
                'url'     => 'admin/customers/edit/' . $c['id'],
                'icon'    => '&#x1f464;',
                'label'   => $c['name'],
                'subtext' => $c['phone'] ?? '',
            ];
        }

        $vehicleModel = new VehicleModel();
        $vehicles = $vehicleModel->searchByTerm($term);
        foreach (array_slice($vehicles, 0, 5) as $v) {
            $results[] = [
                'url'     => 'admin/vehicles/edit/' . $v['id'],
                'icon'    => '&#x1f697;',
                'label'   => $v['registration_number'],
                'subtext' => ($v['make'] ?? '') . ' ' . ($v['model'] ?? ''),
            ];
        }

        return $this->response->setJSON($results);
    }
}
