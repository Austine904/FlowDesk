<?= $this->extend('technician/layout') ?>

<?= $this->section('content') ?>
<p class="text-sm text-gray-500 mb-6">Welcome, <?= esc($name) ?>. Here are your assigned jobs.</p>

<!-- Availability Status -->
<div class="mb-6">
    <?php if ($isAvailable): ?>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
            Available
        </span>
    <?php else: ?>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">
            <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span>
            Currently Assigned — <?= $activeJobCount ?> active job(s)
        </span>
    <?php endif; ?>
</div>

<!-- Stat cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Total Assigned -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Assigned</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= $totalJobs ?? 0 ?></p>
            </div>
            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-briefcase text-indigo-600 text-lg"></i>
            </div>
        </div>
    </div>
    <!-- Awaiting Diagnosis -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Awaiting Diagnosis</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= $awaitingDiagnosis ?? 0 ?></p>
            </div>
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-stethoscope text-amber-600 text-lg"></i>
            </div>
        </div>
    </div>
    <!-- In Progress -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">In Progress</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= $inProgress ?? 0 ?></p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-gear text-blue-600 text-lg"></i>
            </div>
        </div>
    </div>
    <!-- Completed -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Completed</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= $completed ?? 0 ?></p>
            </div>
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Jobs -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-base font-semibold text-gray-900">My Recent Jobs</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider">
                    <th class="text-left px-6 py-3">Job No</th>
                    <th class="text-left px-6 py-3">Customer</th>
                    <th class="text-left px-6 py-3">Vehicle</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-left px-6 py-3">Date In</th>
                    <th class="text-left px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($recentJobs)): ?>
                    <?php foreach ($recentJobs as $job): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= esc($job['job_no'] ?? '') ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= esc($job['customer_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= esc($job['registration_number'] ?? '') ?></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <?= esc($job['job_status'] ?? '') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?= esc($job['date_in'] ?? '') ?></td>
                        <td class="px-6 py-4">
                            <a href="<?= base_url('mechanic/jobs/' . ($job['id'] ?? '')) ?>" class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-wrench"></i> Diagnose
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">No jobs assigned to you yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        <a href="<?= base_url('mechanic/jobs') ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            View All Assigned Jobs <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
</div>
<?= $this->endSection() ?>
