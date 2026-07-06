<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCardModel extends Model
{
    protected $table            = 'job_cards';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'job_no',
        'customer_id',
        'vehicle_id',
        'date_in',
        'time_in',
        'start_date',
        'end_date',
        'diagnosis',
        'diagnosis_category',
        'initial_damage_notes',
        'job_status',
        'mileage_in',
        'fuel_level',
        'estimated_labor_hours',
        'assigned_service_advisor_id',
        'assigned_mechanic_id',
        'job_summary',
        'quote_amount',
        'quote_status',
        'completed_at',
    ];

    public function getWithDetails(int $id): ?array
    {
        return $this->select('job_cards.*, customers.name AS customer_name, vehicles.registration_number, CONCAT(users.first_name, " ", users.last_name) AS advisor_name, CONCAT(mechanic.first_name, " ", mechanic.last_name) AS mechanic_name')
            ->join('customers', 'customers.id = job_cards.customer_id', 'left')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->join('users', 'users.id = job_cards.assigned_service_advisor_id', 'left')
            ->join('users as mechanic', 'mechanic.id = job_cards.assigned_mechanic_id', 'left')
            ->where('job_cards.id', $id)
            ->first();
    }

    public function getByStatus(string $status): array
    {
        return $this->select('job_cards.*, CONCAT(mechanic.first_name, " ", mechanic.last_name) AS mechanic_name')
            ->join('users as mechanic', 'mechanic.id = job_cards.assigned_mechanic_id', 'left')
            ->where('job_status', $status)
            ->findAll();
    }

    public function getAssignedToMechanic(int $mechanic_id): array
    {
        return $this->select('job_cards.*, customers.name AS customer_name, vehicles.registration_number')
            ->join('customers', 'customers.id = job_cards.customer_id', 'left')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->where('job_cards.assigned_mechanic_id', $mechanic_id)
            ->orderBy('job_cards.date_in', 'DESC')
            ->findAll();
    }

    public function getRecentJobs(int $limit = 10): array
    {
        return $this->select('job_cards.*, customers.name AS customer_name, vehicles.registration_number')
            ->join('customers', 'customers.id = job_cards.customer_id', 'left')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->orderBy('job_cards.created_at', 'DESC')
            ->findAll($limit);
    }

    public function generateJobNo(): string
    {
        $prefix = 'JOB-' . date('Ymd') . '-';

        $last = $this->select('job_no')
            ->where("job_no LIKE '$prefix%'")
            ->orderBy('job_no', 'DESC')
            ->first();

        if ($last) {
            $suffix = (int) substr($last['job_no'], -3) + 1;
        } else {
            $suffix = 1;
        }

        return $prefix . str_pad((string) $suffix, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusHistory(int $job_card_id): array
    {
        $historyModel = new \App\Models\JobStatusHistoryModel();
        return $historyModel->getByJobCard($job_card_id);
    }
}
