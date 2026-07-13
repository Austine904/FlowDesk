<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $pageTitle = ($action === 'edit' ? 'Edit Supplier' : 'Add Supplier'); ?>
<div class="space-y-6">
    <h3 class="text-lg font-semibold text-gray-900"><i class="bi bi-truck-flatbed me-2"></i> <?= $action === 'add' ? 'Add Supplier' : 'Edit Supplier' ?></h3>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <form method="POST" action="<?= base_url($action === 'add' ? 'admin/suppliers/create' : 'admin/suppliers/update/' . $supplier['id']) ?>">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-600">*</span></label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="name" name="name" value="<?= esc($supplier['name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-6">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-save"></i> Save Supplier</button>
                    <a href="<?= base_url('admin/suppliers') ?>" class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
