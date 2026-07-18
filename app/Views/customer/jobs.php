<?= $this->extend('customer/layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">My Jobs</h1>
    <p class="text-sm text-gray-500 mt-1">View the status of all your vehicle service jobs</p>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date In</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($jobs)): ?>
                <?php foreach ($jobs as $job): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($job['job_no']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($job['date_in'] ?? '') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?= esc($job['make'] ?? '') ?> <?= esc($job['model'] ?? '') ?> (<?= esc($job['registration_number'] ?? '') ?>)
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $job['job_status'] === 'Completed' ? 'bg-emerald-50 text-emerald-700' : ($job['job_status'] === 'Cancelled' ? 'bg-red-50 text-red-700' : 'bg-indigo-50 text-indigo-700') ?>">
                            <?= esc($job['job_status']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No jobs found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
