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
        'is_stocked',
        'quantity_in_hand',
        'reorder_level',
        'unit',
    ];

    public function search(string $term): array
    {
        return $this->select('id, name, part_number, unit_price, is_stocked, quantity_in_hand, reorder_level, unit')
            ->groupStart()
            ->like('name', $term)
            ->orLike('part_number', $term)
            ->groupEnd()
            ->findAll();
    }

    public function getLowStock(): array
    {
        return $this->where('is_stocked', 1)
            ->where('quantity_in_hand <= reorder_level')
            ->orderBy('(quantity_in_hand - reorder_level)', 'ASC')
            ->findAll();
    }

    public function incrementStock(int $id, float $quantity): void
    {
        $this->builder()
            ->set('quantity_in_hand', 'quantity_in_hand + ' . (float)$quantity, false)
            ->where('id', $id)
            ->update();
    }

    public function decrementStock(int $id, float $quantity): void
    {
        $this->builder()
            ->set('quantity_in_hand', 'GREATEST(quantity_in_hand - ' . (float)$quantity . ', 0)', false)
            ->where('id', $id)
            ->update();
    }
}
