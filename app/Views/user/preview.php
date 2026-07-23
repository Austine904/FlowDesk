<?php
$pageTitle = 'Preview — Confirm New User';
$title = 'Preview — Confirm New User';
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Preview User Details</h1>
                <p class="text-sm text-gray-500">Review before saving</p>
            </div>
        </div>

        <?php $step1 = session('step1_data'); ?>
        <?php $step2 = session('step2_data'); ?>
        <?php $step3 = session('step3_data'); ?>

        <!-- Company Information -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Company Information</h2>
                <a href="<?= base_url('user/add_step1') ?>" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">Edit</a>
            </div>
            <?php if (!empty($step1['profile_picture'])): ?>
            <div class="flex items-center gap-4 mb-3">
                <img src="<?= base_url($step1['profile_picture']) ?>" alt="Profile" class="w-20 h-20 rounded-lg object-cover border border-gray-200">
            </div>
            <?php endif; ?>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Role</dt><dd class="text-gray-900 font-medium"><?= $step1['role'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Company ID</dt><dd class="text-gray-900 font-medium"><?= $step1['company_id'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Date of Employment</dt><dd class="text-gray-900 font-medium"><?= $step1['date_of_employment'] ?? '' ?></dd></div>
            </dl>
        </div>

        <hr class="border-gray-200 mb-6">

        <!-- Personal Information -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Personal Information</h2>
                <a href="<?= base_url('user/add_step2') ?>" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">Edit</a>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">First Name</dt><dd class="text-gray-900 font-medium"><?= $step2['first_name'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Last Name</dt><dd class="text-gray-900 font-medium"><?= $step2['last_name'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Date of Birth</dt><dd class="text-gray-900 font-medium"><?= $step2['dob'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">National ID</dt><dd class="text-gray-900 font-medium"><?= $step2['national_id'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Gender</dt><dd class="text-gray-900 font-medium"><?= $step2['gender'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Phone Number</dt><dd class="text-gray-900 font-medium"><?= $step2['phone_number'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Home Address</dt><dd class="text-gray-900 font-medium"><?= $step2['address'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="text-gray-900 font-medium"><?= $step2['email'] ?? '' ?></dd></div>
            </dl>
        </div>

        <hr class="border-gray-200 mb-6">

        <!-- Next of Kin -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Next of Kin</h2>
                <a href="<?= base_url('user/add_step3') ?>" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">Edit</a>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">First Name</dt><dd class="text-gray-900 font-medium"><?= $step3['kin_first_name'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Last Name</dt><dd class="text-gray-900 font-medium"><?= $step3['kin_last_name'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Relationship</dt><dd class="text-gray-900 font-medium"><?= $step3['relationship'] ?? '' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd class="text-gray-900 font-medium"><?= $step3['kin_phone_number'] ?? '' ?></dd></div>
            </dl>
        </div>

        <div class="flex justify-between pt-4 border-t border-gray-200">
            <div class="flex gap-3">
                <a href="<?= base_url('admin/users') ?>"
                   class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Cancel
                </a>
                <button type="button" onclick="window.location.href = BASE_URL + 'user/add_step3'"
                        class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Previous
                </button>
            </div>
            <form method="POST" action="<?= base_url('user/saveUser') ?>" id="saveUserForm">
                <?= csrf_field() ?>
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Confirm & Save
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
