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
                <div class="col-md-3"><strong>Customer:</strong> <?= esc($customer['name'] ?? '') ?></div>
                <div class="col-md-3"><strong>Vehicle Reg:</strong> <?= esc($vehicle['registration_number'] ?? '') ?></div>
                <div class="col-md-3"><strong>Mileage In:</strong> <?= esc($job['mileage_in'] ?? '') ?></div>
            </div>
        </div>
    </div>

    <?php if (!empty($valid_transitions)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Job Status</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <strong>Current Status:</strong>
                    <span class="badge fs-6 ms-2 <?php
                        $colorMap = [
                            'Awaiting Assignment' => 'bg-secondary',
                            'Awaiting Diagnosis' => 'bg-info',
                            'Diagnosis Complete' => 'bg-primary',
                            'Quote Sent' => 'bg-primary',
                            'Approved' => 'bg-success',
                            'In Progress' => 'bg-primary',
                            'Awaiting Parts' => 'bg-warning text-dark',
                            'Quality Check' => 'bg-info',
                            'Ready for Invoice' => 'bg-success',
                            'Paid' => 'bg-success',
                            'Completed' => 'bg-success',
                            'On Hold' => 'bg-warning text-dark',
                            'Rework' => 'bg-danger',
                            'Cancelled' => 'bg-danger',
                        ];
                        echo $colorMap[$job['job_status']] ?? 'bg-secondary';
                    ?>"><?= esc($job['job_status']) ?></span>
                </div>
                <div class="col-md-8">
                    <strong>Actions:</strong>
                    <div class="d-inline-flex flex-wrap gap-1 ms-2" id="mechanicTransitionButtons">
                        <?php foreach ($valid_transitions as $nextStatus): ?>
                            <button class="btn btn-sm btn-outline-primary btn-mechanic-status" data-job-id="<?= $job['id'] ?>" data-new-status="<?= $nextStatus ?>"><?= $nextStatus ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div id="mechanicStatusMessage" class="mt-2"></div>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('mechanic/save_diagnosis') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="job_id" value="<?= esc($job['id']) ?>">

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Diagnosis</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="diagnosis_category" class="form-label">Job Category</label>
                    <select class="form-select" id="diagnosis_category" name="diagnosis_category">
                        <option value="">-- Select Category --</option>
                        <option value="Engine & Drivetrain" <?= ($job['diagnosis_category'] ?? '') === 'Engine & Drivetrain' ? 'selected' : '' ?>>Engine & Drivetrain</option>
                        <option value="Brakes & Suspension" <?= ($job['diagnosis_category'] ?? '') === 'Brakes & Suspension' ? 'selected' : '' ?>>Brakes & Suspension</option>
                        <option value="Electrical & Electronics" <?= ($job['diagnosis_category'] ?? '') === 'Electrical & Electronics' ? 'selected' : '' ?>>Electrical & Electronics</option>
                        <option value="Transmission & Gearbox" <?= ($job['diagnosis_category'] ?? '') === 'Transmission & Gearbox' ? 'selected' : '' ?>>Transmission & Gearbox</option>
                        <option value="Cooling System" <?= ($job['diagnosis_category'] ?? '') === 'Cooling System' ? 'selected' : '' ?>>Cooling System</option>
                        <option value="Air Conditioning" <?= ($job['diagnosis_category'] ?? '') === 'Air Conditioning' ? 'selected' : '' ?>>Air Conditioning</option>
                        <option value="Body & Paint" <?= ($job['diagnosis_category'] ?? '') === 'Body & Paint' ? 'selected' : '' ?>>Body & Paint</option>
                        <option value="Tyres & Wheels" <?= ($job['diagnosis_category'] ?? '') === 'Tyres & Wheels' ? 'selected' : '' ?>>Tyres & Wheels</option>
                        <option value="Routine Service" <?= ($job['diagnosis_category'] ?? '') === 'Routine Service' ? 'selected' : '' ?>>Routine Service</option>
                        <option value="Diagnostics Only" <?= ($job['diagnosis_category'] ?? '') === 'Diagnostics Only' ? 'selected' : '' ?>>Diagnostics Only</option>
                        <option value="Other" <?= ($job['diagnosis_category'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
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
                        <?php if (!empty($job_parts)): ?>
                            <?php foreach ($job_parts as $part): ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="parts[][inventory_id]" value="<?= esc($part['inventory_id'] ?? '') ?>">
                                    <input type="text" class="form-control part-search" data-inventory-id="<?= esc($part['inventory_id'] ?? '') ?>" value="<?= esc(($part['name'] ?? '') . ' (' . ($part['part_number'] ?? '') . ')') ?>">
                                </td>
                                <td><input type="number" class="form-control" name="parts[][quantity_required]" value="<?= esc($part['quantity_required'] ?? 1) ?>"></td>
                                <td><input type="number" step="0.01" class="form-control part-unit-price" name="parts[][unit_price]" value="<?= esc($part['unit_price_at_estimate'] ?? 0) ?>"></td>
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
                            <th>Rate/hr</th>
                            <th>Cost</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($job_tasks)): ?>
                            <?php foreach ($job_tasks as $task): ?>
                            <tr>
                                <td><input type="text" class="form-control" name="tasks[][task_name]" value="<?= esc($task['task_name'] ?? '') ?>"></td>
                                <td><input type="number" step="0.5" class="form-control task-hours" name="tasks[][estimated_hours]" value="<?= esc($task['estimated_hours'] ?? 0) ?>"></td>
                                <td><input type="number" step="0.01" class="form-control task-rate" name="tasks[][rate_per_hour]" value="<?= esc($task['rate_per_hour'] ?? org_setting('default_labor_rate', 1500)) ?>"></td>
                                <td><input type="text" class="form-control task-cost" value="<?= esc(($task['rate_per_hour'] ?? org_setting('default_labor_rate', 1500)) * ($task['estimated_hours'] ?? 0)) ?>" readonly></td>
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
            <a href="<?= base_url('mechanic/jobs') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.search-parts-dropdown {
    position: absolute;
    z-index: 1000;
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    width: 100%;
}
.search-parts-dropdown .part-item {
    padding: 6px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f0f2f5;
}
.search-parts-dropdown .part-item:hover {
    background-color: #e9ecef;
}
.search-parts-dropdown .part-item:last-child {
    border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add part row
    document.getElementById('addPartRow')?.addEventListener('click', function() {
        const tbody = document.querySelector('#partsTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="hidden" name="parts[][inventory_id]" value="">
                <input type="text" class="form-control part-search" data-inventory-id="" placeholder="Search parts...">
            </td>
            <td><input type="number" class="form-control" name="parts[][quantity_required]" value="1"></td>
            <td><input type="number" step="0.01" class="form-control part-unit-price" name="parts[][unit_price]" value="0"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
        `;
        tbody.appendChild(row);
    });

    // Auto-calculate labor cost when hours or rate changes
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('task-hours') || e.target.classList.contains('task-rate')) {
            const tr = e.target.closest('tr');
            const hours = parseFloat(tr.querySelector('.task-hours').value) || 0;
            const rate = parseFloat(tr.querySelector('.task-rate').value) || 0;
            const costInput = tr.querySelector('.task-cost');
            costInput.value = (hours * rate).toFixed(2);
        }
    });

    // Add task row
    document.getElementById('addTaskRow')?.addEventListener('click', function() {
        const tbody = document.querySelector('#tasksTable tbody');
        const row = document.createElement('tr');
        const defaultRate = <?= org_setting('default_labor_rate', 1500) ?>;
        row.innerHTML = `
            <td><input type="text" class="form-control" name="tasks[][task_name]"></td>
            <td><input type="number" step="0.5" class="form-control task-hours" name="tasks[][estimated_hours]" value="0"></td>
            <td><input type="number" step="0.01" class="form-control task-rate" name="tasks[][rate_per_hour]" value="${defaultRate}"></td>
            <td><input type="text" class="form-control task-cost" value="0" readonly></td>
            <td><input type="text" class="form-control" name="tasks[][notes]"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
        `;
        tbody.appendChild(row);
    });

    // Remove row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });

    // Parts search autocomplete
    let searchTimeout;
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('part-search')) {
            clearTimeout(searchTimeout);
            const input = e.target;
            const query = input.value.trim();

            // Remove existing dropdown
            const existing = input.closest('td').querySelector('.search-parts-dropdown');
            if (existing) existing.remove();

            if (query.length < 2) return;

            searchTimeout = setTimeout(function() {
                fetch('<?= base_url('mechanic/inventory/search') ?>?query=' + encodeURIComponent(query), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    const td = input.closest('td');
                    td.style.position = 'relative';
                    const dropdown = document.createElement('div');
                    dropdown.className = 'search-parts-dropdown';

                    if (data.length === 0) {
                        dropdown.innerHTML = '<div class="part-item text-muted">No parts found</div>';
                    } else {
                        data.forEach(function(part) {
                            const item = document.createElement('div');
                            item.className = 'part-item';

                            var stockBadge = '';
                            if (part.is_stocked == 0) {
                                stockBadge = '<span class="badge bg-secondary ms-1">Catalog Only</span>';
                            } else {
                                var qty = parseFloat(part.quantity_in_hand);
                                var reorder = parseFloat(part.reorder_level);
                                if (qty <= 0) {
                                    stockBadge = '<span class="badge bg-danger ms-1">Out of Stock</span>';
                                } else if (qty <= reorder) {
                                    stockBadge = '<span class="badge bg-warning text-dark ms-1">Low Stock (' + qty + ')</span>';
                                } else {
                                    stockBadge = '<span class="badge bg-success ms-1">In Stock (' + qty + ')</span>';
                                }
                            }

                            item.innerHTML = part.name + ' (' + (part.part_number || 'N/A') + ') - ' + parseFloat(part.unit_price).toFixed(2) + ' ' + stockBadge;
                            item.dataset.id = part.id;
                            item.dataset.name = part.name;
                            item.dataset.partNumber = part.part_number || '';
                            item.dataset.unitPrice = part.unit_price || 0;
                            item.addEventListener('click', function() {
                                const tr = input.closest('tr');
                                tr.querySelector('.part-search').value = this.dataset.name + ' (' + this.dataset.partNumber + ')';
                                tr.querySelector('.part-search').dataset.inventoryId = this.dataset.id;
                                tr.querySelector('input[name$="[inventory_id]"]').value = this.dataset.id;
                                const priceInput = tr.querySelector('.part-unit-price');
                                if (priceInput) priceInput.value = parseFloat(this.dataset.unitPrice).toFixed(2);
                                dropdown.remove();
                            });
                            dropdown.appendChild(item);
                        });
                    }
                    td.appendChild(dropdown);
                })
                .catch(function() {});
            }, 300);
        }
    });

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('part-search')) {
            document.querySelectorAll('.search-parts-dropdown').forEach(function(el) { el.remove(); });
        }
    });

    // Mechanic status transition buttons
    document.querySelectorAll('.btn-mechanic-status').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const jobId = this.dataset.jobId;
            const newStatus = this.dataset.newStatus;
            const msgDiv = document.getElementById('mechanicStatusMessage');
            const originalText = this.textContent;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            msgDiv.innerHTML = '';

            var csrfName = document.querySelector('meta[name="csrf-name"]')?.getAttribute('content');
            var csrfHash = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            var formData = new URLSearchParams();
            formData.append('new_status', newStatus);
            if (csrfName && csrfHash) {
                formData.append(csrfName, csrfHash);
            }

            fetch(BASE_URL + '/mechanic/jobs/update_status/' + jobId, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: formData.toString()
            })
            .then(function(res) { return res.json(); })
            .then(function(response) {
                if (response.status === 'success') {
                    msgDiv.innerHTML = '<span class="text-success">' + response.message + '</span>';
                    // Reload page to reflect new status
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    msgDiv.innerHTML = '<span class="text-danger">' + (response.message || 'Error updating status') + '</span>';
                }
            })
            .catch(function() {
                msgDiv.innerHTML = '<span class="text-danger">Error updating status. Please try again.</span>';
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
    });
});
</script>
<?= $this->endSection() ?>