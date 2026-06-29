<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4">Mechanic Diagnosis</h3>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Job Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Job No:</strong> <?= esc($job['job_no'] ?? '') ?></div>
                <div class="col-md-3"><strong>Customer:</strong> <?= esc($job['customer_name'] ?? '') ?></div>
                <div class="col-md-3"><strong>Vehicle Reg:</strong> <?= esc($job['registration_number'] ?? '') ?></div>
                <div class="col-md-3"><strong>Mileage In:</strong> <?= esc($job['mileage_in'] ?? '') ?></div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= base_url('job_intake/save_diagnosis') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="job_card_id" value="<?= esc($job['id']) ?>">

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Diagnosis</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis Notes</label>
                    <textarea class="form-control" id="diagnosis" name="diagnosis" rows="4"><?= esc($job['diagnosis'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Parts Required</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addPartRow">Add Part</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="partsTable">
                    <thead>
                        <tr>
                            <th>Part</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($parts)): ?>
                            <?php foreach ($parts as $part): ?>
                            <tr>
                                <td><input type="text" class="form-control part-search" name="parts[][inventory_id]" value="<?= esc($part['inventory_id'] ?? '') ?>"></td>
                                <td><input type="number" class="form-control" name="parts[][quantity_required]" value="<?= esc($part['quantity_required'] ?? 1) ?>"></td>
                                <td><input type="number" step="0.01" class="form-control" name="parts[][unit_price_at_estimate]" value="<?= esc($part['unit_price_at_estimate'] ?? 0) ?>"></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Labor Tasks</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addTaskRow">Add Task</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="tasksTable">
                    <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Est. Hours</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tasks)): ?>
                            <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><input type="text" class="form-control" name="tasks[][task_name]" value="<?= esc($task['task_name'] ?? '') ?>"></td>
                                <td><input type="number" step="0.5" class="form-control" name="tasks[][estimated_hours]" value="<?= esc($task['estimated_hours'] ?? 0) ?>"></td>
                                <td><input type="text" class="form-control" name="tasks[][notes]" value="<?= esc($task['notes'] ?? '') ?>"></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Diagnosis</button>
            <a href="<?= base_url('admin/jobs') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addPartRow')?.addEventListener('click', function() {
        const tbody = document.querySelector('#partsTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" class="form-control part-search" name="parts[][inventory_id]"></td>
            <td><input type="number" class="form-control" name="parts[][quantity_required]" value="1"></td>
            <td><input type="number" step="0.01" class="form-control" name="parts[][unit_price_at_estimate]" value="0"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
        `;
        tbody.appendChild(row);
    });

    document.getElementById('addTaskRow')?.addEventListener('click', function() {
        const tbody = document.querySelector('#tasksTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" class="form-control" name="tasks[][task_name]"></td>
            <td><input type="number" step="0.5" class="form-control" name="tasks[][estimated_hours]" value="0"></td>
            <td><input type="text" class="form-control" name="tasks[][notes]"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
        `;
        tbody.appendChild(row);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
<?= $this->endSection() ?>
