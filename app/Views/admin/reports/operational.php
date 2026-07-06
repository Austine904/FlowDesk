<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Operational Reports</h3>
        <div>
            <a href="<?= base_url('admin/reports/export/operational/csv') ?>" class="btn btn-outline-success btn-sm">
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
                    <h6>Total Jobs</h6>
                    <h3><?= $totalJobs ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Completed</h6>
                    <h3><?= $totalCompleted ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Avg Turnaround</h6>
                    <h3><?= round($avgTurnaround, 1) ?> days</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Overdue Jobs</h6>
                    <h3><?= $totalOverdue ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs by Status (doughnut) + Completed per month (bar) -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><strong>Jobs by Status</strong></div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><strong>Jobs Completed per Month</strong></div>
                <div class="card-body">
                    <canvas id="completedChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs per Mechanic -->
    <div class="card mb-4">
        <div class="card-header"><strong>Jobs per Mechanic</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Mechanic</th><th>Total Jobs</th><th>Avg Turnaround (days)</th></tr></thead>
                    <tbody>
                        <?php foreach ($jobsPerMechanic as $m): ?>
                        <tr>
                            <td><?= esc($m['first_name'] . ' ' . $m['last_name']) ?></td>
                            <td><?= (int) $m['total_jobs'] ?></td>
                            <td><?= round((float) ($m['avg_turnaround'] ?? 0), 1) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($jobsPerMechanic)): ?>
                        <tr><td colspan="3" class="text-muted">No mechanic data for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Overdue Jobs -->
    <div class="card mb-4">
        <div class="card-header"><strong>Overdue Jobs</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Job No</th><th>Customer</th><th>Vehicle</th><th>Mechanic</th><th>Expected End</th><th>Days Overdue</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($overdueJobs as $j): ?>
                        <tr>
                            <td><?= esc($j['job_no']) ?></td>
                            <td><?= esc($j['customer_name']) ?></td>
                            <td><?= esc($j['registration_number']) ?></td>
                            <td><?= esc(($j['first_name'] ?? '') . ' ' . ($j['last_name'] ?? '')) ?></td>
                            <td><?= $j['end_date'] ?></td>
                            <td class="text-danger fw-bold"><?= (int) $j['days_overdue'] ?> days</td>
                            <td><?= esc($j['job_status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($overdueJobs)): ?>
                        <tr><td colspan="7" class="text-muted">No overdue jobs.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Jobs by Diagnosis Category -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><strong>Jobs by Diagnosis Category</strong></div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><strong>Category Breakdown</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead><tr><th>Category</th><th>Count</th></tr></thead>
                            <tbody>
                                <?php foreach ($jobsByCategory as $cat): ?>
                                <tr>
                                    <td><?= esc($cat['diagnosis_category']) ?></td>
                                    <td><?= (int) $cat['count'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($jobsByCategory)): ?>
                                <tr><td colspan="2" class="text-muted">No categorized jobs in this period.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sublet Spend by Supplier -->
    <div class="card mb-4">
        <div class="card-header"><strong>Sublet Spend by Supplier</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Supplier</th><th>Count</th><th>Total Cost</th></tr></thead>
                    <tbody>
                        <?php foreach ($subletSpend as $s): ?>
                        <tr>
                            <td><?= esc($s['name']) ?></td>
                            <td><?= (int) $s['count'] ?></td>
                            <td><?= number_format($s['total_cost'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($subletSpend)): ?>
                        <tr><td colspan="3" class="text-muted">No sublet spend in this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Jobs by status doughnut
    var statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        var statusLabels = <?= json_encode(array_column($jobsByStatus, 'job_status')) ?>;
        var statusData = <?= json_encode(array_map(function($s) { return (int) $s['count']; }, $jobsByStatus)) ?>;
        var colorMap = {
            'Awaiting Assignment': '#6c757d','Awaiting Diagnosis': '#007bff','Diagnosis Complete': '#ffc107',
            'Approved': '#17a2b8','In Progress': '#6f42c1','Awaiting Parts': '#fd7e14','Quality Check': '#20c997',
            'Ready for Invoice': '#e83e8c','Quote Sent': '#6610f2','Paid': '#28a745','Completed': '#28a745',
            'On Hold': '#343a40','Rework': '#6c757d','Cancelled': '#dc3545'
        };
        var colors = statusLabels.map(function(l) { return colorMap[l] || '#999'; });
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{ data: statusData, backgroundColor: colors }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // Completed per month bar chart
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
                        backgroundColor: '#28a745'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        } else {
            completedCtx.parentElement.innerHTML = '<p class="text-muted text-center">No completed jobs yet.</p>';
        }
    }

    // Diagnosis category horizontal bar chart
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
                        backgroundColor: '#17a2b8'
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
            catCtx.parentElement.innerHTML = '<p class="text-muted text-center">No categorized data for this period.</p>';
        }
    }
});
</script>
<?= $this->endSection() ?>
