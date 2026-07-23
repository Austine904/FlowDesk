<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $pageTitle = 'Payment ' . ($payment['payment_ref'] ?? ''); ?>

<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <a href="<?= base_url('admin/outgoing_payments') ?>" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to Payments</a>
    </div>

    <!-- Approval Timeline -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-6">Approval Timeline</h3>
        <div class="space-y-0">
            <!-- Step 1: Raised -->
            <div class="flex items-start gap-4 pb-6 relative">
                <div class="flex flex-col items-center">
                    <div class="w-6 h-6 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="w-0.5 h-full bg-gray-200 mt-1"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Raised</p>
                    <p class="text-xs text-gray-500"><?= esc($payment['raised_by_name'] ?? '') ?> · <?= esc($payment['created_at'] ?? '') ?></p>
                </div>
            </div>
            <!-- Step 2: Review -->
            <div class="flex items-start gap-4 pb-6 relative">
                <div class="flex flex-col items-center">
                    <?php $step2Done = $payment['status'] !== 'Pending Approval'; ?>
                    <div class="w-6 h-6 rounded-full <?= $step2Done ? 'bg-green-500' : 'bg-amber-400' ?> flex items-center justify-center flex-shrink-0">
                        <?php if ($payment['status'] === 'Rejected'): ?>
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        <?php elseif ($step2Done): ?>
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        <?php else: ?>
                        <span class="text-white text-xs font-bold">!</span>
                        <?php endif; ?>
                    </div>
                    <div class="w-0.5 h-full bg-gray-200 mt-1"></div>
                </div>
                <div class="flex-1">
                    <?php if ($payment['status'] === 'Rejected'): ?>
                    <p class="text-sm font-medium text-red-600">Rejected</p>
                    <p class="text-xs text-gray-500">Reason: <?= esc($payment['rejection_reason'] ?? '') ?></p>
                    <?php elseif ($payment['approved_by_name']): ?>
                    <p class="text-sm font-medium text-green-600">Approved</p>
                    <p class="text-xs text-gray-500"><?= esc($payment['approved_by_name']) ?> · <?= esc($payment['approved_at'] ?? '') ?></p>
                    <?php else: ?>
                    <p class="text-sm font-medium text-amber-600">Pending Review</p>
                    <p class="text-xs text-gray-500">Awaiting approval</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Step 3: Paid -->
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-6 h-6 rounded-full <?= $payment['status'] === 'Paid' ? 'bg-green-500' : 'bg-gray-300' ?> flex items-center justify-center flex-shrink-0">
                        <?php if ($payment['status'] === 'Paid'): ?>
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        <?php else: ?>
                        <span class="text-white text-xs">3</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex-1">
                    <?php if ($payment['status'] === 'Paid'): ?>
                    <p class="text-sm font-medium text-green-600">Paid</p>
                    <p class="text-xs text-gray-500"><?= esc($payment['payment_date'] ?? '') ?> · <?= esc($payment['payment_method']) ?><?= $payment['reference_no'] ? ' · Ref: ' . esc($payment['reference_no']) : '' ?></p>
                    <?php else: ?>
                    <p class="text-sm font-medium text-gray-400">Paid</p>
                    <p class="text-xs text-gray-400">Not yet paid</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Payment Details</h3>
            <?php
            $typeBadge = match($payment['payment_type']) {
                'LPO' => 'bg-blue-100 text-blue-700',
                'Sublet' => 'bg-purple-100 text-purple-700',
                'Expense' => 'bg-orange-100 text-orange-700',
                'Staff Reimbursement' => 'bg-teal-100 text-teal-700',
                default => 'bg-gray-100 text-gray-700',
            };
            $statusBadge = match($payment['status']) {
                'Pending Approval' => 'bg-amber-100 text-amber-800',
                'Approved' => 'bg-blue-100 text-blue-800',
                'Paid' => 'bg-emerald-100 text-emerald-800',
                'Rejected' => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-700',
            };
            ?>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeBadge ?>"><?= esc($payment['payment_type']) ?></span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusBadge ?>"><?= esc($payment['status']) ?></span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="block text-xs text-gray-500">Payment Ref</span>
                    <span class="font-medium text-gray-900"><?= esc($payment['payment_ref']) ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Payee</span>
                    <span class="font-medium text-gray-900"><?= esc($payment['payee_name'] ?? '-') ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Amount</span>
                    <span class="font-medium text-gray-900">KSh <?= number_format($payment['amount'] ?? 0, 2) ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Method</span>
                    <span class="font-medium text-gray-900"><?= esc($payment['payment_method']) ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Reference</span>
                    <span class="font-medium text-gray-900"><?= esc($payment['reference_no'] ?? '-') ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Payment Date</span>
                    <span class="font-medium text-gray-900"><?= esc($payment['payment_date'] ?? '-') ?></span>
                </div>
            </div>
            <?php if (!empty($payment['notes'])): ?>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="block text-xs text-gray-500 mb-1">Notes</span>
                <p class="text-sm text-gray-700"><?= nl2br(esc($payment['notes'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Source Document Card -->
    <?php if (!empty($sourceDoc)): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Source Document</h3>
        </div>
        <div class="p-6">
            <?php if ($payment['source_type'] === 'lpos'): ?>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-xs text-gray-500">LPO No</span>
                        <span class="font-medium text-gray-900"><?= esc($sourceDoc['lpo_no'] ?? '') ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Supplier</span>
                        <span class="font-medium text-gray-900"><?= esc($sourceDoc['supplier_name'] ?? '') ?></span>
                    </div>
                </div>
                <?php if (!empty($sourceItems)): ?>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($sourceItems as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-gray-900"><?= esc($item['name'] ?? '') ?></td>
                                <td class="px-3 py-2 text-right text-gray-600"><?= number_format($item['quantity_ordered'], 2) ?></td>
                                <td class="px-3 py-2 text-right text-gray-600">KSh <?= number_format($item['unit_price'], 2) ?></td>
                                <td class="px-3 py-2 text-right text-gray-900 font-medium">KSh <?= number_format($item['quantity_ordered'] * $item['unit_price'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <div class="text-right">
                    <a href="<?= base_url('admin/lpos/view/' . $payment['source_id']) ?>" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View LPO →</a>
                </div>
            </div>
            <?php elseif ($payment['source_type'] === 'sublets'): ?>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-xs text-gray-500">Job No</span>
                        <span class="font-medium text-gray-900"><?= esc($sourceDoc['job_no'] ?? '') ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Provider</span>
                        <span class="font-medium text-gray-900"><?= esc($sourceDoc['provider_name'] ?? '') ?></span>
                    </div>
                </div>
                <p class="text-sm text-gray-600"><?= esc($sourceDoc['description'] ?? '') ?></p>
                <div class="text-right">
                    <a href="<?= base_url('admin/sublets') ?>" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View Sublet →</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <?php if (session()->get('role') === 'admin'): ?>
    <div class="flex items-center gap-3">
        <?php if ($payment['status'] === 'Pending Approval'): ?>
        <button onclick="openApprove(<?= $payment['id'] ?>)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Approve</button>
        <button onclick="openReject(<?= $payment['id'] ?>)" class="bg-white border border-red-300 text-red-700 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium">Reject</button>
        <?php elseif ($payment['status'] === 'Approved'): ?>
        <button onclick="openMarkPaid(<?= $payment['id'] ?>)" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Mark as Paid</button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Approve Modal -->
<div id="approveModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Payment</h3>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to approve <?= esc($payment['payment_ref']) ?>?</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('approveModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</button>
            <button id="confirmApproveBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Approve</button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Payment</h3>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
            <textarea id="rejectionReason" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" rows="3" minlength="10" required></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('rejectModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</button>
            <button id="confirmRejectBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Reject</button>
        </div>
    </div>
</div>

<!-- Mark Paid Modal -->
<div id="markPaidModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="markPaidModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mark as Paid</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                <input type="date" id="paidDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference No.</label>
                <input type="text" id="paidRef" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Optional">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeModal('markPaidModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</button>
            <button id="confirmPaidBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Mark as Paid</button>
        </div>
    </div>
</div>

<script>
function closeModal(name) {
    document.getElementById(name).classList.add('hidden');
    var backdrop = document.getElementById(name + '-backdrop');
    if (backdrop) backdrop.classList.add('hidden');
}

window.openApprove = function(id) {
    closeModal('rejectModal'); closeModal('markPaidModal');
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModal-backdrop').classList.remove('hidden');
    document.getElementById('confirmApproveBtn').onclick = function() {
        $.ajax({
            url: '<?= base_url('admin/outgoing_payments/approve/') ?>' + id,
            type: 'POST',
            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            success: function(r) { if (r.status === 'success') { location.reload(); } else { alert(r.message); } },
            error: function() { alert('Error.'); }
        });
    };
};

window.openReject = function(id) {
    closeModal('approveModal'); closeModal('markPaidModal');
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal-backdrop').classList.remove('hidden');
    document.getElementById('confirmRejectBtn').onclick = function() {
        var reason = document.getElementById('rejectionReason').value;
        if (reason.length < 10) { alert('Reason must be at least 10 characters.'); return; }
        $.ajax({
            url: '<?= base_url('admin/outgoing_payments/reject/') ?>' + id,
            type: 'POST',
            data: { rejection_reason: reason, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            success: function(r) { if (r.status === 'success') { location.reload(); } else { alert(r.message); } },
            error: function() { alert('Error.'); }
        });
    };
};

window.openMarkPaid = function(id) {
    closeModal('approveModal'); closeModal('rejectModal');
    document.getElementById('markPaidModal').classList.remove('hidden');
    document.getElementById('markPaidModal-backdrop').classList.remove('hidden');
    document.getElementById('confirmPaidBtn').onclick = function() {
        var date = document.getElementById('paidDate').value;
        if (!date) { alert('Payment date is required.'); return; }
        $.ajax({
            url: '<?= base_url('admin/outgoing_payments/mark_paid/') ?>' + id,
            type: 'POST',
            data: { payment_date: date, reference_no: document.getElementById('paidRef').value, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            success: function(r) { if (r.status === 'success') { location.reload(); } else { alert(r.message); } },
            error: function() { alert('Error.'); }
        });
    };
};
</script>

<?= $this->endSection() ?>
