<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            <i class="bi bi-gear-fill mr-2"></i> Organization Settings
        </h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <i class="bi bi-check-circle"></i>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
            <button type="button" class="ml-auto text-emerald-600 hover:text-emerald-800" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
            <button type="button" class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors = session()->getFlashdata('errors'))): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center gap-3">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Please fix the following errors:</span>
                <button type="button" class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <ul class="mt-2 list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/settings/update') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900"><i class="bi bi-building mr-2"></i> Organization Profile</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="org_name" class="block text-sm font-medium text-gray-700 mb-1">Organization Name <span class="text-red-500">*</span></label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="org_name" name="org_name" value="<?= esc($settings['org_name'] ?? 'FlowDesk Organization') ?>" required>
                    </div>
                    <div>
                        <label for="org_logo" class="block text-sm font-medium text-gray-700 mb-1">Organization Logo</label>
                        <?php if (!empty($settings['org_logo'])): ?>
                            <div class="mb-2">
                                <img src="<?= base_url($settings['org_logo']) ?>" alt="Current Logo" class="max-h-16 rounded border p-1">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" id="org_logo" name="org_logo" accept=".jpg,.jpeg,.png,.gif,.svg">
                        <p class="text-xs text-gray-500 mt-1">Allowed: JPG, JPEG, PNG, GIF, SVG</p>
                    </div>
                    <div class="col-span-full">
                        <label for="org_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="org_address" name="org_address" rows="2"><?= esc($settings['org_address'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label for="org_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="org_phone" name="org_phone" value="<?= esc($settings['org_phone'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="org_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="org_email" name="org_email" value="<?= esc($settings['org_email'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="org_website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="org_website" name="org_website" value="<?= esc($settings['org_website'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900"><i class="bi bi-currency-dollar mr-2"></i> Financial Settings</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency Code</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="currency" name="currency" value="<?= esc($settings['currency'] ?? 'KES') ?>">
                    </div>
                    <div>
                        <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="currency_symbol" name="currency_symbol" value="<?= esc($settings['currency_symbol'] ?? 'KSh') ?>">
                    </div>
                    <div>
                        <label for="vat_rate" class="block text-sm font-medium text-gray-700 mb-1">VAT Rate (%)</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="vat_rate" name="vat_rate" step="0.01" min="0" max="99.99" value="<?= esc($settings['vat_rate'] ?? 16.00) ?>">
                    </div>
                    <div>
                        <label for="default_labor_rate" class="block text-sm font-medium text-gray-700 mb-1">Default Labor Rate (per hour)</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="default_labor_rate" name="default_labor_rate" step="0.01" min="0" value="<?= esc($settings['default_labor_rate'] ?? 1500.00) ?>">
                    </div>
                    <div>
                        <label for="invoice_prefix" class="block text-sm font-medium text-gray-700 mb-1">Invoice Prefix</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="invoice_prefix" name="invoice_prefix" value="<?= esc($settings['invoice_prefix'] ?? 'INV-') ?>">
                    </div>
                    <div>
                        <label for="invoice_due_days" class="block text-sm font-medium text-gray-700 mb-1">Invoice Due Days</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="invoice_due_days" name="invoice_due_days" min="0" value="<?= esc($settings['invoice_due_days'] ?? 14) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900"><i class="bi bi-calendar mr-2"></i> Organization Settings</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="fin_year_start_month" class="block text-sm font-medium text-gray-700 mb-1">Financial Year Start Month</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="fin_year_start_month" name="fin_year_start_month">
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

        <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1"><i class="bi bi-save"></i> Save Settings</button>
            <a href="<?= base_url('admin/dashboard') ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
