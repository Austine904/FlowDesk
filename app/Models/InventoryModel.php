<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table            = 'inventory';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'name',
        'part_number',
        'unit_price',
    ];

    public function search(string $term): array
    {
        return $this->groupStart()
            ->like('name', $term)
            ->orLike('part_number', $term)
            ->groupEnd()
            ->findAll();
    }
}
