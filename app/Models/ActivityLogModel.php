<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
    ];

    public function log(int $user_id, string $action, string $entity_type, $entity_id, string $description): int
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $this->insert([
            'user_id'     => $user_id,
            'action'      => $action,
            'entity_type' => $entity_type,
            'entity_id'   => $entity_id,
            'description' => $description,
            'ip_address'  => $ip,
        ]);

        return (int) $this->getInsertID();
    }

    public function getRecent(int $limit = 50): array
    {
        return $this->select('activity_log.*, CONCAT(users.first_name, " ", users.last_name) AS user_name')
            ->join('users', 'users.id = activity_log.user_id', 'left')
            ->orderBy('activity_log.created_at', 'DESC')
            ->findAll($limit);
    }

    public function getByUser(int $user_id, int $limit = 50): array
    {
        return $this->select('activity_log.*, CONCAT(users.first_name, " ", users.last_name) AS user_name')
            ->join('users', 'users.id = activity_log.user_id', 'left')
            ->where('activity_log.user_id', $user_id)
            ->orderBy('activity_log.created_at', 'DESC')
            ->findAll($limit);
    }

    public function getByEntity(string $entity_type, int $entity_id): array
    {
        return $this->select('activity_log.*, CONCAT(users.first_name, " ", users.last_name) AS user_name')
            ->join('users', 'users.id = activity_log.user_id', 'left')
            ->where('activity_log.entity_type', $entity_type)
            ->where('activity_log.entity_id', $entity_id)
            ->orderBy('activity_log.created_at', 'DESC')
            ->findAll();
    }

    public function getByPeriod(string $start_date, string $end_date): array
    {
        return $this->select('activity_log.*, CONCAT(users.first_name, " ", users.last_name) AS user_name')
            ->join('users', 'users.id = activity_log.user_id', 'left')
            ->where('activity_log.created_at >=', $start_date)
            ->where('activity_log.created_at <=', $end_date)
            ->orderBy('activity_log.created_at', 'DESC')
            ->findAll();
    }
}