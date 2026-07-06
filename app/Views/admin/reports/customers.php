<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Customer Reports</h3>
        <div>
            <a href="<?= base_url('admin/reports/export/customers/csv') ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <form method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-auto">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
        </div>
        <div class="col-auto">
            <label class="form-label">End Date</label>
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
                    <h6>Total Customers</h6>
                    <h3><?= $totalCustomers ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>New This Period</h6>
                    <h3><?= $newThisPeriod ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body">
                    <h6>With Outstanding</h6>
                    <h3><?= $outstandingCount ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Total Outstanding</h6>
                    <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalOutstandingAmount, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers by Revenue -->
    <div class="card mb-4">
        <div class="card-header"><strong>Top 10 Customers by Revenue</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Invoice Count</th><th>Total Paid</th></tr></thead>
                    <tbody>
                        <?php $rank = 1; ?>
                        <?php foreach ($topCustomers as $c): ?>
                        <tr>
                            <td><?= $rank++ ?></td>
                            <td><?= esc($c['name']) ?></td>
                            <td><?= esc($c['phone']) ?></td>
                            <td><?= (int) $c['invoice_count'] ?></td>
                            <td><?= number_format($c['total_paid'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($topCustomers)): ?>
                        <tr><td colspan="5" class="text-muted">No revenue data for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Visit Frequency -->
    <div class="card mb-4">
        <div class="card-header"><strong>Customer Visit Frequency</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Name</th><th>Phone</th><th>Visit Count</th><th>Last Visit</th></tr></thead>
                    <tbody>
                        <?php foreach ($visitFrequency as $c): ?>
                        <tr>
                            <td><?= esc($c['name']) ?></td>
                            <td><?= esc($c['phone']) ?></td>
                            <td><?= (int) $c['visit_count'] ?></td>
                            <td><?= $c['last_visit'] ?? '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($visitFrequency)): ?>
                        <tr><td colspan="4" class="text-muted">No visit data for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customers with Outstanding Balances -->
    <div class="card mb-4">
        <div class="card-header"><strong>Customers with Outstanding Balances</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Invoice Count</th><th>Total Outstanding</th></tr></thead>
                    <tbody>
                        <?php foreach ($outstandingCustomers as $c): ?>
                        <tr>
                            <td><?= esc($c['name']) ?></td>
                            <td><?= esc($c['phone']) ?></td>
                            <td><?= esc($c['email'] ?? '-') ?></td>
                            <td><?= (int) $c['invoice_count'] ?></td>
                            <td class="text-danger fw-bold"><?= number_format($c['total_outstanding'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($outstandingCustomers)): ?>
                        <tr><td colspan="5" class="text-muted">No customers with outstanding balances.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- New Customers per Month -->
    <div class="card mb-4">
        <div class="card-header"><strong>New Customers per Month (Last 12 Months)</strong></div>
        <div class="card-body">
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
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23,162,184,0.1)',
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
            ctx.parentElement.innerHTML = '<p class="text-muted text-center">No customer data available.</p>';
        }
    }
});
</script>
<?= $this->endSection() ?>
