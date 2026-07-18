<?php

namespace App\Controllers;

use App\Models\JobCardModel;
use App\Models\InvoiceModel;
use App\Models\CustomerModel;
use App\Models\VehicleModel;

class CustomerPortal extends BaseController
{
    protected $customerId;

    public function __construct()
    {
        $this->customerId = session()->get('customer_id');
    }

    public function dashboard()
    {
        if (!$this->customerId) {
            return view('customer/dashboard', [
                'customerName' => session()->get('user_name'),
                'activeJobs' => 0,
                'completedJobs' => 0,
                'outstandingInvoices' => 0,
                'recentJobs' => [],
            ]);
        }

        $jobCardModel = new JobCardModel();

        $allJobs = $jobCardModel->where('customer_id', $this->customerId)->findAll();

        $activeJobs = 0;
        $completedJobs = 0;
        foreach ($allJobs as $job) {
            if (in_array($job['job_status'], ['In Progress', 'Awaiting Diagnosis', 'Awaiting Assignment', 'Awaiting Parts', 'Quality Check', 'Approved', 'Ready for Invoice'])) {
                $activeJobs++;
            }
            if ($job['job_status'] === 'Completed') {
                $completedJobs++;
            }
        }

        $recentJobs = $jobCardModel->select('job_cards.*, vehicles.registration_number')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->where('job_cards.customer_id', $this->customerId)
            ->orderBy('job_cards.created_at', 'DESC')
            ->findAll(5);

        $invoiceModel = new InvoiceModel();
        $outstandingInvoices = $invoiceModel->where('customer_id', $this->customerId)
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->countAllResults();

        $customerModel = new CustomerModel();
        $customer = $customerModel->find($this->customerId);
        $customerName = $customer['name'] ?? session()->get('user_name');

        return view('customer/dashboard', [
            'customerName' => $customerName,
            'activeJobs' => $activeJobs,
            'completedJobs' => $completedJobs,
            'outstandingInvoices' => $outstandingInvoices,
            'recentJobs' => $recentJobs,
        ]);
    }

    public function jobs()
    {
        if (!$this->customerId) {
            return view('customer/jobs', ['jobs' => []]);
        }

        $jobCardModel = new JobCardModel();
        $jobs = $jobCardModel->select('job_cards.*, vehicles.registration_number, vehicles.make, vehicles.model')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->where('job_cards.customer_id', $this->customerId)
            ->orderBy('job_cards.created_at', 'DESC')
            ->findAll();

        return view('customer/jobs', ['jobs' => $jobs]);
    }

    public function invoices()
    {
        if (!$this->customerId) {
            return view('customer/invoices', ['invoices' => []]);
        }

        $invoiceModel = new InvoiceModel();
        $invoices = $invoiceModel->select('invoices.*, job_cards.job_no')
            ->join('job_cards', 'job_cards.id = invoices.job_card_id', 'left')
            ->where('invoices.customer_id', $this->customerId)
            ->orderBy('invoices.created_at', 'DESC')
            ->findAll();

        return view('customer/invoices', ['invoices' => $invoices]);
    }

    public function invoice($id)
    {
        if (!$this->customerId) {
            return redirect()->to('/customer/invoices');
        }

        $invoiceModel = new InvoiceModel();
        $invoice = $invoiceModel->getWithDetails($id);

        if (!$invoice || $invoice['customer_id'] != $this->customerId) {
            return redirect()->to('/customer/invoices')->with('error', 'Invoice not found.');
        }

        return view('customer/invoice_view', ['invoice' => $invoice]);
    }
}
