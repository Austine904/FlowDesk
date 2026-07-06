<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'invoice_no',
        'job_card_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'parts_total',
        'labor_total',
        'sublet_total',
        'subtotal',
        'vat_rate',
        'vat_amount',
        'discount',
        'grand_total',
        'amount_paid',
        'status',
        'notes',
        'created_by',
    ];

    public function generateInvoiceNo(): string
    {
        $prefix = org_setting('invoice_prefix', 'INV-');
        $ym = date('Ym');
        $like = $prefix . $ym . '-';

        $last = $this->select('invoice_no')
            ->where("invoice_no LIKE '{$like}%'")
            ->orderBy('invoice_no', 'DESC')
            ->first();

        if ($last) {
            $suffix = (int) substr($last['invoice_no'], -3) + 1;
        } else {
            $suffix = 1;
        }

        return $like . str_pad((string) $suffix, 3, '0', STR_PAD_LEFT);
    }

    public function generateFromJobCard(int $job_card_id, int $created_by, float $discount = 0.00): array
    {
        $existing = $this->where('job_card_id', $job_card_id)->first();
        if ($existing) {
            return $existing;
        }

        $db = \Config\Database::connect();

        $partsRow = $db->table('job_card_parts_required')
            ->selectSum('quantity_required * unit_price_at_estimate', 'parts_total')
            ->where('job_card_id', $job_card_id)
            ->get()
            ->getRowArray();

        $laborRow = $db->table('job_card_labor_tasks')
            ->selectSum('labor_cost', 'labor_total')
            ->where('job_card_id', $job_card_id)
            ->get()
            ->getRowArray();

        $subletRow = $db->table('sublets')
            ->selectSum('cost', 'sublet_total')
            ->where('job_card_id', $job_card_id)
            ->where('status !=', 'Cancelled')
            ->get()
            ->getRowArray();

        $partsTotal  = (float) ($partsRow['parts_total'] ?? 0);
        $laborTotal  = (float) ($laborRow['labor_total'] ?? 0);
        $subletTotal = (float) ($subletRow['sublet_total'] ?? 0);
        $subtotal    = $partsTotal + $laborTotal + $subletTotal;
        $vatRate     = (float) org_setting('vat_rate', 16);
        $vatAmount   = $subtotal * ($vatRate / 100);
        $grandTotal  = $subtotal + $vatAmount - $discount;

        $job = $db->table('job_cards')
            ->select('customer_id')
            ->where('id', $job_card_id)
            ->get()
            ->getRowArray();

        $invoiceDate = date('Y-m-d');
        $dueDays     = (int) org_setting('invoice_due_days', 14);
        $dueDate     = date('Y-m-d', strtotime("+{$dueDays} days"));

        $invoiceNo   = $this->generateInvoiceNo();

        $data = [
            'invoice_no'    => $invoiceNo,
            'job_card_id'   => $job_card_id,
            'customer_id'   => $job['customer_id'],
            'invoice_date'  => $invoiceDate,
            'due_date'      => $dueDate,
            'parts_total'   => $partsTotal,
            'labor_total'   => $laborTotal,
            'sublet_total'  => $subletTotal,
            'subtotal'      => $subtotal,
            'vat_rate'      => $vatRate,
            'vat_amount'    => $vatAmount,
            'discount'      => $discount,
            'grand_total'   => $grandTotal,
            'amount_paid'   => 0.00,
            'status'        => 'Draft',
            'created_by'    => $created_by,
        ];

        $this->insert($data);
        $data['id'] = $this->getInsertID();

        return $data;
    }

    public function getWithDetails(int $id = null): array
    {
        $builder = $this->select('invoices.*, customers.name AS customer_name, customers.phone AS customer_phone, job_cards.job_no, CONCAT(users.first_name, " ", users.last_name) AS created_by_name')
            ->join('customers', 'customers.id = invoices.customer_id', 'left')
            ->join('job_cards', 'job_cards.id = invoices.job_card_id', 'left')
            ->join('users', 'users.id = invoices.created_by', 'left');

        if ($id !== null) {
            $result = $builder->where('invoices.id', $id)->first();
            return $result ?: [];
        }

        return $builder->orderBy('invoices.created_at', 'DESC')->findAll();
    }

    public function updateAmountPaid(int $invoice_id): void
    {
        $db = \Config\Database::connect();

        $paymentRow = $db->table('payments')
            ->selectSum('amount', 'total_paid')
            ->where('invoice_id', $invoice_id)
            ->get()
            ->getRowArray();

        $amountPaid = (float) ($paymentRow['total_paid'] ?? 0);
        $invoice    = $this->find($invoice_id);
        $grandTotal = (float) ($invoice['grand_total'] ?? 0);

        $status = $invoice['status'] ?? 'Draft';
        if ($amountPaid == 0) {
            $status = ($invoice['status'] === 'Sent') ? 'Sent' : 'Draft';
        } elseif ($amountPaid > 0 && $amountPaid < $grandTotal) {
            $status = 'Partially Paid';
        } elseif ($amountPaid >= $grandTotal) {
            $status = 'Paid';
        }

        $this->update($invoice_id, [
            'amount_paid' => $amountPaid,
            'status'      => $status,
        ]);
    }
}
