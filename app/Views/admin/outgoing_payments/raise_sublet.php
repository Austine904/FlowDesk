<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $pageTitle = 'Raise Sublet Payment'; ?>

<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Raise Sublet Payment</h1>
        <a href="<?= base_url('admin/sublets') ?>" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to Sublets</a>
    </div>

    <!-- Gate passed banner -->
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="text-sm font-medium text-emerald-800">Sublet Verified — Ready for Payment</p>
            <p class="text-xs text-emerald-600 mt-0.5">All validation checks passed.</p>
        </div>
    </div>

    <!-- Sublet Cross-reference -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Sublet Details</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="block text-xs text-gray-500">Job No</span>
                    <span class="font-medium text-gray-900"><?= esc($sublet['job_no'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Provider</span>
                    <span class="font-medium text-gray-900"><?= esc($sublet['provider_name'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Date Sent</span>
                    <span class="font-medium text-gray-900"><?= esc($sublet['date_sent'] ?? '') ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Date Returned</span>
                    <span class="font-medium text-gray-900"><?= esc($sublet['date_returned'] ?? '-') ?></span>
                </div>
            </div>
            <div>
                <span class="block text-xs text-gray-500">Description</span>
                <p class="text-sm text-gray-900 mt-1"><?= esc($sublet['description'] ?? '') ?></p>
            </div>
            <div class="flex items-center justify-end gap-6 pt-2 border-t border-gray-100">
                <div class="text-sm">
                    <span class="text-gray-500">Cost:</span>
                    <span class="font-medium text-gray-900">KSh <?= number_format($sublet['cost'] ?? 0, 2) ?></span>
                </div>
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
            <input type="hidden" name="payment_type" value="Sublet">
            <input type="hidden" name="source_id" value="<?= $sublet['id'] ?>">

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
                <a href="<?= base_url('admin/sublets') ?>" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium">Submit for Approval</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
