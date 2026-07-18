<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Financial Reports</h3>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/reports/export/financial/csv') ?>" class="bg-white border border-emerald-500 text-emerald-600 hover:bg-emerald-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
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
            <p class="text-xs font-medium text-indigo-100 uppercase tracking-wider">Total Revenue</p>
            <p class="text-xl font-bold mt-1"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalRevenue, 2) ?></p>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-red-100 uppercase tracking-wider">Outstanding</p>
            <p class="text-xl font-bold mt-1"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalOutstanding, 2) ?></p>
        </div>
        <div class="bg-cyan-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-cyan-100 uppercase tracking-wider">Avg Invoice Value</p>
            <p class="text-xl font-bold mt-1"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($avgInvoiceValue, 2) ?></p>
        </div>
        <div class="bg-amber-400 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-amber-800 uppercase tracking-wider">Total Discounts</p>
            <p class="text-xl font-bold text-amber-900 mt-1"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalDiscount, 2) ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Revenue Trend (Last 12 Months)</h4>
        </div>
        <div class="p-6">
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Revenue by Payment Method</h4>
            </div>
            <div class="p-6">
                <canvas id="methodChart" height="200"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Payment Method Breakdown</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($revenueByMethod as $m): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900"><?= esc($m['payment_method']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $m['count'] ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($m['total'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($revenueByMethod)): ?>
                        <tr><td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">No payments in this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Outstanding Invoices</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($outstandingInvoices as $inv): ?>
                    <?php $daysOverdue = max(0, (strtotime('today') - strtotime($inv['due_date'])) / 86400); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($inv['invoice_no']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($inv['customer_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($inv['job_no']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= $inv['invoice_date'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= $inv['due_date'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($inv['grand_total'], 2) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= number_format($inv['amount_paid'], 2) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($inv['balance_due'], 2) ?></td>
                        <td class="px-4 py-3 text-sm"><?= esc($inv['status']) ?></td>
                        <td class="px-4 py-3 text-sm text-red-600 font-medium"><?= $inv['status'] === 'Overdue' ? floor($daysOverdue) . ' days' : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($outstandingInvoices)): ?>
                    <tr><td colspan="10" class="px-4 py-8 text-center text-sm text-gray-400">No outstanding invoices.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Invoice Aging</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bucket</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-sm text-gray-700">Current (not yet due)</td><td class="px-4 py-3 text-sm"><?= $agingBuckets['current']['count'] ?></td><td class="px-4 py-3 text-sm"><?= number_format($agingBuckets['current']['total'], 2) ?></td></tr>
                        <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-sm text-gray-700">1-30 days overdue</td><td class="px-4 py-3 text-sm"><?= $agingBuckets['30']['count'] ?></td><td class="px-4 py-3 text-sm"><?= number_format($agingBuckets['30']['total'], 2) ?></td></tr>
                        <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-sm text-gray-700">31-60 days overdue</td><td class="px-4 py-3 text-sm"><?= $agingBuckets['60']['count'] ?></td><td class="px-4 py-3 text-sm"><?= number_format($agingBuckets['60']['total'], 2) ?></td></tr>
                        <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-sm text-gray-700">61+ days overdue</td><td class="px-4 py-3 text-sm"><?= $agingBuckets['90']['count'] ?></td><td class="px-4 py-3 text-sm"><?= number_format($agingBuckets['90']['total'], 2) ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Petty Cash Summary</h4>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Income</p>
                        <p class="text-lg font-bold text-emerald-600 mt-1"><?= number_format($pettyCashSummary['total_income'] ?? 0, 2) ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Expenses</p>
                        <p class="text-lg font-bold text-red-600 mt-1"><?= number_format($pettyCashSummary['total_expenses'] ?? 0, 2) ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Balance</p>
                        <p class="text-lg font-bold text-gray-900 mt-1"><?= number_format($pettyCashSummary['current_balance'] ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">LPO Spend by Supplier</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LPO Count</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($lpoSpendBySupplier as $s): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($s['name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $s['lpo_count'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($s['total_spend'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lpoSpendBySupplier)): ?>
                    <tr><td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">No LPOs in this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var revCtx = document.getElementById('revenueChart');
    if (revCtx) {
        var labels = <?= json_encode(array_column($revenueByPeriod, 'period')) ?>;
        var data = <?= json_encode(array_map(function($r) { return (float) $r['revenue']; }, $revenueByPeriod)) ?>;
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: data,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    var methodCtx = document.getElementById('methodChart');
    if (methodCtx) {
        var methodLabels = <?= json_encode(array_column($revenueByMethod, 'payment_method')) ?>;
        var methodData = <?= json_encode(array_map(function($m) { return (float) $m['total']; }, $revenueByMethod)) ?>;
        if (methodLabels.length > 0) {
            new Chart(methodCtx, {
                type: 'doughnut',
                data: {
                    labels: methodLabels,
                    datasets: [{
                        data: methodData,
                        backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        } else {
            methodCtx.parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center">No payment data for this period.</p>';
        }
    }
});
</script>
<?= $this->endSection() ?>
