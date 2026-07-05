<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4"><i class="bi bi-boxes me-2"></i> <?= $action === 'add' ? 'Add New Part' : 'Edit Part' ?></h3>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url($action === 'add' ? 'admin/inventory/create' : 'admin/inventory/update/' . $inventory['id']) ?>">
                <?= csrf_field() ?>
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Part Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= esc($inventory['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="part_number" class="form-label">Part Number</label>
                        <input type="text" class="form-control" id="part_number" name="part_number" value="<?= esc($inventory['part_number'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="unit_price" class="form-label">Unit Price (<?= org_setting('currency_symbol', 'KSh') ?>) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01" min="0" value="<?= esc($inventory['unit_price'] ?? '0') ?>" required>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Part</button>
                    <a href="<?= base_url('admin/inventory') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
