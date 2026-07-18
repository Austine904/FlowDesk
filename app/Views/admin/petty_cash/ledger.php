<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
@media print { .no-print { display: none !important; } }
</style>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            <i class="bi bi-book mr-2"></i> Petty Cash Ledger
        </h1>
        <div class="no-print flex gap-2">
            <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
            <a href="<?= base_url('admin/pettycash') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-arrow-left"></i> Back to Petty Cash</a>
        </div>
    </div>

    <?php
    $totalIncome = 0;
    $totalExpense = 0;
    if (!empty($transactions)):
        foreach ($transactions as $t):
            $totalIncome += ($t['type'] === 'Income') ? (float)$t['amount'] : 0;
            $totalExpense += ($t['type'] === 'Expense') ? (float)$t['amount'] : 0;
        endforeach;
    endif;
    $finalBalance = $totalIncome - $totalExpense;
    ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Income</p>
            <p class="text-2xl font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalIncome, 2) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Expenses</p>
            <p class="text-2xl font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalExpense, 2) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Final Balance</p>
            <p class="text-2xl font-bold <?= $finalBalance >= 0 ? 'text-emerald-600' : 'text-red-600' ?>"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($finalBalance, 2) ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Ledger</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full divide-y divide-gray-200" id="ledgerTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Income (<?= org_setting('currency_symbol', 'KSh') ?>)</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Expense (<?= org_setting('currency_symbol', 'KSh') ?>)</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Running Balance (<?= org_setting('currency_symbol', 'KSh') ?>)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $t): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($t['transaction_date']) ?></td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $t['type'] === 'Income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>"><?= esc($t['type']) ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($t['category']) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($t['description']) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($t['reference_no'] ?? '') ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= $t['type'] === 'Income' ? number_format($t['amount'], 2) : '' ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= $t['type'] === 'Expense' ? number_format($t['amount'], 2) : '' ?></td>
                                    <td class="px-4 py-3 text-sm text-right font-bold <?= $t['running_balance'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                        <?= number_format($t['running_balance'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-sm text-gray-500 text-center">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-bold border-t-2 border-gray-200">
                            <td colspan="5" class="px-4 py-3 text-sm text-gray-900 text-right">Totals</td>
                            <td class="px-4 py-3 text-sm text-emerald-600 text-right"><?= number_format($totalIncome, 2) ?></td>
                            <td class="px-4 py-3 text-sm text-red-600 text-right"><?= number_format($totalExpense, 2) ?></td>
                            <td class="px-4 py-3 text-sm text-right <?= $finalBalance >= 0 ? 'text-emerald-600' : 'text-red-600' ?> font-bold">
                                <?= number_format($finalBalance, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
