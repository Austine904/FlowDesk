<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Financial Reports</h3>
        <div>
            <a href="<?= base_url('admin/reports/export/financial/csv') ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
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
                    <h6>Total Revenue</h6>
                    <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalRevenue, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Outstanding</h6>
                    <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalOutstanding, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Avg Invoice Value</h6>
                    <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($avgInvoiceValue, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body">
                    <h6>Total Discounts</h6>
                    <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalDiscount, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="card mb-4">
        <div class="card-header"><strong>Revenue Trend (Last 12 Months)</strong></div>
        <div class="card-body">
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    <!-- Revenue by Payment Method -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><strong>Revenue by Payment Method</strong></div>
                <div class="card-body">
                    <canvas id="methodChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><strong>Payment Method Breakdown</strong></div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Method</th><th>Count</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php foreach ($revenueByMethod as $m): ?>
                            <tr>
                                <td><?= esc($m['payment_method']) ?></td>
                                <td><?= (int) $m['count'] ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($m['total'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($revenueByMethod)): ?>
                            <tr><td colspan="3" class="text-muted">No payments in this period.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Invoices -->
    <div class="card mb-4">
        <div class="card-header"><strong>Outstanding Invoices</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Invoice No</th><th>Customer</th><th>Job No</th><th>Date</th><th>Due</th><th>Grand Total</th><th>Paid</th><th>Balance</th><th>Status</th><th>Days Overdue</th></tr></thead>
                    <tbody>
                        <?php foreach ($outstandingInvoices as $inv): ?>
                        <?php $daysOverdue = max(0, (strtotime('today') - strtotime($inv['due_date'])) / 86400); ?>
                        <tr>
                            <td><?= esc($inv['invoice_no']) ?></td>
                            <td><?= esc($inv['customer_name']) ?></td>
                            <td><?= esc($inv['job_no']) ?></td>
                            <td><?= $inv['invoice_date'] ?></td>
                            <td><?= $inv['due_date'] ?></td>
                            <td><?= number_format($inv['grand_total'], 2) ?></td>
                            <td><?= number_format($inv['amount_paid'], 2) ?></td>
                            <td><?= number_format($inv['balance_due'], 2) ?></td>
                            <td><?= esc($inv['status']) ?></td>
                            <td><?= $inv['status'] === 'Overdue' ? floor($daysOverdue) . ' days' : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($outstandingInvoices)): ?>
                        <tr><td colspan="10" class="text-muted">No outstanding invoices.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Invoice Aging -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><strong>Invoice Aging</strong></div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Bucket</th><th>Count</th><th>Amount</th></tr></thead>
                        <tbody>
                            <tr><td>Current (not yet due)</td><td><?= $agingBuckets['current']['count'] ?></td><td><?= number_format($agingBuckets['current']['total'], 2) ?></td></tr>
                            <tr><td>1-30 days overdue</td><td><?= $agingBuckets['30']['count'] ?></td><td><?= number_format($agingBuckets['30']['total'], 2) ?></td></tr>
                            <tr><td>31-60 days overdue</td><td><?= $agingBuckets['60']['count'] ?></td><td><?= number_format($agingBuckets['60']['total'], 2) ?></td></tr>
                            <tr><td>61+ days overdue</td><td><?= $agingBuckets['90']['count'] ?></td><td><?= number_format($agingBuckets['90']['total'], 2) ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><strong>Petty Cash Summary</strong></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h6>Income</h6>
                            <h5 class="text-success"><?= number_format($pettyCashSummary['total_income'] ?? 0, 2) ?></h5>
                        </div>
                        <div class="col-4">
                            <h6>Expenses</h6>
                            <h5 class="text-danger"><?= number_format($pettyCashSummary['total_expenses'] ?? 0, 2) ?></h5>
                        </div>
                        <div class="col-4">
                            <h6>Balance</h6>
                            <h5><?= number_format($pettyCashSummary['current_balance'] ?? 0, 2) ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LPO Spend by Supplier -->
    <div class="card mb-4">
        <div class="card-header"><strong>LPO Spend by Supplier</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Supplier</th><th>LPO Count</th><th>Total Spend</th></tr></thead>
                    <tbody>
                        <?php foreach ($lpoSpendBySupplier as $s): ?>
                        <tr>
                            <td><?= esc($s['name']) ?></td>
                            <td><?= (int) $s['lpo_count'] ?></td>
                            <td><?= number_format($s['total_spend'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($lpoSpendBySupplier)): ?>
                        <tr><td colspan="3" class="text-muted">No LPOs in this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue trend chart
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
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
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

    // Payment method doughnut chart
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
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        } else {
            methodCtx.parentElement.innerHTML = '<p class="text-muted text-center">No payment data for this period.</p>';
        }
    }
});
</script>
<?= $this->endSection() ?>
