<?php

namespace App\Models;

use CodeIgniter\Model;

class LpoItemModel extends Model
{
    protected $table            = 'lpo_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'lpo_id',
        'inventory_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
    ];

    public function getByLpo(int $lpoId): array
    {
        return $this->select('lpo_items.*, inventory.name, inventory.part_number, inventory.unit')
            ->join('inventory', 'inventory.id = lpo_items.inventory_id', 'LEFT')
            ->where('lpo_items.lpo_id', $lpoId)
            ->orderBy('lpo_items.id', 'ASC')
            ->findAll();
    }

    public function deleteByLpo(int $lpoId): void
    {
        $this->where('lpo_id', $lpoId)->delete();
    }
}
