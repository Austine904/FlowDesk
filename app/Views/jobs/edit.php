<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4">Edit Job</h3>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url('admin/jobs/update/' . $job['id']) ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="job_name" class="form-label">Job Name</label>
                    <input type="text" class="form-control" id="job_name" name="job_name" value="<?= esc($job['job_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= esc($job['description'] ?? '') ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?= $job['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="completed" <?= $job['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $job['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="assigned_to" class="form-label">Assigned To</label>
                        <select class="form-select" id="assigned_to" name="assigned_to">
                            <option value="">Select User</option>
                            <?php if (!empty($service_advisors)): ?>
                                <?php foreach ($service_advisors as $advisor): ?>
                                    <option value="<?= esc($advisor['id']) ?>" <?= ($job['assigned_to'] ?? '') == $advisor['id'] ? 'selected' : '' ?>><?= esc($advisor['first_name'] . ' ' . $advisor['last_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Job</button>
                    <a href="<?= base_url('admin/jobs') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
