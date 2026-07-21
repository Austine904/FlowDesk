<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Raise Supplier Payment</h1>
        <a href="<?= base_url('admin/lpos/view/' . $lpo['id']) ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to LPO
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors = session()->getFlashdata('errors'))): ?>
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">LPO Cross-Reference</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">LPO No</span>
                    <span class="text-sm text-gray-900 font-semibold"><?= esc($lpo['lpo_no']) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</span>
                    <span class="text-sm text-gray-900"><?= esc($supplier['name'] ?? $lpo['supplier_name'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">LPO Date</span>
                    <span class="text-sm text-gray-900"><?= esc($lpo['lpo_date']) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Received</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Total LPO Amount</span>
                    <span class="text-lg font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($lpo['total_amount'] ?? 0, 2) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Already Paid</span>
                    <span class="text-lg font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($alreadyPaid, 2) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Balance Due</span>
                    <span class="text-lg font-bold text-indigo-600"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($balanceDue, 2) ?></span>
                </div>
            </div>

            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Line Items</h3>
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Ordered</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Received</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900"><?= esc($item['name'] ?? 'N/A') ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= number_format($item['quantity_ordered'], 2) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= number_format($item['quantity_received'] ?? 0, 2) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($item['unit_price'], 2) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($item['quantity_ordered'] * $item['unit_price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Total:</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($lpo['total_amount'] ?? 0, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Payment Details</h2>
        </div>
        <div class="p-6">
            <form method="POST" action="<?= base_url('admin/supplier_payments/store') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="lpo_id" value="<?= $lpo['id'] ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (<?= org_setting('currency_symbol', 'KSh') ?>)</label>
                        <input type="number" step="0.01" min="0.01" max="<?= $balanceDue ?>" name="amount"
                               value="<?= $balanceDue ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               required>
                        <p class="text-xs text-gray-400 mt-1">Max: <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($balanceDue, 2) ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white" required>
                            <option value="">-- Select --</option>
                            <option value="Cash">Cash</option>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Submit for Approval
                    </button>
                    <a href="<?= base_url('admin/lpos/view/' . $lpo['id']) ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
