<!-- Event Details Modal -->
<div id="actionModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('actionModal')"></div>
<div id="actionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900" id="actionModalLabel">
                <i class="bi bi-info-circle-fill me-2"></i> Event Details
            </h5>
            <button type="button" onclick="closeModal('actionModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <h6 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Event Information</h6>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><span class="font-medium text-gray-700">Event Type:</span> <span id="event_type" class="text-gray-500 ml-1"></span></div>
                    <div><span class="font-medium text-gray-700">Title:</span> <span id="event_title" class="text-gray-500 ml-1"></span></div>
                    <div><span class="font-medium text-gray-700">Start Time:</span> <span id="event_start" class="text-gray-500 ml-1"></span></div>
                    <div><span class="font-medium text-gray-700">End Time:</span> <span id="event_end" class="text-gray-500 ml-1"></span></div>
                    <div><span class="font-medium text-gray-700">Status:</span> <span id="event_status" class="text-gray-500 ml-1"></span></div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h6 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Job Information</h6>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><span class="font-medium text-gray-700">Job No:</span> <span id="event_job_no" class="text-gray-500 ml-1"></span></div>
                    <div><span class="font-medium text-gray-700">Vehicle:</span> <span id="event_vehicle" class="text-gray-500 ml-1"></span></div>
                    <div><span class="font-medium text-gray-700">Mechanic:</span> <span id="event_mechanic" class="text-gray-500 ml-1"></span></div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h6 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Customer</h6>
                <div class="text-sm"><span class="font-medium text-gray-700">Name:</span> <span id="event_customer" class="text-gray-500 ml-1"></span></div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h6 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Description</h6>
                <div class="text-sm text-gray-500"><span id="event_description"></span></div>
            </div>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeModal('actionModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <i class="bi bi-x-circle"></i> Close
            </button>
        </div>
    </div>
</div>

<!-- Multi-Step Add Event Modal -->
<style>
  .progress-dots {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
    gap: 0.5rem;
  }
  .progress-dots .dot {
    width: 12px;
    height: 12px;
    background-color: #ccc;
    border-radius: 50%;
    transition: background-color 0.3s ease;
  }
  .progress-dots .dot.active {
    background-color: #4f46e5;
  }
  #addEventModal .modal-body {
    min-height: 500px;
  }
</style>

<div id="addEventModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('addEventModal')"></div>
<div id="addEventModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900" id="addEventModalLabel">Add New Calendar Event</h5>
            <button type="button" onclick="closeModal('addEventModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6" style="min-height:500px;">
            <div class="progress-dots text-center mb-4">
                <span class="dot active" data-step="1"></span>
                <span class="dot" data-step="2"></span>
                <span class="dot" data-step="3"></span>
            </div>

            <form id="addEventForm" novalidate>
                <!-- Step 1 -->
                <div class="form-step" data-step="1">
                    <h6 class="text-sm font-semibold text-indigo-600 mb-4">Step 1: Event Details</h6>
                    <div class="mb-4">
                        <label for="eventTitle" class="block text-sm font-medium text-gray-700 mb-1">Event Title <span class="text-red-500">*</span></label>
                        <input type="text" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="eventTitle" name="title" required>
                        <div class="invalid-feedback hidden text-xs text-red-600 mt-1">Event Title is required.</div>
                    </div>
                    <div class="mb-4">
                        <label for="eventType" class="block text-sm font-medium text-gray-700 mb-1">Event Type <span class="text-red-500">*</span></label>
                        <select class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none select2-basic" id="eventType" name="event_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="general">General</option>
                            <option value="job_pickup">Job Pickup</option>
                            <option value="appointment">Appointment</option>
                            <option value="meeting">Meeting</option>
                            <option value="holiday">Holiday</option>
                            <option value="leave">Leave</option>
                        </select>
                        <div class="invalid-feedback hidden text-xs text-red-600 mt-1">Event Type is required.</div>
                    </div>
                    <div class="mb-4">
                        <label for="eventColor" class="block text-sm font-medium text-gray-700 mb-1">Event Color</label>
                        <input type="color" class="w-full h-10 border border-gray-300 rounded-lg px-1 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none cursor-pointer" id="eventColor" name="color" value="#007bff">
                    </div>
                    <div class="mb-4">
                        <label for="eventDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="eventDescription" name="description" rows="3"></textarea>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="form-step hidden" data-step="2">
                    <h6 class="text-sm font-semibold text-indigo-600 mb-4">Step 2: Scheduling</h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="eventStartTimeInput" class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="eventStartTimeInput" name="start_time" required>
                        </div>
                        <div>
                            <label for="eventEndTimeInput" class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                            <input type="datetime-local" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="eventEndTimeInput" name="end_time">
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="eventAllDay" name="all_day">
                        <label class="text-sm text-gray-700" for="eventAllDay">All Day Event</label>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="form-step hidden" data-step="3">
                    <h6 class="text-sm font-semibold text-indigo-600 mb-4">Step 3: Related Info & Notifications</h6>
                    <div id="dynamicFieldsContainer"></div>
                    <div class="mb-4">
                        <label for="notifyUsers" class="block text-sm font-medium text-gray-700 mb-1">Notify Users</label>
                        <select id="notifyUsers" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none select2-full" name="notify_users[]" multiple>
                            <?php foreach (($users_for_notification ?? []) as $user): ?>
                                <option value="<?= esc($user['id']) ?>">
                                    <?= esc($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['role'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="priority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2" id="prevStepBtn" disabled>
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2" id="nextStepBtn">
                        Next <i class="bi bi-arrow-right"></i>
                    </button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2 hidden" id="submitEventBtn">
                        <span class="hidden" role="status"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></span>
                        <span class="button-text"><i class="bi bi-check-circle me-2"></i> Save Event</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var BASE_URL = '<?= base_url() ?>';
var loggedInUserId = <?= session()->get('user_id') ?? 'null' ?>;

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    var backdrop = document.getElementById(id + '-backdrop');
    if (backdrop) backdrop.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    var backdrop = document.getElementById(id + '-backdrop');
    if (backdrop) backdrop.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

window.closeModal = closeModal;

document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;

    const steps = document.querySelectorAll('.form-step');
    const nextBtn = document.getElementById('nextStepBtn');
    const prevBtn = document.getElementById('prevStepBtn');
    const submitBtn = document.getElementById('submitEventBtn');
    const form = document.getElementById('addEventForm');
    const typeSelect = document.getElementById('eventType');
    const dynamicContainer = document.getElementById('dynamicFieldsContainer');
    const progressDots = document.querySelectorAll('.progress-dots .dot');

    function updateStepDisplay() {
        steps.forEach(step => step.classList.add('hidden'));
        document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('hidden');
        prevBtn.disabled = currentStep === 1;
        nextBtn.classList.toggle('hidden', currentStep === steps.length);
        submitBtn.classList.toggle('hidden', currentStep !== steps.length);

        progressDots.forEach(dot => dot.classList.remove('active'));
        document.querySelector(`.progress-dots .dot[data-step="${currentStep}"]`)?.classList.add('active');
    }

    function generateDynamicFields(type) {
        let html = '';

        if (type === 'job_pickup') {
            html += `<div class="mb-4">
                <label for="relatedJob" class="block text-sm font-medium text-gray-700 mb-1">Select Job</label>
                <select id="relatedJob" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 select2-related" name="related_id"></select>
                <input type="hidden" name="related_table" value="jobs">
            </div>`;
        } else if (type === 'appointment' || type === 'meeting') {
            html += `<div class="mb-4">
                <label for="relatedCustomer" class="block text-sm font-medium text-gray-700 mb-1">Select Customer</label>
                <select id="relatedCustomer" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 select2-related" name="related_id"></select>
                <input type="hidden" name="related_table" value="customers">
            </div>`;
        } else if (type === 'leave') {
            html += `<div class="mb-4">
                <label for="leaveType" class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                <input type="text" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" name="leave_type">
            </div>`;
            html += `<input type="hidden" name="related_table" value="users">
                <input type="hidden" name="related_id" value="${loggedInUserId}">`;
        }

        dynamicContainer.innerHTML = html;

        $('.select2-related').select2({
            dropdownParent: $('#addEventModal'),
            ajax: {
                url: type === 'job_pickup' ? `${BASE_URL}admin/calendar/getRelatedJobs` : `${BASE_URL}admin/calendar/getCustomersList`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.label
                        }))
                    };
                }
            }
        });
    }

    typeSelect.addEventListener('change', function() {
        generateDynamicFields(this.value);
    });

    nextBtn.addEventListener('click', function() {
        if (currentStep < steps.length) {
            currentStep++;
            updateStepDisplay();
        }
    });

    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateStepDisplay();
        }
    });

    $(".select2-basic, .select2-full").select2({
        dropdownParent: $('#addEventModal')
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch(`${BASE_URL}admin/calendar/addEvent`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success || result.message?.includes("success")) {
                Swal.fire('Success', 'Event added successfully!', 'success');
                closeModal('addEventModal');
                form.reset();
                currentStep = 1;
                updateStepDisplay();
            } else {
                Swal.fire('Error', result.message || 'Something went wrong.', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', error.message || 'Server error occurred.', 'error');
        });
    });

    updateStepDisplay();
});
</script>
