<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class PaymentsController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        // Current month stats
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $totalReceived = $db->table('payments')
            ->selectSum('amount', 'total')
            ->where('payment_date >=', $monthStart)
            ->where('payment_date <=', $monthEnd)
            ->get()
            ->getRowArray()['total'] ?? 0;

        $totalTransactions = $db->table('payments')
            ->where('payment_date >=', $monthStart)
            ->where('payment_date <=', $monthEnd)
            ->countAllResults();

        $outstandingBalance = $db->table('invoices')
            ->selectSum('balance_due', 'total')
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->get()
            ->getRowArray()['total'] ?? 0;

        $avgPayment = $totalTransactions > 0 ? $totalReceived / $totalTransactions : 0;

        // Payment method breakdown for current month
        $methodBreakdown = $db->table('payments')
            ->select('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->where('payment_date >=', $monthStart)
            ->where('payment_date <=', $monthEnd)
            ->groupBy('payment_method')
            ->get()
            ->getResultArray();

        $methodLabels = ['Cash', 'M-Pesa', 'Bank Transfer', 'Insurance', 'Credit'];
        $methodStats = [];
        foreach ($methodLabels as $m) {
            $methodStats[$m] = ['total' => 0, 'count' => 0];
        }
        foreach ($methodBreakdown as $mb) {
            $methodStats[$mb['payment_method']] = [
                'total' => (float) ($mb['total'] ?? 0),
                'count' => (int) ($mb['count'] ?? 0),
            ];
        }

        return view('admin/payments/index', compact(
            'totalReceived', 'totalTransactions', 'outstandingBalance', 'avgPayment',
            'methodStats', 'monthStart', 'monthEnd'
        ));
    }

    public function load()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $db = \Config\Database::connect();

        $draw   = $this->request->getPost('draw');
        $start  = (int) ($this->request->getPost('start') ?? 0);
        $length = (int) ($this->request->getPost('length') ?? 10);
        $search = $this->request->getPost('search')['value'] ?? '';

        // Additional date filter from GET
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $builder = $db->table('payments');
        $builder->select('
            payments.id,
            payments.payment_date,
            payments.amount,
            payments.payment_method,
            payments.reference_no,
            payments.invoice_id,
            invoices.invoice_no,
            invoices.job_card_id,
            customers.name AS customer_name,
            customers.phone AS customer_phone,
            job_cards.job_no,
            CONCAT(users.first_name, " ", users.last_name) AS received_by_name,
            receipts.id AS receipt_id,
            receipts.receipt_no
        ')
        ->join('invoices', 'invoices.id = payments.invoice_id', 'left')
        ->join('customers', 'customers.id = invoices.customer_id', 'left')
        ->join('job_cards', 'job_cards.id = invoices.job_card_id', 'left')
        ->join('users', 'users.id = payments.received_by', 'left')
        ->join('receipts', 'receipts.payment_id = payments.id', 'left');

        // Date filter
        if (!empty($startDate) && !empty($endDate)) {
            $builder->where('payments.payment_date >=', $startDate)
                    ->where('payments.payment_date <=', $endDate);
        }

        $totalRecords = $builder->countAllResults(false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('customers.name', $search)
                ->orLike('invoices.invoice_no', $search)
                ->orLike('job_cards.job_no', $search)
                ->orLike('payments.reference_no', $search)
                ->orLike('payments.payment_method', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('payments.payment_date', 'DESC')
                ->orderBy('payments.id', 'DESC');

        $data = $builder->limit($length, $start)->get()->getResultArray();

        return $this->respond([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }

    public function filter()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');

        if (empty($startDate) || empty($endDate)) {
            return $this->failValidationErrors('Both start_date and end_date are required.');
        }

        $db = \Config\Database::connect();

        $totalReceived = $db->table('payments')
            ->selectSum('amount', 'total')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->get()
            ->getRowArray()['total'] ?? 0;

        $totalTransactions = $db->table('payments')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->countAllResults();

        $outstandingBalance = $db->table('invoices')
            ->selectSum('balance_due', 'total')
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->get()
            ->getRowArray()['total'] ?? 0;

        $avgPayment = $totalTransactions > 0 ? $totalReceived / $totalTransactions : 0;

        $methodBreakdown = $db->table('payments')
            ->select('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->groupBy('payment_method')
            ->get()
            ->getResultArray();

        return $this->respond([
            'status'             => 'success',
            'total_received'     => (float) $totalReceived,
            'total_transactions' => (int) $totalTransactions,
            'outstanding_balance' => (float) $outstandingBalance,
            'avg_payment'        => (float) $avgPayment,
            'method_breakdown'   => $methodBreakdown,
        ]);
    }
}
