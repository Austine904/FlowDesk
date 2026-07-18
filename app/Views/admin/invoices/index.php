<?= $this->extend('layouts/main') ?>

<?php $pageTitle = 'Invoices'; ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900"><i class="bi bi-receipt mr-2"></i> <?= $pageTitle ?></h1>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/invoices/mark_overdue') ?>" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors" onclick="return confirm('Mark overdue invoices?')">
                <i class="bi bi-clock-history mr-1"></i> Mark Overdue
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-0">
            <div class="overflow-x-auto rounded-xl">
                <table id="invoicesTable" class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($invoices as $inv): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($inv['invoice_no']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($inv['customer_name'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($inv['job_no'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($inv['invoice_date']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($inv['due_date']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['grand_total'], 2) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['amount_paid'], 2) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['balance_due'], 2) ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <?php
                                        $badgeMap = [
                                            'Draft' => 'bg-gray-100 text-gray-700',
                                            'Sent' => 'bg-blue-100 text-blue-700',
                                            'Partially Paid' => 'bg-amber-100 text-amber-700',
                                            'Paid' => 'bg-emerald-100 text-emerald-700',
                                            'Overdue' => 'bg-red-100 text-red-700',
                                            'Cancelled' => 'bg-red-100 text-red-700',
                                        ];
                                        $badgeClass = $badgeMap[$inv['status']] ?? 'bg-gray-100 text-gray-700';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>"><?= esc($inv['status']) ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="<?= base_url('admin/invoices/view/' . $inv['id']) ?>" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    FlowDesk.clientSideTable('#invoicesTable', {
        order: [[3, 'desc']],
        language: {
            emptyTable: 'No invoices found. Generate an invoice from a job card to get started.'
        }
    });
});
</script>
<?= $this->endSection() ?>
