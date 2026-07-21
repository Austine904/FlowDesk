<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $pageTitle = ($action === 'edit' ? 'Edit Part' : 'Add New Part'); ?>
<div class="space-y-6">
    <h3 class="text-lg font-semibold text-gray-900"><i class="bi bi-boxes me-2"></i> <?= $action === 'add' ? 'Add New Part' : 'Edit Part' ?></h3>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <form method="POST" action="<?= base_url($action === 'add' ? 'admin/inventory/create' : 'admin/inventory/update/' . $inventory['id']) ?>">
                <?= csrf_field() ?>
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Part Name <span class="text-red-600">*</span></label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="name" name="name" value="<?= esc($inventory['name'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label for="part_number" class="block text-sm font-medium text-gray-700 mb-1">Part Number</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="part_number" name="part_number" value="<?= esc($inventory['part_number'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-1">Unit Price (<?= org_setting('currency_symbol', 'KSh') ?>) <span class="text-red-600">*</span></label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="unit_price" name="unit_price" step="0.01" min="0" value="<?= esc($inventory['unit_price'] ?? '0') ?>" required>
                    </div>
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="unit" name="unit" value="<?= esc($inventory['unit'] ?? 'piece') ?>">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" class="sr-only peer" id="is_stocked" name="is_stocked" value="1" <?= isset($inventory['is_stocked']) && $inventory['is_stocked'] ? 'checked' : '' ?>>
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-indigo-600 rounded-full transition-colors"></div>
                                <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Track stock for this item</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4" id="stockFields" style="<?= (isset($inventory['is_stocked']) && $inventory['is_stocked']) ? '' : 'display:none;' ?>">
                    <div>
                        <label for="quantity_in_hand" class="block text-sm font-medium text-gray-700 mb-1">Quantity In Hand</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="quantity_in_hand" name="quantity_in_hand" step="0.01" min="0" value="<?= esc($inventory['quantity_in_hand'] ?? '0') ?>">
                    </div>
                    <div>
                        <label for="reorder_level" class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="reorder_level" name="reorder_level" step="0.01" min="0" value="<?= esc($inventory['reorder_level'] ?? '0') ?>">
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-6">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-save"></i> Save Part</button>
                    <a href="<?= base_url('admin/inventory') ?>" class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var stockToggle = document.getElementById('is_stocked');
    var stockFields = document.getElementById('stockFields');
    if (stockToggle) {
        stockToggle.addEventListener('change', function() {
            stockFields.style.display = this.checked ? '' : 'none';
        });
    }
})();
</script>
<?= $this->endSection() ?>
