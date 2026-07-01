<div class="table-responsive rounded">
    <table class="table table-striped table-bordered" style="width:100%">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Job Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jobs)): ?>
                <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?= esc($job['id']) ?></td>
                        <td><?= esc($job['job_name']) ?></td>
                        <td><?= esc($job['description'] ?? '') ?></td>
                        <td><?= esc(ucfirst($job['status'])) ?></td>
                        <td><?= esc($job['assigned_to'] ?? 'N/A') ?></td>
                        <td><?= esc($job['created_at']) ?></td>
                        <td>
                            <a href="<?= base_url('admin/jobs/edit/' . $job['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                            <a href="<?= base_url('admin/jobs/delete/' . $job['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No jobs found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
