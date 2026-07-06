<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4"><i class="bi bi-gear-fill me-2"></i> Organization Settings</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors = session()->getFlashdata('errors'))): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> Please fix the following errors:
            <ul class="mb-0 mt-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/settings/update') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i> Organization Profile</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="org_name" class="form-label">Organization Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="org_name" name="org_name" value="<?= esc($settings['org_name'] ?? 'FlowDesk Organization') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="org_logo" class="form-label">Organization Logo</label>
                        <?php if (!empty($settings['org_logo'])): ?>
                            <div class="mb-2">
                                <img src="<?= base_url($settings['org_logo']) ?>" alt="Current Logo" style="max-height: 60px;" class="border rounded p-1">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="org_logo" name="org_logo" accept=".jpg,.jpeg,.png,.gif,.svg">
                        <div class="form-text">Allowed: JPG, JPEG, PNG, GIF, SVG</div>
                    </div>
                    <div class="col-12">
                        <label for="org_address" class="form-label">Address</label>
                        <textarea class="form-control" id="org_address" name="org_address" rows="2"><?= esc($settings['org_address'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="org_phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="org_phone" name="org_phone" value="<?= esc($settings['org_phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="org_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="org_email" name="org_email" value="<?= esc($settings['org_email'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="org_website" class="form-label">Website</label>
                        <input type="url" class="form-control" id="org_website" name="org_website" value="<?= esc($settings['org_website'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i> Financial Settings</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="currency" class="form-label">Currency Code</label>
                        <input type="text" class="form-control" id="currency" name="currency" value="<?= esc($settings['currency'] ?? 'KES') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="currency_symbol" class="form-label">Currency Symbol</label>
                        <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="<?= esc($settings['currency_symbol'] ?? 'KSh') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="vat_rate" class="form-label">VAT Rate (%)</label>
                        <input type="number" class="form-control" id="vat_rate" name="vat_rate" step="0.01" min="0" max="99.99" value="<?= esc($settings['vat_rate'] ?? 16.00) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="default_labor_rate" class="form-label">Default Labor Rate (per hour)</label>
                        <input type="number" class="form-control" id="default_labor_rate" name="default_labor_rate" step="0.01" min="0" value="<?= esc($settings['default_labor_rate'] ?? 1500.00) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="invoice_prefix" class="form-label">Invoice Prefix</label>
                        <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" value="<?= esc($settings['invoice_prefix'] ?? 'INV-') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="invoice_due_days" class="form-label">Invoice Due Days</label>
                        <input type="number" class="form-control" id="invoice_due_days" name="invoice_due_days" min="0" value="<?= esc($settings['invoice_due_days'] ?? 14) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar me-2"></i> Organization Settings</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="fin_year_start_month" class="form-label">Financial Year Start Month</label>
                        <select class="form-select" id="fin_year_start_month" name="fin_year_start_month">
                            <?php $currentMonth = $settings['fin_year_start_month'] ?? 1; ?>
                            <?php $months = ['','January','February','March','April','May','June','July','August','September','October','November','December']; ?>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($currentMonth == $m) ? 'selected' : '' ?>><?= $months[$m] ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mb-5">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Settings</button>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
