<?php

namespace App\Controllers;

use App\Models\JobCardModel;
use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\InventoryModel;
use App\Models\LpoModel;
use App\Models\PettyCashModel;

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
        helper('time');

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $jobCardModel = new JobCardModel();
        $userModel = new UserModel();
        $vehicleModel = new VehicleModel();

        $recentActivity = [];

        $jobs = $jobCardModel->select('id, updated_at, job_status')->orderBy('updated_at', 'DESC')->findAll(3);
        foreach ($jobs as $job) {
            $recentActivity[] = [
                'type' => 'jobs',
                'icon' => 'bi-briefcase',
                'text' => "Job #{$job['id']} status updated to '{$job['job_status']}'",
                'time' => timeAgo($job['updated_at']),
            ];
        }

        $users = $userModel->select('id, first_name, last_name, role, created_at')->orderBy('created_at', 'DESC')->findAll(3);
        foreach ($users as $user) {
            $name = esc($user['first_name'] . ' ' . $user['last_name']);
            $recentActivity[] = [
                'type' => 'users',
                'icon' => 'bi-person-plus',
                'text' => "New user <a href='#' class='activity-link'>{$name}</a> ({$user['role']}) registered by admin.",
                'time' => timeAgo($user['created_at']),
            ];
        }

        $vehicles = $vehicleModel->select('registration_number, make, model, owner_id, created_at')->orderBy('created_at', 'DESC')->findAll(3);
        foreach ($vehicles as $v) {
            $vehicleText = "{$v['make']} {$v['model']} ({$v['registration_number']})";
            $recentActivity[] = [
                'type' => 'vehicles',
                'icon' => 'bi-car-front',
                'text' => "New vehicle <a href='#' class='activity-link'>{$vehicleText}</a> registered.",
                'time' => timeAgo($v['created_at']),
            ];
        }

        $jobCards = $jobCardModel->getRecentJobs(5);
        foreach ($jobCards as $jobCard) {
            $vehicleregistration = esc($jobCard['registration_number'] ?? 'Unknown Vehicle');
            $diagnosis = esc($jobCard['diagnosis']);
            $recentActivity[] = [
                'type' => 'job_cards',
                'icon' => 'bi-file-earmark-text',
                'text' => "New job card added for vehicle <a href='#' class='activity-link'>{$vehicleregistration}</a> with description: {$diagnosis}",
                'time' => timeAgo($jobCard['created_at']),
            ];
        }

        usort($recentActivity, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));

        $userCount = $userModel->countAllResults();

        $vehicleCount = $vehicleModel->where('status', 'On Job')->countAllResults();

        $latestUsers = $userModel->orderBy('created_at', 'DESC')->findAll(5);

        $latestVehicles = $vehicleModel->orderBy('created_at', 'DESC')->findAll(5);

        $jobStatusQuery = $jobCardModel->builder()
            ->select("job_status, COUNT(*) as count")
            ->groupBy("job_status")
            ->get()
            ->getResult();

        $jobStatusQuery = array_map(function ($row) {
            return (object)[
                'job_status' => $row->job_status,
                'count' => $row->count,
            ];
        }, $jobStatusQuery);

        $labels = [];
        $counts = [];
        $backgroundColors = [];
        $borderColors = [];

        $statusColors = [
            'Awaiting Assignment' => '#6c757d',
            'Awaiting Diagnosis' => '#007bff',
            'Diagnosis Complete' => '#ffc107',
            'Approved' => '#17a2b8',
            'In Progress' => '#6f42c1',
            'Awaiting Parts' => '#fd7e14',
            'Quality Check' => '#20c997',
            'Ready for Invoice' => '#e83e8c',
            'Quote Sent' => '#6610f2',
            'Paid' => '#28a745',
            'Completed' => '#28a745',
            'On Hold' => '#343a40',
            'Rework' => '#6c757d',
            'Cancelled' => '#dc3545',
        ];

        $defaultColor = '#999999';
        $defaultBorderColor = '#ffffff';

        $jobStatusData = [
            'Awaiting Assignment' => 0,
            'Awaiting Diagnosis' => 0,
            'Diagnosis Complete' => 0,
            'Approved' => 0,
            'In Progress' => 0,
            'Awaiting Parts' => 0,
            'Quality Check' => 0,
            'Ready for Invoice' => 0,
            'Quote Sent' => 0,
            'Paid' => 0,
            'Completed' => 0,
            'On Hold' => 0,
            'Rework' => 0,
            'Cancelled' => 0,
        ];

        foreach ($jobStatusQuery as $row) {
            $currentStatus = $row->job_status;
            $count = (int)$row->count;

            $labels[] = $currentStatus;
            $counts[] = $count;

            $backgroundColors[] = $statusColors[$currentStatus] ?? $defaultColor;
            $borderColor[] = $defaultBorderColor;

            $jobStatusTotals[$currentStatus] = $count;
            $status = $row->job_status;
            if (array_key_exists($status, $jobStatusData)) {
                $jobStatusData[$status] = (int)$row->count;
            }
        }

        $totalJobsQuery = array_sum($jobStatusData);

        $db = \Config\Database::connect();

        $revenueData = $db->table('payments')
            ->select("MONTH(payment_date) as month, SUM(amount) as total")
            ->where('payment_date >=', date('Y-m-d', strtotime('-6 months')))
            ->groupBy('MONTH(payment_date)')
            ->orderBy('MONTH(payment_date)', 'ASC')
            ->get()
            ->getResultArray();

        $revenueByMonth = [];
        $revenueLabels = [];
        $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        for ($i = 5; $i >= 0; $i--) {
            $m = (int) date('m', strtotime("-{$i} months"));
            $revenueByMonth[] = 0;
            $revenueLabels[] = $monthNames[$m];
        }
        foreach ($revenueData as $row) {
            $monthIdx = (int) $row['month'];
            $revIdx = 5 - ((int) date('m') - $monthIdx);
            if ($revIdx >= 0 && $revIdx < 6) {
                $revenueByMonth[$revIdx] = (float) $row['total'];
            }
        }

        $thisMonthRevenue = $db->table('payments')
            ->selectSum('amount', 'total')
            ->where('payment_date >=', date('Y-m-01'))
            ->where('payment_date <=', date('Y-m-t'))
            ->get()
            ->getRowArray();
        $totalRevenue = (float) ($thisMonthRevenue['total'] ?? 0);

        $outstandingRow = $db->table('invoices')
            ->selectSum('balance_due', 'total')
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->get()
            ->getRowArray();
        $outstandingBalance = (float) ($outstandingRow['total'] ?? 0);

        $inventoryModel = new InventoryModel();
        $lowStockItems = $inventoryModel->getLowStock();

        $lpoModel = new LpoModel();
        $pendingLPOs = $lpoModel->where('status', 'Sent')->countAllResults();

        $pettyCashModel = new PettyCashModel();
        $pettyCashSummary = $pettyCashModel->getSummary();

        $data = [
            'pendingLPOs'        => $pendingLPOs,
            'pettyCashBalance'   => $pettyCashSummary['current_balance'],
            'totalRevenue'       => $totalRevenue,
            'outstandingBalance' => $outstandingBalance,
            'revenueByMonth'     => json_encode($revenueByMonth),
            'revenueLabels'      => json_encode($revenueLabels),
            'userCount'          => $userCount,
            'vehicleCount'    => $vehicleCount,
            'latestUsers'     => $latestUsers,
            'latestVehicles'  => $latestVehicles,

            'awaitingAssignmentJobs' => $jobStatusData['Awaiting Assignment'],
            'awaitingDiagnosisJobs' => $jobStatusData['Awaiting Diagnosis'],
            'diagnosedJobs' => $jobStatusData['Diagnosis Complete'],
            'approvedJobs'    => $jobStatusData['Approved'],
            'inProgressJobs'  => $jobStatusData['In Progress'],
            'awaitingPartsJobs' => $jobStatusData['Awaiting Parts'],
            'qualityCheckJobs' => $jobStatusData['Quality Check'],
            'readyForInvoiceJobs' => $jobStatusData['Ready for Invoice'],
            'quoteSentJobs'   => $jobStatusData['Quote Sent'],
            'paidJobs'        => $jobStatusData['Paid'],
            'completedJobs'   => $jobStatusData['Completed'],
            'onHoldJobs'      => $jobStatusData['On Hold'],
            'reworkJobs'      => $jobStatusData['Rework'],
            'cancelledJobs'   => $jobStatusData['Cancelled'],
            'activeJobs'      => $jobStatusData['In Progress'] + $jobStatusData['Awaiting Parts'] + $jobStatusData['Quality Check'] + $jobStatusData['Ready for Invoice'],
            'totalJobs'       => $totalJobsQuery,
            'jobStatusData'   => json_encode($jobStatusData),

            'recentActivity'  => $recentActivity,
            'lowStockItems'   => $lowStockItems,
        ];

        $mergedData = array_merge($data, ['recentActivity' => $recentActivity]);

        return view('admin/dashboard', $mergedData);
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
