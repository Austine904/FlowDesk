<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $pageTitle = 'Dashboard'; ?>

<!-- Stats Grid (4 cards) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <!-- Card: Total Jobs -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Active</span>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= $totalJobs ?? 0 ?></p>
        <p class="text-sm text-gray-500 mt-1">Total Job Cards</p>
    </div>

    <!-- Card: This Month Revenue -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalRevenue ?? 0, 2) ?></p>
        <p class="text-sm text-gray-500 mt-1">This Month Revenue</p>
    </div>

    <!-- Card: Outstanding Balance -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($outstandingBalance ?? 0, 2) ?></p>
        <p class="text-sm text-gray-500 mt-1">Outstanding Balance</p>
    </div>

    <!-- Card: Petty Cash Balance -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 <?= ($pettyCashBalance ?? 0) >= 0 ? 'bg-emerald-50' : 'bg-red-50' ?> rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 <?= ($pettyCashBalance ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold <?= ($pettyCashBalance ?? 0) >= 0 ? 'text-gray-900' : 'text-red-600' ?>">
            <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format(abs($pettyCashBalance ?? 0), 2) ?>
        </p>
        <p class="text-sm text-gray-500 mt-1">Petty Cash Balance</p>
    </div>
</div>

<!-- Second row: Revenue chart + Job Status chart -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- Revenue Trend (spans 2 cols) -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue Trend (Last 6 Months)</h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    <!-- Job Status Doughnut -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Jobs by Status</h3>
        <canvas id="jobStatusChart"></canvas>
    </div>
</div>

<!-- Third row: Recent Jobs + Quick Actions + Alerts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Recent Jobs (spans 2 cols) -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Job Cards</h3>
            <a href="<?= base_url('admin/jobs') ?>" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">View all →</a>
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

    <!-- Right column: Quick actions + Alerts -->
    <div class="space-y-6">

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="<?= base_url('admin/jobs') ?>" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition-colors">
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

        <!-- Alerts: Low Stock + Pending LPOs -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Alerts</h3>
            <div class="space-y-3">
                <?php if (!empty($lowStockItems)): ?>
                <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-lg">
                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-medium text-amber-800"><?= count($lowStockItems) ?> items low on stock</p>
                        <a href="<?= base_url('admin/inventory') ?>" class="text-xs text-amber-600 hover:underline">View inventory →</a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (($pendingLPOs ?? 0) > 0): ?>
                <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <div>
                        <p class="text-xs font-medium text-blue-800"><?= $pendingLPOs ?> LPOs pending delivery</p>
                        <a href="<?= base_url('admin/lpos') ?>" class="text-xs text-blue-600 hover:underline">View LPOs →</a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (empty($lowStockItems) && ($pendingLPOs ?? 0) === 0): ?>
                <p class="text-xs text-gray-400 text-center py-2">No alerts at this time</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
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

const statusCtx = document.getElementById('jobStatusChart').getContext('2d');
const jobStatusData = <?= $jobStatusData ?? '{}' ?>;
const statusLabels = Object.keys(jobStatusData).filter(function(k) { return jobStatusData[k] > 0; });
const statusCounts = statusLabels.map(function(k) { return jobStatusData[k]; });
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusCounts,
            backgroundColor: ['#4f46e5','#10b981','#f59e0b','#ef4444','#6b7280','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#e11d48','#64748b','#84cc16','#06b6d4'],
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
</script>
<?= $this->endSection() ?>
