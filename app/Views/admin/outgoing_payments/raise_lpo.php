<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $pageTitle = 'Raise LPO Payment'; ?>

<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Raise LPO Payment</h1>
        <a href="<?= base_url('admin/lpos/view/' . $lpo['id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to LPO</a>
    </div>

    <!-- Gate passed banner -->
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="text-sm font-medium text-emerald-800">LPO Verified — Ready for Payment</p>
            <p class="text-xs text-emerald-600 mt-0.5">All validation checks passed.</p>
        </div>
    </div>

    <!-- LPO Cross-reference -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">LPO: <?= esc($lpo['lpo_no']) ?></h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="block text-xs text-gray-500">Supplier</span>
                    <span class="font-medium text-gray-900"><?= esc($lpo['supplier_name'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">LPO Date</span>
                    <span class="font-medium text-gray-900"><?= esc($lpo['lpo_date'] ?? '') ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Status</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"><?= esc($lpo['status']) ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Total</span>
                    <span class="font-medium text-gray-900">KSh <?= number_format($lpo['total_amount'] ?? 0, 2) ?></span>
                </div>
            </div>

            <?php if (!empty($items)): ?>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty Ordered</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty Received</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-gray-900"><?= esc($item['name'] ?? '') ?></td>
                            <td class="px-3 py-2 text-gray-600"><?= number_format($item['quantity_ordered'], 2) ?></td>
                            <td class="px-3 py-2 text-gray-600"><?= number_format($item['quantity_received'] ?? 0, 2) ?></td>
                            <td class="px-3 py-2 text-gray-600">KSh <?= number_format($item['unit_price'], 2) ?></td>
                            <td class="px-3 py-2 text-right text-gray-900 font-medium">KSh <?= number_format($item['quantity_ordered'] * $item['unit_price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <div class="flex items-center justify-end gap-6 pt-2 border-t border-gray-100">
                <div class="text-sm">
                    <span class="text-gray-500">Already Paid:</span>
                    <span class="font-medium text-gray-700 ml-1">KSh <?= number_format($alreadyPaid ?? 0, 2) ?></span>
                </div>
                <div class="text-sm">
                    <span class="text-gray-500">Balance Due:</span>
                    <span class="font-bold text-indigo-600 text-lg ml-1">KSh <?= number_format($balanceDue ?? 0, 2) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Payment Details</h3>
        </div>
        <form method="POST" action="<?= base_url('admin/outgoing_payments/store') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="payment_type" value="LPO">
            <input type="hidden" name="source_id" value="<?= $lpo['id'] ?>">

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" max="<?= $balanceDue ?>" name="amount" value="<?= $balanceDue ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    <p class="text-xs text-gray-400 mt-1">Balance due: KSh <?= number_format($balanceDue ?? 0, 2) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 bg-white" required>
                        <option value="">Select method</option>
                        <option value="Cash">Cash</option>
                        <option value="M-Pesa">M-Pesa</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Optional"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <a href="<?= base_url('admin/lpos/view/' . $lpo['id']) ?>" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium">Submit for Approval</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
