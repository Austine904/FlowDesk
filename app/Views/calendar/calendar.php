<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>



<div class="container  mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📅 FlowDesk Schedule</h2>
        <div class="legend d-flex">
            <div class="legend-item"><span style="background-color: #198754;"></span> Service</div>
            <div class="legend-item"><span style="background-color: #0dcaf0;"></span> Inspection</div>
            <div class="legend-item"><span style="background-color: #ffc107;"></span> Repair</div>
            <div class="legend-item"><span style="background-color: #dc3545;"></span> Cancelled</div>
        </div>
    </div>

    <div class="calendar-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 p-3 bg-light rounded shadow-sm sticky-top" style="z-index: 1020;">
            <div class="d-flex align-items-center gap-2">
                <label for="eventTypeFilter" class="form-label mb-0">Filter by Type</label>
                <select id="eventTypeFilter" class="form-select form-select-sm" style="width: 150px;">
                    <option value="all">All</option>
                    <option value="service">Service</option>
                    <option value="inspection">Inspection</option>
                    <option value="repair">Repair</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>


            <!-- Toast for Feedback -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="calendarToast" class="toast text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
                    <div class="toast-body">
                        Filter applied successfully!
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <label for="startDate" class="form-label mb-0">From</label>
                <input type="date" id="startDate" class="form-control form-control-sm">
                <label for="endDate" class="form-label mb-0">To</label>
                <input type="date" id="endDate" class="form-control form-control-sm">
            </div>
            <div class="d-flex align-items-center gap-2">
                <input id="eventSearchInput" type="text" class="form-control form-control-sm" placeholder="Search events..." style="width: 200px;">
                <button id="addEventBtn" class="btn btn-primary btn-sm">➕ Add Event</button>
            </div>
        </div>
        <div id="calendar"></div>
        <small id="eventCount" class="text-muted ms-2"></small>
    </div>

</div>

<?= $this->include('calendar/modals') ?>



<?= $this->endSection() ?>