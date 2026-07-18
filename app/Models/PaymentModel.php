<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'invoice_id',
        'amount',
        'payment_method',
        'reference_no',
        'payment_date',
        'received_by',
        'notes',
    ];

    public function getByInvoice(int $invoice_id): array
    {
        return $this->select('payments.*, CONCAT(users.first_name, " ", users.last_name) AS received_by_name')
            ->join('users', 'users.id = payments.received_by', 'left')
            ->where('payments.invoice_id', $invoice_id)
            ->orderBy('payments.payment_date', 'ASC')
            ->findAll();
    }

    public function getMonthlyRevenue(int $months = 6): array
    {
        $db = \Config\Database::connect();
        return $db->table('payments')
            ->select("YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount) as total")
            ->where('payment_date >=', date('Y-m-d', strtotime("-{$months} months")))
            ->groupBy('YEAR(payment_date), MONTH(payment_date)')
            ->orderBy('YEAR(payment_date)', 'ASC')
            ->orderBy('MONTH(payment_date)', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getThisMonthRevenue(): float
    {
        $result = $this->selectSum('amount', 'total')
            ->where('payment_date >=', date('Y-m-01'))
            ->where('payment_date <=', date('Y-m-t'))
            ->get()
            ->getRowArray();
        return (float) ($result['total'] ?? 0);
    }
}
