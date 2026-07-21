<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierPaymentModel extends Model
{
    protected $table            = 'supplier_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payment_ref', 'lpo_id', 'supplier_id', 'amount', 'payment_method',
        'account_id', 'reference_no', 'payment_date', 'status', 'notes',
        'raised_by', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generatePaymentRef(): string
    {
        $prefix = 'SPY-' . date('Ym') . '-';
        $last = $this->select('payment_ref')
            ->like('payment_ref', $prefix, 'after')
            ->orderBy('payment_ref', 'DESC')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last['payment_ref'], -3);
            $newNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNum = '001';
        }

        return $prefix . $newNum;
    }

    public function getWithDetails($id = null): array
    {
        $builder = $this->builder();
        $builder->select('
            supplier_payments.*,
            suppliers.name AS supplier_name,
            lpos.lpo_no,
            lpos.total_amount AS lpo_total,
            raised.first_name AS raised_by_first_name,
            raised.last_name AS raised_by_last_name,
            approved.first_name AS approved_by_first_name,
            approved.last_name AS approved_by_last_name
        ')
        ->join('suppliers', 'suppliers.id = supplier_payments.supplier_id', 'left')
        ->join('lpos', 'lpos.id = supplier_payments.lpo_id', 'left')
        ->join('users AS raised', 'raised.id = supplier_payments.raised_by', 'left')
        ->join('users AS approved', 'approved.id = supplier_payments.approved_by', 'left');

        if ($id !== null) {
            $builder->where('supplier_payments.id', $id);
            $result = $builder->get()->getRowArray();
            if ($result) {
                $result['raised_by_name'] = trim(($result['raised_by_first_name'] ?? '') . ' ' . ($result['raised_by_last_name'] ?? ''));
                $result['approved_by_name'] = trim(($result['approved_by_first_name'] ?? '') . ' ' . ($result['approved_by_last_name'] ?? ''));
            }
            return $result ?: [];
        }

        $results = $builder->get()->getResultArray();
        foreach ($results as &$row) {
            $row['raised_by_name'] = trim(($row['raised_by_first_name'] ?? '') . ' ' . ($row['raised_by_last_name'] ?? ''));
            $row['approved_by_name'] = trim(($row['approved_by_first_name'] ?? '') . ' ' . ($row['approved_by_last_name'] ?? ''));
        }
        return $results;
    }

    public function getPendingApprovals(): array
    {
        $builder = $this->builder();
        $builder->select('
            supplier_payments.*,
            suppliers.name AS supplier_name,
            lpos.lpo_no,
            lpos.total_amount AS lpo_total,
            raised.first_name AS raised_by_first_name,
            raised.last_name AS raised_by_last_name
        ')
        ->join('suppliers', 'suppliers.id = supplier_payments.supplier_id', 'left')
        ->join('lpos', 'lpos.id = supplier_payments.lpo_id', 'left')
        ->join('users AS raised', 'raised.id = supplier_payments.raised_by', 'left')
        ->where('supplier_payments.status', 'Pending Approval')
        ->orderBy('supplier_payments.created_at', 'ASC');

        $results = $builder->get()->getResultArray();
        foreach ($results as &$row) {
            $row['raised_by_name'] = trim(($row['raised_by_first_name'] ?? '') . ' ' . ($row['raised_by_last_name'] ?? ''));
        }
        return $results;
    }

    public function getByLpo(int $lpo_id): array
    {
        return $this->where('lpo_id', $lpo_id)->orderBy('created_at', 'DESC')->findAll();
    }

    public function getTotalPaidForLpo(int $lpo_id): float
    {
        $result = $this->select('COALESCE(SUM(amount), 0) AS total')
            ->where('lpo_id', $lpo_id)
            ->where('status', 'Paid')
            ->get()
            ->getRowArray();

        return (float) ($result['total'] ?? 0);
    }
}
