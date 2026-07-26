<?php
$title = 'Job ' . ($job['job_no'] ?? '');
?>
<?= $this->extend('technician/layout') ?>

<?= $this->section('content') ?>
<div id="jobDetailData" data-job-id="<?= $job['id'] ?>"></div>

<?php if (session()->getFlashdata('errors')): ?>
<div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <ul class="list-disc list-inside text-sm space-y-1">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
        <li><?= esc($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Job Details Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-base font-semibold text-gray-900">Job Details</h3>
    </div>
    <div class="px-6 py-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Job No</span>
                <p class="text-sm font-semibold text-gray-900 mt-0.5"><?= esc($job['job_no'] ?? '') ?></p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</span>
                <p class="text-sm font-semibold text-gray-900 mt-0.5"><?= esc($customer['name'] ?? '') ?></p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle Reg</span>
                <p class="text-sm font-semibold text-gray-900 mt-0.5"><?= esc($vehicle['registration_number'] ?? '') ?></p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mileage In</span>
                <p class="text-sm font-semibold text-gray-900 mt-0.5"><?= esc($job['mileage_in'] ?? '') ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Job Status Card -->
<?php if (!empty($valid_transitions)): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-base font-semibold text-gray-900">Job Status</h3>
    </div>
    <div class="px-6 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Current Status:</span>
                <?php
                $statusColorMap = [
                    'Awaiting Assignment' => 'bg-gray-100 text-gray-800',
                    'Awaiting Diagnosis' => 'bg-blue-50 text-blue-700',
                    'Diagnosis Complete' => 'bg-indigo-50 text-indigo-700',
                    'Quote Sent' => 'bg-indigo-50 text-indigo-700',
                    'Approved' => 'bg-emerald-50 text-emerald-700',
                    'In Progress' => 'bg-indigo-50 text-indigo-700',
                    'Awaiting Parts' => 'bg-amber-50 text-amber-700',
                    'Quality Check' => 'bg-blue-50 text-blue-700',
                    'Ready for Invoice' => 'bg-emerald-50 text-emerald-700',
                    'Paid' => 'bg-emerald-50 text-emerald-700',
                    'Completed' => 'bg-emerald-50 text-emerald-700',
                    'On Hold' => 'bg-amber-50 text-amber-700',
                    'Rework' => 'bg-red-50 text-red-700',
                    'Cancelled' => 'bg-red-50 text-red-700',
                ];
                $badgeClass = $statusColorMap[$job['job_status']] ?? 'bg-gray-100 text-gray-800';
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $badgeClass ?>"><?= esc($job['job_status']) ?></span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-medium text-gray-700">Actions:</span>
                <div class="flex flex-wrap gap-2" id="mechanicTransitionButtons">
                    <?php foreach ($valid_transitions as $nextStatus): ?>
                        <button class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg border border-indigo-300 text-indigo-700 bg-white hover:bg-indigo-50 transition-colors btn-mechanic-status"
                                data-job-id="<?= $job['id'] ?>" data-new-status="<?= $nextStatus ?>"><?= $nextStatus ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div id="mechanicStatusMessage" class="mt-3"></div>
    </div>
</div>
<?php endif; ?>

<!-- Diagnosis Form -->
<form method="POST" action="<?= base_url('mechanic/save_diagnosis') ?>" id="diagnosisForm">
    <?= csrf_field() ?>
    <input type="hidden" name="job_id" value="<?= esc($job['id']) ?>">

    <!-- Diagnosis Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Diagnosis</h3>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <?php $catValue = old('diagnosis_category') ?? ($job['diagnosis_category'] ?? ''); ?>
                <label for="diagnosis_category" class="block text-sm font-medium text-gray-700 mb-1">Job Category</label>
                <select id="diagnosis_category" name="diagnosis_category" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- Select Category --</option>
                    <option value="Engine & Drivetrain" <?= $catValue === 'Engine & Drivetrain' ? 'selected' : '' ?>>Engine & Drivetrain</option>
                    <option value="Brakes & Suspension" <?= $catValue === 'Brakes & Suspension' ? 'selected' : '' ?>>Brakes & Suspension</option>
                    <option value="Electrical & Electronics" <?= $catValue === 'Electrical & Electronics' ? 'selected' : '' ?>>Electrical & Electronics</option>
                    <option value="Transmission & Gearbox" <?= $catValue === 'Transmission & Gearbox' ? 'selected' : '' ?>>Transmission & Gearbox</option>
                    <option value="Cooling System" <?= $catValue === 'Cooling System' ? 'selected' : '' ?>>Cooling System</option>
                    <option value="Air Conditioning" <?= $catValue === 'Air Conditioning' ? 'selected' : '' ?>>Air Conditioning</option>
                    <option value="Body & Paint" <?= $catValue === 'Body & Paint' ? 'selected' : '' ?>>Body & Paint</option>
                    <option value="Tyres & Wheels" <?= $catValue === 'Tyres & Wheels' ? 'selected' : '' ?>>Tyres & Wheels</option>
                    <option value="Routine Service" <?= $catValue === 'Routine Service' ? 'selected' : '' ?>>Routine Service</option>
                    <option value="Diagnostics Only" <?= $catValue === 'Diagnostics Only' ? 'selected' : '' ?>>Diagnostics Only</option>
                    <option value="Other" <?= $catValue === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div>
                <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Notes</label>
                <textarea id="diagnosis" name="diagnosis" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?= esc(old('diagnosis') ?? ($job['diagnosis'] ?? '')) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Parts Required Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Parts Required</h3>
            <button type="button" id="addPartRow" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-plus text-xs"></i> Add Part
            </button>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm" id="partsTable">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider">
                            <th class="text-left px-4 py-3">Part</th>
                            <th class="text-left px-4 py-3">Quantity</th>
                            <th class="text-left px-4 py-3">Unit Price</th>
                            <th class="text-left px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (!empty($job_parts)): ?>
                            <?php $partIdx = 0; ?>
                            <?php foreach ($job_parts as $part): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="hidden" name="parts[<?= $partIdx ?>][inventory_id]" value="<?= esc($part['inventory_id'] ?? '') ?>">
                                    <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent part-search"
                                           data-inventory-id="<?= esc($part['inventory_id'] ?? '') ?>"
                                           value="<?= esc(($part['name'] ?? '') . ' (' . ($part['part_number'] ?? '') . ')') ?>"
                                           placeholder="Search parts...">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" class="w-24 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           name="parts[<?= $partIdx ?>][quantity_required]" value="<?= esc($part['quantity_required'] ?? 1) ?>">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" class="w-28 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent part-unit-price"
                                           name="parts[<?= $partIdx ?>][unit_price]" value="<?= esc($part['unit_price_at_estimate'] ?? 0) ?>">
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors remove-row">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </td>
                            </tr>
                            <?php $partIdx++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Labor Tasks Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Labor Tasks</h3>
            <button type="button" id="addTaskRow" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-plus text-xs"></i> Add Task
            </button>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm" id="tasksTable">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider">
                            <th class="text-left px-4 py-3">Task Name</th>
                            <th class="text-left px-4 py-3">Est. Hours</th>
                            <th class="text-left px-4 py-3">Rate/hr</th>
                            <th class="text-left px-4 py-3">Cost</th>
                            <th class="text-left px-4 py-3">Notes</th>
                            <th class="text-left px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (!empty($job_tasks)): ?>
                            <?php $taskIdx = 0; ?>
                            <?php foreach ($job_tasks as $task): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           name="tasks[<?= $taskIdx ?>][task_name]" value="<?= esc($task['task_name'] ?? '') ?>">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.5" class="w-24 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent task-hours"
                                           name="tasks[<?= $taskIdx ?>][estimated_hours]" value="<?= esc($task['estimated_hours'] ?? 0) ?>">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" class="w-28 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent task-rate"
                                           name="tasks[<?= $taskIdx ?>][rate_per_hour]" value="<?= esc($task['rate_per_hour'] ?? org_setting('default_labor_rate', 1500)) ?>">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" class="w-28 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-600 task-cost"
                                           value="<?= esc(($task['rate_per_hour'] ?? org_setting('default_labor_rate', 1500)) * ($task['estimated_hours'] ?? 0)) ?>" readonly>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           name="tasks[<?= $taskIdx ?>][notes]" value="<?= esc($task['notes'] ?? '') ?>">
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors remove-row">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </td>
                            </tr>
                            <?php $taskIdx++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
            <i class="fas fa-save"></i> Save Diagnosis
        </button>
        <a href="<?= base_url('mechanic/jobs') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            Cancel
        </a>
    </div>
</form>

<!-- Photo Upload Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-base font-semibold text-gray-900">Upload Photo</h3>
    </div>
    <div class="px-6 py-4">
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
            <div class="flex-1 w-full sm:w-auto">
                <label for="photoFile" class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                <input type="file" id="photoFile" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
            </div>
            <div>
                <label for="photoType" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="photoType" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="Progress">Progress</option>
                    <option value="Completion">Completion</option>
                </select>
            </div>
            <div class="flex-1 w-full sm:w-auto">
                <label for="photoCaption" class="block text-sm font-medium text-gray-700 mb-1">Caption (optional)</label>
                <input type="text" id="photoCaption" placeholder="e.g. Brake pad wear" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <button id="photoUploadBtn" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                    <i class="fas fa-upload"></i> Upload
                </button>
                <div id="photoUploadProgress" class="hidden mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-indigo-600 h-1.5 rounded-full animate-pulse" style="width: 60%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Gallery -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-base font-semibold text-gray-900">Job Photos</h3>
    </div>
    <div class="px-6 py-4 space-y-6">
        <?php
        $jobCardPhotoModel = new \App\Models\JobCardPhotoModel();
        $photoTypes = ['Intake', 'Progress', 'Completion'];
        $typeLabels = ['Intake' => 'Intake Photos', 'Progress' => 'Progress Photos', 'Completion' => 'Completion Photos'];
        ?>
        <?php foreach ($photoTypes as $pt): ?>
        <?php $photos = $jobCardPhotoModel->getByJobCardAndType($job['id'], $pt); ?>
        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-3"><?= $typeLabels[$pt] ?></h4>
            <?php if (!empty($photos)): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="photoGallery-<?= $pt ?>">
                <?php foreach ($photos as $photo): ?>
                <div class="relative group">
                    <a href="<?= base_url($photo['file_path']) ?>" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow">
                        <img src="<?= base_url($photo['file_path']) ?>" alt="<?= esc($photo['caption'] ?? $photo['file_name'] ?? 'Job photo') ?>" class="w-full h-32 object-cover">
                    </a>
                    <?php if (!empty($photo['caption'])): ?>
                    <p class="mt-1 text-xs text-gray-500 truncate"><?= esc($photo['caption']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-400 py-3 text-center gallery-empty">No <?= strtolower($typeLabels[$pt]) ?> yet.</p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Time Tracking Widget -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-900"><i class="fas fa-clock mr-2"></i> Time Tracking</h3>
        <div id="clockState"></div>
    </div>
    <div class="px-6 py-4">
        <button id="clockToggle" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors" data-action="clockin">
            <i class="fas fa-play-circle"></i> Clock In
        </button>
        <div class="overflow-x-auto rounded-lg border border-gray-200 mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider">
                        <th class="text-left px-4 py-3">Clock In</th>
                        <th class="text-left px-4 py-3">Clock Out</th>
                        <th class="text-left px-4 py-3">Duration</th>
                        <th class="text-left px-4 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody id="timeLogList" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-400">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.search-parts-dropdown {
    position: absolute;
    z-index: 1000;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    max-height: 200px;
    overflow-y: auto;
    width: 100%;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}
.search-parts-dropdown .part-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
}
.search-parts-dropdown .part-item:hover {
    background-color: #f9fafb;
}
.search-parts-dropdown .part-item:last-child {
    border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add part row — use indexed naming so all fields in one row share the same index
    let partRowIndex = document.querySelectorAll('#partsTable tbody tr').length;
    document.getElementById('addPartRow')?.addEventListener('click', function() {
        const tbody = document.querySelector('#partsTable tbody');
        const idx = partRowIndex++;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-4 py-3">
                <input type="hidden" name="parts[${idx}][inventory_id]" value="">
                <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent part-search" data-inventory-id="" placeholder="Search parts...">
            </td>
            <td class="px-4 py-3"><input type="number" class="w-24 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" name="parts[${idx}][quantity_required]" value="1"></td>
            <td class="px-4 py-3"><input type="number" step="0.01" class="w-28 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent part-unit-price" name="parts[${idx}][unit_price]" value="0"></td>
            <td class="px-4 py-3"><button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors remove-row"><i class="fas fa-trash-alt"></i> Remove</button></td>
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

    // Add task row — use indexed naming so all fields in one row share the same index
    let taskRowIndex = document.querySelectorAll('#tasksTable tbody tr').length;
    document.getElementById('addTaskRow')?.addEventListener('click', function() {
        const tbody = document.querySelector('#tasksTable tbody');
        const idx = taskRowIndex++;
        const defaultRate = <?= org_setting('default_labor_rate', 1500) ?>;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-4 py-3"><input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" name="tasks[${idx}][task_name]"></td>
            <td class="px-4 py-3"><input type="number" step="0.5" class="w-24 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent task-hours" name="tasks[${idx}][estimated_hours]" value="0"></td>
            <td class="px-4 py-3"><input type="number" step="0.01" class="w-28 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent task-rate" name="tasks[${idx}][rate_per_hour]" value="${defaultRate}"></td>
            <td class="px-4 py-3"><input type="text" class="w-28 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-600 task-cost" value="0" readonly></td>
            <td class="px-4 py-3"><input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" name="tasks[${idx}][notes]"></td>
            <td class="px-4 py-3"><button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors remove-row"><i class="fas fa-trash-alt"></i> Remove</button></td>
        `;
        tbody.appendChild(row);
    });

    // Remove row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const btn = e.target.classList.contains('remove-row') ? e.target : e.target.closest('.remove-row');
            btn.closest('tr').remove();
        }
    });

    // Request Part via AJAX (triggered from the search dropdown)
    window.requestPart = function(inventoryId, partName, jobId) {
        var csrf = getCsrfMeta();
        var data = new FormData();
        data.append('inventory_id', inventoryId);
        data.append('note', '');

        var note = prompt('Request note for "' + partName + '" (optional):');
        if (note !== null) {
            data.set('note', note || '');
        } else {
            return;
        }

        fetch(BASE_URL + '/mechanic/jobs/' + jobId + '/request_part', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf.hash
            },
            body: data
        })
        .then(function(res) { return res.json(); })
        .then(function(response) {
            if (response.status === 'success') {
                Swal.fire('Requested!', response.message, 'success');
            } else {
                Swal.fire('Error', response.message || 'Could not request part.', 'error');
            }
        })
        .catch(function() {
            Swal.fire('Error', 'Network error. Please try again.', 'error');
        });
    }

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
                        dropdown.innerHTML = '<div class="part-item text-gray-400">No parts found</div>';
                    } else {
                        data.forEach(function(part) {
                            const item = document.createElement('div');
                            item.className = 'part-item';

                            var stockBadge = '';
                            var isOutOfStock = false;
                            if (part.is_stocked == 0) {
                                stockBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 ms-2">Catalog Only</span>';
                            } else {
                                var qty = parseFloat(part.quantity_in_hand);
                                var reorder = parseFloat(part.reorder_level);
                                if (qty <= 0) {
                                    isOutOfStock = true;
                                    stockBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 ms-2">Out of Stock</span>';
                                } else if (qty <= reorder) {
                                    stockBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ms-2">Low Stock (' + qty + ')</span>';
                                } else {
                                    stockBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ms-2">In Stock (' + qty + ')</span>';
                                }
                            }

                            var requestBtn = '';
                            if (isOutOfStock) {
                                requestBtn = '<button class="request-part-btn ml-2 inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-colors" data-inventory-id="' + part.id + '" data-part-name="' + part.name + '"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Request</button>';
                            }

                            item.innerHTML = part.name + ' (' + (part.part_number || 'N/A') + ') - ' + parseFloat(part.unit_price).toFixed(2) + ' ' + stockBadge + requestBtn;
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

    // Request Part button in search dropdown — stop propagation to prevent select
    document.addEventListener('click', function(e) {
        var reqBtn = e.target.closest('.request-part-btn');
        if (reqBtn) {
            e.stopPropagation();
            var invId = reqBtn.dataset.inventoryId;
            var partName = reqBtn.dataset.partName;
            var jobId = document.getElementById('jobDetailData')?.dataset.jobId || '<?= $job['id'] ?? 0 ?>';
            reqBtn.disabled = true;
            reqBtn.innerHTML = '...';
            window.requestPart(invId, partName, jobId);
            return;
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
            this.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
            msgDiv.innerHTML = '';

            var csrfName = document.querySelector('meta[name="csrf-name"]')?.getAttribute('content');
            var BASE_URL = '<?= base_url() ?>';
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
                    msgDiv.innerHTML = '<span class="text-emerald-600 font-medium text-sm">' + response.message + '</span>';
                    // Reload page to reflect new status
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    msgDiv.innerHTML = '<span class="text-red-600 font-medium text-sm">' + (response.message || 'Error updating status') + '</span>';
                }
            })
            .catch(function() {
                msgDiv.innerHTML = '<span class="text-red-600 font-medium text-sm">Error updating status. Please try again.</span>';
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
    });

    // Form submit validation — ensure all part rows have a selected inventory item
    document.getElementById('diagnosisForm').addEventListener('submit', function(e) {
        var invalidRows = [];
        document.querySelectorAll('input[name$="[inventory_id]"]').forEach(function(input) {
            if (!input.value || input.value === '0') {
                var tr = input.closest('tr');
                if (tr) {
                    var searchInput = tr.querySelector('.part-search');
                    if (searchInput) {
                        searchInput.classList.add('border-red-500', 'focus:ring-red-500');
                    }
                    invalidRows.push(tr);
                }
            }
        });
        if (invalidRows.length > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Incomplete Part Rows',
                text: 'One or more part rows do not have a part selected. Click the red-highlighted search field and select a part from the results, or remove the row.',
                confirmButtonColor: '#4f46e5'
            });
        }
    });
});
</script>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/assets/js/job-time-photo.js') ?>"></script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
