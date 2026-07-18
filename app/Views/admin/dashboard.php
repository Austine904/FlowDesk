<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$pageTitle = 'Dashboard';
helper('activity');
$hasRevenue = array_sum($revenueByMonth ?? [0]) > 0;
$revenueRange = date('M Y', strtotime('-5 months')) . ' – ' . date('M Y');
$jsd = $jobStatusData ?? [];
$tooltipParts = [];
foreach ($jsd as $s => $c) {
    if ($c > 0) {
        $tooltipParts[] = $s . ': ' . $c;
    }
}
$tooltipJobBreakdown = !empty($tooltipParts) ? implode(', ', $tooltipParts) : 'No jobs';
?>

<?php if (($totalJobs ?? null) === null && ($totalRevenue ?? null) === null): ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="animate-pulse">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 rounded-lg"></div>
                <div class="w-16 h-5 bg-gray-200 rounded-full"></div>
            </div>
            <div class="h-8 w-20 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-28 bg-gray-200 rounded"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="animate-pulse">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 rounded-lg"></div>
            </div>
            <div class="h-8 w-28 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-28 bg-gray-200 rounded"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="animate-pulse">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 rounded-lg"></div>
            </div>
            <div class="h-8 w-28 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-28 bg-gray-200 rounded"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="animate-pulse">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 rounded-lg"></div>
            </div>
            <div class="h-8 w-28 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-28 bg-gray-200 rounded"></div>
        </div>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="animate-pulse">
            <div class="h-4 w-48 bg-gray-200 rounded mb-4"></div>
            <div class="h-24 bg-gray-200 rounded"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="animate-pulse">
            <div class="h-4 w-28 bg-gray-200 rounded mb-4"></div>
            <div class="h-32 w-32 bg-gray-200 rounded-full mx-auto"></div>
        </div>
    </div>
</div>

<?php elseif (($totalJobs ?? 0) === 0): ?>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
    <svg class="w-20 h-20 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <h2 class="text-xl font-semibold text-gray-900 mb-2">Welcome to FlowDesk!</h2>
    <p class="text-gray-500 mb-6 max-w-md mx-auto">Get started by creating your first job card. From there you can manage vehicles, customers, inventory, and more.</p>
    <a href="<?= base_url('job_intake') ?>" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Create your first job card →
    </a>
</div>

<?php else: ?>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-lg font-bold text-gray-900"><?= $todayJobsCompleted ?? 0 ?></p>
            <p class="text-xs text-gray-500">Jobs completed today</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </div>
        <div>
            <p class="text-lg font-bold text-gray-900"><?= $todayNewCustomers ?? 0 ?></p>
            <p class="text-xs text-gray-500">New customers today</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-lg font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($todayRevenue ?? 0, 2) ?></p>
            <p class="text-xs text-gray-500">Revenue today</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <?php if (($activeJobs ?? 0) > 0): ?>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full"><?= $activeJobs ?> active</span>
            <?php endif; ?>
        </div>
        <p class="text-2xl font-bold text-gray-900" title="<?= esc($tooltipJobBreakdown) ?>"><?= $totalJobs ?? 0 ?></p>
        <p class="text-sm text-gray-500 mt-1">Total Job Cards</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900" title="Revenue collected this month"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalRevenue ?? 0, 2) ?></p>
        <p class="text-sm text-gray-500 mt-1">This Month Revenue</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900" title="Total outstanding balance across all invoices"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($outstandingBalance ?? 0, 2) ?></p>
        <p class="text-sm text-gray-500 mt-1">Outstanding Balance</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 <?= ($pettyCashBalance ?? 0) >= 0 ? 'bg-emerald-50' : 'bg-red-50' ?> rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 <?= ($pettyCashBalance ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <p class="text-2xl font-bold <?= ($pettyCashBalance ?? 0) >= 0 ? 'text-gray-900' : 'text-red-600' ?>" title="Current petty cash balance">
                <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format(abs($pettyCashBalance ?? 0), 2) ?>
            </p>
            <?php $pcb = $pettyCashBalance ?? 0; ?>
            <?php if ($pcb > 0): ?>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full inline-flex items-center gap-0.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                positive
            </span>
            <?php elseif ($pcb < 0): ?>
            <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full inline-flex items-center gap-0.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                negative
            </span>
            <?php else: ?>
            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">— zero</span>
            <?php endif; ?>
        </div>
        <p class="text-sm text-gray-500 mt-1">Petty Cash Balance</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue Trend (<?= $revenueRange ?>)</h3>
        <?php if ($hasRevenue): ?>
        <canvas id="revenueChart" height="100"></canvas>
        <?php else: ?>
        <div class="flex items-center justify-center h-24 text-sm text-gray-400">No revenue data yet</div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Jobs by Status</h3>
        <canvas id="jobStatusChart"></canvas>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Recent Activity</h3>
    </div>
    <div class="divide-y divide-gray-50">
        <?php if (!empty($recentActivity ?? [])): ?>
        <?php foreach (array_slice($recentActivity ?? [], 0, 8) as $activity): ?>
        <div class="px-6 py-3 flex items-start gap-3 hover:bg-gray-50">
            <div class="w-8 h-8 bg-indigo-50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900">
                    <span class="font-medium"><?= esc($activity['user_name'] ?? 'System') ?></span>
                    <?= esc($activity['description'] ?? '') ?>
                </p>
                <p class="text-xs text-gray-400 mt-0.5"><?= !empty($activity['created_at']) ? timeAgo($activity['created_at']) : '' ?></p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="px-6 py-8 text-center text-sm text-gray-400">No recent activity</div>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Job Cards</h3>
            <a href="<?= base_url('admin/jobs') ?>" class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline font-medium transition-colors">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (!empty($recentJobs)): ?>
            <?php foreach ($recentJobs as $job): ?>
            <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                <div>
                    <p class="text-sm font-medium text-gray-900"><?= esc($job['job_no']) ?></p>
                    <p class="text-xs text-gray-500"><?= esc($job['customer_name'] ?? '') ?> • <?= esc($job['registration_number'] ?? '') ?></p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">
                    <?= esc($job['job_status']) ?>
                </span>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="px-6 py-8 text-center text-sm text-gray-400">No recent job cards</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-6">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="<?= base_url('job_intake') ?>" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Job Intake
                </a>
                <a href="<?= base_url('admin/invoices') ?>" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                    View Invoices
                </a>
                <a href="<?= base_url('admin/lpos/add') ?>" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    New LPO
                </a>
                <a href="<?= base_url('admin/pettycash/add') ?>" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Add Petty Cash Entry
                </a>
                <a href="<?= base_url('admin/reports') ?>" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View Reports
                </a>
            </div>
        </div>

        <?php if (!empty($upcomingEvents ?? [])): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Upcoming Events</h3>
            <div class="space-y-3">
                <?php foreach (array_slice($upcomingEvents, 0, 5) as $event): ?>
                <div class="flex items-start gap-3">
                    <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0" style="background-color: <?= esc($event['color'] ?? '#007bff') ?>"></div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate"><?= esc($event['title'] ?? '') ?></p>
                        <p class="text-xs text-gray-400">
                            <?php if (!empty($event['start_time'])): ?>
                            <?= esc(date('M j, g:i A', strtotime($event['start_time']))) ?>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($event['event_type'])): ?>
                        <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 mt-1"><?= esc($event['event_type']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Alerts</h3>
            <div class="space-y-3">
                <?php if (!empty($lowStockItems)): ?>
                <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-medium text-red-800"><?= count($lowStockItems) ?> items low on stock</p>
                        <a href="<?= base_url('admin/inventory') ?>" class="text-xs text-red-600 hover:underline">View inventory →</a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (($pendingLPOs ?? 0) > 0): ?>
                <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-lg">
                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <div>
                        <p class="text-xs font-medium text-amber-800"><?= $pendingLPOs ?> LPOs pending delivery</p>
                        <a href="<?= base_url('admin/lpos') ?>" class="text-xs text-amber-600 hover:underline">View LPOs →</a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (($overdueInvoiceCount ?? 0) > 0): ?>
                <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-medium text-red-800"><?= $overdueInvoiceCount ?> overdue invoices (<?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($overdueInvoiceTotal ?? 0, 2) ?>)</p>
                        <a href="<?= base_url('admin/invoices') ?>" class="text-xs text-red-600 hover:underline">View invoices →</a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (empty($lowStockItems) && ($pendingLPOs ?? 0) === 0 && ($overdueInvoiceCount ?? 0) === 0): ?>
                <p class="text-xs text-gray-400 text-center py-2">No alerts at this time</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
<?php if ($hasRevenue): ?>
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= $revenueLabels ?? '["Jan","Feb","Mar","Apr","May","Jun"]' ?>,
        datasets: [{
            label: 'Revenue (<?= org_setting('currency_symbol', 'KSh') ?>)',
            data: <?= $revenueByMonth ?? '[0,0,0,0,0,0]' ?>,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79,70,229,0.08)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#4f46e5',
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f3f4f6' },
                ticks: { callback: function(v) { return '<?= org_setting('currency_symbol', 'KSh') ?> ' + v.toLocaleString(); } }
            },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

const statusCtx = document.getElementById('jobStatusChart');
if (statusCtx) {
    const ctx = statusCtx.getContext('2d');
    const jobStatusData = <?= $jobStatusData ?? '{}' ?>;
    const statusLabels = Object.keys(jobStatusData).filter(function(k) { return jobStatusData[k] > 0; });
    const statusCounts = statusLabels.map(function(k) { return jobStatusData[k]; });
    const colors = <?= json_encode($jobStatusColors ?? []) ?>;
    const bgColors = statusLabels.map(function(k) { return colors[k] || '#6b7280'; });
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: bgColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, padding: 12 }
                }
            }
        }
    });
}
</script>
<?= $this->endSection() ?>
