<?php

namespace App\Controllers;

use App\Models\JobCardModel;
use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\InventoryModel;
use App\Models\LpoModel;
use App\Models\PettyCashModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;
use App\Models\ActivityLogModel;
use App\Models\CalendarEventModel;
use Config\JobStatus;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $data = [
            'name' => session()->get('user_name'),
            'role' => session()->get('role'),
            'userId' => session()->get('user_id'),
        ];

        return view('admin/dashboard', $data);
    }

    public function admin()
    {
        helper('activity');

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $data = array_merge(
            ['pageTitle' => 'Dashboard'],
            $this->buildStatsData(),
            $this->buildJobStatusChartData(),
            $this->buildRevenueData(),
            $this->buildAlertsData(),
            $this->buildUpcomingEvents(),
            $this->buildRecentActivity()
        );

        return view('admin/dashboard', $data);
    }

    private function buildStatsData(): array
    {
        $userModel = new UserModel();
        $vehicleModel = new VehicleModel();
        $jobCardModel = new JobCardModel();

        return [
            'userCount'      => $userModel->countAllResults(),
            'vehicleCount'   => $vehicleModel->where('status', 'On Job')->countAllResults(),
            'latestUsers'    => $userModel->orderBy('created_at', 'DESC')->findAll(5),
            'latestVehicles' => $vehicleModel->orderBy('created_at', 'DESC')->findAll(5),
            'recentJobs'     => $jobCardModel->getRecentJobs(5),
        ];
    }

    private function buildJobStatusChartData(): array
    {
        $jobCardModel = new JobCardModel();
        $jobStatus = new JobStatus();
        $statusColors = $jobStatus->statusColors;

        $jobStatusQuery = $jobCardModel->builder()
            ->select("job_status, COUNT(*) as count")
            ->groupBy("job_status")
            ->get()
            ->getResult();

        $defaultColor = '#999999';
        $jobStatusData = [];

        foreach ($jobStatusQuery as $row) {
            $status = $row->job_status;
            $count = (int) $row->count;

            if ($count === 0) {
                continue;
            }

            $jobStatusData[$status] = $count;
        }

        $totalJobsQuery = array_sum($jobStatusData);

        $activeJobs = ($jobStatusData['In Progress'] ?? 0) +
            ($jobStatusData['Awaiting Parts'] ?? 0) +
            ($jobStatusData['Quality Check'] ?? 0) +
            ($jobStatusData['Ready for Invoice'] ?? 0);

        return [
            'awaitingAssignmentJobs' => $jobStatusData['Awaiting Assignment'] ?? 0,
            'awaitingDiagnosisJobs'  => $jobStatusData['Awaiting Diagnosis'] ?? 0,
            'diagnosedJobs'          => $jobStatusData['Diagnosis Complete'] ?? 0,
            'approvedJobs'           => $jobStatusData['Approved'] ?? 0,
            'inProgressJobs'         => $jobStatusData['In Progress'] ?? 0,
            'awaitingPartsJobs'      => $jobStatusData['Awaiting Parts'] ?? 0,
            'qualityCheckJobs'       => $jobStatusData['Quality Check'] ?? 0,
            'readyForInvoiceJobs'    => $jobStatusData['Ready for Invoice'] ?? 0,
            'quoteSentJobs'          => $jobStatusData['Quote Sent'] ?? 0,
            'paidJobs'               => $jobStatusData['Paid'] ?? 0,
            'completedJobs'          => $jobStatusData['Completed'] ?? 0,
            'onHoldJobs'             => $jobStatusData['On Hold'] ?? 0,
            'reworkJobs'             => $jobStatusData['Rework'] ?? 0,
            'cancelledJobs'          => $jobStatusData['Cancelled'] ?? 0,
            'activeJobs'             => $activeJobs,
            'totalJobs'              => $totalJobsQuery,
            'jobStatusData'          => json_encode($jobStatusData),
            'jobStatusColors'        => json_encode($statusColors),
        ];
    }

    private function buildRevenueData(): array
    {
        $paymentModel = new PaymentModel();
        $invoiceModel = new InvoiceModel();

        $revenueData = $paymentModel->getMonthlyRevenue(6);

        $revenueByMonth = [];
        $revenueLabels = [];
        $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $start = date('Y-m-01');
        for ($i = 5; $i >= 0; $i--) {
            $ts = strtotime("-{$i} months", strtotime($start));
            $key = date('Y-m', $ts);
            $revenueByMonth[$key] = 0;
            $revenueLabels[] = $monthNames[(int) date('m', $ts)];
        }

        foreach ($revenueData as $row) {
            $key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
            if (array_key_exists($key, $revenueByMonth)) {
                $revenueByMonth[$key] = (float) $row['total'];
            }
        }

        $revenueByMonthNumeric = array_values($revenueByMonth);

        return [
            'revenueByMonth'     => json_encode($revenueByMonthNumeric),
            'revenueLabels'      => json_encode($revenueLabels),
            'totalRevenue'       => $paymentModel->getThisMonthRevenue(),
            'outstandingBalance' => $invoiceModel->getOutstandingBalance(),
        ];
    }

    private function buildAlertsData(): array
    {
        $inventoryModel = new InventoryModel();
        $lpoModel = new LpoModel();
        $pettyCashModel = new PettyCashModel();
        $invoiceModel = new InvoiceModel();

        $lowStockItems = $inventoryModel->getLowStock();
        $pendingLPOs = $lpoModel->where('status', 'Sent')->countAllResults();
        $pettyCashSummary = $pettyCashModel->getSummary();
        $overdueInvoiceCount = $invoiceModel->getOverdueCount();
        $overdueInvoiceTotal = $invoiceModel->getOverdueTotal();

        return [
            'lowStockItems'      => $lowStockItems,
            'pendingLPOs'        => $pendingLPOs,
            'pettyCashBalance'   => $pettyCashSummary['current_balance'] ?? 0,
            'overdueInvoiceCount' => $overdueInvoiceCount,
            'overdueInvoiceTotal' => $overdueInvoiceTotal,
        ];
    }

    private function buildUpcomingEvents(): array
    {
        $calendarModel = new CalendarEventModel();
        $upcomingEvents = $calendarModel->getUpcoming(5);

        return [
            'upcomingEvents' => $upcomingEvents ?? [],
        ];
    }

    private function buildRecentActivity(): array
    {
        $activityLogModel = new ActivityLogModel();
        $recentLogs = $activityLogModel->getRecent(10);

        $activityIcons = [
            'status_change'    => 'bi-arrow-left-right',
            'payment_recorded' => 'bi-cash-coin',
            'lpo_created'      => 'bi-file-text',
            'lpo_received'     => 'bi-box-seam',
            'job_created'      => 'bi-briefcase',
            'diagnosis_saved'  => 'bi-clipboard-check',
            'user_created'     => 'bi-person-plus',
            'petty_cash_entry' => 'bi-wallet2',
        ];

        $recentActivity = [];
        foreach ($recentLogs as $log) {
            $recentActivity[] = [
                'icon'     => $activityIcons[$log['action']] ?? 'bi-circle',
                'text'     => esc($log['description']),
                'time'     => timeAgo($log['created_at']),
                'user'     => esc($log['user_name'] ?? 'System'),
                'sort_key' => $log['created_at'],
            ];
        }

        usort($recentActivity, fn($a, $b) => strcmp($b['sort_key'], $a['sort_key']));

        return [
            'recentActivity' => $recentActivity,
        ];
    }

    public function mechanic()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'mechanic') {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $jobCardModel = new JobCardModel();

        $allJobs = $jobCardModel->getAssignedToMechanic($userId);
        $totalJobs = count($allJobs);

        $awaitingDiagnosis = 0;
        $inProgress = 0;
        $completed = 0;
        $recentJobs = [];

        foreach ($allJobs as $job) {
            switch ($job['job_status']) {
                case 'Awaiting Diagnosis':
                    $awaitingDiagnosis++;
                    break;
                case 'In Progress':
                    $inProgress++;
                    break;
                case 'Completed':
                    $completed++;
                    break;
            }
        }

        $recentJobs = array_slice($allJobs, 0, 5);

        return view('mechanic_dashboard', [
            'name' => session()->get('user_name'),
            'totalJobs' => $totalJobs,
            'awaitingDiagnosis' => $awaitingDiagnosis,
            'inProgress' => $inProgress,
            'completed' => $completed,
            'recentJobs' => $recentJobs,
        ]);
    }

    private function restrictTo($requiredRole, $view)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== $requiredRole) {
            return redirect()->to('/login');
        }

        return view($view, ['name' => session()->get('user_name')]);
    }

    public function receptionist()
    {
        return $this->restrictTo('receptionist', 'receptionist_dashboard');
    }

    public function customer()
    {
        return $this->restrictTo('customer', 'customer_dashboard');
    }

    public function unauthorized()
    {
        return view('errors/unauthorized');
    }
}
