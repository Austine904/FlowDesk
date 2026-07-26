<?= $this->extend('technician/layout') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-base font-semibold text-gray-900"><i class="fas fa-history mr-2"></i> Job History</h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider">
                        <th class="text-left px-4 py-3">Job No</th>
                        <th class="text-left px-4 py-3">Vehicle</th>
                        <th class="text-left px-4 py-3">Customer</th>
                        <th class="text-left px-4 py-3">Date In</th>
                        <th class="text-left px-4 py-3">Completed</th>
                        <th class="text-left px-4 py-3">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($completed_jobs)): ?>
                        <?php foreach ($completed_jobs as $job): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900"><?= esc($job['job_no'] ?? '') ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= esc($job['registration_number'] ?? '') ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= esc($job['customer_name'] ?? '') ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= esc($job['date_in'] ?? '') ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= esc($job['completed_at'] ? date('Y-m-d', strtotime($job['completed_at'])) : '') ?></td>
                            <td class="px-4 py-3">
                                <a href="<?= base_url('mechanic/jobs/' . $job['id']) ?>" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No completed jobs yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
