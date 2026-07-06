<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
@media print {
    .btn { display: none; }
    .sidebar { display: none; }
    div[style*="margin-left"] { margin-left: 0 !important; }
    footer { display: none; }
    h3 .btn { display: none; }
}
</style>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-book me-2"></i> Petty Cash Ledger</h3>
        <div>
            <button class="btn btn-outline-secondary me-2" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
            <a href="<?= base_url('admin/pettycash') ?>" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Back to Petty Cash</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="ledgerTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th>Income (<?= org_setting('currency_symbol', 'KSh') ?>)</th>
                            <th>Expense (<?= org_setting('currency_symbol', 'KSh') ?>)</th>
                            <th>Running Balance (<?= org_setting('currency_symbol', 'KSh') ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalIncome = 0;
                        $totalExpense = 0;
                        ?>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $t): ?>
                                <?php
                                $totalIncome += ($t['type'] === 'Income') ? (float) $t['amount'] : 0;
                                $totalExpense += ($t['type'] === 'Expense') ? (float) $t['amount'] : 0;
                                ?>
                                <tr>
                                    <td><?= esc($t['transaction_date']) ?></td>
                                    <td>
                                        <span class="badge <?= $t['type'] === 'Income' ? 'bg-success' : 'bg-danger' ?>"><?= esc($t['type']) ?></span>
                                    </td>
                                    <td><?= esc($t['category']) ?></td>
                                    <td><?= esc($t['description']) ?></td>
                                    <td><?= esc($t['reference_no'] ?? '') ?></td>
                                    <td class="text-end"><?= $t['type'] === 'Income' ? number_format($t['amount'], 2) : '' ?></td>
                                    <td class="text-end"><?= $t['type'] === 'Expense' ? number_format($t['amount'], 2) : '' ?></td>
                                    <td class="text-end <?= $t['running_balance'] >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                        <?= number_format($t['running_balance'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">Totals</td>
                            <td class="text-end text-success"><?= number_format($totalIncome, 2) ?></td>
                            <td class="text-end text-danger"><?= number_format($totalExpense, 2) ?></td>
                            <td class="text-end <?= ($totalIncome - $totalExpense) >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= number_format($totalIncome - $totalExpense, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
