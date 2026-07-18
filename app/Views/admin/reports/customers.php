<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Customer Reports</h3>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/reports/export/customers/csv') ?>" class="bg-white border border-emerald-500 text-emerald-600 hover:bg-emerald-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
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
            <p class="text-xs font-medium text-indigo-100 uppercase tracking-wider">Total Customers</p>
            <p class="text-xl font-bold mt-1"><?= $totalCustomers ?></p>
        </div>
        <div class="bg-emerald-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-emerald-100 uppercase tracking-wider">New This Period</p>
            <p class="text-xl font-bold mt-1"><?= $newThisPeriod ?></p>
        </div>
        <div class="bg-amber-400 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-amber-800 uppercase tracking-wider">With Outstanding</p>
            <p class="text-xl font-bold text-amber-900 mt-1"><?= $outstandingCount ?></p>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-red-100 uppercase tracking-wider">Total Outstanding</p>
            <p class="text-xl font-bold mt-1"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalOutstandingAmount, 2) ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Top 10 Customers by Revenue</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Count</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $rank = 1; ?>
                    <?php foreach ($topCustomers as $c): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700"><?= $rank++ ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($c['name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($c['phone']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $c['invoice_count'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($c['total_paid'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($topCustomers)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">No revenue data for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Customer Visit Frequency</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Count</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($visitFrequency as $c): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($c['name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($c['phone']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $c['visit_count'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= $c['last_visit'] ?? '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($visitFrequency)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No visit data for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Customers with Outstanding Balances</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Count</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Outstanding</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($outstandingCustomers as $c): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($c['name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($c['phone']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($c['email'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $c['invoice_count'] ?></td>
                        <td class="px-4 py-3 text-sm text-red-600 font-bold"><?= number_format($c['total_outstanding'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($outstandingCustomers)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">No customers with outstanding balances.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">New Customers per Month (Last 12 Months)</h4>
        </div>
        <div class="p-6">
            <canvas id="newCustomersChart" height="80"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('newCustomersChart');
    if (ctx) {
        var labels = <?= json_encode(array_column($newCustomersPerMonth, 'period')) ?>;
        var data = <?= json_encode(array_map(function($c) { return (int) $c['count']; }, $newCustomersPerMonth)) ?>;
        if (labels.length > 0) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'New Customers',
                        data: data,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6,182,212,0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        } else {
            ctx.parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center">No customer data available.</p>';
        }
    }
});
</script>
<?= $this->endSection() ?>
