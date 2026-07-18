<?= $this->extend('customer/layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Welcome, <?= esc($customerName ?? session()->get('user_name')) ?></h1>
    <p class="text-sm text-gray-500 mt-1">Here's an overview of your vehicle services</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= $activeJobs ?? 0 ?></p>
        <p class="text-sm text-gray-500 mt-1">Active Jobs</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= $completedJobs ?? 0 ?></p>
        <p class="text-sm text-gray-500 mt-1">Completed Jobs</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= $outstandingInvoices ?? 0 ?></p>
        <p class="text-sm text-gray-500 mt-1">Outstanding Invoices</p>
    </div>
</div>

<!-- Recent Jobs -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Recent Jobs</h3>
    </div>
    <div class="divide-y divide-gray-50">
        <?php if (!empty($recentJobs)): ?>
        <?php foreach ($recentJobs as $job): ?>
        <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
            <div>
                <p class="text-sm font-medium text-gray-900"><?= esc($job['job_no']) ?></p>
                <p class="text-xs text-gray-500"><?= esc($job['registration_number'] ?? '') ?></p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full font-medium <?= $job['job_status'] === 'Completed' ? 'bg-emerald-50 text-emerald-700' : ($job['job_status'] === 'Cancelled' ? 'bg-red-50 text-red-700' : 'bg-indigo-50 text-indigo-700') ?>">
                <?= esc($job['job_status']) ?>
            </span>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="px-6 py-8 text-center text-sm text-gray-400">No jobs found</div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
