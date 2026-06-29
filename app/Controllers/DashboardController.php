<?php

namespace App\Controllers;

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
        // Check login and admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        $recentActivity = [];

        // Jobs
        $jobs = $db->query("SELECT id, updated_at, job_status FROM job_cards ORDER BY updated_at DESC LIMIT 3")->getResultArray();
        foreach ($jobs as $job) {
            $recentActivity[] = [
                'type' => 'jobs',
                'icon' => 'bi-briefcase',
                'text' => "Job #{$job['id']} status updated to '{$job['job_status']}'",
                'time' => timeAgo($job['updated_at']),
            ];
        }

        // Users
        $users = $db->query("SELECT id, first_name, last_name, role, created_at FROM users ORDER BY created_at DESC LIMIT 3")->getResultArray();
        foreach ($users as $user) {
            $name = esc($user['first_name'] . ' ' . $user['last_name']);
            $recentActivity[] = [
                'type' => 'users',
                'icon' => 'bi-person-plus',
                'text' => "New user <a href='#' class='activity-link'>{$name}</a> ({$user['role']}) registered by admin.",
                'time' => timeAgo($user['created_at']),
            ];
        }

        // Vehicles
        $vehicles = $db->query("SELECT registration_number, make, model, owner_id, created_at FROM vehicles ORDER BY created_at DESC LIMIT 3")->getResultArray();
        foreach ($vehicles as $v) {
            $vehicleText = "{$v['make']} {$v['model']} ({$v['registration_number']})";
            $recentActivity[] = [
                'type' => 'vehicles',
                'icon' => 'bi-car-front',
                'text' => "New vehicle <a href='#' class='activity-link'>{$vehicleText}</a> registered.",
                'time' => timeAgo($v['created_at']),
            ];
        }

        //New Job card
        $jobCards = $db->query("SELECT id, vehicle_id, diagnosis, created_at FROM job_cards ORDER BY created_at DESC LIMIT 3")->getResultArray();
        foreach ($jobCards as $jobCard) {
            $vehicleregistration = $db->table('vehicles')
                ->select('registration_number')
                ->where('id', $jobCard['vehicle_id'])
                ->get()
                ->getRow();
            $vehicleregistration = esc($vehicleregistration->registration_number ?? 'Unknown Vehicle');
            $diagnosis = esc($jobCard['diagnosis']);
            $recentActivity[] = [
                'type' => 'job_cards',
                'icon' => 'bi-file-earmark-text',
                'text' => "New job card added for vehicle <a href='#' class='activity-link'>{$vehicleregistration}</a> with description: {$diagnosis}",
                'time' => timeAgo($jobCard['created_at']),
            ];
        }

        // Sort all activity by timestamp descending
        usort($recentActivity, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));


        // Count total users
        $userCount = $db->table('users')
            ->selectCount('id', 'total_users')
            ->get()
            ->getRow()
            ->total_users ?? 0;

        // Count total vehicles
        $vehicleCount = $db->table('vehicles')
            // ->selectCount('id', 'total_vehicles')
            ->selectCount('status', 'total_vehicles')
            ->where('status', 'On Job')
            ->get()
            ->getRow()
            ->total_vehicles ?? 0;

        // Latest 5 users
        $latestUsers = $db->table('users')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Latest 5 vehicles
        $latestVehicles = $db->table('vehicles')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Get job status counts in a single query
        $jobStatusQuery = $db->table('job_cards')
            ->select("job_status, COUNT(*) as count")
            ->groupBy("job_status")
            ->get()
            ->getResult();


        // Prepare job status data
        // $jobStatusData = [];
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


        //statusColors
        $statusColors = [
            'Awaiting Diagnosis' => '#007bff', // Bootstrap Primary Blue
            'Diagnosis Complete' => '#ffc107', // Bootstrap Warning Yellow
            'Approved' => '#17a2b8',           // Bootstrap Info Cyan
            'In Progress' => '#6f42c1',         // Bootstrap Purple
            'Awaiting Parts' => '#fd7e14',      // Bootstrap Orange
            'Quality Check' => '#20c997',       // Bootstrap Teal
            'Ready for Invoice' => '#e83e8c',   // Bootstrap Pink
            'Paid' => '#28a745',                // Bootstrap Success Green
            'Completed' => '#28a745',           // Bootstrap Success Green (same as Paid, or a shade different)
            'Cancelled' => '#dc3545',           // Bootstrap Danger Red
            'Rework' => '#6c757d',              // Bootstrap Secondary Gray
            'On Hold' => '#343a40',             // Bootstrap Dark Gray
            'Quote Sent' => '#6610f2'           // Bootstrap Indigo (if you have this status)
            // Add more statuses and their desired colors here
        ];

        $defaultColor = '#999999'; // Fallback color for undefined statuses
        $defaultBorderColor = '#ffffff'; // White border for doughnut segments



        // Initialize default values
        $jobStatusData = [

            'Awaiting Diagnosis' => 0,
            'Diagnosis Complete' => 0,
            'Approved' => 0,
            'In Progress' => 0,
            'Awaiting Parts' => 0,
            'Quality Check' => 0,
            'Ready for Invoice' => 0,
            'Paid' => 0,
            'Completed' => 0,
            'Cancelled' => 0,
            'Rework' => 0,
        ];

        foreach ($jobStatusQuery as $row) {
            $currentStatus = $row->job_status;
            $count= (int)$row->count;

              $labels[] = $currentStatus;
            $counts[] = $count;
            
            // Assign specific color or default
            $backgroundColors[] = $statusColors[$currentStatus] ?? $defaultColor;
            $borderColor[] = $defaultBorderColor;

            $jobStatusTotals[$currentStatus] = $count;
            $status = $row->job_status;
            if (array_key_exists($status, $jobStatusData)) {
                $jobStatusData[$status] = (int)$row->count;
            }
        }

        // Count total jobs
        $totalJobsQuery = array_sum($jobStatusData);

        $pendingLPOs = 0; // placeholder until LPO module is built

        $data = [
            'pendingLPOs'     => $pendingLPOs,
            'userCount'       => $userCount,
            'vehicleCount'    => $vehicleCount,
            'latestUsers'     => $latestUsers,
            'latestVehicles'  => $latestVehicles,

            'awaitingDiagnosisJobs' => $jobStatusData['Awaiting Diagnosis'],
            'diagnosedJobs' => $jobStatusData['Diagnosis Complete'],
            'approvedJobs'    => $jobStatusData['Approved'],
            'inProgressJobs'  => $jobStatusData['In Progress'],
            'awaitingPartsJobs' => $jobStatusData['Awaiting Parts'],
            'qualityCheckJobs' => $jobStatusData['Quality Check'],
            'readyForInvoiceJobs' => $jobStatusData['Ready for Invoice'],
            'paidJobs'        => $jobStatusData['Paid'],
            'completedJobs'   => $jobStatusData['Completed'],
            'cancelledJobs'   => $jobStatusData['Cancelled'],
            'reworkJobs'      => $jobStatusData['Rework'],
            'activeJobs'      => $jobStatusData['In Progress'] + $jobStatusData['Awaiting Parts'] + $jobStatusData['Quality Check'] + $jobStatusData['Ready for Invoice'],
            'totalJobs'       => $totalJobsQuery,
            'jobStatusData'   => json_encode($jobStatusData),

            'recentActivity'  => $recentActivity,
        ];



        // Merge the data arrays
        $mergedData = array_merge($data, ['recentActivity' => $recentActivity]);

        // Return the view with the combined data
        return view('admin/dashboard', $mergedData);
    }

    public function mechanic()
    {
        return $this->restrictTo('mechanic', 'mechanic_dashboard');
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
