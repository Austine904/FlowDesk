<div class="space-y-6">
    <form method="POST" action="<?= base_url('admin/users/update/' . $user['id']) ?>" id="editUserForm">
        <?= csrf_field() ?>
        <div class="flex items-center gap-4 mb-6">
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="<?= base_url($user['profile_picture']) ?>" alt="Profile" class="w-16 h-16 rounded-full object-cover border border-gray-200">
            <?php else: ?>
                <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-lg font-semibold text-indigo-600"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?></span>
                </div>
            <?php endif; ?>
            <div>
                <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="text-xs text-gray-400 mt-1">Leave empty to keep current picture. JPG, PNG, or WebP only.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="first_name" id="first_name" value="<?= esc($user['first_name']) ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="<?= esc($user['last_name']) ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="<?= esc($user['email']) ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" value="<?= esc($user['phone_number']) ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="mechanic" <?= $user['role'] === 'mechanic' ? 'selected' : '' ?>>Mechanic</option>
                    <option value="receptionist" <?= $user['role'] === 'receptionist' ? 'selected' : '' ?>>Receptionist</option>
                    <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                </select>
            </div>
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" id="gender" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    <option value="">Select Gender</option>
                    <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($user['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div>
                <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="dob" id="dob" value="<?= esc($user['dob'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="national_id" class="block text-sm font-medium text-gray-700 mb-1">National ID</label>
                <input type="text" name="national_id" id="national_id" value="<?= esc($user['national_id'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="date_of_employment" class="block text-sm font-medium text-gray-700 mb-1">Date of Employment</label>
                <input type="date" name="date_of_employment" id="date_of_employment" value="<?= esc($user['date_of_employment'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password <span class="text-gray-400 font-normal">(leave blank to keep current)</span></label>
                <input type="password" name="password" id="password" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div class="sm:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" id="address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none"><?= esc($user['address'] ?? '') ?></textarea>
            </div>
        </div>

        <hr class="border-gray-200 my-6">
        <h5 class="text-base font-semibold text-gray-900 mb-4">Next of Kin Information</h5>
        <p class="text-sm text-gray-500 mb-4" id="kinStatus">Loading next of kin...</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="kin_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="kin_first_name" id="kin_first_name" value="<?= esc($user['kin_first_name'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="kin_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="kin_last_name" id="kin_last_name" value="<?= esc($user['kin_last_name'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="relationship" class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                <input type="text" name="relationship" id="relationship" value="<?= esc($user['relationship'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="kin_phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="kin_phone_number" id="kin_phone_number" value="<?= esc($user['kin_phone_number'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="window.hideModal('actionModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Update User
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Updating...';
    $.ajax({
        url: form.action,
        type: 'POST',
        data: $(form).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire('Updated!', res.message, 'success').then(function() {
                    window.hideModal('actionModal');
                    if (window.userTable) window.userTable.ajax.reload();
                });
            } else {
                Swal.fire('Error', res.message || 'Update failed.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Update User';
            }
        },
        error: function(xhr) {
            FlowDesk.handleAjaxError(xhr, 'update');
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Update User';
        }
    });
});
</script>