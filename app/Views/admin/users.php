<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900"><i class="bi bi-people me-2"></i> User Management</h3>
        <button onclick="openModal('<?= base_url('admin/users/add') ?>', 'Add New User')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <i class="bi bi-person-plus"></i> Add User
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1" id="deleteSelectedBtn">
                    <i class="bi bi-trash"></i> Delete Selected
                </button>
                <select id="role-filter" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" onchange="if (window.userTable) { window.userTable.ajax.reload(); }">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="mechanic">Mechanic</option>
                    <option value="receptionist">Receptionist</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table id="userTable" class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><input type="checkbox" id="select_all" class="rounded border-gray-300"></th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div id="actionModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('actionModal')"></div>
<div id="actionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900" id="actionModalLabel"></h5>
            <button type="button" onclick="closeModal('actionModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <div id="modalContent" class="text-center py-5">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600 mx-auto"></div>
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div id="userDetailsModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('userDetailsModal')"></div>
<div id="userDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900"><i class="bi bi-person-circle me-2"></i> User Details</h5>
            <button type="button" onclick="closeModal('userDetailsModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <img id="profile_picture" src="https://placehold.co/150x150/cccccc/333333?text=No+Photo" class="w-32 h-32 rounded-full mx-auto object-cover border-2 border-gray-200" alt="User Photo">
                    <h5 class="mt-3 font-semibold text-gray-900" id="user-fullname"></h5>
                    <div class="mt-2 text-sm text-gray-500"><span class="font-medium">Company ID:</span> <span id="company_id"></span></div>
                    <div class="text-sm text-gray-500"><span class="font-medium">Role:</span> <span id="user-role"></span></div>
                    <div class="text-sm text-gray-500"><span class="font-medium">Phone:</span> <span id="user-phone"></span></div>
                    <div class="text-sm text-gray-500"><span class="font-medium">Email:</span> <span id="user-email"></span></div>
                </div>
                <div class="md:col-span-2">
                    <div class="border-b border-gray-200 mb-4">
                        <nav class="flex gap-4">
                            <button class="tab-link active px-3 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600" data-tab="personal">Personal Info</button>
                            <button class="tab-link px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="employment">Employment</button>
                            <button class="tab-link px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="kin">Next of Kin</button>
                        </nav>
                    </div>
                    <div id="tab-personal" class="tab-content space-y-3">
                        <div class="text-sm"><span class="font-medium text-gray-700">Date of Birth:</span> <span class="text-gray-900" id="dob"></span></div>
                        <div class="text-sm"><span class="font-medium text-gray-700">National ID:</span> <span class="text-gray-900" id="national_id"></span></div>
                        <div class="text-sm"><span class="font-medium text-gray-700">Address:</span> <span class="text-gray-900" id="user-address"></span></div>
                    </div>
                    <div id="tab-employment" class="tab-content hidden space-y-3">
                        <div class="text-sm"><span class="font-medium text-gray-700">Employment Date:</span> <span class="text-gray-900" id="date_of_employment"></span></div>
                        <div class="text-sm"><span class="font-medium text-gray-700">Department:</span> <span class="text-gray-900" id="department">N/A</span></div>
                    </div>
                    <div id="tab-kin" class="tab-content hidden space-y-3">
                        <div class="text-sm"><span class="font-medium text-gray-700">Next of Kin Name:</span> <span class="text-gray-900" id="kin_first_name"></span> <span class="text-gray-900" id="kin_last_name"></span></div>
                        <div class="text-sm"><span class="font-medium text-gray-700">Next of Kin Phone:</span> <span class="text-gray-900" id="kin_phone_number"></span></div>
                        <div class="text-sm"><span class="font-medium text-gray-700">Relationship:</span> <span class="text-gray-900" id="kin_relationship"></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeModal('userDetailsModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Close</button>
            <button type="button" onclick="openModal('<?= base_url('admin/users/edit/') ?>' + document.getElementById('company_id').innerText, 'Edit User')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1"><i class="bi bi-pencil"></i> Edit User</button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirmDeleteModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="confirmDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900"><i class="bi bi-exclamation-triangle text-amber-500 me-2"></i> Confirm Deletion</h5>
        </div>
        <div class="p-6 text-center text-sm text-gray-600">
            Are you sure you want to delete the selected user(s)? This action cannot be undone.
        </div>
        <div class="flex items-center justify-center gap-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeModal('confirmDeleteModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    var backdrop = document.getElementById(id + '-backdrop');
    if (backdrop) backdrop.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    var backdrop = document.getElementById(id + '-backdrop');
    if (backdrop) backdrop.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

window.openModal = function(url, title) {
    var label = document.getElementById('actionModalLabel');
    var content = document.getElementById('modalContent');
    if (label) label.textContent = title || 'Form';
    if (content) content.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600 mx-auto"></div>';
    openModal('actionModal');
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { if (!r.ok) throw new Error('Network response was not ok'); return r.text(); })
        .then(function(data) { if (content) content.innerHTML = data; })
        .catch(function(error) {
            if (content) content.innerHTML = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">Error loading content: ' + error.message + '</div>';
        });
};

$(document).ready(function() {
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    window.userTable = FlowDesk.serverSideTable('#userTable', {
        ajax: {
            url: '<?= base_url('admin/users/fetch') ?>',
            type: 'GET',
            data: function(d) {
                d.role_filter = $('#role-filter').val();
            }
        },
        columns: [
            { data: 'id', orderable: false, render: function(data) { return '<input type="checkbox" name="users[]" value="' + data + '" class="rounded border-gray-300">'; } },
            { data: 'id' },
            { data: 'name' },
            { data: 'phone' },
            { data: 'role', render: function(data) { return ucfirst(data); } },
            { data: null, orderable: false, render: function(data) {
                return '<div class="flex items-center gap-2">' +
                    '<button onclick="editUser(' + data.id + ')" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>' +
                    '<button onclick="viewUserDetails(' + data.id + ')" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="View"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>' +
                    '<button onclick="deleteUser(' + data.id + ')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
                    '</div>';
            } }
        ]
    });

    var selectAll = document.getElementById('select_all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            var checkboxes = window.userTable.rows({ page: 'current' }).nodes().to$().find('input[name="users[]"]');
            checkboxes.prop('checked', this.checked);
        });
    }

    document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
        var checked = window.userTable.rows().nodes().to$().find('input[name="users[]"]:checked');
        if (checked.length === 0) {
            Swal.fire('No Selection', 'Please select at least one user to delete.', 'info');
            return;
        }
        if (!confirm('Are you sure you want to delete the selected user(s)?')) return;
        var ids = [];
        checked.each(function() { ids.push($(this).val()); });
        $.ajax({
            url: '<?= base_url('admin/users/bulk_action') ?>',
            type: 'POST',
            data: { users: ids, action: 'delete' },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Deleted!', res.message, 'success');
                    window.userTable.ajax.reload();
                } else {
                    Swal.fire('Error!', res.message || 'Delete failed.', 'error');
                }
            },
            error: function(xhr) { FlowDesk.handleAjaxError(xhr, 'delete'); }
        });
    });
});

window.editUser = function(id) {
    window.openModal('<?= base_url('admin/users/edit/') ?>' + id, 'Edit User');
};

window.viewUserDetails = function(id) {
    var label = document.getElementById('user-fullname');
    var companyId = document.getElementById('company_id');
    var profilePic = document.getElementById('profile_picture');
    if (label) label.innerText = 'Loading...';
    if (companyId) companyId.innerText = '';
    if (profilePic) profilePic.src = 'https://placehold.co/150x150/cccccc/333333?text=Loading...';
    openModal('userDetailsModal');

    fetch('<?= base_url('admin/users/fetch/') ?>' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { if (!r.ok) throw new Error('Failed to fetch user details'); return r.json(); })
        .then(function(data) {
            document.getElementById('profile_picture').src = data.profile_picture ? '<?= base_url() ?>/' + data.profile_picture : 'https://placehold.co/150x150/cccccc/333333?text=No+Photo';
            document.getElementById('user-fullname').innerText = (data.first_name || '') + ' ' + (data.last_name || '');
            document.getElementById('company_id').innerText = data.company_id || 'N/A';
            document.getElementById('user-role').innerText = ucfirst(data.role || 'N/A');
            document.getElementById('user-phone').innerText = data.phone_number || 'N/A';
            document.getElementById('user-email').innerText = data.email || 'N/A';
            document.getElementById('dob').innerText = data.dob || 'N/A';
            document.getElementById('national_id').innerText = data.national_id || 'N/A';
            document.getElementById('user-address').innerText = data.address || 'N/A';
            document.getElementById('date_of_employment').innerText = data.date_of_employment || 'N/A';
            document.getElementById('department').innerText = data.department || 'N/A';
            var kin = data.next_of_kin || {};
            document.getElementById('kin_first_name').innerText = (kin.kin_first_name || '') + ' ' + (kin.kin_last_name || '');
            document.getElementById('kin_last_name').innerText = kin.kin_last_name || '';
            document.getElementById('kin_phone_number').innerText = kin.kin_phone_number || 'N/A';
            document.getElementById('kin_relationship').innerText = kin.relationship || 'N/A';
        })
        .catch(function(error) {
            var body = document.querySelector('#userDetailsModal .p-6');
            if (body) body.innerHTML = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">Failed to load user details: ' + error.message + '</div>';
        });
};

window.deleteUser = function(id) {
    if (!confirm('Are you sure you want to delete this user?')) return;
    $.ajax({
        url: '<?= base_url('admin/users/delete/') ?>' + id,
        type: 'GET',
        success: function(res) {
            Swal.fire('Deleted!', 'User deleted successfully.', 'success');
            if (window.userTable) window.userTable.ajax.reload();
        },
        error: function(xhr) { FlowDesk.handleAjaxError(xhr, 'delete'); }
    });
};

function ucfirst(str) {
    if (typeof str !== 'string' || str.length === 0) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

document.addEventListener('click', function(e) {
    var tabBtn = e.target.closest('.tab-link');
    if (tabBtn) {
        document.querySelectorAll('.tab-link').forEach(function(t) {
            t.classList.remove('text-indigo-600', 'border-indigo-600');
            t.classList.add('text-gray-500');
        });
        tabBtn.classList.add('text-indigo-600', 'border-indigo-600');
        tabBtn.classList.remove('text-gray-500');
        document.querySelectorAll('.tab-content').forEach(function(c) { c.classList.add('hidden'); });
        var tab = document.getElementById('tab-' + tabBtn.dataset.tab);
        if (tab) tab.classList.remove('hidden');
    }
});
</script>
<?= $this->endSection(); ?>
