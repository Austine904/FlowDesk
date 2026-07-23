<?php

namespace App\Models;

use CodeIgniter\Model;

class OutgoingPaymentModel extends Model
{
    protected $table            = 'outgoing_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payment_ref', 'payment_type', 'source_type', 'source_id',
        'supplier_id', 'payee_id', 'payee_type', 'amount', 'payment_method',
        'reference_no', 'payment_date', 'status', 'notes',
        'raised_by', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generatePaymentRef(): string
    {
        $prefix = 'OPY-' . date('Ym') . '-';
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
            outgoing_payments.*,
            suppliers.name AS supplier_name
        ')
        ->join('suppliers', 'suppliers.id = outgoing_payments.supplier_id', 'left');

        if ($id !== null) {
            $builder->where('outgoing_payments.id', $id);
            $result = $builder->get()->getRowArray();
            if ($result) {
                $result = $this->enrichWithNames($result);
                $result = $this->enrichWithSourceRef($result);
            }
            return $result ?: [];
        }

        $results = $builder->orderBy('outgoing_payments.created_at', 'DESC')->get()->getResultArray();
        foreach ($results as &$row) {
            $row = $this->enrichWithNames($row);
            $row = $this->enrichWithSourceRef($row);
        }
        return $results;
    }

    public function enrichWithNames(array $row): array
    {
        $db = \Config\Database::connect();

        $raiser = $db->table('users')->select("CONCAT(first_name, ' ', last_name) AS name")->where('id', $row['raised_by'])->get()->getRowArray();
        $row['raised_by_name'] = $raiser['name'] ?? '';

        if (!empty($row['approved_by'])) {
            $approver = $db->table('users')->select("CONCAT(first_name, ' ', last_name) AS name")->where('id', $row['approved_by'])->get()->getRowArray();
            $row['approved_by_name'] = $approver['name'] ?? '';
        } else {
            $row['approved_by_name'] = '';
        }

        if (!empty($row['payee_id']) && $row['payee_type'] === 'Staff') {
            $payee = $db->table('users')->select("CONCAT(first_name, ' ', last_name) AS name")->where('id', $row['payee_id'])->get()->getRowArray();
            $row['payee_user_name'] = $payee['name'] ?? '';
        } else {
            $row['payee_user_name'] = '';
        }

        if ($row['payee_type'] === 'Supplier' && !empty($row['supplier_id'])) {
            $row['payee_name'] = $row['supplier_name'] ?? '';
        } elseif ($row['payee_type'] === 'Staff') {
            $row['payee_name'] = $row['payee_user_name'] ?? '';
        } elseif ($row['payee_type'] === 'Other') {
            $row['payee_name'] = 'Other';
        } else {
            $row['payee_name'] = $row['supplier_name'] ?? '';
        }

        return $row;
    }

    public function enrichWithSourceRef(array $row): array
    {
        $db = \Config\Database::connect();

        if ($row['source_type'] === 'lpos' && !empty($row['source_id'])) {
            $src = $db->table('lpos')->select('lpo_no')->where('id', $row['source_id'])->get()->getRowArray();
            $row['source_ref'] = $src['lpo_no'] ?? '';
        } elseif ($row['source_type'] === 'sublets' && !empty($row['source_id'])) {
            $src = $db->table('sublets')->select('description')->where('id', $row['source_id'])->get()->getRowArray();
            $row['source_ref'] = !empty($src['description']) ? substr($src['description'], 0, 50) : '';
        } else {
            $row['source_ref'] = '';
        }

        return $row;
    }

    public function getPendingApprovals(): array
    {
        $results = $this->where('status', 'Pending Approval')
            ->orderBy('created_at', 'ASC')
            ->findAll();

        foreach ($results as &$row) {
            $row = $this->enrichWithNames($row);
            $row = $this->enrichWithSourceRef($row);
        }
        return $results;
    }

    public function getBySource(string $source_type, int $source_id): array
    {
        return $this->where('source_type', $source_type)
            ->where('source_id', $source_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getTotalPaidForSource(string $source_type, int $source_id): float
    {
        $result = $this->select('COALESCE(SUM(amount), 0) AS total')
            ->where('source_type', $source_type)
            ->where('source_id', $source_id)
            ->where('status', 'Paid')
            ->get()
            ->getRowArray();

        return (float) ($result['total'] ?? 0);
    }

    public function validateGate(string $payment_type, ?string $source_type, ?int $source_id): array
    {
        $payment_type = strtolower($payment_type);

        if ($payment_type === 'lpo') {
            $lpo = \Config\Database::connect()->table('lpos')->where('id', $source_id)->get()->getRowArray();
            if (!$lpo) return ['valid' => false, 'message' => 'LPO not found.'];
            if ($lpo['status'] !== 'Received') return ['valid' => false, 'message' => 'LPO must be Received. Current: ' . $lpo['status']];
            $existing = $this->where('source_type', 'lpos')->where('source_id', $source_id)->whereIn('status', ['Pending Approval', 'Approved'])->countAllResults();
            if ($existing > 0) return ['valid' => false, 'message' => 'A payment is already pending for this LPO.'];
            return ['valid' => true, 'message' => ''];
        }

        if ($payment_type === 'sublet') {
            $sublet = \Config\Database::connect()->table('sublets')->where('id', $source_id)->get()->getRowArray();
            if (!$sublet) return ['valid' => false, 'message' => 'Sublet not found.'];
            if ($sublet['status'] !== 'Completed') return ['valid' => false, 'message' => 'Sublet must be Completed. Current: ' . $sublet['status']];
            $existing = $this->where('source_type', 'sublets')->where('source_id', $source_id)->whereIn('status', ['Pending Approval', 'Approved'])->countAllResults();
            if ($existing > 0) return ['valid' => false, 'message' => 'A payment already exists for this sublet.'];
            return ['valid' => true, 'message' => ''];
        }

        if ($payment_type === 'expense') {
            return ['valid' => false, 'message' => 'Expense payments coming soon. Use Petty Cash for now.'];
        }

        if ($payment_type === 'staff reimbursement') {
            return ['valid' => false, 'message' => 'Staff reimbursement coming soon.'];
        }

        if ($payment_type === 'ad-hoc') {
            return ['valid' => true, 'message' => ''];
        }

        return ['valid' => false, 'message' => 'Unknown payment type.'];
    }
}
