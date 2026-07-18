<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4">Edit Job Card — <?= esc($job['job_no']) ?></h3>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <!-- Read-only context: customer & vehicle -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Customer</h6>
                    <p class="mb-1"><strong><?= esc($customer['name'] ?? '—') ?></strong></p>
                    <p class="mb-1 text-muted"><?= esc($customer['phone'] ?? '—') ?></p>
                    <p class="mb-0 text-muted"><?= esc($customer['email'] ?? '—') ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Vehicle</h6>
                    <p class="mb-1"><strong><?= esc($vehicle['registration_number'] ?? '—') ?></strong></p>
                    <p class="mb-1 text-muted"><?= esc(($vehicle['make'] ?? '') . ' ' . ($vehicle['model'] ?? '') . ' (' . ($vehicle['year_of_manufacture'] ?? '—') . ')') ?></p>
                    <p class="mb-0 text-muted">VIN: <?= esc($vehicle['vin'] ?? '—') ?></p>
                </div>
            </div>

            <hr class="mb-4">

            <form method="POST" action="<?= base_url('admin/jobs/update/' . $job['id']) ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="job_no" class="form-label">Job Number</label>
                    <input type="text" class="form-control" id="job_no" value="<?= esc($job['job_no']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="reported_problem" class="form-label">Reported Problem</label>
                    <textarea class="form-control" id="reported_problem" name="reported_problem" rows="3" minlength="10" required><?= esc($job['diagnosis']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="initial_damage_notes" class="form-label">Initial Damage Notes</label>
                    <textarea class="form-control" id="initial_damage_notes" name="initial_damage_notes" rows="3" maxlength="500"><?= esc($job['initial_damage_notes'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="mileage_in" class="form-label">Mileage In</label>
                        <input type="number" class="form-control" id="mileage_in" name="mileage_in" min="0" value="<?= esc($job['mileage_in']) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fuel_level" class="form-label">Fuel Level</label>
                        <select class="form-select" id="fuel_level" name="fuel_level" required>
                            <?php foreach (['Empty', '1/4', '1/2', '3/4', 'Full'] as $level): ?>
                                <option value="<?= $level ?>" <?= $job['fuel_level'] === $level ? 'selected' : '' ?>><?= $level ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="job_status" class="form-label">Status</label>
                        <select class="form-select" id="job_status" name="job_status" required>
                            <?php foreach (['Awaiting Assignment', 'Awaiting Diagnosis', 'In Progress', 'Awaiting Parts', 'Completed', 'Cancelled'] as $status): ?>
                                <option value="<?= $status ?>" <?= $job['job_status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="assigned_service_advisor_id" class="form-label">Service Advisor</label>
                        <select class="form-select" id="assigned_service_advisor_id" name="assigned_service_advisor_id" required>
                            <option value="">Select Advisor</option>
                            <?php if (!empty($service_advisors)): ?>
                                <?php foreach ($service_advisors as $advisor): ?>
                                    <option value="<?= esc($advisor['id']) ?>" <?= $job['assigned_service_advisor_id'] == $advisor['id'] ? 'selected' : '' ?>>
                                        <?= esc($advisor['first_name'] . ' ' . $advisor['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="assigned_mechanic_id" class="form-label">Assigned Mechanic</label>
                        <select class="form-select" id="assigned_mechanic_id" name="assigned_mechanic_id">
                            <option value="">Unassigned</option>
                            <?php if (!empty($mechanics)): ?>
                                <?php foreach ($mechanics as $mechanic): ?>
                                    <option value="<?= esc($mechanic['id']) ?>" <?= ($job['assigned_mechanic_id'] ?? '') == $mechanic['id'] ? 'selected' : '' ?>>
                                        <?= esc($mechanic['first_name'] . ' ' . $mechanic['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date In</label>
                        <input type="text" class="form-control" value="<?= esc($job['date_in']) ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Time In</label>
                        <input type="text" class="form-control" value="<?= esc(substr($job['time_in'], 0, 5)) ?>" disabled>
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