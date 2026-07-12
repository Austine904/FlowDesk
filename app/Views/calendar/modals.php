<!-- Re-using actionModal for Event Details -->
<div id="actionModal" class="modal fade" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-4">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title" id="actionModalLabel">
          <i class="bi bi-info-circle-fill me-2"></i> Event Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">

        <!-- Section: Event Info -->
        <div class="section-box mb-3">
          <h6 class="section-title">🗓️ Event Information</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <div><strong>Event Type:</strong> <span id="event_type" class="text-muted ms-1"></span></div>
              <div><strong>Title:</strong> <span id="event_title" class="text-muted ms-1"></span></div>
            </div>
            <div class="col-md-6">
              <div><strong>Start Time:</strong> <span id="event_start" class="text-muted ms-1"></span></div>
              <div><strong>End Time:</strong> <span id="event_end" class="text-muted ms-1"></span></div>
              <div><strong>Status:</strong> <span id="event_status" class="text-muted ms-1"></span></div>
            </div>
          </div>
        </div>

        <!-- Section: Job Info -->
        <div class="section-box mb-3">
          <h6 class="section-title">🔧 Job Information</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <div><strong>Job No:</strong> <span id="event_job_no" class="text-muted ms-1"></span></div>
              <div><strong>Vehicle:</strong> <span id="event_vehicle" class="text-muted ms-1"></span></div>
            </div>
            <div class="col-md-6">
              <div><strong>Mechanic:</strong> <span id="event_mechanic" class="text-muted ms-1"></span></div>
            </div>
          </div>
        </div>

        <!-- Section: Customer -->
        <div class="section-box mb-3">
          <h6 class="section-title">👤 Customer</h6>
          <div><strong>Name:</strong> <span id="event_customer" class="text-muted ms-1"></span></div>
        </div>

        <!-- Section: Description -->
        <div class="section-box">
          <h6 class="section-title">📝 Description</h6>
          <div><span id="event_description" class="text-muted"></span></div>
        </div>

      </div>

      <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Close
        </button>
      </div>
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
    background-color: var(--primary-color, #007bff);
  }
  #addEventModal .modal-body {
    min-height: 500px;
    /* Ensures uniform height across steps */
  }
  
</style>



<div id="addEventModal" class="modal fade" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEventModalLabel">Add New Calendar Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Progress Dots -->
        <div class="progress-dots text-center mb-4">
          <span class="dot active" data-step="1"></span>
          <span class="dot" data-step="2"></span>
          <span class="dot" data-step="3"></span>
        </div>

        <form id="addEventForm" novalidate>
          <!-- Step 1 -->
          <div class="form-step" data-step="1">
            <h6 class="mb-3 text-primary">Step 1: Event Details</h6>
            <div class="mb-3">
              <label for="eventTitle" class="form-label">Event Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="eventTitle" name="title" required>
              <div class="invalid-feedback">Event Title is required.</div>
            </div>
            <div class="mb-3">
              <label for="eventType" class="form-label">Event Type <span class="text-danger">*</span></label>
              <select class="form-select select2-basic" id="eventType" name="event_type" required>
                <option value="">-- Select Type --</option>
                <option value="general">General</option>
                <option value="job_pickup">Job Pickup</option>
                <option value="appointment">Appointment</option>
                <option value="meeting">Meeting</option>
                <option value="holiday">Holiday</option>
                <option value="leave">Leave</option>
              </select>
              <div class="invalid-feedback">Event Type is required.</div>
            </div>
            <div class="mb-3">
              <label for="eventColor" class="form-label">Event Color</label>
              <input type="color" class="form-control form-control-color" id="eventColor" name="color" value="#007bff">
            </div>
            <div class="mb-3">
              <label for="eventDescription" class="form-label">Description</label>
              <textarea class="form-control" id="eventDescription" name="description" rows="3"></textarea>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="form-step d-none" data-step="2">
            <h6 class="mb-3 text-primary">Step 2: Scheduling</h6>
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="eventStartTimeInput" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="eventStartTimeInput" name="start_time" required>
              </div>
              <div class="col-md-6">
                <label for="eventEndTimeInput" class="form-label">End Date & Time</label>
                <input type="datetime-local" class="form-control" id="eventEndTimeInput" name="end_time">
              </div>
            </div>
            <div class="form-check mb-3">
              <input type="checkbox" class="form-check-input" id="eventAllDay" name="all_day">
              <label class="form-check-label" for="eventAllDay">All Day Event</label>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="form-step d-none" data-step="3">
            <h6 class="mb-3 text-primary">Step 3: Related Info & Notifications</h6>
            <div id="dynamicFieldsContainer"></div>
            <div class="mb-3">
              <label for="notifyUsers" class="form-label">Notify Users</label>
              <select id="notifyUsers" class="form-select select2-full" name="notify_users[]" multiple>
                <?php foreach (($users_for_notification ?? []) as $user): ?>
                  <option value="<?= esc($user['id']) ?>">
                    <?= esc($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['role'] . ')') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="priority" class="form-label">Priority</label>
              <select class="form-select" id="priority" name="priority">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
              </select>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" id="prevStepBtn" disabled>
              <i class="bi bi-arrow-left me-2"></i> Back
            </button>
            <button type="button" class="btn btn-primary" id="nextStepBtn">
              Next <i class="bi bi-arrow-right ms-2"></i>
            </button>
            <button type="submit" class="btn btn-success d-none" id="submitEventBtn">
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              <span class="button-text"><i class="bi bi-check-circle me-2"></i> Save Event</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!--Add Event Modal Script-->


<script>
  var BASE_URL = '<?= base_url() ?>';
  // ✅ Add Event Modal JS - Step 3 Dynamic Logic (A + C route)

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
      steps.forEach(step => step.classList.add('d-none'));
      document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('d-none');
      prevBtn.disabled = currentStep === 1;
      nextBtn.classList.toggle('d-none', currentStep === steps.length);
      submitBtn.classList.toggle('d-none', currentStep !== steps.length);

      progressDots.forEach(dot => dot.classList.remove('active'));
      document.querySelector(`.progress-dots .dot[data-step="${currentStep}"]`)?.classList.add('active');
    }

    function generateDynamicFields(type) {
      let html = '';

      if (type === 'job_pickup') {
        html += `<div class="mb-3">
        <label for="relatedJob" class="form-label">Select Job</label>
        <select id="relatedJob" class="form-select select2-related" name="related_id"></select>
        <input type="hidden" name="related_table" value="jobs">
      </div>`;
      } else if (type === 'appointment' || type === 'meeting') {
        html += `<div class="mb-3">
        <label for="relatedCustomer" class="form-label">Select Customer</label>
        <select id="relatedCustomer" class="form-select select2-related" name="related_id"></select>
        <input type="hidden" name="related_table" value="customers">
      </div>`;
      } else if (type === 'leave') {
        html += `<div class="mb-3">
        <label for="leaveType" class="form-label">Leave Type</label>
        <input type="text" class="form-control" name="leave_type">
      </div>`;
        html += `<input type="hidden" name="related_table" value="users">
        <input type="hidden" name="related_id" value="${loggedInUserId}">`;
      }

      dynamicContainer.innerHTML = html;

      // Load select2 AJAX only for related select
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
            bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
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