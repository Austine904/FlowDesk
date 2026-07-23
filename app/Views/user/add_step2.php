<?php
$pageTitle = 'Add User — Step 2 of 3';
$title = 'Add User — Step 2 of 3';
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add New User</h1>
                <p class="text-sm text-gray-500">Step 2 of 3 — Personal Information</p>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-600">Step 2 of 3</span>
                <span class="text-sm text-gray-500">66%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: 66%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500">
                <span>Account Setup</span>
                <span class="text-indigo-600 font-medium">Personal Info</span>
                <span>Next of Kin</span>
            </div>
        </div>

        <form id="step2Form" method="POST" action="<?= base_url('user/add_step2') ?>">
            <?= csrf_field() ?>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               value="<?= esc($step2Data['first_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               value="<?= esc($step2Data['last_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="dob" name="dob" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               value="<?= esc($step2Data['dob'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="national_id" class="block text-sm font-medium text-gray-700 mb-1">National ID Number</label>
                        <input type="text" id="national_id" name="national_id" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               value="<?= esc($step2Data['national_id'] ?? '') ?>">
                    </div>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-2">Gender</span>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="gender" value="Male" required
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                   <?= ($step2Data['gender'] ?? '') === 'Male' ? 'checked' : '' ?>> Male
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="gender" value="Female"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                   <?= ($step2Data['gender'] ?? '') === 'Female' ? 'checked' : '' ?>> Female
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="gender" value="Other"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                   <?= ($step2Data['gender'] ?? '') === 'Other' ? 'checked' : '' ?>> Other
                        </label>
                    </div>
                </div>
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           value="<?= esc($step2Data['phone_number'] ?? '') ?>">
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                    <input type="text" id="address" name="address" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           value="<?= esc($step2Data['address'] ?? '') ?>">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           value="<?= esc($step2Data['email'] ?? '') ?>">
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="button" onclick="window.location.href = BASE_URL + 'user/add_step1'"
                        class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Previous
                </button>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Next Step
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#step2Form').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: BASE_URL + 'user/add_step2',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.href = BASE_URL + 'user/add_step3';
            } else {
                alert(response.message || 'Validation failed.');
            }
        },
        error: function() {
            alert('Failed to submit Step 2.');
        }
    });
});
</script>
<?= $this->endSection() ?>
