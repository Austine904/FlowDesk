<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCardLaborModel extends Model
{
    protected $table            = 'job_card_labor_tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'job_card_id',
        'task_name',
        'estimated_hours',
        'rate_per_hour',
        'notes',
    ];

    public function getByJobCard(int $job_card_id): array
    {
        return $this->select('job_card_labor_tasks.*, (estimated_hours * rate_per_hour) AS labor_cost')
            ->where('job_card_id', $job_card_id)
            ->findAll();
    }

    public function deleteByJobCard(int $job_card_id): void
    {
        $this->where('job_card_id', $job_card_id)->delete();
    }
}
