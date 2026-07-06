<?php

namespace App\Models;

use CodeIgniter\Model;

class LpoModel extends Model
{
    protected $table            = 'lpos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'lpo_no',
        'supplier_id',
        'job_card_id',
        'raised_by',
        'lpo_date',
        'expected_date',
        'status',
        'notes',
        'total_amount',
    ];

    public function generateLpoNo(): string
    {
        $prefix = 'LPO-';
        $ym = date('Ym');
        $like = $prefix . $ym . '-';

        $last = $this->select('lpo_no')
            ->where("lpo_no LIKE '{$like}%'")
            ->orderBy('lpo_no', 'DESC')
            ->first();

        if ($last) {
            $suffix = (int) substr($last['lpo_no'], -3) + 1;
        } else {
            $suffix = 1;
        }

        return $prefix . $ym . '-' . str_pad($suffix, 3, '0', STR_PAD_LEFT);
    }

    public function getWithDetails($id = null)
    {
        $builder = $this->builder();
        $builder->select('lpos.*, suppliers.name as supplier_name, users.first_name, users.last_name, users.first_name as raised_by_first_name, users.last_name as raised_by_last_name, job_cards.job_no')
            ->join('suppliers', 'suppliers.id = lpos.supplier_id', 'LEFT')
            ->join('users', 'users.id = lpos.raised_by', 'LEFT')
            ->join('job_cards', 'job_cards.id = lpos.job_card_id', 'LEFT');

        if ($id !== null) {
            $builder->where('lpos.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->orderBy('lpos.id', 'DESC')->get()->getResultArray();
    }

    public function recalculateTotal(int $lpoId): void
    {
        $db = \Config\Database::connect();
        $result = $db->table('lpo_items')
            ->selectSum('line_total', 'total')
            ->where('lpo_id', $lpoId)
            ->get()
            ->getRowArray();

        $total = (float) ($result['total'] ?? 0);
        $this->update($lpoId, ['total_amount' => $total]);
    }
}
