<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $pageTitle = 'My Profile'; ?>

<div class="max-w-3xl mx-auto space-y-6">

    <!-- Profile Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Profile Information</h3>
            <p class="text-xs text-gray-500 mt-1">Update your personal details and avatar</p>
        </div>

        <form method="post" action="<?= base_url('admin/profile/update') ?>" enctype="multipart/form-data" class="p-6">
            <?= csrf_field() ?>

            <?php if (session()->getFlashdata('errors')): ?>
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Avatar Section -->
            <div class="flex items-center gap-6 mb-6 pb-6 border-b border-gray-100">
                <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?= base_url('uploads/users/' . $user['profile_picture']) ?>" alt="Profile" class="w-full h-full object-cover">
                    <?php else: ?>
                    <span class="text-indigo-600 font-bold text-2xl"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900"><?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?></p>
                    <p class="text-xs text-gray-500"><?= esc($user['company_id'] ?? '') ?></p>
                    <label class="mt-2 inline-flex items-center gap-2 text-xs text-indigo-600 hover:text-indigo-700 cursor-pointer font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Change Photo
                        <input type="file" name="profile_picture" accept="image/*" class="hidden">
                    </label>
                </div>
            </div>

            <!-- Name Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="<?= esc($user['first_name'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="<?= esc($user['last_name'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                </div>
            </div>

            <!-- Email & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="<?= esc($user['email'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="<?= esc($user['phone_number'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <!-- Read-only fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company ID</label>
                    <input type="text" value="<?= esc($user['company_id'] ?? '') ?>" disabled
                           class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <div class="pt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                            <?= esc(ucfirst($user['role'] ?? '')) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-6 py-2 text-sm font-medium transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Password Change Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Change Password</h3>
            <p class="text-xs text-gray-500 mt-1">Update your password (leave blank to keep current)</p>
        </div>

        <form method="post" action="<?= base_url('admin/profile/update') ?>" class="p-6">
            <?= csrf_field() ?>

            <div class="space-y-4 mb-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" id="current_password" placeholder="Enter current password"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="new_password" id="new_password" placeholder="Enter new password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-6 py-2 text-sm font-medium transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
