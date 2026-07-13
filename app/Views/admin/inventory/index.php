<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $pageTitle = 'Parts & Inventory'; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900"><i class="bi bi-boxes me-2"></i> Inventory / Parts</h3>
        <button onclick="openModal('addInventoryModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add New Part
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

    <div id="lowStockBanner" class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg hidden">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong id="lowStockCount"></strong> item(s) are low on stock or out of stock.
        <a href="#inventoryTable" class="font-medium underline">Scroll to view</a>.
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <table id="inventoryTable" class="w-full divide-y divide-gray-200" style="width:100%">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Part Name</th>
                        <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Part Number</th>
                        <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Unit Price</th>
                        <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Unit</th>
                        <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Stock Status</th>
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
    var table = $('#inventoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/inventory/load') ?>',
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
            { data: 'part_number', defaultContent: '—' },
            {
                data: 'unit_price',
                render: function(data) {
                    return '<?= org_setting('currency_symbol', 'KSh') ?> ' + parseFloat(data).toFixed(2);
                }
            },
            { data: 'unit', defaultContent: 'piece' },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    if (data.is_stocked == 0) {
                        return '<span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-700">Catalog Only</span>';
                    }
                    var qty = parseFloat(data.quantity_in_hand);
                    var reorder = parseFloat(data.reorder_level);
                    if (qty <= 0) {
                        return '<span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-red-100 text-red-700">Out of Stock</span>';
                    } else if (qty <= reorder) {
                        return '<span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700">Low Stock (' + qty + ')</span>';
                    } else {
                        return '<span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700">In Stock (' + qty + ')</span>';
                    }
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function(data) {
                    return `
                        <button onclick="editInventory(${data})" class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1 btn-delete" data-id="${data}"><i class="bi bi-trash"></i> Delete</button>
                    `;
                }
            }
        ],
        drawCallback: function(settings) {
            var api = this.api();
            var data = api.rows({ filter: 'applied' }).data().toArray();
            var lowCount = 0;
            data.forEach(function(row) {
                if (row.is_stocked == 1 && parseFloat(row.quantity_in_hand) <= parseFloat(row.reorder_level)) {
                    lowCount++;
                }
            });
            if (lowCount > 0) {
                $('#lowStockBanner').removeClass('hidden');
                $('#lowStockCount').text(lowCount);
            } else {
                $('#lowStockBanner').addClass('hidden');
            }
        }
    });

    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var csrf = getCsrfMeta();
        if (!confirm('Are you sure you want to delete this part?')) return;
        $.ajax({
            url: '<?= base_url('admin/inventory/delete/') ?>' + id,
            type: 'POST',
            data: { [csrf.name]: csrf.hash },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#inventoryTable').DataTable().ajax.reload();
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
<!-- Add Inventory Modal -->
<div id="addInventoryModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('addInventoryModal')"></div>
<div id="addInventoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">Add New Part</h5>
            <button type="button" onclick="closeModal('addInventoryModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addInventoryForm">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Part Name <span class="text-red-600">*</span></label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="add_inv_name" name="name" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Part Number</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="add_inv_part_number" name="part_number">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price <span class="text-red-600">*</span></label>
                        <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="add_inv_unit_price" name="unit_price" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="add_inv_unit" name="unit" value="piece">
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" class="sr-only peer" id="add_inv_is_stocked" name="is_stocked" value="1" onchange="toggleStockFields('add')">
                            <div class="w-10 h-6 bg-gray-200 peer-checked:bg-indigo-600 rounded-full transition-colors"></div>
                            <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Track stock</span>
                    </label>
                </div>
                <div id="add_stock_fields" class="hidden grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity In Hand</label>
                        <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="add_inv_quantity_in_hand" name="quantity_in_hand" value="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                        <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="add_inv_reorder_level" name="reorder_level" value="0">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-save"></i> Save Part</button>
                <button type="button" onclick="closeModal('addInventoryModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Inventory Modal -->
<div id="editInventoryModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('editInventoryModal')"></div>
<div id="editInventoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">Edit Part</h5>
            <button type="button" onclick="closeModal('editInventoryModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editInventoryForm">
            <?= csrf_field() ?>
            <input type="hidden" id="edit_inv_id" name="id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Part Name <span class="text-red-600">*</span></label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_inv_name" name="name" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Part Number</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_inv_part_number" name="part_number">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price <span class="text-red-600">*</span></label>
                        <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_inv_unit_price" name="unit_price" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_inv_unit" name="unit" value="piece">
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" class="sr-only peer" id="edit_inv_is_stocked" name="is_stocked" value="1" onchange="toggleStockFields('edit')">
                            <div class="w-10 h-6 bg-gray-200 peer-checked:bg-indigo-600 rounded-full transition-colors"></div>
                            <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Track stock</span>
                    </label>
                </div>
                <div id="edit_stock_fields" class="hidden grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity In Hand</label>
                        <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_inv_quantity_in_hand" name="quantity_in_hand" value="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                        <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_inv_reorder_level" name="reorder_level" value="0">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-save"></i> Update Part</button>
                <button type="button" onclick="closeModal('editInventoryModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/assets/js/inventory.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
