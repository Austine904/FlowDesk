<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $pageTitle = 'Ad-hoc Payment'; ?>

<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Ad-hoc Payment</h1>
        <a href="<?= base_url('admin/outgoing_payments') ?>" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to Payments</a>
    </div>

    <!-- Warning -->
    <div class="flex items-start gap-3 p-4 bg-amber-50 rounded-lg border border-amber-200">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="text-sm font-medium text-amber-800">No source document. Admin only.</p>
            <p class="text-xs text-amber-600 mt-0.5">This payment is not linked to an LPO or sublet.</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Payment Details</h3>
        </div>
        <form method="POST" action="<?= base_url('admin/outgoing_payments/store') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="payment_type" value="Ad-hoc">

            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payee Type <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="payee_type" value="Supplier" checked onchange="togglePayeeFields()" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Supplier</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="payee_type" value="Staff" onchange="togglePayeeFields()" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Staff</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="payee_type" value="Other" onchange="togglePayeeFields()" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Other</span>
                        </label>
                    </div>
                </div>

                <div id="supplierField">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                    <select name="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="">Select supplier</option>
                        <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="staffField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Staff Member <span class="text-red-500">*</span></label>
                    <select name="payee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="">Select staff</option>
                        <?php foreach ($staff as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= esc($u['first_name'] . ' ' . $u['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="otherField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payee Name</label>
                    <input type="text" name="payee_other_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Enter payee name" disabled>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference No.</label>
                    <input type="text" name="reference_no" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Optional reference">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Optional"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <a href="<?= base_url('admin/outgoing_payments') ?>" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium">Submit for Approval</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePayeeFields() {
    var val = document.querySelector('input[name="payee_type"]:checked').value;
    document.getElementById('supplierField').classList.toggle('hidden', val !== 'Supplier');
    document.getElementById('staffField').classList.toggle('hidden', val !== 'Staff');
    document.getElementById('otherField').classList.toggle('hidden', val !== 'Other');
    document.querySelector('#supplierField select').disabled = (val !== 'Supplier');
    document.querySelector('#staffField select').disabled = (val !== 'Staff');
    document.querySelector('#otherField input').disabled = (val !== 'Other');
}
</script>

<?= $this->endSection() ?>
