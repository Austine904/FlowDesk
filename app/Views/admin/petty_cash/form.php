<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4"><i class="bi bi-cash-stack me-2"></i> <?= $action === 'add' ? 'Add Transaction' : 'Edit Transaction' ?></h3>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url($action === 'add' ? 'admin/pettycash/create' : 'admin/pettycash/update/' . $transaction['id']) ?>">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="transaction_date" name="transaction_date"
                            value="<?= esc($transaction['transaction_date'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeIncome" value="Income"
                                    <?= (isset($transaction['type']) && $transaction['type'] === 'Income') ? 'checked' : 'checked' ?>>
                                <label class="form-check-label" for="typeIncome">Income</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeExpense" value="Expense"
                                    <?= (isset($transaction['type']) && $transaction['type'] === 'Expense') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="typeExpense">Expense</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <optgroup label="Income">
                                <option value="Float Top-up" <?= (isset($transaction['category']) && $transaction['category'] === 'Float Top-up') ? 'selected' : '' ?>>Float Top-up</option>
                                <option value="Customer Refund" <?= (isset($transaction['category']) && $transaction['category'] === 'Customer Refund') ? 'selected' : '' ?>>Customer Refund</option>
                                <option value="Miscellaneous Income" <?= (isset($transaction['category']) && $transaction['category'] === 'Miscellaneous Income') ? 'selected' : '' ?>>Miscellaneous Income</option>
                            </optgroup>
                            <optgroup label="Expense">
                                <option value="Supplies" <?= (isset($transaction['category']) && $transaction['category'] === 'Supplies') ? 'selected' : '' ?>>Supplies</option>
                                <option value="Transport" <?= (isset($transaction['category']) && $transaction['category'] === 'Transport') ? 'selected' : '' ?>>Transport</option>
                                <option value="Meals & Refreshments" <?= (isset($transaction['category']) && $transaction['category'] === 'Meals & Refreshments') ? 'selected' : '' ?>>Meals & Refreshments</option>
                                <option value="Utilities" <?= (isset($transaction['category']) && $transaction['category'] === 'Utilities') ? 'selected' : '' ?>>Utilities</option>
                                <option value="Repairs & Maintenance" <?= (isset($transaction['category']) && $transaction['category'] === 'Repairs & Maintenance') ? 'selected' : '' ?>>Repairs & Maintenance</option>
                                <option value="Stationery" <?= (isset($transaction['category']) && $transaction['category'] === 'Stationery') ? 'selected' : '' ?>>Stationery</option>
                                <option value="Miscellaneous Expense" <?= (isset($transaction['category']) && $transaction['category'] === 'Miscellaneous Expense') ? 'selected' : '' ?>>Miscellaneous Expense</option>
                            </optgroup>
                            <option value="Other" <?= (isset($transaction['category']) && !in_array($transaction['category'], ['Float Top-up','Customer Refund','Miscellaneous Income','Supplies','Transport','Meals & Refreshments','Utilities','Repairs & Maintenance','Stationery','Miscellaneous Expense']) ? 'selected' : '') ?>>Other</option>
                        </select>
                        <div id="otherCategoryWrap" class="mt-2" style="display: <?= (isset($transaction['category']) && !in_array($transaction['category'] ?? '', ['Float Top-up','Customer Refund','Miscellaneous Income','Supplies','Transport','Meals & Refreshments','Utilities','Repairs & Maintenance','Stationery','Miscellaneous Expense'])) ? 'block' : 'none' ?>;">
                            <input type="text" class="form-control" id="other_category" name="other_category" placeholder="Enter custom category"
                                value="<?= (isset($transaction['category']) && !in_array($transaction['category'], ['Float Top-up','Customer Refund','Miscellaneous Income','Supplies','Transport','Meals & Refreshments','Utilities','Repairs & Maintenance','Stationery','Miscellaneous Expense'])) ? esc($transaction['category']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="amount" class="form-label">Amount (<?= org_setting('currency_symbol', 'KSh') ?>) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01"
                            value="<?= esc($transaction['amount'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="reference_no" class="form-label">Reference No</label>
                        <input type="text" class="form-control" id="reference_no" name="reference_no"
                            value="<?= esc($transaction['reference_no'] ?? '') ?>" placeholder="Receipt number (optional)">
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?= esc($transaction['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Transaction</button>
                    <a href="<?= base_url('admin/pettycash') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var categorySelect = document.getElementById('category');
    var otherWrap = document.getElementById('otherCategoryWrap');
    var otherInput = document.getElementById('other_category');

    function toggleOther() {
        if (categorySelect.value === 'Other') {
            otherWrap.style.display = 'block';
            otherInput.name = 'category';
            categorySelect.name = '';
        } else {
            otherWrap.style.display = 'none';
            otherInput.name = '';
            categorySelect.name = 'category';
        }
    }

    categorySelect.addEventListener('change', toggleOther);
    toggleOther();
});
</script>
<?= $this->endSection() ?>
