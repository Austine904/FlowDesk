<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Staff Reports</h3>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/reports/export/staff/csv') ?>" class="bg-white border border-emerald-500 text-emerald-600 hover:bg-emerald-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <button onclick="window.print()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" name="start_date" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" value="<?= $start_date ?>">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" name="end_date" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" value="<?= $end_date ?>">
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filter</button>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-indigo-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-indigo-100 uppercase tracking-wider">Total Staff</p>
            <p class="text-xl font-bold mt-1"><?= $totalStaff ?></p>
        </div>
        <div class="bg-emerald-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-emerald-100 uppercase tracking-wider">Active Actions</p>
            <p class="text-xl font-bold mt-1"><?= $staffActive ?></p>
        </div>
        <div class="bg-cyan-600 text-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-cyan-100 uppercase tracking-wider">Status Changes</p>
            <p class="text-xl font-bold mt-1"><?= $statusChanges ?></p>
        </div>
        <div class="bg-amber-400 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-amber-800 uppercase tracking-wider">Payments Recorded</p>
            <p class="text-xl font-bold text-amber-900 mt-1"><?= $paymentsRecorded ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Jobs per Service Advisor</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jobs</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($advisorJobs as $a): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($a['first_name'] . ' ' . $a['last_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($a['company_id']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $a['total_jobs'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= (int) $a['completed'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($advisorJobs)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No advisor data for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Activity Log</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $actionStyles = [
                        'status_change' => 'bg-indigo-100 text-indigo-700',
                        'payment_recorded' => 'bg-emerald-100 text-emerald-700',
                        'lpo_created' => 'bg-amber-100 text-amber-700',
                        'lpo_received' => 'bg-amber-100 text-amber-700',
                        'job_created' => 'bg-purple-100 text-purple-700',
                        'diagnosis_saved' => 'bg-teal-100 text-teal-700',
                        'user_created' => 'bg-gray-200 text-gray-700',
                        'petty_cash_entry' => 'bg-amber-100 text-amber-700',
                    ]; ?>
                    <?php foreach ($activityLog as $log): ?>
                    <?php $badgeClass = $actionStyles[$log['action']] ?? 'bg-gray-100 text-gray-700'; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700"><?= $log['created_at'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($log['user_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>"><?= esc($log['action']) ?></span></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($log['entity_type']) ?> #<?= $log['entity_id'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= esc($log['description']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($activityLog)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">No activity for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
