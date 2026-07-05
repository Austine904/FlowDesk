<?php

namespace App\Models;

use CodeIgniter\Model;

class JobStatusHistoryModel extends Model
{
    protected $table            = 'job_status_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'job_card_id',
        'from_status',
        'to_status',
        'changed_by',
        'notes',
    ];

    public function getByJobCard(int $job_card_id): array
    {
        return $this->select('job_status_history.*, CONCAT(users.first_name, " ", users.last_name) AS changed_by_name')
            ->join('users', 'users.id = job_status_history.changed_by', 'left')
            ->where('job_status_history.job_card_id', $job_card_id)
            ->orderBy('job_status_history.created_at', 'ASC')
            ->findAll();
    }
}
