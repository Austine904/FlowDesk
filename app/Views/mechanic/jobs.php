<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4">My Assigned Jobs</h3>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="mechanicJobsTable">
                    <thead>
                        <tr>
                            <th>Job No</th>
                            <th>Customer</th>
                            <th>Vehicle Reg</th>
                            <th>Status</th>
                            <th>Date In</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jobs)): ?>
                            <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?= esc($job['job_no'] ?? '') ?></td>
                                <td><?= esc($job['customer_name'] ?? '') ?></td>
                                <td><?= esc($job['registration_number'] ?? '') ?></td>
                                <td><?= esc($job['job_status'] ?? '') ?></td>
                                <td><?= esc($job['date_in'] ?? '') ?></td>
                                <td>
                                    <a href="<?= base_url('mechanic/jobs/' . ($job['id'] ?? '')) ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-wrench"></i> Diagnose
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No jobs assigned to you yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($jobs)): ?>
<script>
$(document).ready(function() {
    $('#mechanicJobsTable').DataTable({
        order: [[4, 'desc']]
    });
});
</script>
<?php endif; ?>

<?= $this->endSection() ?>