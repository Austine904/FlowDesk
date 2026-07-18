<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">Schedule</h2>
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5 text-xs font-medium"><span class="w-3 h-3 rounded-full" style="background-color: #198754;"></span> Service</span>
            <span class="flex items-center gap-1.5 text-xs font-medium"><span class="w-3 h-3 rounded-full" style="background-color: #0dcaf0;"></span> Inspection</span>
            <span class="flex items-center gap-1.5 text-xs font-medium"><span class="w-3 h-3 rounded-full" style="background-color: #ffc107;"></span> Repair</span>
            <span class="flex items-center gap-1.5 text-xs font-medium"><span class="w-3 h-3 rounded-full" style="background-color: #dc3545;"></span> Cancelled</span>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Today's Events</span>
                <span class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900"><?= $todayEventsCount + $todayJobsCount ?></p>
            <p class="text-xs text-gray-500 mt-1"><?= $todayEventsCount ?> calendar &bull; <?= $todayJobsCount ?> job drop-offs</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Jobs</span>
                <span class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900"><?= $activeJobsCount ?></p>
            <p class="text-xs text-gray-500 mt-1">across all statuses</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Upcoming Jobs</span>
                <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900"><?= count($upcomingJobs) ?></p>
            <p class="text-xs text-gray-500 mt-1">nearest active jobs</p>
        </div>
    </div>

    <!-- Main layout: Calendar + Upcoming sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar (spans 2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <label for="eventTypeFilter" class="text-sm font-medium text-gray-700">Filter</label>
                    <select id="eventTypeFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                        <option value="all">All</option>
                        <option value="service">Service</option>
                        <option value="inspection">Inspection</option>
                        <option value="repair">Repair</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <label for="startDate" class="text-sm font-medium text-gray-700">From</label>
                    <input type="date" id="startDate" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    <label for="endDate" class="text-sm font-medium text-gray-700">To</label>
                    <input type="date" id="endDate" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div class="flex items-center gap-3">
                    <input id="eventSearchInput" type="text" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none w-48" placeholder="Search events...">
                    <button id="addEventBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Event
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div id="calendar"></div>
                <small id="eventCount" class="text-gray-400 ms-2"></small>
            </div>
        </div>

        <!-- Right sidebar: Upcoming Events -->
        <div class="space-y-6">
            <!-- Upcoming Events -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Upcoming Events</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php if (!empty($upcomingEvents)): ?>
                        <?php foreach ($upcomingEvents as $event): ?>
                        <div class="px-5 py-3 hover:bg-gray-50">
                            <div class="flex items-start gap-3">
                                <span class="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0" style="background-color: <?= esc($event['color'] ?? '#007bff') ?>"></span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate"><?= esc($event['title']) ?></p>
                                    <p class="text-xs text-gray-500">
                                        <?= date('D, M j g:i A', strtotime($event['start_time'])) ?>
                                        <?php if (!empty($event['event_type'])): ?>
                                        &bull; <?= esc(ucfirst($event['event_type'])) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="px-5 py-8 text-center text-sm text-gray-400">No upcoming events</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Jobs -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Active Jobs</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php if (!empty($upcomingJobs)): ?>
                        <?php foreach ($upcomingJobs as $job): ?>
                        <div class="px-5 py-3 hover:bg-gray-50">
                            <div class="flex items-start gap-3">
                                <span class="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0 bg-indigo-500"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?= esc($job['job_no']) ?></p>
                                        <span class="text-xs px-1.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-medium flex-shrink-0"><?= esc($job['job_status']) ?></span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">
                                        <?= esc($job['registration_number'] ?? '') ?>
                                        <?php if (!empty($job['customer_name'])): ?>&bull; <?= esc($job['customer_name']) ?><?php endif; ?>
                                    </p>
                                    <p class="text-xs text-gray-400">In: <?= date('M j', strtotime($job['date_in'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="px-5 py-8 text-center text-sm text-gray-400">No active jobs</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('calendar/modals') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/assets/js/calendar.js') ?>"></script>
<?= $this->endSection() ?>
