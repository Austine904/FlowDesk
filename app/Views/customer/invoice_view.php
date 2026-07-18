<?= $this->extend('customer/layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('customer/invoices') ?>" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">&larr; Back to Invoices</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?= esc($invoice['invoice_no'] ?? '') ?></h1>
            <p class="text-sm text-gray-500"><?= esc($invoice['invoice_date'] ?? '') ?></p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
            <?= $invoice['status'] === 'Paid' ? 'bg-emerald-50 text-emerald-700' : ($invoice['status'] === 'Overdue' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') ?>">
            <?= esc($invoice['status']) ?>
        </span>
    </div>

    <div class="space-y-3 mb-6">
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Job Card</span>
            <span class="text-gray-900 font-medium"><?= esc($invoice['job_no'] ?? 'N/A') ?></span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Parts Total</span>
            <span class="text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['parts_total'] ?? 0, 2) ?></span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Labor Total</span>
            <span class="text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['labor_total'] ?? 0, 2) ?></span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Sublet Total</span>
            <span class="text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['sublet_total'] ?? 0, 2) ?></span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Subtotal</span>
            <span class="text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['subtotal'] ?? 0, 2) ?></span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">VAT (<?= number_format($invoice['vat_rate'] ?? 0, 1) ?>%)</span>
            <span class="text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['vat_amount'] ?? 0, 2) ?></span>
        </div>
        <?php if (($invoice['discount'] ?? 0) > 0): ?>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Discount</span>
            <span class="text-red-600">-<?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['discount'] ?? 0, 2) ?></span>
        </div>
        <?php endif; ?>
        <div class="flex justify-between text-sm pt-3 border-t border-gray-100">
            <span class="text-base font-semibold text-gray-900">Grand Total</span>
            <span class="text-base font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['grand_total'] ?? 0, 2) ?></span>
        </div>
    </div>

    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600 font-medium">Amount Paid</span>
            <span class="text-emerald-600 font-semibold"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['amount_paid'] ?? 0, 2) ?></span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-600 font-medium">Balance Due</span>
            <span class="<?= ($invoice['balance_due'] ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600' ?> font-semibold">
                <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['balance_due'] ?? 0, 2) ?>
            </span>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
