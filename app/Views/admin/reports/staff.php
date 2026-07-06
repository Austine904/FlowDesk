<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Staff Reports</h3>
        <div>
            <a href="<?= base_url('admin/reports/export/staff/csv') ?>" class="btn btn-outline-success btn-sm">
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
                    <h6>Total Staff</h6>
                    <h3><?= $totalStaff ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Active Actions</h6>
                    <h3><?= $staffActive ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Status Changes</h6>
                    <h3><?= $statusChanges ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body">
                    <h6>Payments Recorded</h6>
                    <h3><?= $paymentsRecorded ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs per Service Advisor -->
    <div class="card mb-4">
        <div class="card-header"><strong>Jobs per Service Advisor</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Name</th><th>Company ID</th><th>Total Jobs</th><th>Completed</th></tr></thead>
                    <tbody>
                        <?php foreach ($advisorJobs as $a): ?>
                        <tr>
                            <td><?= esc($a['first_name'] . ' ' . $a['last_name']) ?></td>
                            <td><?= esc($a['company_id']) ?></td>
                            <td><?= (int) $a['total_jobs'] ?></td>
                            <td><?= (int) $a['completed'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($advisorJobs)): ?>
                        <tr><td colspan="4" class="text-muted">No advisor data for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="card mb-4">
        <div class="card-header"><strong>Activity Log</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Date/Time</th><th>Staff Member</th><th>Action</th><th>Entity</th><th>Description</th></tr></thead>
                    <tbody>
                        <?php $actionColors = [
                            'status_change' => 'bg-primary',
                            'payment_recorded' => 'bg-success',
                            'lpo_created' => 'bg-warning text-dark',
                            'lpo_received' => 'bg-warning text-dark',
                            'job_created' => 'bg-purple',
                            'diagnosis_saved' => 'bg-teal',
                            'user_created' => 'bg-secondary',
                            'petty_cash_entry' => 'bg-warning text-dark',
                        ]; ?>
                        <?php foreach ($activityLog as $log): ?>
                        <?php $badge = $actionColors[$log['action']] ?? 'bg-secondary'; ?>
                        <tr>
                            <td><?= $log['created_at'] ?></td>
                            <td><?= esc($log['user_name'] ?? '') ?></td>
                            <td><span class="badge <?= $badge ?>"><?= esc($log['action']) ?></span></td>
                            <td><?= esc($log['entity_type']) ?> #<?= $log['entity_id'] ?></td>
                            <td><?= esc($log['description']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($activityLog)): ?>
                        <tr><td colspan="5" class="text-muted">No activity for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple { background-color: #6f42c1 !important; color: white !important; }
.bg-teal { background-color: #20c997 !important; color: white !important; }
</style>
<?= $this->endSection() ?>
