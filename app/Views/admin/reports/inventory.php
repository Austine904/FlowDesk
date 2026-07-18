<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Inventory Reports</h3>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/reports/export/inventory/csv') ?>" class="bg-white border border-emerald-500 text-emerald-600 hover:bg-emerald-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <button onclick="window.print()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
            <input type="date" name="start_date" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" value="<?= $start_date ?>">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
            <input type="date" name="end_date" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" value="<?= $end_date ?>">
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filter</button>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-indigo-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-indigo-100 uppercase tracking-wider">Stock Items</p>
            <p class="text-xl font-bold mt-1"><?= $totalStockItems ?></p>
        </div>
        <div class="bg-amber-400 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-amber-800 uppercase tracking-wider">Low Stock Items</p>
            <p class="text-xl font-bold text-amber-900 mt-1"><?= $lowStockCount ?></p>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-red-100 uppercase tracking-wider">Out of Stock</p>
            <p class="text-xl font-bold mt-1"><?= $outOfStockCount ?></p>
        </div>
        <div class="bg-emerald-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-emerald-100 uppercase tracking-wider">Inventory Value</p>
            <p class="text-xl font-bold mt-1"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalValue, 2) ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Stock Levels</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty in Hand</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reorder Level</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($stockedItems as $item): ?>
                    <?php
                    $qty = (float) $item['quantity_in_hand'];
                    $reorder = (float) $item['reorder_level'];
                    if ($qty <= 0) { $badgeBg = 'bg-red-100'; $badgeText = 'text-red-700'; $statusText = 'Out of Stock'; }
                    elseif ($qty <= $reorder) { $badgeBg = 'bg-amber-100'; $badgeText = 'text-amber-700'; $statusText = 'Low Stock'; }
                    else { $badgeBg = 'bg-emerald-100'; $badgeText = 'text-emerald-700'; $statusText = 'In Stock'; }
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($item['name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($item['part_number'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($item['unit']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= $qty ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= $reorder ?></td>
                        <td class="px-4 py-3 text-sm"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $badgeBg ?> <?= $badgeText ?>"><?= $statusText ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stockedItems)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No stocked items found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($lowStockItems)): ?>
    <div class="bg-white rounded-xl border border-amber-300 shadow-sm">
        <div class="px-6 py-4 border-b border-amber-300 bg-amber-50">
            <h4 class="text-sm font-semibold text-amber-800">Low Stock Alerts</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-amber-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty in Hand</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reorder Level</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($lowStockItems as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($item['name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($item['part_number'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-sm text-red-600 font-bold"><?= (float) $item['quantity_in_hand'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (float) $item['reorder_level'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($item['unit']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
        <div class="lg:col-span-4 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Most Used Parts</h4>
            </div>
            <div class="p-6">
                <canvas id="partsChart" height="200"></canvas>
            </div>
        </div>
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900">Parts Usage</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Times Used</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jobs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($mostUsedParts as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900"><?= esc($p['name']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?= esc($p['part_number'] ?? '-') ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $p['total_used'] ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $p['jobs_count'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($mostUsedParts)): ?>
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No parts used in this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Top 10 Jobs by Parts Cost</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parts Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($partsSpendPerJob as $p): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($p['job_no']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($p['customer_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($p['registration_number']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($p['parts_cost'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($partsSpendPerJob)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No parts data available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var partsCtx = document.getElementById('partsChart');
    if (partsCtx) {
        var partsLabels = <?= json_encode(array_column($mostUsedParts, 'name')) ?>;
        var partsData = <?= json_encode(array_map(function($p) { return (int) $p['total_used']; }, $mostUsedParts)) ?>;
        if (partsLabels.length > 0) {
            new Chart(partsCtx, {
                type: 'bar',
                data: {
                    labels: partsLabels,
                    datasets: [{
                        label: 'Times Used',
                        data: partsData,
                        backgroundColor: '#f97316'
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
            partsCtx.parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center">No parts usage data for this period.</p>';
        }
    }
});
</script>
<?= $this->endSection() ?>
