<?php
$sublet = $sublet ?? null;
$job_cards = $job_cards ?? [];
$sublet_providers = $sublet_providers ?? [];

$is_edit = (bool)$sublet;
$pageTitle = $is_edit ? 'Edit Sublet' : 'Add Sublet';

$id = $is_edit ? esc($sublet['id']) : '';
$job_card_id = $is_edit ? esc($sublet['job_card_id']) : '';
$sublet_provider_id = $is_edit ? esc($sublet['sublet_provider_id']) : '';
$description = $is_edit ? esc($sublet['description']) : '';
$cost = $is_edit ? esc($sublet['cost']) : '';
$status = $is_edit ? esc($sublet['status']) : 'Pending';
$date_sent = $is_edit ? esc($sublet['date_sent']) : date('Y-m-d');
$date_returned = $is_edit && $sublet['date_returned'] ? esc($sublet['date_returned']) : '';
$notes = $is_edit ? esc($sublet['notes']) : '';
?>

<form id="subletForm">
    <?= csrf_field() ?>
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
    <?php endif; ?>

    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1">
                <label for="job_card_id" class="block text-sm font-medium text-gray-700">Job Card <span class="text-red-500">*</span></label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="job_card_id" name="job_card_id" required>
                    <option value="">Select Job Card</option>
                    <?php foreach ($job_cards as $job_card): ?>
                        <option value="<?= esc($job_card['id']) ?>"
                            data-vehicle-reg="<?= esc($job_card['registration_number'] ?? 'N/A') ?>"
                            <?= ($job_card['id'] == $job_card_id) ? 'selected' : '' ?>>
                            <?= esc($job_card['job_no'] ?? 'N/A') ?> (<?= esc($job_card['registration_number'] ?? 'N/A') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-red-600 mt-1 hidden" id="job_card_id-error">Please select a Job Card.</p>
            </div>

            <div class="space-y-1">
                <label for="sublet_provider_id" class="block text-sm font-medium text-gray-700">Sublet Provider <span class="text-red-500">*</span></label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="sublet_provider_id" name="sublet_provider_id" required>
                    <option value="">Select Provider</option>
                    <?php foreach ($sublet_providers as $provider): ?>
                        <option value="<?= esc($provider['id']) ?>" <?= ($provider['id'] == $sublet_provider_id) ? 'selected' : '' ?>>
                            <?= esc($provider['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-red-600 mt-1 hidden" id="sublet_provider_id-error">Please select a Sublet Provider.</p>
            </div>
        </div>

        <div class="space-y-1">
            <label for="description" class="block text-sm font-medium text-gray-700">Description of Sublet Work <span class="text-red-500">*</span></label>
            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="description" name="description" rows="3" required><?= $description ?></textarea>
            <p class="text-xs text-red-600 mt-1 hidden" id="description-error">Description is required (min 5 characters).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1">
                <label for="cost" class="block text-sm font-medium text-gray-700">Cost <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="cost" name="cost" value="<?= $cost ?>" required min="0">
                <p class="text-xs text-red-600 mt-1 hidden" id="cost-error">Cost is required and must be a non-negative number.</p>
            </div>

            <div class="space-y-1">
                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="status" name="status" required>
                    <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= ($status == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= ($status == 'Completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="Invoiced" <?= ($status == 'Invoiced') ? 'selected' : '' ?>>Invoiced</option>
                    <option value="Paid" <?= ($status == 'Paid') ? 'selected' : '' ?>>Paid</option>
                    <option value="Cancelled" <?= ($status == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <p class="text-xs text-red-600 mt-1 hidden" id="status-error">Please select a status.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1">
                <label for="date_sent" class="block text-sm font-medium text-gray-700">Date Sent <span class="text-red-500">*</span></label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="date_sent" name="date_sent" value="<?= $date_sent ?>" required>
                <p class="text-xs text-red-600 mt-1 hidden" id="date_sent-error">Date Sent is required.</p>
            </div>

            <div class="space-y-1">
                <label for="date_returned" class="block text-sm font-medium text-gray-700">Date Returned</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="date_returned" name="date_returned" value="<?= $date_returned ?>">
                <p class="text-xs text-red-600 mt-1 hidden" id="date_returned-error">Date Returned must be after Date Sent.</p>
            </div>
        </div>

        <div class="space-y-1">
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="notes" name="notes" rows="2"><?= $notes ?></textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="closeModal('actionModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white hidden" id="formSpinner" role="status"></div>
                <span class="button-text"><?= $is_edit ? 'Update Sublet' : 'Add Sublet' ?></span>
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subletForm = document.getElementById('subletForm');
        const submitBtn = subletForm.querySelector('button[type="submit"]');
        const spinner = document.getElementById('formSpinner');

        subletForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            document.querySelectorAll('[id$="-error"]').forEach(el => {
                el.classList.add('hidden');
            });
            subletForm.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500', 'ring-red-500');
            });

            let isValid = true;
            const showError = function(fieldId, message) {
                const input = document.getElementById(fieldId);
                if (input) {
                    input.classList.add('border-red-500', 'ring-red-500');
                }
                const errorEl = document.getElementById(fieldId + '-error');
                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.classList.remove('hidden');
                }
                isValid = false;
            };

            subletForm.querySelectorAll('[required]').forEach(input => {
                if (!input.value) {
                    showError(input.id, 'This field is required.');
                }
            });

            const costInput = document.getElementById('cost');
            if (costInput.value && parseFloat(costInput.value) < 0) {
                showError('cost', 'Cost cannot be negative.');
            }

            const dateSentInput = document.getElementById('date_sent');
            const dateReturnedInput = document.getElementById('date_returned');
            if (dateReturnedInput.value && dateSentInput.value && new Date(dateReturnedInput.value) < new Date(dateSentInput.value)) {
                showError('date_returned', 'Date Returned cannot be earlier than Date Sent.');
            }

            if (!isValid) return;

            submitBtn.disabled = true;
            spinner.classList.remove('hidden');
            submitBtn.querySelector('.button-text').classList.add('invisible');

            const formData = new FormData(subletForm);
            const url = '<?= base_url('admin/sublets/save') ?>';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    Swal.fire('Success!', result.message, 'success');
                    closeModal('actionModal');
                    document.dispatchEvent(new CustomEvent('subletSaved'));
                } else {
                    let errorMessage = result.message || 'Failed to save sublet.';
                    if (result.errors) {
                        for (const field in result.errors) {
                            showError(field, result.errors[field]);
                        }
                        Swal.fire({ title: 'Validation Failed!', html: "Please correct the highlighted errors.", icon: 'error' });
                    } else {
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                }
            } catch (error) {
                console.error('Error saving sublet:', error);
                Swal.fire('Error!', 'An unexpected error occurred: ' + error.message, 'error');
            } finally {
                submitBtn.disabled = false;
                spinner.classList.add('hidden');
                submitBtn.querySelector('.button-text').classList.remove('invisible');
            }
        });
    });
</script>
