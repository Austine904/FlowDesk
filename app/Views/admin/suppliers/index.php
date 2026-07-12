<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $pageTitle = 'Suppliers'; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900"><i class="bi bi-truck-flatbed me-2"></i> Suppliers</h3>
        <button onclick="openModal('addSupplierModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add New Supplier
        </button>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <table id="suppliersTable" class="w-full divide-y divide-gray-200" style="width:100%">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Supplier Name</th>
                        <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#suppliersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/suppliers/load') ?>',
            type: 'POST',
            data: function(d) {
                var csrf = getCsrfMeta();
                d[csrf.name] = csrf.hash;
            }
        },
        order: [[0, 'asc']],
        pageLength: 25,
        columns: [
            { data: 'name' },
            {
                data: 'id',
                orderable: false,
                render: function(data) {
                    return `
                        <button onclick="editSupplier(${data})" class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1 btn-delete" data-id="${data}"><i class="bi bi-trash"></i> Delete</button>
                    `;
                }
            }
        ]
    });

    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var csrf = getCsrfMeta();
        if (!confirm('Are you sure you want to delete this supplier?')) return;
        $.ajax({
            url: '<?= base_url('admin/suppliers/delete/') ?>' + id,
            type: 'POST',
            data: { [csrf.name]: csrf.hash },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#suppliersTable').DataTable().ajax.reload();
                } else {
                    alert(res.message || 'Delete failed.');
                }
            },
            error: function(xhr) {
                var msg = 'Delete failed.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || r.messages?.error || msg;
                } catch(e) {}
                alert(msg);
            }
        });
    });
});
</script>
<!-- Add Supplier Modal -->
<div id="addSupplierModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('addSupplierModal')"></div>
<div id="addSupplierModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">Add New Supplier</h5>
            <button type="button" onclick="closeModal('addSupplierModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addSupplierForm">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-600">*</span></label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="add_supplier_name" name="name" required>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-save"></i> Save Supplier</button>
                <button type="button" onclick="closeModal('addSupplierModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div id="editSupplierModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('editSupplierModal')"></div>
<div id="editSupplierModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">Edit Supplier</h5>
            <button type="button" onclick="closeModal('editSupplierModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editSupplierForm">
            <?= csrf_field() ?>
            <input type="hidden" id="edit_supplier_id" name="id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-600">*</span></label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_supplier_name" name="name" required>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-save"></i> Update Supplier</button>
                <button type="button" onclick="closeModal('editSupplierModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/assets/js/suppliers.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
