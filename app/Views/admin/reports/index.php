<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Reports</h3>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="<?= base_url('admin/reports/financial') ?>" class="text-decoration-none">
                <div class="card border-primary h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-currency-dollar display-3 text-primary"></i>
                        <h4 class="mt-3">Financial Reports</h4>
                        <p class="text-muted">Revenue, invoices, payments, petty cash</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('admin/reports/operational') ?>" class="text-decoration-none">
                <div class="card border-success h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-gear-wide-connected display-3 text-success"></i>
                        <h4 class="mt-3">Operational Reports</h4>
                        <p class="text-muted">Jobs, mechanics, turnaround time</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('admin/reports/inventory') ?>" class="text-decoration-none">
                <div class="card border-warning h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-box-seam display-3 text-warning"></i>
                        <h4 class="mt-3">Inventory Reports</h4>
                        <p class="text-muted">Stock levels, parts usage, inventory value</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('admin/reports/customers') ?>" class="text-decoration-none">
                <div class="card border-info h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people display-3 text-info"></i>
                        <h4 class="mt-3">Customer Reports</h4>
                        <p class="text-muted">Top customers, visits, outstanding balances</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('admin/reports/staff') ?>" class="text-decoration-none">
                <div class="card border-secondary h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-person-badge display-3 text-secondary"></i>
                        <h4 class="mt-3">Staff Reports</h4>
                        <p class="text-muted">Advisor performance, activity log</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
