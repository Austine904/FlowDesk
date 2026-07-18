<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <h3 class="text-lg font-semibold text-gray-900"><i class="bi bi-tools me-2"></i> My Assigned Jobs</h3>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full divide-y divide-gray-200" id="mechanicJobsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle Reg</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date In</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($jobs)): ?>
                            <?php foreach ($jobs as $job): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($job['job_no'] ?? '') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($job['customer_name'] ?? '') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($job['registration_number'] ?? '') ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700"><?= esc($job['job_status'] ?? '') ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($job['date_in'] ?? '') ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="<?= base_url('mechanic/jobs/' . ($job['id'] ?? '')) ?>" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                        <i class="bi bi-wrench"></i> Diagnose
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-sm text-center text-gray-500">No jobs assigned to you yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($jobs)): ?>
<script>
$(document).ready(function() {
    FlowDesk.clientSideTable('#mechanicJobsTable', {
        order: [[4, 'desc']]
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
