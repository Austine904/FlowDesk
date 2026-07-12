<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarEventModel extends Model
{
    protected $table            = 'calendar_events';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    protected $allowedFields = [
        'title',
        'description',
        'start_time',
        'end_time',
        'all_day',
        'event_type',
        'color',
        'related_table',
        'related_id',
        'priority',
        'created_by_user_id',
    ];

    public function getUpcoming(int $limit = 10): array
    {
        return $this->where('start_time >=', date('Y-m-d H:i:s'))
            ->orderBy('start_time', 'ASC')
            ->findAll($limit);
    }

    public function getByDateRange(string $start, string $end): array
    {
        return $this->where('start_time >=', $start)
            ->where('start_time <=', $end)
            ->orderBy('start_time', 'ASC')
            ->findAll();
    }
}
