<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $pageTitle = 'New Outgoing Payment'; ?>

<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">New Outgoing Payment</h1>
        <p class="text-sm text-gray-500 mt-1">Select payment type</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <a href="<?= base_url('admin/lpos') ?>" class="bg-white rounded-xl border-2 border-blue-200 hover:border-blue-400 shadow-sm p-6 transition-colors group">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700">LPO Payment</h3>
            <p class="text-sm text-gray-500 mt-1">Pay a supplier for goods received via an LPO. Select an LPO that has been fully received.</p>
            <span class="text-xs text-blue-600 font-medium mt-3 inline-block group-hover:underline">Select LPO →</span>
        </a>

        <a href="<?= base_url('admin/sublets') ?>" class="bg-white rounded-xl border-2 border-purple-200 hover:border-purple-400 shadow-sm p-6 transition-colors group">
            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-700">Sublet Payment</h3>
            <p class="text-sm text-gray-500 mt-1">Pay an external provider for completed sublet work.</p>
            <span class="text-xs text-purple-600 font-medium mt-3 inline-block group-hover:underline">Select Sublet →</span>
        </a>

        <div class="bg-white rounded-xl border-2 border-orange-200 shadow-sm p-6 opacity-60">
            <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Expense</h3>
            <p class="text-sm text-gray-500 mt-1">Pay an overhead expense. Use Petty Cash for now.</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 mt-3">Coming Soon</span>
        </div>

        <div class="bg-white rounded-xl border-2 border-teal-200 shadow-sm p-6 opacity-60">
            <div class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Staff Reimbursement</h3>
            <p class="text-sm text-gray-500 mt-1">Reimburse a staff member for out-of-pocket expenses.</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700 mt-3">Coming Soon</span>
        </div>

        <a href="<?= base_url('admin/outgoing_payments/raise/adhoc') ?>" class="bg-white rounded-xl border-2 border-gray-200 hover:border-gray-400 shadow-sm p-6 transition-colors group">
            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-gray-700">Ad-hoc Payment</h3>
            <p class="text-sm text-gray-500 mt-1">One-off payment without a source document. Admin only.</p>
            <span class="text-xs text-gray-600 font-medium mt-3 inline-block group-hover:underline">Raise →</span>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
