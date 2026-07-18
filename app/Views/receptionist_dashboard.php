<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h3>Welcome, <?= esc($name ?? 'Receptionist') ?></h3>
            <p class="text-muted">Here's your overview for today.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                    <i class="bi bi-plus-circle" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">New Job Intake</h4>
                    <p class="mb-4">Create a new job card for a customer</p>
                    <a href="<?= base_url('admin/jobs') ?>" class="btn btn-light btn-lg">
                        <i class="bi bi-arrow-right"></i> Go to Job Intake
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">View Customers</h4>
                    <p class="mb-4">Browse and manage customer records</p>
                    <a href="<?= base_url('admin/customers') ?>" class="btn btn-light btn-lg">
                        <i class="bi bi-arrow-right"></i> View Customers
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
