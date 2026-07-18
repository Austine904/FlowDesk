<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Operational Reports</h3>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/reports/export/operational/csv') ?>" class="bg-white border border-emerald-500 text-emerald-600 hover:bg-emerald-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <button onclick="window.print()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" name="start_date" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" value="<?= $start_date ?>">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" name="end_date" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" value="<?= $end_date ?>">
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filter</button>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-indigo-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-indigo-100 uppercase tracking-wider">Total Jobs</p>
            <p class="text-xl font-bold mt-1"><?= $totalJobs ?></p>
        </div>
        <div class="bg-emerald-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-emerald-100 uppercase tracking-wider">Completed</p>
            <p class="text-xl font-bold mt-1"><?= $totalCompleted ?></p>
        </div>
        <div class="bg-cyan-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-cyan-100 uppercase tracking-wider">Avg Turnaround</p>
            <p class="text-xl font-bold mt-1"><?= round($avgTurnaround, 1) ?> days</p>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-red-100 uppercase tracking-wider">Overdue Jobs</p>
            <p class="text-xl font-bold mt-1"><?= $totalOverdue ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Jobs by Status</h4>
            </div>
            <div class="p-6">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Jobs Completed per Month</h4>
            </div>
            <div class="p-6">
                <canvas id="completedChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Jobs per Mechanic</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mechanic</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jobs</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Turnaround (days)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($jobsPerMechanic as $m): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($m['first_name'] . ' ' . $m['last_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $m['total_jobs'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= round((float) ($m['avg_turnaround'] ?? 0), 1) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($jobsPerMechanic)): ?>
                    <tr><td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">No mechanic data for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Overdue Jobs</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mechanic</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected End</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($overdueJobs as $j): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($j['job_no']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($j['customer_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($j['registration_number']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc(($j['first_name'] ?? '') . ' ' . ($j['last_name'] ?? '')) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= $j['end_date'] ?></td>
                        <td class="px-4 py-3 text-sm text-red-600 font-bold"><?= (int) $j['days_overdue'] ?> days</td>
                        <td class="px-4 py-3 text-sm"><?= esc($j['job_status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($overdueJobs)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">No overdue jobs.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Jobs by Diagnosis Category</h4>
            </div>
            <div class="p-6">
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Category Breakdown</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($jobsByCategory as $cat): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900"><?= esc($cat['diagnosis_category']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $cat['count'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($jobsByCategory)): ?>
                        <tr><td colspan="2" class="px-4 py-8 text-center text-sm text-gray-400">No categorized jobs in this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Sublet Spend by Supplier</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($subletSpend as $s): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($s['name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $s['count'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($s['total_cost'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($subletSpend)): ?>
                    <tr><td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">No sublet spend in this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        var statusLabels = <?= json_encode(array_column($jobsByStatus, 'job_status')) ?>;
        var statusData = <?= json_encode(array_map(function($s) { return (int) $s['count']; }, $jobsByStatus)) ?>;
        var colorMap = {
            'Awaiting Assignment': '#6b7280','Awaiting Diagnosis': '#4f46e5','Diagnosis Complete': '#f59e0b',
            'Approved': '#06b6d4','In Progress': '#8b5cf6','Awaiting Parts': '#f97316','Quality Check': '#14b8a6',
            'Ready for Invoice': '#ec4899','Quote Sent': '#a855f7','Paid': '#10b981','Completed': '#10b981',
            'On Hold': '#374151','Rework': '#6b7280','Cancelled': '#ef4444'
        };
        var colors = statusLabels.map(function(l) { return colorMap[l] || '#9ca3af'; });
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{ data: statusData, backgroundColor: colors }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    var completedCtx = document.getElementById('completedChart');
    if (completedCtx) {
        var compLabels = <?= json_encode(array_column($completedPerPeriod, 'period')) ?>;
        var compData = <?= json_encode(array_map(function($c) { return (int) $c['count']; }, $completedPerPeriod)) ?>;
        if (compLabels.length > 0) {
            new Chart(completedCtx, {
                type: 'bar',
                data: {
                    labels: compLabels,
                    datasets: [{
                        label: 'Completed',
                        data: compData,
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        } else {
            completedCtx.parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center">No completed jobs yet.</p>';
        }
    }

    var catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        var catLabels = <?= json_encode(array_column($jobsByCategory, 'diagnosis_category')) ?>;
        var catData = <?= json_encode(array_map(function($c) { return (int) $c['count']; }, $jobsByCategory)) ?>;
        if (catLabels.length > 0) {
            new Chart(catCtx, {
                type: 'bar',
                data: {
                    labels: catLabels,
                    datasets: [{
                        label: 'Jobs',
                        data: catData,
                        backgroundColor: '#06b6d4'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        } else {
            catCtx.parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center">No categorized data for this period.</p>';
        }
    }
});
</script>
<?= $this->endSection() ?>
