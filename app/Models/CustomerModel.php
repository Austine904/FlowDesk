<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';
    protected $dateFormat       = 'datetime';

    protected $allowedFields = [
        'name',
        'phone',
        'email',
        'address',
    ];

    public function searchByPhoneOrName(string $term): array
    {
        return $this->groupStart()
            ->like('phone', $term)
            ->orLike('name', $term)
            ->groupEnd()
            ->findAll();
    }

    public function getWithVehicleCount(): array
    {
        return $this->select('customers.*, COUNT(vehicles.id) AS vehicle_count')
            ->join('vehicles', 'vehicles.owner_id = customers.id', 'left')
            ->groupBy('customers.id')
            ->findAll();
    }
}
