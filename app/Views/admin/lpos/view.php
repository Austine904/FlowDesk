<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">LPO: <?= esc($lpo['lpo_no']) ?></h1>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="<?= base_url('admin/lpos') ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">LPO Details</h2>
            <?php
            $badgeMap = ['Draft'=>'bg-gray-100 text-gray-700','Sent'=>'bg-blue-100 text-blue-700','Partially Received'=>'bg-amber-100 text-amber-700','Received'=>'bg-emerald-100 text-emerald-700','Cancelled'=>'bg-red-100 text-red-700'];
            $badgeCls = $badgeMap[$lpo['status']] ?? 'bg-gray-100 text-gray-700';
            ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeCls ?>"><?= esc($lpo['status']) ?></span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</span>
                    <span class="text-sm text-gray-900"><?= esc($lpo['supplier_name'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Job Ref</span>
                    <span class="text-sm text-gray-900"><?= esc($lpo['job_no'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Raised By</span>
                    <span class="text-sm text-gray-900"><?= esc(($lpo['raised_by_first_name'] ?? '') . ' ' . ($lpo['raised_by_last_name'] ?? '')) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">LPO Date</span>
                    <span class="text-sm text-gray-900"><?= esc($lpo['lpo_date']) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Expected Delivery</span>
                    <span class="text-sm text-gray-900"><?= esc($lpo['expected_date'] ?? 'Not set') ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</span>
                    <span class="text-sm text-gray-900 font-semibold"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($lpo['total_amount'] ?? 0, 2) ?></span>
                </div>
            </div>
            <?php if (!empty($lpo['notes'])): ?>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes</span>
                <p class="text-sm text-gray-700"><?= nl2br(esc($lpo['notes'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Line Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part No.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Ordered</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Received</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $idx = 1; ?>
                    <?php foreach ($items as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= $idx++ ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($item['name'] ?? 'N/A') ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= esc($item['part_number'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= esc($item['unit'] ?? 'piece') ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($item['quantity_ordered'], 2) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($item['quantity_received'] ?? 0, 2) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($item['unit_price'], 2) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format(($item['quantity_ordered'] * $item['unit_price']), 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Grand Total:</td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($lpo['total_amount'] ?? 0, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if (isset($supplierPayments) && !empty($supplierPayments)): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Supplier Payments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($supplierPayments as $sp): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium"><?= esc($sp['payment_ref']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($sp['amount'], 2) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($sp['payment_method']) ?></td>
                        <td class="px-4 py-3 text-sm">
                            <?php
                            $spBadge = match($sp['status']) {
                                'Pending Approval' => 'bg-amber-100 text-amber-800',
                                'Approved' => 'bg-blue-100 text-blue-800',
                                'Paid' => 'bg-emerald-100 text-emerald-800',
                                'Rejected' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $spBadge ?>"><?= esc($sp['status']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= esc($sp['payment_date'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-sm">
                            <a href="<?= base_url('admin/supplier_payments/view/' . $sp['id']) ?>" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Total Paid:</td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalPaid ?? 0, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php elseif (isset($canRaisePayment)): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Supplier Payment</h2>
                <?php if ($canRaisePayment): ?>
                <p class="text-sm text-gray-500 mt-1">This LPO has been fully received. You can now raise a supplier payment.</p>
                <?php else: ?>
                <p class="text-sm text-gray-500 mt-1">LPO must be in Received status before payment can be raised.</p>
                <?php endif; ?>
            </div>
            <?php if ($canRaisePayment): ?>
            <a href="<?= base_url('admin/supplier_payments/raise/' . $lpo['id']) ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Raise Payment
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="flex items-center gap-2 no-print">
        <?php if ($lpo['status'] === 'Draft'): ?>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2 btn-status-update" data-new-status="Sent">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            Mark as Sent
        </button>
        <?php endif; ?>
        <?php if (in_array($lpo['status'], ['Sent', 'Partially Received'])): ?>
        <a href="<?= base_url('admin/lpos/receive/' . $lpo['id']) ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Receive Items
        </a>
        <?php endif; ?>
        <?php if ($lpo['status'] === 'Draft'): ?>
        <a href="<?= base_url('admin/lpos/edit/' . $lpo['id']) ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
        <button class="bg-white border border-red-300 text-red-700 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2 btn-status-update" data-new-status="Cancelled">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel LPO
        </button>
        <?php endif; ?>
        <?php if ($lpo['status'] === 'Sent'): ?>
        <button class="bg-white border border-red-300 text-red-700 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2 btn-status-update" data-new-status="Cancelled">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel LPO
        </button>
        <?php endif; ?>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
}
</style>

<script>
$(document).ready(function() {
    $(document).on('click', '.btn-status-update', function() {
        var btn = $(this);
        var newStatus = btn.data('new-status');
        var id = <?= $lpo['id'] ?>;
        var csrf = getCsrfMeta();

        if (!confirm('Change LPO status to "' + newStatus + '"?')) return;

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '<?= base_url('admin/lpos/update_status/') ?>' + id,
            type: 'POST',
            data: { new_status: newStatus, [csrf.name]: csrf.hash },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    location.reload();
                } else {
                    alert(res.message || 'Failed to update status.');
                }
            },
            error: function() {
                alert('Error updating status.');
            },
            complete: function() {
                btn.prop('disabled', false).text(btn.data('original-text') || 'Update');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
