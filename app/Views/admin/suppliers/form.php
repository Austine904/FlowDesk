<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4"><i class="bi bi-truck-flatbed me-2"></i> <?= $action === 'add' ? 'Add Supplier' : 'Edit Supplier' ?></h3>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url($action === 'add' ? 'admin/suppliers/create' : 'admin/suppliers/update/' . $supplier['id']) ?>">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= esc($supplier['name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Supplier</button>
                    <a href="<?= base_url('admin/suppliers') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
