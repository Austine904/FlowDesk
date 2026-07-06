<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\PaymentModel;
use App\Models\JobCardModel;
use App\Models\JobStatusHistoryModel;

class InvoicesController extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;

    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $invoiceModel = new InvoiceModel();
        $invoices = $invoiceModel->getWithDetails();

        return view('admin/invoices/index', ['invoices' => $invoices]);
    }

    public function generate($job_card_id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $discount = (float) ($this->request->getPost('discount') ?? 0);

        $invoiceModel = new InvoiceModel();
        $invoice = $invoiceModel->generateFromJobCard((int) $job_card_id, (int) session()->get('user_id'), $discount);

        return redirect()->to('/admin/invoices/view/' . $invoice['id'])
            ->with('success', 'Invoice generated successfully.');
    }

    public function view($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $invoiceModel = new InvoiceModel();
        $paymentModel = new PaymentModel();

        $invoice = $invoiceModel->getWithDetails((int) $id);

        if (empty($invoice)) {
            return redirect()->to('/admin/invoices')->with('error', 'Invoice not found.');
        }

        $payments = $paymentModel->getByInvoice((int) $id);

        $db = \Config\Database::connect();

        $parts = $db->table('job_card_parts_required')
            ->select('job_card_parts_required.*, inventory.name, inventory.part_number')
            ->join('inventory', 'inventory.id = job_card_parts_required.inventory_id', 'left')
            ->where('job_card_parts_required.job_card_id', $invoice['job_card_id'])
            ->get()
            ->getResultArray();

        $tasks = $db->table('job_card_labor_tasks')
            ->where('job_card_id', $invoice['job_card_id'])
            ->get()
            ->getResultArray();

        $sublets = $db->table('sublets')
            ->select('sublets.*, suppliers.name AS provider_name')
            ->join('suppliers', 'suppliers.id = sublets.sublet_provider_id', 'left')
            ->where('sublets.job_card_id', $invoice['job_card_id'])
            ->where('sublets.status !=', 'Cancelled')
            ->get()
            ->getResultArray();

        return view('admin/invoices/view', compact('invoice', 'payments', 'parts', 'tasks', 'sublets'));
    }

    public function recordPayment($invoice_id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'amount'         => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[Cash,M-Pesa,Bank Transfer,Insurance,Credit]',
            'payment_date'   => 'required|valid_date',
            'reference_no'   => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $invoiceModel = new InvoiceModel();
        $paymentModel = new PaymentModel();
        $invoice = $invoiceModel->find((int) $invoice_id);

        if (!$invoice) {
            return redirect()->to('/admin/invoices')->with('error', 'Invoice not found.');
        }

        $paymentModel->insert([
            'invoice_id'    => (int) $invoice_id,
            'amount'        => (float) $this->request->getPost('amount'),
            'payment_method' => $this->request->getPost('payment_method'),
            'reference_no'  => $this->request->getPost('reference_no'),
            'payment_date'  => $this->request->getPost('payment_date'),
            'received_by'   => (int) session()->get('user_id'),
            'notes'         => $this->request->getPost('notes'),
        ]);

        $invoiceModel->updateAmountPaid((int) $invoice_id);

        $invoice = $invoiceModel->find((int) $invoice_id);

        if ($invoice && $invoice['status'] === 'Paid') {
            $db = \Config\Database::connect();
            $db->transStart();

            $jobCardModel = new JobCardModel();
            $job = $jobCardModel->find($invoice['job_card_id']);

            if ($job && $job['job_status'] !== 'Paid') {
                $currentStatus = $job['job_status'];
                $jobCardModel->update($invoice['job_card_id'], ['job_status' => 'Paid']);

                $historyModel = new JobStatusHistoryModel();
                $historyModel->insert([
                    'job_card_id' => $invoice['job_card_id'],
                    'from_status' => $currentStatus,
                    'to_status'   => 'Paid',
                    'changed_by'  => session()->get('user_id'),
                    'notes'       => 'Invoice paid',
                ]);
            }

            $db->transComplete();
        }

        $amount = $this->request->getPost('amount');
        $payment_method = $this->request->getPost('payment_method');
        log_activity('payment_recorded', 'invoice', (int) $invoice_id, "Payment of {$amount} recorded via {$payment_method}");

        return redirect()->to('/admin/invoices/view/' . $invoice_id)
            ->with('success', 'Payment recorded successfully.');
    }

    public function load()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $invoiceModel = new InvoiceModel();

        $draw   = $this->request->getPost('draw');
        $start  = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';
        $order  = $this->request->getPost('order');
        $columns = $this->request->getPost('columns');

        $builder = $invoiceModel->builder();
        $builder->select('invoices.id, invoices.invoice_no, invoices.invoice_date, invoices.due_date, invoices.grand_total, invoices.amount_paid, invoices.balance_due, invoices.status, customers.name AS customer_name, job_cards.job_no')
            ->join('customers', 'customers.id = invoices.customer_id', 'left')
            ->join('job_cards', 'job_cards.id = invoices.job_card_id', 'left');

        $totalRecords = $builder->countAllResults(false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('invoices.invoice_no', $search)
                ->orLike('customers.name', $search)
                ->orLike('job_cards.job_no', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        if ($order) {
            $colIdx = $order[0]['column'];
            $colName = $columns[$colIdx]['data'] ?? 'invoices.id';
            $colDir  = $order[0]['dir'];
            $builder->orderBy($colName, $colDir);
        } else {
            $builder->orderBy('invoices.created_at', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResultArray();

        return $this->respond([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }

    public function markOverdue()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $invoiceModel = new InvoiceModel();
        $invoiceModel->where('due_date <', date('Y-m-d'))
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->set(['status' => 'Overdue'])
            ->update();

        return redirect()->to('/admin/invoices')->with('success', 'Overdue invoices updated.');
    }
}
