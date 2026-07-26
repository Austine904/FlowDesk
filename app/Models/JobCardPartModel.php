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
        'status',
        'requested_at',
        'requested_note',
        'requested_by',
    ];

    public function getByJobCard(int $job_card_id): array
    {
        return $this->select('job_card_parts_required.*, inventory.name, inventory.part_number, inventory.is_stocked, inventory.quantity_in_hand')
            ->join('inventory', 'inventory.id = job_card_parts_required.inventory_id', 'left')
            ->where('job_card_parts_required.job_card_id', $job_card_id)
            ->findAll();
    }

    public function deleteByJobCard(int $job_card_id): void
    {
        $this->where('job_card_id', $job_card_id)->delete();
    }

    public function getRequestedParts(): array
    {
        return $this->select('job_card_parts_required.*, inventory.name, inventory.part_number, inventory.unit_price, job_cards.job_no, CONCAT(users.first_name, " ", users.last_name) as requested_by_name')
            ->join('inventory', 'inventory.id = job_card_parts_required.inventory_id', 'left')
            ->join('job_cards', 'job_cards.id = job_card_parts_required.job_card_id', 'left')
            ->join('users', 'users.id = job_card_parts_required.requested_by', 'left')
            ->where('job_card_parts_required.status', 'Pending')
            ->orderBy('job_card_parts_required.requested_at', 'DESC')
            ->findAll();
    }

    public function requestPart(int $partId, int $jobCardId, int $userId, string $note = ''): bool
    {
        return $this->update($partId, [
            'status'       => 'Pending',
            'requested_at' => date('Y-m-d H:i:s'),
            'requested_note' => $note ?: null,
            'requested_by' => $userId,
        ]);
    }
}
