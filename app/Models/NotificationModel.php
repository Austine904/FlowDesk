<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'user_id',
        'type',
        'title',
        'message',
        'related_type',
        'related_id',
        'is_read',
        'created_at',
    ];

    public function getForUser(int $userId, bool $unreadOnly = false): array
    {
        $this->where('user_id', $userId);
        if ($unreadOnly) {
            $this->where('is_read', 0);
        }
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function markRead(int $notificationId): bool
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }

    public function markAllRead(int $userId): bool
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();
    }

    public function notify(int $userId, string $type, string $title, string $message, ?string $relatedType = null, ?int $relatedId = null): int
    {
        return $this->insert([
            'user_id'      => $userId,
            'type'         => $type,
            'title'        => $title,
            'message'      => $message,
            'related_type' => $relatedType,
            'related_id'   => $relatedId,
            'is_read'      => 0,
        ]);
    }
}
