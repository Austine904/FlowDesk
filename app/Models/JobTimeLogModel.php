<?php

namespace App\Models;

use CodeIgniter\Model;

class JobTimeLogModel extends Model
{
    protected $table            = 'job_time_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'job_card_id',
        'mechanic_id',
        'clock_in',
        'clock_out',
        'duration_minutes',
        'notes',
    ];

    public function getOpenLogForMechanic(int $jobCardId, int $mechanicId): ?array
    {
        $row = $this->where('job_card_id', $jobCardId)
            ->where('mechanic_id', $mechanicId)
            ->where('clock_out IS NULL')
            ->first();
        return $row ?: null;
    }

    public function getLogsForJobCard(int $jobCardId): array
    {
        return $this->where('job_card_id', $jobCardId)
            ->orderBy('clock_in', 'DESC')
            ->findAll();
    }

    public function clockIn(int $jobCardId, int $mechanicId): int
    {
        $existing = $this->getOpenLogForMechanic($jobCardId, $mechanicId);
        if ($existing) {
            return (int) $existing['id'];
        }

        return $this->insert([
            'job_card_id'   => $jobCardId,
            'mechanic_id'   => $mechanicId,
            'clock_in'      => date('Y-m-d H:i:s'),
        ]);
    }

    public function clockOut(int $logId): bool
    {
        $log = $this->find($logId);
        if (!$log || $log['clock_out'] !== null) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $clockIn = new \DateTime($log['clock_in']);
        $clockOut = new \DateTime($now);
        $duration = (int) $clockIn->diff($clockOut)->format('%i') + ($clockIn->diff($clockOut)->h * 60);

        return $this->update($logId, [
            'clock_out'       => $now,
            'duration_minutes' => $duration,
        ]);
    }
}
