<?php
$pageTitle = 'Add User — Step 1 of 3';
$title = 'Add User — Step 1 of 3';
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add New User</h1>
                <p class="text-sm text-gray-500">Step 1 of 3 — Account Setup</p>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-600">Step 1 of 3</span>
                <span class="text-sm text-gray-500">33%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: 33%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500">
                <span class="text-indigo-600 font-medium">Account Setup</span>
                <span>Personal Info</span>
                <span>Next of Kin</span>
            </div>
        </div>

        <form method="POST" action="<?= base_url('user/add_step1') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div id="error-msg" class="hidden bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm mb-4"></div>
            <div class="space-y-4">
                <div>
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">User Image</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="role" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="" disabled <?= empty($step1Data['role']) ? 'selected' : '' ?>>Select Role</option>
                        <option value="admin" <?= ($step1Data['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="mechanic" <?= ($step1Data['role'] ?? '') === 'mechanic' ? 'selected' : '' ?>>Mechanic</option>
                        <option value="receptionist" <?= ($step1Data['role'] ?? '') === 'receptionist' ? 'selected' : '' ?>>Receptionist</option>
                    </select>
                </div>
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company ID</label>
                    <input type="text" id="company_id" name="company_id" readonly
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-gray-50 text-gray-500 focus:outline-none"
                           value="<?= esc($step1Data['company_id'] ?? '') ?>">
                    <p class="text-xs text-gray-400 mt-1">Auto-generated based on role.</p>
                </div>
                <div>
                    <label for="date_of_employment" class="block text-sm font-medium text-gray-700 mb-1">Date of Employment</label>
                    <input type="text" id="date_of_employment" name="date_of_employment" readonly
                           value="<?= esc($step1Data['date_of_employment'] ?? date('Y-m-d')) ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-gray-50 text-gray-500 focus:outline-none">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" <?= empty($step1Data['password']) ? 'required' : '' ?> minlength="8"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" <?= empty($step1Data['password']) ? 'required' : '' ?>
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="<?= base_url('admin/users') ?>"
                   class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Cancel
                </a>
                <button type="button" id="nextStep"
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
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    if (!role) return;
    fetch(BASE_URL + 'user/getLastId?role=' + role)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            document.getElementById('company_id').value = data.company_id || '';
            document.getElementById('date_of_employment').value = new Date().toISOString().split('T')[0];
        })
        .catch(function() {
            document.getElementById('error-msg').textContent = 'Error generating ID.';
            document.getElementById('error-msg').classList.remove('hidden');
        });
});

document.getElementById('nextStep').addEventListener('click', function() {
    var form = document.querySelector('form');
    var formData = new FormData(form);
    var csrf = getCsrfMeta();
    formData.append(csrf.name, csrf.hash);

    fetch(BASE_URL + 'user/add_step1', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            window.location.href = BASE_URL + 'user/add_step2';
        } else {
            alert(data.message);
        }
    })
    .catch(function() {
        document.getElementById('error-msg').textContent = 'Something went wrong. Please try again.';
        document.getElementById('error-msg').classList.remove('hidden');
    });
});
</script>
<?= $this->endSection() ?>
