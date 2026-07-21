<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Payment: <?= esc($payment['payment_ref']) ?></h1>
        <a href="<?= base_url('admin/supplier_payments') ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Payments
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Payment Details</h2>
            <?php
            $badgeMap = [
                'Pending Approval' => 'bg-amber-100 text-amber-800',
                'Approved' => 'bg-blue-100 text-blue-800',
                'Paid' => 'bg-emerald-100 text-emerald-800',
                'Rejected' => 'bg-red-100 text-red-800',
            ];
            $bc = $badgeMap[$payment['status']] ?? 'bg-gray-100 text-gray-700';
            ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $bc ?>"><?= esc($payment['status']) ?></span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Ref</span>
                    <span class="text-sm text-gray-900 font-semibold"><?= esc($payment['payment_ref']) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</span>
                    <span class="text-sm text-gray-900"><?= esc($payment['supplier_name'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</span>
                    <span class="text-sm text-gray-900 font-semibold"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($payment['amount'], 2) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</span>
                    <span class="text-sm text-gray-900"><?= esc($payment['payment_method']) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">LPO Reference</span>
                    <span class="text-sm text-gray-900">
                        <a href="<?= base_url('admin/lpos/view/' . $payment['lpo_id']) ?>" class="text-indigo-600 hover:underline"><?= esc($payment['lpo_no'] ?? 'N/A') ?></a>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</span>
                    <span class="text-sm text-gray-900"><?= esc($payment['payment_date'] ?? 'Not set') ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Reference No</span>
                    <span class="text-sm text-gray-900"><?= esc($payment['reference_no'] ?? '-') ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">LPO Total</span>
                    <span class="text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($payment['lpo_total'] ?? 0, 2) ?></span>
                </div>
            </div>

            <?php if (!empty($payment['notes'])): ?>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes</span>
                <p class="text-sm text-gray-700"><?= nl2br(esc($payment['notes'])) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($payment['rejection_reason'])): ?>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="block text-xs font-medium text-red-600 uppercase tracking-wider mb-1">Rejection Reason</span>
                <p class="text-sm text-red-700"><?= esc($payment['rejection_reason']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Approval Timeline</h2>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <div class="w-0.5 h-12 bg-gray-200"></div>
                </div>
                <div class="pb-6">
                    <p class="text-sm font-medium text-gray-900">Raised</p>
                    <p class="text-xs text-gray-500">by <?= esc($payment['raised_by_name'] ?? 'N/A') ?> on <?= esc($payment['created_at']) ?></p>
                </div>
            </div>

            <?php if ($payment['status'] === 'Approved' || $payment['status'] === 'Paid'): ?>
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="w-0.5 h-12 bg-gray-200"></div>
                </div>
                <div class="pb-6">
                    <p class="text-sm font-medium text-gray-900">Approved</p>
                    <p class="text-xs text-gray-500">by <?= esc($payment['approved_by_name'] ?? 'N/A') ?> on <?= esc($payment['approved_at']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($payment['status'] === 'Rejected'): ?>
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Rejected</p>
                    <p class="text-xs text-gray-500">Reason: <?= esc($payment['rejection_reason']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($payment['status'] === 'Paid'): ?>
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Paid</p>
                    <p class="text-xs text-gray-500">on <?= esc($payment['payment_date'] ?? 'N/A') ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">LPO Details</h2>
        </div>
        <div class="overflow-x-auto">
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
                        <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-900">LPO Total:</td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($lpo['total_amount'] ?? 0, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
