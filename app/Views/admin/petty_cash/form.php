<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            <i class="bi bi-cash-stack mr-2"></i> <?= $action === 'add' ? 'Add Transaction' : 'Edit Transaction' ?>
        </h1>
    </div>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 max-w-2xl mx-auto">
            <ul class="mb-0 list-disc list-inside">
                <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm max-w-2xl mx-auto">
        <div class="p-6">
            <form method="POST" action="<?= base_url($action === 'add' ? 'admin/pettycash/create' : 'admin/pettycash/update/' . $transaction['id']) ?>">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-1">Transaction Date <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="transaction_date" name="transaction_date"
                            value="<?= esc($transaction['transaction_date'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <div class="flex gap-4 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" name="type" value="Income"
                                    <?= (isset($transaction['type']) && $transaction['type'] === 'Income') ? 'checked' : 'checked' ?>>
                                <span class="text-sm text-gray-700">Income</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" name="type" value="Expense"
                                    <?= (isset($transaction['type']) && $transaction['type'] === 'Expense') ? 'checked' : '' ?>>
                                <span class="text-sm text-gray-700">Expense</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="category" name="category" required>
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
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="other_category" name="other_category" placeholder="Enter custom category"
                                value="<?= (isset($transaction['category']) && !in_array($transaction['category'], ['Float Top-up','Customer Refund','Miscellaneous Income','Supplies','Transport','Meals & Refreshments','Utilities','Repairs & Maintenance','Stationery','Miscellaneous Expense'])) ? esc($transaction['category']) : '' ?>">
                        </div>
                    </div>
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (<?= org_setting('currency_symbol', 'KSh') ?>) <span class="text-red-500">*</span></label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="amount" name="amount" step="0.01" min="0.01"
                            value="<?= esc($transaction['amount'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label for="reference_no" class="block text-sm font-medium text-gray-700 mb-1">Reference No</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="reference_no" name="reference_no"
                            value="<?= esc($transaction['reference_no'] ?? '') ?>" placeholder="Receipt number (optional)">
                    </div>
                    <div class="col-span-full">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="description" name="description" rows="3" required><?= esc($transaction['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1"><i class="bi bi-save"></i> Save Transaction</button>
                    <a href="<?= base_url('admin/pettycash') ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
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
})();
</script>
<?= $this->endSection() ?>
