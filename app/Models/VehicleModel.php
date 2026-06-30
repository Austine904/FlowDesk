<?php

namespace App\Models;

use CodeIgniter\Model;

class VehicleModel extends Model
{
    protected $table            = 'vehicles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'owner_id',
        'registration_number',
        'vin',
        'make',
        'model',
        'year_of_manufacture',
        'engine_number',
        'chassis_number',
        'fuel_type',
        'transmission',
        'color',
        'mileage',
        'reported_problem',
        'status',
    ];

    public function getByOwner(int $owner_id): array
    {
        return $this->where('owner_id', $owner_id)->findAll();
    }

    public function getByRegistration(string $reg): ?array
    {
        return $this->where('registration_number', $reg)->first();
    }

    public function searchByTerm(string $term): array
    {
        return $this->groupStart()
            ->like('registration_number', $term)
            ->orLike('vin', $term)
            ->orLike('chassis_number', $term)
            ->groupEnd()
            ->findAll();
    }
}
