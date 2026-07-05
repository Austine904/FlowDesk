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

    <form method="POST" action="<?= base_url('mechanic/save_diagnosis') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="job_id" value="<?= esc($job['id']) ?>">

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
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($job_tasks)): ?>
                            <?php foreach ($job_tasks as $task): ?>
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

    // Add task row
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
                fetch('<?= base_url('mechanic/search_parts') ?>?query=' + encodeURIComponent(query), {
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
                            item.textContent = part.name + ' (' + part.part_number + ') - $' + parseFloat(part.unit_price).toFixed(2);
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
});
</script>
<?= $this->endSection() ?>