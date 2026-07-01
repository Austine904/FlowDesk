<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCardPartModel extends Model
{
    protected $table            = 'job_card_parts_required';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'job_card_id',
        'inventory_id',
        'quantity_required',
        'unit_price_at_estimate',
    ];

    public function getByJobCard(int $job_card_id): array
    {
        return $this->select('job_card_parts_required.*, inventory.name, inventory.part_number')
            ->join('inventory', 'inventory.id = job_card_parts_required.inventory_id', 'left')
            ->where('job_card_parts_required.job_card_id', $job_card_id)
            ->findAll();
    }

    public function deleteByJobCard(int $job_card_id): void
    {
        $this->where('job_card_id', $job_card_id)->delete();
    }
}
