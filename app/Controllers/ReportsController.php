<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\InventoryModel;
use App\Models\JobCardModel;
use App\Models\PettyCashModel;
use App\Models\ActivityLogModel;

class ReportsController extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;

    public function index()
    {
        return view('admin/reports/index');
    }

    public function financial()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date   = $this->request->getGet('end_date') ?? date('Y-m-t');

        $db = \Config\Database::connect();

        // Revenue by month (last 12 months)
        $revenueByPeriod = $db->table('payments')
            ->select("DATE_FORMAT(payment_date, '%Y-%m') as period, SUM(amount) as revenue")
            ->groupBy('period')
            ->orderBy('period', 'ASC')
            ->limit(12)
            ->get()
            ->getResultArray();

        // Revenue by payment method
        $revenueByMethod = $db->table('payments')
            ->select("payment_method, SUM(amount) as total, COUNT(*) as count")
            ->where('payment_date >=', $start_date)
            ->where('payment_date <=', $end_date)
            ->groupBy('payment_method')
            ->get()
            ->getResultArray();

        // Outstanding invoices
        $outstandingInvoices = $db->table('invoices')
            ->select('invoices.*, customers.name as customer_name, job_cards.job_no')
            ->join('customers', 'customers.id = invoices.customer_id')
            ->join('job_cards', 'job_cards.id = invoices.job_card_id')
            ->whereNotIn('invoices.status', ['Paid', 'Cancelled'])
            ->orderBy('invoices.due_date', 'ASC')
            ->get()
            ->getResultArray();

        // Invoice aging buckets
        $agingBuckets = ['current' => ['count' => 0, 'total' => 0], '30' => ['count' => 0, 'total' => 0], '60' => ['count' => 0, 'total' => 0], '90' => ['count' => 0, 'total' => 0]];
        $today = new \DateTime();
        foreach ($outstandingInvoices as $inv) {
            $due = new \DateTime($inv['due_date']);
            $days = (int) $today->diff($due)->format('%r%a');
            $balance = (float) ($inv['balance_due'] ?? 0);
            if ($days <= 0) {
                $agingBuckets['current']['count']++;
                $agingBuckets['current']['total'] += $balance;
            } elseif ($days <= 30) {
                $agingBuckets['30']['count']++;
                $agingBuckets['30']['total'] += $balance;
            } elseif ($days <= 60) {
                $agingBuckets['60']['count']++;
                $agingBuckets['60']['total'] += $balance;
            } else {
                $agingBuckets['90']['count']++;
                $agingBuckets['90']['total'] += $balance;
            }
        }

        // Petty cash summary
        $pettyCashModel = new PettyCashModel();
        $pettyCashSummary = $pettyCashModel->getSummaryByPeriod($start_date, $end_date);

        // LPO spend by supplier
        $lpoSpendBySupplier = $db->table('lpos')
            ->select('suppliers.name, SUM(lpos.total_amount) as total_spend, COUNT(*) as lpo_count')
            ->join('suppliers', 'suppliers.id = lpos.supplier_id')
            ->where('lpos.lpo_date >=', $start_date)
            ->where('lpos.lpo_date <=', $end_date)
            ->where('lpos.status !=', 'Cancelled')
            ->groupBy('suppliers.id')
            ->orderBy('total_spend', 'DESC')
            ->get()
            ->getResultArray();

        // Summary cards
        $totalRevenue = $db->table('payments')
            ->selectSum('amount', 'total')
            ->where('payment_date >=', $start_date)
            ->where('payment_date <=', $end_date)
            ->get()
            ->getRowArray()['total'] ?? 0;

        $totalOutstanding = $db->table('invoices')
            ->selectSum('balance_due', 'total')
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->get()
            ->getRowArray()['total'] ?? 0;

        $avgInvoiceValue = $db->table('invoices')
            ->select('AVG(grand_total) as avg')
            ->where('invoice_date >=', $start_date)
            ->where('invoice_date <=', $end_date)
            ->get()
            ->getRowArray()['avg'] ?? 0;

        $totalDiscount = $db->table('invoices')
            ->selectSum('discount', 'total')
            ->where('invoice_date >=', $start_date)
            ->where('invoice_date <=', $end_date)
            ->get()
            ->getRowArray()['total'] ?? 0;

        $data = compact(
            'start_date', 'end_date',
            'revenueByPeriod', 'revenueByMethod',
            'outstandingInvoices', 'agingBuckets',
            'pettyCashSummary', 'lpoSpendBySupplier',
            'totalRevenue', 'totalOutstanding',
            'avgInvoiceValue', 'totalDiscount'
        );

        return view('admin/reports/financial', $data);
    }

    public function operational()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date   = $this->request->getGet('end_date') ?? date('Y-m-t');

        $db = \Config\Database::connect();

        // Jobs by status snapshot
        $jobsByStatus = $db->table('job_cards')
            ->select('job_status, COUNT(*) as count')
            ->groupBy('job_status')
            ->get()
            ->getResultArray();

        // Jobs completed per month (last 12 months)
        $completedPerPeriod = $db->table('job_status_history')
            ->select("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count")
            ->where('to_status', 'Completed')
            ->groupBy('period')
            ->orderBy('period', 'ASC')
            ->limit(12)
            ->get()
            ->getResultArray();

        // Average turnaround time
        $avgTurnaround = $db->table('job_cards')
            ->select('AVG(DATEDIFF(completed_at, date_in)) as avg_days')
            ->where('completed_at IS NOT NULL')
            ->where('completed_at >=', $start_date)
            ->where('completed_at <=', $end_date)
            ->get()
            ->getRowArray()['avg_days'] ?? 0;

        // Jobs per mechanic
        $jobsPerMechanic = $db->table('job_cards jc')
            ->select("u.first_name, u.last_name, COUNT(jc.id) as total_jobs, AVG(DATEDIFF(jc.completed_at, jc.date_in)) as avg_turnaround")
            ->join('users u', 'u.id = jc.assigned_mechanic_id')
            ->where('jc.date_in >=', $start_date)
            ->where('jc.date_in <=', $end_date)
            ->groupBy('jc.assigned_mechanic_id')
            ->orderBy('total_jobs', 'DESC')
            ->get()
            ->getResultArray();

        // Overdue jobs
        $overdueJobs = $db->table('job_cards jc')
            ->select('jc.*, c.name as customer_name, v.registration_number, u.first_name, u.last_name, DATEDIFF(CURDATE(), jc.end_date) as days_overdue')
            ->join('customers c', 'c.id = jc.customer_id')
            ->join('vehicles v', 'v.id = jc.vehicle_id')
            ->join('users u', 'u.id = jc.assigned_mechanic_id', 'left')
            ->where('jc.end_date <', date('Y-m-d'))
            ->whereNotIn('jc.job_status', ['Completed', 'Paid', 'Cancelled'])
            ->orderBy('days_overdue', 'DESC')
            ->get()
            ->getResultArray();

        // Jobs by diagnosis category
        $jobsByCategory = $db->table('job_cards')
            ->select('diagnosis_category, COUNT(*) as count')
            ->where('date_in >=', $start_date)
            ->where('date_in <=', $end_date)
            ->where('diagnosis_category IS NOT NULL')
            ->groupBy('diagnosis_category')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        // Sublet spend by supplier
        $subletSpend = $db->table('sublets')
            ->select('suppliers.name, SUM(sublets.cost) as total_cost, COUNT(*) as count')
            ->join('suppliers', 'suppliers.id = sublets.sublet_provider_id')
            ->where('sublets.date_sent >=', $start_date)
            ->where('sublets.date_sent <=', $end_date)
            ->where('sublets.status !=', 'Cancelled')
            ->groupBy('suppliers.id')
            ->orderBy('total_cost', 'DESC')
            ->get()
            ->getResultArray();

        // Summary cards
        $totalJobs = $db->table('job_cards')
            ->where('date_in >=', $start_date)
            ->where('date_in <=', $end_date)
            ->countAllResults();

        $totalCompleted = $db->table('job_status_history')
            ->where('to_status', 'Completed')
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date)
            ->countAllResults();

        $totalOverdue = count($overdueJobs);

        $data = compact(
            'start_date', 'end_date',
            'jobsByStatus', 'completedPerPeriod',
            'avgTurnaround', 'jobsPerMechanic',
            'overdueJobs', 'jobsByCategory',
            'subletSpend', 'totalJobs',
            'totalCompleted', 'totalOverdue'
        );

        return view('admin/reports/operational', $data);
    }

    public function inventory()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-01-01');
        $end_date   = $this->request->getGet('end_date') ?? date('Y-m-t');

        $db = \Config\Database::connect();
        $inventoryModel = new InventoryModel();

        // All stocked items
        $stockedItems = $inventoryModel->where('is_stocked', 1)->findAll();

        // Low stock items
        $lowStockItems = $inventoryModel->getLowStock();

        // Most used parts
        $mostUsedParts = $db->table('job_card_parts_required jcpr')
            ->select('i.name, i.part_number, SUM(jcpr.quantity_required) as total_used, COUNT(DISTINCT jcpr.job_card_id) as jobs_count')
            ->join('inventory i', 'i.id = jcpr.inventory_id')
            ->join('job_cards jc', 'jc.id = jcpr.job_card_id')
            ->where('jc.created_at >=', $start_date)
            ->where('jc.created_at <=', $end_date)
            ->groupBy('jcpr.inventory_id')
            ->orderBy('total_used', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        // Total inventory value
        $totalValue = $db->table('inventory')
            ->selectSum('quantity_in_hand * unit_price', 'total_value')
            ->where('is_stocked', 1)
            ->get()
            ->getRowArray()['total_value'] ?? 0;

        // Parts spend per job (top 10)
        $partsSpendPerJob = $db->table('job_card_parts_required jcpr')
            ->select('jc.job_no, c.name as customer_name, v.registration_number, SUM(jcpr.quantity_required * jcpr.unit_price_at_estimate) as parts_cost')
            ->join('job_cards jc', 'jc.id = jcpr.job_card_id')
            ->join('customers c', 'c.id = jc.customer_id')
            ->join('vehicles v', 'v.id = jc.vehicle_id')
            ->groupBy('jcpr.job_card_id')
            ->orderBy('parts_cost', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Summary cards
        $totalStockItems = $db->table('inventory')
            ->where('is_stocked', 1)
            ->countAllResults();

        $lowStockCount = count($lowStockItems);

        $outOfStockCount = $db->table('inventory')
            ->where('is_stocked', 1)
            ->where('quantity_in_hand <=', 0)
            ->countAllResults();

        $data = compact(
            'stockedItems', 'lowStockItems',
            'mostUsedParts', 'totalValue',
            'partsSpendPerJob',
            'totalStockItems', 'lowStockCount',
            'outOfStockCount', 'start_date', 'end_date'
        );

        return view('admin/reports/inventory', $data);
    }

    public function customers()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-01-01');
        $end_date   = $this->request->getGet('end_date') ?? date('Y-m-t');

        $db = \Config\Database::connect();

        // Top 10 customers by revenue
        $topCustomers = $db->table('payments p')
            ->select('c.name, c.phone, SUM(p.amount) as total_paid, COUNT(DISTINCT i.id) as invoice_count')
            ->join('invoices i', 'i.id = p.invoice_id')
            ->join('customers c', 'c.id = i.customer_id')
            ->where('p.payment_date >=', $start_date)
            ->where('p.payment_date <=', $end_date)
            ->groupBy('i.customer_id')
            ->orderBy('total_paid', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Customer visit frequency
        $visitFrequency = $db->table('customers c')
            ->select('c.name, c.phone, COUNT(jc.id) as visit_count, MAX(jc.date_in) as last_visit')
            ->join('job_cards jc', 'jc.customer_id = c.id AND jc.date_in >= ' . $db->escape($start_date) . ' AND jc.date_in <= ' . $db->escape($end_date), 'left')
            ->groupBy('c.id')
            ->orderBy('visit_count', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        // Customers with outstanding balances
        $outstandingCustomers = $db->table('invoices i')
            ->select('c.name, c.phone, c.email, SUM(i.balance_due) as total_outstanding, COUNT(i.id) as invoice_count')
            ->join('customers c', 'c.id = i.customer_id')
            ->whereNotIn('i.status', ['Paid', 'Cancelled'])
            ->groupBy('i.customer_id')
            ->having('total_outstanding > 0')
            ->orderBy('total_outstanding', 'DESC')
            ->get()
            ->getResultArray();

        // New customers per month (last 12 months)
        $newCustomersPerMonth = $db->table('customers')
            ->select("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period', 'ASC')
            ->limit(12)
            ->get()
            ->getResultArray();

        // Summary cards
        $totalCustomers = $db->table('customers')->countAllResults();

        $newThisPeriod = $db->table('customers')
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date)
            ->countAllResults();

        $totalOutstandingAmount = array_sum(array_column($outstandingCustomers, 'total_outstanding'));
        $outstandingCount = count($outstandingCustomers);

        $data = compact(
            'start_date', 'end_date',
            'topCustomers', 'visitFrequency',
            'outstandingCustomers', 'newCustomersPerMonth',
            'totalCustomers', 'newThisPeriod',
            'totalOutstandingAmount', 'outstandingCount'
        );

        return view('admin/reports/customers', $data);
    }

    public function staff()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date   = $this->request->getGet('end_date') ?? date('Y-m-t');

        $db = \Config\Database::connect();

        // Jobs per service advisor
        $advisorJobs = $db->table('job_cards jc')
            ->select("u.first_name, u.last_name, u.company_id, COUNT(jc.id) as total_jobs, SUM(CASE WHEN jc.job_status = 'Completed' THEN 1 ELSE 0 END) as completed")
            ->join('users u', 'u.id = jc.assigned_service_advisor_id')
            ->where('jc.date_in >=', $start_date)
            ->where('jc.date_in <=', $end_date)
            ->groupBy('jc.assigned_service_advisor_id')
            ->orderBy('total_jobs', 'DESC')
            ->get()
            ->getResultArray();

        // Activity log for period
        $activityLogModel = new ActivityLogModel();
        $activityLog = $activityLogModel->getByPeriod($start_date, $end_date);

        // Summary cards
        $totalStaff = $db->table('users')->countAllResults();

        $staffActive = $db->table('activity_log')
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date)
            ->countAllResults(true);

        $statusChanges = $db->table('job_status_history')
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date)
            ->countAllResults();

        $paymentsRecorded = $db->table('payments')
            ->where('payment_date >=', $start_date)
            ->where('payment_date <=', $end_date)
            ->countAllResults();

        $data = compact(
            'start_date', 'end_date',
            'advisorJobs', 'activityLog',
            'totalStaff', 'staffActive',
            'statusChanges', 'paymentsRecorded'
        );

        return view('admin/reports/staff', $data);
    }

    public function export($report, $format)
    {
        if ($format !== 'csv') {
            return redirect()->back()->with('error', 'Only CSV export is supported.');
        }

        $data = [];
        $filename = '';

        switch ($report) {
            case 'financial':
                $filename = 'financial_report.csv';
                $this->buildFinancialExport($data);
                break;
            case 'operational':
                $filename = 'operational_report.csv';
                $this->buildOperationalExport($data);
                break;
            case 'inventory':
                $filename = 'inventory_report.csv';
                $this->buildInventoryExport($data);
                break;
            case 'customers':
                $filename = 'customer_report.csv';
                $this->buildCustomerExport($data);
                break;
            case 'staff':
                $filename = 'staff_report.csv';
                $this->buildStaffExport($data);
                break;
            default:
                return redirect()->back()->with('error', 'Unknown report type.');
        }

        $this->response->setContentType('text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);

        return $this->response;
    }

    private function buildFinancialExport(array &$data): void
    {
        $db = \Config\Database::connect();

        $data[] = ['--- Outstanding Invoices ---'];
        $data[] = ['Invoice No', 'Customer', 'Job No', 'Invoice Date', 'Due Date', 'Grand Total', 'Amount Paid', 'Balance', 'Status'];
        $invoices = $db->table('invoices i')
            ->select('i.invoice_no, c.name as customer_name, jc.job_no, i.invoice_date, i.due_date, i.grand_total, i.amount_paid, i.balance_due, i.status')
            ->join('customers c', 'c.id = i.customer_id')
            ->join('job_cards jc', 'jc.id = i.job_card_id')
            ->whereNotIn('i.status', ['Paid', 'Cancelled'])
            ->orderBy('i.due_date', 'ASC')
            ->get()
            ->getResultArray();
        foreach ($invoices as $row) {
            $data[] = array_values($row);
        }

        $data[] = [];
        $data[] = ['--- Revenue by Payment Method ---'];
        $data[] = ['Payment Method', 'Total', 'Count'];
        $methods = $db->table('payments')
            ->select("payment_method, SUM(amount) as total, COUNT(*) as count")
            ->groupBy('payment_method')
            ->get()
            ->getResultArray();
        foreach ($methods as $row) {
            $data[] = array_values($row);
        }
    }

    private function buildOperationalExport(array &$data): void
    {
        $db = \Config\Database::connect();

        $data[] = ['--- Jobs by Status ---'];
        $data[] = ['Status', 'Count'];
        $statuses = $db->table('job_cards')
            ->select('job_status, COUNT(*) as count')
            ->groupBy('job_status')
            ->get()
            ->getResultArray();
        foreach ($statuses as $row) {
            $data[] = array_values($row);
        }

        $data[] = [];
        $data[] = ['--- Jobs per Mechanic ---'];
        $data[] = ['Mechanic', 'Total Jobs', 'Avg Turnaround (days)'];
        $mechanics = $db->table('job_cards jc')
            ->select("CONCAT(u.first_name, ' ', u.last_name) as mechanic, COUNT(jc.id) as total_jobs, AVG(DATEDIFF(jc.completed_at, jc.date_in)) as avg_turnaround")
            ->join('users u', 'u.id = jc.assigned_mechanic_id')
            ->groupBy('jc.assigned_mechanic_id')
            ->orderBy('total_jobs', 'DESC')
            ->get()
            ->getResultArray();
        foreach ($mechanics as $row) {
            $data[] = [$row['mechanic'], $row['total_jobs'], round((float) ($row['avg_turnaround'] ?? 0), 1)];
        }

        $data[] = [];
        $data[] = ['--- Overdue Jobs ---'];
        $data[] = ['Job No', 'Customer', 'Vehicle', 'Mechanic', 'End Date', 'Days Overdue'];
        $overdue = $db->table('job_cards jc')
            ->select("jc.job_no, c.name as customer, v.registration_number as vehicle, CONCAT(u.first_name, ' ', u.last_name) as mechanic, jc.end_date, DATEDIFF(CURDATE(), jc.end_date) as days_overdue")
            ->join('customers c', 'c.id = jc.customer_id')
            ->join('vehicles v', 'v.id = jc.vehicle_id')
            ->join('users u', 'u.id = jc.assigned_mechanic_id', 'left')
            ->where('jc.end_date <', date('Y-m-d'))
            ->whereNotIn('jc.job_status', ['Completed', 'Paid', 'Cancelled'])
            ->orderBy('days_overdue', 'DESC')
            ->get()
            ->getResultArray();
        foreach ($overdue as $row) {
            $data[] = array_values($row);
        }
    }

    private function buildInventoryExport(array &$data): void
    {
        $db = \Config\Database::connect();

        $data[] = ['--- Stock Levels ---'];
        $data[] = ['Item Name', 'Part Number', 'Unit', 'Qty in Hand', 'Reorder Level', 'Status'];
        $items = $db->table('inventory')
            ->where('is_stocked', 1)
            ->get()
            ->getResultArray();
        foreach ($items as $row) {
            $status = 'In Stock';
            if ((float) $row['quantity_in_hand'] <= 0) $status = 'Out of Stock';
            elseif ((float) $row['quantity_in_hand'] <= (float) $row['reorder_level']) $status = 'Low Stock';
            $data[] = [$row['name'], $row['part_number'], $row['unit'], $row['quantity_in_hand'], $row['reorder_level'], $status];
        }

        $data[] = [];
        $data[] = ['--- Most Used Parts ---'];
        $data[] = ['Part Name', 'Part Number', 'Times Used', 'Jobs Count'];
        $parts = $db->table('job_card_parts_required jcpr')
            ->select('i.name, i.part_number, SUM(jcpr.quantity_required) as total_used, COUNT(DISTINCT jcpr.job_card_id) as jobs_count')
            ->join('inventory i', 'i.id = jcpr.inventory_id')
            ->groupBy('jcpr.inventory_id')
            ->orderBy('total_used', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();
        foreach ($parts as $row) {
            $data[] = array_values($row);
        }
    }

    private function buildCustomerExport(array &$data): void
    {
        $db = \Config\Database::connect();

        $data[] = ['--- Top Customers by Revenue ---'];
        $data[] = ['Name', 'Phone', 'Total Paid', 'Invoice Count'];
        $customers = $db->table('payments p')
            ->select('c.name, c.phone, SUM(p.amount) as total_paid, COUNT(DISTINCT i.id) as invoice_count')
            ->join('invoices i', 'i.id = p.invoice_id')
            ->join('customers c', 'c.id = i.customer_id')
            ->groupBy('i.customer_id')
            ->orderBy('total_paid', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        foreach ($customers as $row) {
            $data[] = array_values($row);
        }

        $data[] = [];
        $data[] = ['--- Customers with Outstanding Balances ---'];
        $data[] = ['Name', 'Phone', 'Email', 'Total Outstanding', 'Invoice Count'];
        $outstanding = $db->table('invoices i')
            ->select('c.name, c.phone, c.email, SUM(i.balance_due) as total_outstanding, COUNT(i.id) as invoice_count')
            ->join('customers c', 'c.id = i.customer_id')
            ->whereNotIn('i.status', ['Paid', 'Cancelled'])
            ->groupBy('i.customer_id')
            ->having('total_outstanding > 0')
            ->orderBy('total_outstanding', 'DESC')
            ->get()
            ->getResultArray();
        foreach ($outstanding as $row) {
            $data[] = array_values($row);
        }
    }

    private function buildStaffExport(array &$data): void
    {
        $db = \Config\Database::connect();

        $data[] = ['--- Jobs per Service Advisor ---'];
        $data[] = ['Name', 'Company ID', 'Total Jobs', 'Completed'];
        $advisors = $db->table('job_cards jc')
            ->select("CONCAT(u.first_name, ' ', u.last_name) as name, u.company_id, COUNT(jc.id) as total_jobs, SUM(CASE WHEN jc.job_status = 'Completed' THEN 1 ELSE 0 END) as completed")
            ->join('users u', 'u.id = jc.assigned_service_advisor_id')
            ->groupBy('jc.assigned_service_advisor_id')
            ->orderBy('total_jobs', 'DESC')
            ->get()
            ->getResultArray();
        foreach ($advisors as $row) {
            $data[] = array_values($row);
        }
    }
}