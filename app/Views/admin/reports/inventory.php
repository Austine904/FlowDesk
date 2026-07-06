<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Inventory Reports</h3>
        <div>
            <a href="<?= base_url('admin/reports/export/inventory/csv') ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <form method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-auto">
            <label class="form-label">From Date</label>
            <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
        </div>
        <div class="col-auto">
            <label class="form-label">To Date</label>
            <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Stock Items</h6>
                    <h3><?= $totalStockItems ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body">
                    <h6>Low Stock Items</h6>
                    <h3><?= $lowStockCount ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Out of Stock</h6>
                    <h3><?= $outOfStockCount ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Inventory Value</h6>
                    <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalValue, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Levels Table -->
    <div class="card mb-4">
        <div class="card-header"><strong>Stock Levels</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Part Name</th><th>Part No</th><th>Unit</th><th>Qty in Hand</th><th>Reorder Level</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($stockedItems as $item): ?>
                        <?php
                        $qty = (float) $item['quantity_in_hand'];
                        $reorder = (float) $item['reorder_level'];
                        if ($qty <= 0) $badge = 'danger'; elseif ($qty <= $reorder) $badge = 'warning text-dark'; else $badge = 'success';
                        $statusText = $qty <= 0 ? 'Out of Stock' : ($qty <= $reorder ? 'Low Stock' : 'In Stock');
                        ?>
                        <tr>
                            <td><?= esc($item['name']) ?></td>
                            <td><?= esc($item['part_number'] ?? '-') ?></td>
                            <td><?= esc($item['unit']) ?></td>
                            <td><?= $qty ?></td>
                            <td><?= $reorder ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= $statusText ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($stockedItems)): ?>
                        <tr><td colspan="6" class="text-muted">No stocked items found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert Table -->
    <?php if (!empty($lowStockItems)): ?>
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning"><strong>Low Stock Alerts</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-warning mb-0">
                    <thead><tr><th>Part Name</th><th>Part No</th><th>Qty in Hand</th><th>Reorder Level</th><th>Unit</th></tr></thead>
                    <tbody>
                        <?php foreach ($lowStockItems as $item): ?>
                        <tr>
                            <td><?= esc($item['name']) ?></td>
                            <td><?= esc($item['part_number'] ?? '-') ?></td>
                            <td class="fw-bold text-danger"><?= (float) $item['quantity_in_hand'] ?></td>
                            <td><?= (float) $item['reorder_level'] ?></td>
                            <td><?= esc($item['unit']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Most Used Parts -->
    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header"><strong>Most Used Parts</strong></div>
                <div class="card-body">
                    <canvas id="partsChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header"><strong>Parts Usage</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead><tr><th>Part</th><th>Part No</th><th>Times Used</th><th>Jobs</th></tr></thead>
                            <tbody>
                                <?php foreach ($mostUsedParts as $p): ?>
                                <tr>
                                    <td><?= esc($p['name']) ?></td>
                                    <td><?= esc($p['part_number'] ?? '-') ?></td>
                                    <td><?= (int) $p['total_used'] ?></td>
                                    <td><?= (int) $p['jobs_count'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($mostUsedParts)): ?>
                                <tr><td colspan="4" class="text-muted">No parts used in this period.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parts Spend per Job -->
    <div class="card mb-4">
        <div class="card-header"><strong>Top 10 Jobs by Parts Cost</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Job No</th><th>Customer</th><th>Vehicle</th><th>Parts Cost</th></tr></thead>
                    <tbody>
                        <?php foreach ($partsSpendPerJob as $p): ?>
                        <tr>
                            <td><?= esc($p['job_no']) ?></td>
                            <td><?= esc($p['customer_name']) ?></td>
                            <td><?= esc($p['registration_number']) ?></td>
                            <td><?= number_format($p['parts_cost'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($partsSpendPerJob)): ?>
                        <tr><td colspan="4" class="text-muted">No parts data available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
                        backgroundColor: '#fd7e14'
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
            partsCtx.parentElement.innerHTML = '<p class="text-muted text-center">No parts usage data for this period.</p>';
        }
    }
});
</script>
<?= $this->endSection() ?>
