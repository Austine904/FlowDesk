<?php

namespace App\Models;

use CodeIgniter\Model;

class ReceiptModel extends Model
{
    protected $table            = 'receipts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'receipt_no',
        'payment_id',
        'invoice_id',
        'customer_id',
        'job_card_id',
        'receipt_date',
        'amount_paid',
        'payment_method',
        'reference_no',
        'balance_after',
        'org_name',
        'org_address',
        'org_phone',
        'org_email',
        'org_logo',
        'vat_rate',
        'parts_total',
        'labor_total',
        'sublet_total',
        'subtotal',
        'vat_amount',
        'grand_total',
        'amount_in_words',
        'received_by_name',
        'notes',
        'generated_by',
        'created_at',
    ];

    public function generateReceiptNo(): string
    {
        $prefix = 'RCT-';
        $ym = date('Ym');
        $like = $prefix . $ym . '-';

        $last = $this->select('receipt_no')
            ->where("receipt_no LIKE '{$like}%'")
            ->orderBy('receipt_no', 'DESC')
            ->first();

        if ($last) {
            $suffix = (int) substr($last['receipt_no'], -3) + 1;
        } else {
            $suffix = 1;
        }

        return $prefix . $ym . '-' . str_pad((string) $suffix, 3, '0', STR_PAD_LEFT);
    }

    public function generateFromPayment(int $payment_id, int $generated_by): int
    {
        $existing = $this->where('payment_id', $payment_id)->first();
        if ($existing) {
            return (int) $existing['id'];
        }

        $db = \Config\Database::connect();

        $payment = $db->table('payments')
            ->select('payments.*, invoices.invoice_no, invoices.customer_id, invoices.job_card_id, invoices.parts_total, invoices.labor_total, invoices.sublet_total, invoices.subtotal, invoices.vat_rate, invoices.vat_amount, invoices.grand_total, invoices.amount_paid, invoices.status, customers.name AS customer_name, customers.phone AS customer_phone, job_cards.job_no, vehicles.registration_number, CONCAT(users.first_name, " ", users.last_name) AS received_by_name')
            ->join('invoices', 'invoices.id = payments.invoice_id')
            ->join('customers', 'customers.id = invoices.customer_id')
            ->join('job_cards', 'job_cards.id = invoices.job_card_id', 'left')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->join('users', 'users.id = payments.received_by', 'left')
            ->where('payments.id', $payment_id)
            ->get()
            ->getRowArray();

        if (!$payment) {
            throw new \RuntimeException('Payment not found: ' . $payment_id);
        }

        $actualAmountPaid = (float) ($payment['amount_paid'] ?? 0);
        $grandTotal = (float) ($payment['grand_total'] ?? 0);
        $balanceAfter = $grandTotal - $actualAmountPaid;
        if ($balanceAfter < 0) {
            $balanceAfter = 0;
        }

        $amountInWords = number_to_words((float) $payment['amount']);

        $receiptNo = $this->generateReceiptNo();

        $data = [
            'receipt_no'       => $receiptNo,
            'payment_id'       => $payment_id,
            'invoice_id'       => $payment['invoice_id'],
            'customer_id'      => $payment['customer_id'],
            'job_card_id'      => $payment['job_card_id'] ?? 0,
            'receipt_date'     => $payment['payment_date'],
            'amount_paid'      => $payment['amount'],
            'payment_method'   => $payment['payment_method'],
            'reference_no'     => $payment['reference_no'] ?? null,
            'balance_after'    => $balanceAfter,
            'org_name'         => org_setting('org_name', ''),
            'org_address'      => org_setting('org_address', ''),
            'org_phone'        => org_setting('org_phone', ''),
            'org_email'        => org_setting('org_email', ''),
            'org_logo'         => org_setting('org_logo', ''),
            'vat_rate'         => (float) ($payment['vat_rate'] ?? org_setting('vat_rate', 16)),
            'parts_total'      => (float) ($payment['parts_total'] ?? 0),
            'labor_total'      => (float) ($payment['labor_total'] ?? 0),
            'sublet_total'     => (float) ($payment['sublet_total'] ?? 0),
            'subtotal'         => (float) ($payment['subtotal'] ?? 0),
            'vat_amount'       => (float) ($payment['vat_amount'] ?? 0),
            'grand_total'      => $grandTotal,
            'amount_in_words'  => $amountInWords,
            'received_by_name' => $payment['received_by_name'] ?? '',
            'notes'            => $payment['notes'] ?? null,
            'generated_by'     => $generated_by,
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        $this->insert($data);
        return (int) $this->getInsertID();
    }

    public function getWithDetails($id = null): array
    {
        $builder = $this->builder();
        $builder->select('receipts.*, customers.name AS customer_name, customers.phone AS customer_phone, job_cards.job_no, vehicles.registration_number, invoices.invoice_no')
            ->join('customers', 'customers.id = receipts.customer_id', 'left')
            ->join('job_cards', 'job_cards.id = receipts.job_card_id', 'left')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->join('invoices', 'invoices.id = receipts.invoice_id', 'left');

        if ($id !== null) {
            $result = $builder->where('receipts.id', $id)->first();
            return $result ?: [];
        }

        return $builder->orderBy('receipts.created_at', 'DESC')->get()->getResultArray();
    }

    public function getByInvoice(int $invoice_id): array
    {
        return $this->select('receipts.*')
            ->where('invoice_id', $invoice_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getByPaymentId(int $payment_id): ?array
    {
        $result = $this->where('payment_id', $payment_id)->first();
        return $result ?: null;
    }
}
