<?php
$pageTitle = 'Add User — Step 3 of 3';
$title = 'Add User — Step 3 of 3';
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add New User</h1>
                <p class="text-sm text-gray-500">Step 3 of 3 — Next of Kin</p>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-600">Step 3 of 3</span>
                <span class="text-sm text-gray-500">99%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: 99%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500">
                <span>Account Setup</span>
                <span>Personal Info</span>
                <span class="text-indigo-600 font-medium">Next of Kin</span>
            </div>
        </div>

        <form id="step3Form" method="POST" action="<?= base_url('user/add_step3') ?>">
            <?= csrf_field() ?>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kin_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="kin_first_name" name="kin_first_name" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               value="<?= esc($step3Data['kin_first_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="kin_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="kin_last_name" name="kin_last_name" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               value="<?= esc($step3Data['kin_last_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="relationship" class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <input type="text" id="relationship" name="relationship" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               value="<?= esc($step3Data['relationship'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="kin_phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="kin_phone_number" name="kin_phone_number" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               value="<?= esc($step3Data['kin_phone_number'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="button" onclick="window.location.href = BASE_URL + 'user/add_step2'"
                        class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Previous
                </button>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#step3Form').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: BASE_URL + 'user/add_step3',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.href = BASE_URL + 'user/preview';
            } else {
                alert('Something went wrong!');
            }
        },
        error: function() {
            alert('Failed to submit Step 3.');
        }
    });
});
</script>
<?= $this->endSection() ?>
