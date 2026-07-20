<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (!empty($jobs)): ?>
                <?php foreach ($jobs as $job): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($job['id']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?= esc($job['job_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= esc($job['description'] ?? '') ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?= $job['status'] === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($job['status'] === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') ?>">
                                <?= esc(ucfirst($job['status'])) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= esc($job['assigned_to'] ?? 'N/A') ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= esc($job['created_at']) ?></td>
                        <td class="px-4 py-3 text-sm">
                            <a href="<?= base_url('admin/jobs/delete/' . $job['id']) ?>" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-sm text-gray-500 text-center">No jobs found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
