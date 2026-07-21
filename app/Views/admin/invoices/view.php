<?= $this->extend('layouts/main') ?>

<?php $pageTitle = 'Invoice #' . esc($invoice['invoice_no']); ?>

<?= $this->section('content') ?>
<style>
    @media print {
        body { background: #fff !important; }
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .max-w-7xl { max-width: 100% !important; }
        .shadow-sm { box-shadow: none !important; }
    }
    .print-only { display: none; }
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="no-print flex items-center justify-between mb-6">
        <a href="<?= base_url('admin/invoices') ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">&larr; Back to Invoices</a>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/invoices/pdf/' . $invoice['id']) ?>"
               class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download PDF
            </a>
            <?php if (!empty($invoice['customer_email'])): ?>
                <a href="<?= base_url('admin/invoices/email/' . $invoice['id']) ?>"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Send by Email
                </a>
            <?php else: ?>
                <span class="bg-gray-100 text-gray-400 px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2 cursor-not-allowed" title="Customer has no email address">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    No Email
                </span>
            <?php endif; ?>
            <?php if ($invoice['status'] === 'Draft' || $invoice['status'] === 'Sent'): ?>
                <button onclick="showRegenerateForm()"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Recalculate
                </button>
            <?php endif; ?>
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-printer"></i> Print Invoice</button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="no-print flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6">
            <span><?= esc(session()->getFlashdata('success')) ?></span>
            <?php if (session()->getFlashdata('receipt_id')): ?>
                <a href="<?= base_url('admin/invoices/receipt/' . session()->getFlashdata('receipt_id')) ?>"
                   target="_blank"
                   class="ml-auto bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                    Print Receipt
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="no-print flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors = session()->getFlashdata('errors'))): ?>
        <div class="no-print flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <div class="flex justify-between items-start border-b-2 border-indigo-600 pb-6 mb-6">
                <div>
                    <?php $logo = org_setting('org_logo'); ?>
                    <?php if ($logo): ?>
                        <img src="<?= base_url($logo) ?>" alt="Logo" class="max-h-14 mb-2"><br>
                    <?php endif; ?>
                    <strong><?= esc(org_setting('org_name', 'FlowDesk Organization')) ?></strong><br>
                    <span class="text-sm"><?= nl2br(esc(org_setting('org_address', ''))) ?><br>
                    <?= esc(org_setting('org_phone', '')) ?> | <?= esc(org_setting('org_email', '')) ?></span>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-indigo-600">INVOICE</div>
                    <div class="text-sm text-gray-700"><strong>Invoice No:</strong> <?= esc($invoice['invoice_no']) ?></div>
                    <div class="text-sm text-gray-700"><strong>Date:</strong> <?= esc($invoice['invoice_date']) ?></div>
                    <div class="text-sm text-gray-700"><strong>Due Date:</strong> <?= esc($invoice['due_date']) ?></div>
                    <div class="mt-1">
                        <?php
                            $badgeMap = [
                                'Draft' => 'bg-gray-100 text-gray-700',
                                'Sent' => 'bg-blue-100 text-blue-700',
                                'Partially Paid' => 'bg-amber-100 text-amber-700',
                                'Paid' => 'bg-emerald-100 text-emerald-700',
                                'Overdue' => 'bg-red-100 text-red-700',
                                'Cancelled' => 'bg-red-100 text-red-700',
                            ];
                            $bc = $badgeMap[$invoice['status']] ?? 'bg-gray-100 text-gray-700';
                        ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $bc ?>"><?= esc($invoice['status']) ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Bill To:</h6>
                    <strong><?= esc($invoice['customer_name'] ?? 'N/A') ?></strong><br>
                    <span class="text-sm"><?= esc($invoice['customer_phone'] ?? '') ?></span>
                </div>
                <div class="text-right">
                    <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Reference:</h6>
                    <strong>Job No:</strong> <?= esc($invoice['job_no'] ?? 'N/A') ?><br>
                </div>
            </div>

            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Parts</h3>
            <div class="overflow-x-auto rounded-xl border border-gray-200 mb-6">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($parts)): ?>
                            <?php foreach ($parts as $part): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($part['name'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($part['part_number'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= (int) ($part['quantity_required'] ?? 0) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($part['unit_price_at_estimate'] ?? 0, 2) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format(($part['quantity_required'] ?? 0) * ($part['unit_price_at_estimate'] ?? 0), 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-sm text-gray-500 text-center">No parts recorded.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Labor Tasks</h3>
            <div class="overflow-x-auto rounded-xl border border-gray-200 mb-6">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate/hr</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($tasks)): ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($task['task_name'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($task['estimated_hours'] ?? 0, 2) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($task['rate_per_hour'] ?? 0, 2) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($task['labor_cost'] ?? 0, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm text-gray-500 text-center">No labor tasks recorded.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Sublets</h3>
            <div class="overflow-x-auto rounded-xl border border-gray-200 mb-6">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($sublets)): ?>
                            <?php foreach ($sublets as $sublet): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($sublet['description'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($sublet['provider_name'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($sublet['cost'] ?? 0, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No sublets recorded.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-6">
                <div class="w-full md:w-1/2 lg:w-2/5">
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="w-full divide-y divide-gray-200">
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">Parts Total</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['parts_total'], 2) ?></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">Labor Total</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['labor_total'], 2) ?></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">Sublets Total</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['sublet_total'], 2) ?></td>
                                </tr>
                                <?php if ((float)($invoice['lpo_parts_total'] ?? 0) > 0): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">LPO Parts Total</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['lpo_parts_total'], 2) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">Subtotal</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['subtotal'], 2) ?></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">VAT (<?= number_format($invoice['vat_rate'], 2) ?>%)</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['vat_amount'], 2) ?></td>
                                </tr>
                                <?php if ((float)($invoice['other_charges'] ?? 0) > 0): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">Other Charges <?= !empty($invoice['other_charges_description']) ? '(' . esc($invoice['other_charges_description']) . ')' : '' ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['other_charges'], 2) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">Discount</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">- <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['discount'], 2) ?></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-4 py-2 text-sm font-bold text-lg text-indigo-600">GRAND TOTAL</td>
                                    <td class="px-4 py-2 text-sm font-bold text-lg text-indigo-600 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['grand_total'], 2) ?></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">Amount Paid</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['amount_paid'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-bold text-xl <?= $invoice['balance_due'] == 0 ? 'text-emerald-600' : 'text-red-600' ?>">BALANCE DUE</td>
                                    <td class="px-4 py-2 font-bold text-xl <?= $invoice['balance_due'] == 0 ? 'text-emerald-600' : 'text-red-600' ?> text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['balance_due'], 2) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php if (!empty($invoice['notes'])): ?>
                <div class="mt-6">
                    <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Notes:</h6>
                    <p class="text-sm text-gray-700"><?= nl2br(esc($invoice['notes'])) ?></p>
                </div>
            <?php endif; ?>

            <hr class="border-gray-200 my-6">

            <h5 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h5>
            <?php if (!empty($payments)): ?>
                <div class="overflow-x-auto rounded-xl border border-gray-200 mb-6">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received By</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($payments as $payment): ?>
                                <?php
                                    $receiptForPayment = null;
                                    foreach ($receipts as $r) {
                                        if ($r['payment_id'] == $payment['id']) {
                                            $receiptForPayment = $r;
                                            break;
                                        }
                                    }
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($payment['payment_date']) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($payment['payment_method']) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($payment['reference_no'] ?? '-') ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($payment['amount'], 2) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($payment['received_by_name'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm">
                                        <?php if ($receiptForPayment): ?>
                                            <div class="flex items-center gap-1">
                                                <a href="<?= base_url('admin/invoices/receipt/' . $receiptForPayment['id']) ?>" target="_blank"
                                                   class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-2 py-1 rounded-lg text-xs font-medium transition-colors">
                                                    <i class="bi bi-printer"></i> Print
                                                </a>
                                                <a href="<?= base_url('admin/invoices/receipt_pdf/' . $receiptForPayment['id']) ?>"
                                                   class="inline-flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-2 py-1 rounded-lg text-xs font-medium transition-colors" title="Download PDF">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                                <a href="<?= base_url('admin/invoices/email_receipt/' . $receiptForPayment['id']) ?>"
                                                   class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-xs font-medium transition-colors" title="Email Receipt">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/invoices/generate_receipt/' . $payment['id']) ?>"
                                               class="inline-flex items-center gap-1 bg-gray-50 hover:bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-xs font-medium transition-colors">
                                                Generate
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500 italic">No payments recorded yet.</p>
            <?php endif; ?>

            <?php if ($invoice['balance_due'] > 0 && $invoice['status'] !== 'Cancelled'): ?>
                <div class="no-print bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900">Record Payment</h5>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="<?= base_url('admin/invoices/record_payment/' . $invoice['id']) ?>">
                            <?= csrf_field() ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount <?= org_setting('currency_symbol', 'KSh') ?></label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" required>
                                        <option value="">-- Select --</option>
                                        <option value="Cash">Cash</option>
                                        <option value="M-Pesa">M-Pesa</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Insurance">Insurance</option>
                                        <option value="Credit">Credit</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference No</label>
                                    <input type="text" name="reference_no" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" placeholder="e.g. M-Pesa transaction ID">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                                    <input type="date" name="payment_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" rows="2"></textarea>
                                </div>
                                <div class="col-span-full">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-cash"></i> Record Payment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
window.showRegenerateForm = function() {
    Swal.fire({
        title: 'Recalculate Invoice',
        text: 'This will update totals from the latest job card data.',
        icon: 'question',
        html:
            '<div class="text-left">' +
            '<p class="text-sm text-gray-500 mb-3">Current totals will be replaced with real-time job card data.</p>' +
            '<label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>' +
            '<input id="swal-discount" type="number" step="0.01" min="0" value="<?= esc($invoice['discount']) ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-3">' +
            '<label class="block text-sm font-medium text-gray-700 mb-1">Other Charges</label>' +
            '<input id="swal-other-charges" type="number" step="0.01" min="0" value="<?= esc($invoice['other_charges'] ?? 0) ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-3">' +
            '<label class="block text-sm font-medium text-gray-700 mb-1">Other Charges Description</label>' +
            '<input id="swal-other-charges-desc" type="text" maxlength="255" value="<?= esc($invoice['other_charges_description'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">' +
            '</div>',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        confirmButtonText: 'Recalculate',
        preConfirm: function() {
            return {
                discount: parseFloat(document.getElementById('swal-discount').value) || 0,
                other_charges: parseFloat(document.getElementById('swal-other-charges').value) || 0,
                other_charges_description: document.getElementById('swal-other-charges-desc').value || ''
            };
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;
        var data = result.value;
        var token = document.querySelector('meta[name="csrf-token"]');
        var name = document.querySelector('meta[name="csrf-name"]');
        if (token && name) {
            data[name.getAttribute('content')] = token.getAttribute('content');
        }
        $.ajax({
            url: '<?= base_url('admin/invoices/regenerate/' . $invoice['id']) ?>',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Done!', res.message, 'success').then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message || 'Failed to recalculate.', 'error');
                }
            },
            error: function(xhr) {
                var res = xhr.responseJSON;
                if (xhr.status === 302) {
                    location.reload();
                    return;
                }
                Swal.fire('Error', (res && res.message) || 'Failed to recalculate.', 'error');
            }
        });
    });
};
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
