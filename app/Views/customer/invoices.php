<?= $this->extend('customer/layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">My Invoices</h1>
    <p class="text-sm text-gray-500 mt-1">View and track your invoice payments</p>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($invoices)): ?>
                <?php foreach ($invoices as $inv): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-indigo-600">
                        <a href="<?= base_url('customer/invoice/' . $inv['id']) ?>"><?= esc($inv['invoice_no']) ?></a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($inv['invoice_date'] ?? '') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($inv['job_no'] ?? '') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['grand_total'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['amount_paid'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 text-sm font-medium <?= ($inv['balance_due'] ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600' ?>">
                        <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['balance_due'] ?? 0, 2) ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?= $inv['status'] === 'Paid' ? 'bg-emerald-50 text-emerald-700' : ($inv['status'] === 'Overdue' ? 'bg-red-50 text-red-700' : ($inv['status'] === 'Cancelled' ? 'bg-gray-100 text-gray-600' : 'bg-amber-50 text-amber-700')) ?>">
                            <?= esc($inv['status']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-400">No invoices found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
