<?php

namespace App\Models;

use CodeIgniter\Model;

class SubletModel extends Model
{
    protected $table            = 'sublets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'job_card_id',
        'sublet_provider_id',
        'description',
        'cost',
        'status',
        'date_sent',
        'date_returned',
        'notes',
    ];

    public function getWithDetails(int $id = null): array
    {
        $this->select('sublets.*, job_cards.job_no, vehicles.registration_number, suppliers.name AS provider_name')
            ->join('job_cards', 'job_cards.id = sublets.job_card_id', 'left')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->join('suppliers', 'suppliers.id = sublets.sublet_provider_id', 'left');

        if ($id !== null) {
            $this->where('sublets.id', $id);
            $result = $this->first();
            return $result ? [$result] : [];
        }

        return $this->findAll();
    }
}
