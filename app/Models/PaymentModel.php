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
}
