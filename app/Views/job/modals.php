<style>
    /* Job Details Modal Specifics */
    #jobDetailsModal .modal-dialog {
        max-width: 900px;
    }

    #jobDetailsModal .modal-header {
        background: linear-gradient(90deg, var(--primary-color), var(--primary-hover-color)) !important;
        color: white !important;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    #jobDetailsModal .modal-header .modal-title {
        color: white !important;
    }

    #jobDetailsModal .modal-header .btn-close {
        filter: invert(1);
    }

    #jobDetailsModal .info-item {
        margin-bottom: 0.75rem;
        text-align: left;
    }

    #jobDetailsModal .info-label {
        font-weight: 600;
        color: var(--text-dark);
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    #jobDetailsModal .info-value {
        color: #555;
        font-size: 1rem;
    }

    /* Tab Styling within Job Details Modal */
    #jobDetailsModal .nav-tabs {
        border-bottom: none;
        margin-bottom: 1.5rem;
    }

    #jobDetailsModal .nav-tabs .nav-item .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        background-color: transparent;
        color: var(--text-dark);
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        transition: all 0.2s ease;
        position: relative;
    }

    #jobDetailsModal .nav-tabs .nav-item .nav-link:hover {
        color: var(--primary-color);
        background-color: rgba(0, 123, 255, 0.05);
    }

    #jobDetailsModal .nav-tabs .nav-item .nav-link.active {
        color: var(--primary-color);
        background-color: var(--card-bg);
        font-weight: 600;
    }

    #jobDetailsModal .nav-tabs .nav-item .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--primary-color);
        border-radius: 2px 2px 0 0;
    }

    #jobDetailsModal .tab-content {
        background-color: var(--card-bg);
        border-radius: 0 0 15px 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-top: none;
    }
</style>


<!-- Add Job Modal -->
<div id="addJobModal" class="modal fade" tabindex="-1" aria-labelledby="addJobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addJobModalLabel">Add New Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Please fill in the details below to create a new job card.</p>
                <form id="jobIntakeForm" action="<?= base_url('job_intake/create_job_card') ?>" enctype="multipart/form-data" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3 position-relative">
                        <label for="search_input" class="form-label">Search Customer/Vehicle</label>
                        <input type="text" class="form-control" id="search_input" placeholder="License Plate, VIN, or Phone Number">
                        <div class="error-message" id="error_search_input"></div>
                        <div id="search_results" class="search-results-dropdown hidden"></div>
                    </div>

                    <div class="my-4 border-top pt-4">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="text-primary mb-0">Customer Details</h5>
                            <span id="customer_status" class="badge bg-secondary ms-2">New</span>
                        </div>
                        <input type="hidden" id="customer_id" name="customer_id" value="new">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="new_customer_first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="new_customer_first_name" name="new_customer_first_name" required>
                                <div class="error-message" id="error_new_customer_first_name"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="new_customer_last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="new_customer_last_name" name="new_customer_last_name" required>
                                <div class="error-message" id="error_new_customer_last_name"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="new_customer_phone_number" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="new_customer_phone_number" name="new_customer_phone_number" required>
                                <div class="error-message" id="error_new_customer_phone_number"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="new_customer_email" class="form-label">Email Address (Optional)</label>
                                <input type="email" class="form-control" id="new_customer_email" name="new_customer_email">
                                <div class="error-message" id="error_new_customer_email"></div>
                            </div>
                            <div class="col-12">
                                <label for="new_customer_address" class="form-label">Address (Optional)</label>
                                <textarea class="form-control" id="new_customer_address" name="new_customer_address" rows="2"></textarea>
                                <div class="error-message" id="error_new_customer_address"></div>
                            </div>
                        </div>
                    </div>

                    <div class="my-4 border-top pt-4">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="text-primary mb-0">Vehicle Details</h5>
                            <span id="vehicle_status" class="badge bg-secondary ms-2">New</span>
                        </div>
                        <input type="hidden" id="vehicle_id" name="vehicle_id" value="new">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="new_vehicle_license_plate" class="form-label">Reg No.</label>
                                <input type="text" class="form-control" id="new_vehicle_license_plate" name="new_vehicle_license_plate" required>
                                <div class="error-message" id="error_new_vehicle_license_plate"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="new_vehicle_vin" class="form-label">VIN (Vehicle Identification Number)</label>
                                <input type="text" class="form-control" id="new_vehicle_vin" name="new_vehicle_vin" maxlength="17" required>
                                <div class="error-message" id="error_new_vehicle_vin"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="new_vehicle_make" class="form-label">Make</label>
                                <select class="form-select" id="new_vehicle_make" name="new_vehicle_make" required>
                                    <option value="">Select Make</option>
                                    <option value="Toyota">Toyota</option>
                                    <option value="Nissan">Nissan</option>
                                    <option value="Honda">Honda</option>
                                    <option value="Mazda">Mazda</option>
                                    <option value="Subaru">Subaru</option>
                                    <option value="Mercedes-Benz">Mercedes-Benz</option>
                                    <option value="BMW">BMW</option>
                                    <option value="Audi">Audi</option>
                                    <option value="Ford">Ford</option>
                                    <option value="Volkswagen">Volkswagen</option>
                                </select>
                                <div class="error-message" id="error_new_vehicle_make"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="new_vehicle_model" class="form-label">Model</label>
                                <input type="text" class="form-control" id="new_vehicle_model" name="new_vehicle_model" required>
                                <div class="error-message" id="error_new_vehicle_model"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="new_vehicle_year" class="form-label">Year of Manufacture</label>
                                <input type="number" class="form-control" id="new_vehicle_year" name="new_vehicle_year" min="1900" max="<?= date('Y') + 1 ?>" required>
                                <div class="error-message" id="error_new_vehicle_year"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="new_vehicle_engine_number" class="form-label">Engine Number</label>
                                <input type="text" class="form-control" id="new_vehicle_engine_number" name="new_vehicle_engine_number" required>
                                <div class="error-message" id="error_new_vehicle_engine_number"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="new_vehicle_chassis_number" class="form-label">Chassis Number</label>
                                <input type="text" class="form-control" id="new_vehicle_chassis_number" name="new_vehicle_chassis_number" required>
                                <div class="error-message" id="error_new_vehicle_chassis_number"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="new_vehicle_transmission" class="form-label">Transmission</label>
                                <select class="form-select" id="new_vehicle_transmission" name="new_vehicle_transmission" required>
                                    <option value="">Select Transmission</option>
                                    <option value="Manual">Manual</option>
                                    <option value="Automatic">Automatic</option>
                                    <option value="Semi-Automatic">Semi-Automatic</option>
                                </select>
                                <div class="error-message" id="error_new_vehicle_transmission"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="new_vehicle_fuel_type" class="form-label">Fuel Type</label>
                                <select class="form-select" id="new_vehicle_fuel_type" name="new_vehicle_fuel_type" required>
                                    <option value="">Select Fuel Type</option>
                                    <option value="Petrol">Petrol</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Electric">Electric</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                                <div class="error-message" id="error_new_vehicle_fuel_type"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="new_vehicle_color" class="form-label">Color (Optional)</label>
                                <input type="text" class="form-control" id="new_vehicle_color" name="new_vehicle_color">
                                <div class="error-message" id="error_new_vehicle_color"></div>
                            </div>
                        </div>
                    </div>

                    <div class="my-4 border-top pt-4">
                        <h5 class="mb-3 text-primary">Job Details</h5>
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label for="reported_problem" class="form-label">Customer Reported Problem</label>
                                <textarea class="form-control" id="reported_problem" name="reported_problem" rows="3" required></textarea>
                                <div class="error-message" id="error_reported_problem"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="mileage_in" class="form-label">Mileage In</label>
                                <input type="number" class="form-control" id="mileage_in" name="mileage_in" required min="0">
                                <div class="error-message" id="error_mileage_in"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="fuel_level" class="form-label">Fuel Level</label>
                                <select class="form-select" id="fuel_level" name="fuel_level" required>
                                    <option value="">Select Fuel Level</option>
                                    <option value="Empty">Empty</option>
                                    <option value="1/4">1/4</option>
                                    <option value="1/2">1/2</option>
                                    <option value="3/4">3/4</option>
                                    <option value="Full">Full</option>
                                </select>
                                <div class="error-message" id="error_fuel_level"></div>
                            </div>
                            <div class="col-12">
                                <label for="initial_damage_notes" class="form-label">Initial Visible Damage Notes (Optional)</label>
                                <textarea class="form-control" id="initial_damage_notes" name="initial_damage_notes" rows="2"></textarea>
                                <div class="error-message" id="error_initial_damage_notes"></div>
                            </div>
                            <div class="col-12">
                                <label for="job_card_photos" class="form-label">Upload Job Card Photos (Optional)</label>
                                <input type="file" class="form-control" id="job_card_photos" name="job_card_photos[]" multiple accept="image/*">
                                <div class="photo-preview-container" id="photo_preview_container"></div>
                                <div class="error-message" id="error_job_card_photos"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="assigned_service_advisor_id" class="form-label">Service Advisor</label>
                                <select class="form-select" id="assigned_service_advisor_id" name="assigned_service_advisor_id" required>
                                    <option value="">Select Advisor</option>
                                    <?php if (isset($service_advisors)): ?>
                                        <?php foreach ($service_advisors as $advisor): ?>
                                            <option value="<?= esc($advisor['id'] ?? '') ?>">
                                                <?= esc(($advisor['first_name'] ?? '') . ' ' . ($advisor['last_name'] ?? '') . ' (' . ($advisor['company_id'] ?? '') . ')') ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="error-message" id="error_assigned_service_advisor_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="assigned_mechanic_id" class="form-label">Assign Mechanic <small class="text-muted">(optional)</small></label>
                                <select class="form-select" id="assigned_mechanic_id" name="assigned_mechanic_id">
                                    <option value="">-- Select --</option>
                                    <?php if (isset($mechanics)): ?>
                                        <?php foreach ($mechanics as $mech): ?>
                                            <option value="<?= esc($mech['id'] ?? '') ?>">
                                                <?= esc(($mech['first_name'] ?? '') . ' ' . ($mech['last_name'] ?? '') . ' (' . ($mech['company_id'] ?? '') . ')') ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="error-message" id="error_assigned_mechanic_id"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Create Job Card
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Job details modal -->
<div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-labelledby="jobDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobDetailsModalLabel">
                    <i class="bi bi-briefcase me-2"></i> Job Details - <span id="detail_job_no"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card p-3 mb-3">
                            <h6 class="mb-3 text-primary">Job Card Summary</h6>
                            <div class="info-item"><span class="info-label">Status:</span> <span class="info-value" id="detail_job_status"></span></div>
                            <div class="info-item"><span class="info-label">Reported Problem:</span> <span class="info-value" id="detail_reported_problem"></span></div>
                            <div class="info-item"><span class="info-label">Initial Damage Notes:</span> <span class="info-value" id="detail_initial_damage_notes"></span></div>
                            <div class="info-item"><span class="info-label">Mileage In:</span> <span class="info-value" id="detail_mileage_in"></span></div>
                            <div class="info-item"><span class="info-label">Fuel Level:</span> <span class="info-value" id="detail_fuel_level"></span></div>
                            <div class="info-item"><span class="info-label">Date In:</span> <span class="info-value" id="detail_date_in"></span></div>
                            <div class="info-item"><span class="info-label">Time In:</span> <span class="info-value" id="detail_time_in"></span></div>
                            <div class="info-item"><span class="info-label">Estimated Labor Hours:</span> <span class="info-value" id="detail_estimated_labor_hours"></span></div>
                            <div class="info-item"><span class="info-label">Quote Status:</span> <span class="info-value" id="detail_quote_status"></span></div>
                            <div class="info-item"><span class="info-label">Quote Amount:</span> <span class="info-value" id="detail_quote_amount"></span></div>
                            <div class="info-item"><span class="info-label">Assigned Service Advisor:</span> <span class="info-value" id="detail_assigned_service_advisor"></span></div>
                            <div class="info-item">
                                <span class="info-label">Assigned Mechanic:</span>
                                <span class="info-value" id="detail_assigned_mechanic"></span>
                                <div class="mt-2" id="dispatch_section">
                                    <div class="input-group input-group-sm">
                                        <select class="form-select" id="dispatch_mechanic_id">
                                            <option value="">-- Select Mechanic --</option>
                                        </select>
                                        <button class="btn btn-primary btn-sm" type="button" id="btnAssignMechanic">Assign</button>
                                    </div>
                                    <div id="dispatch_message" class="mt-1"></div>
                                </div>
                            </div>
                            <div class="info-item"><span class="info-label">Diagnosis:</span> <span class="info-value" id="detail_diagnosis"></span></div>
                            <div class="info-item"><span class="info-label">Job Summary:</span> <span class="info-value" id="detail_job_summary"></span></div>
                        </div>
                    </div>

                    <!-- Status Actions -->
                    <div class="card p-3 mb-3" id="statusActionsCard">
                        <h6 class="mb-3 text-primary">Status</h6>
                        <div class="info-item">
                            <span class="info-label">Current Status:</span>
                            <span class="info-value"><span class="badge fs-6" id="detail_status_badge"></span></span>
                        </div>
                        <div class="mt-2" id="transitionButtonsContainer"></div>
                        <div class="mt-2" id="statusTransitionMessage"></div>
                    </div>

                    <div class="col-md-7">
                        <ul class="nav nav-tabs mb-3" id="jobDetailsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="customer-vehicle-tab" data-bs-toggle="tab" data-bs-target="#customer-vehicle" type="button" role="tab" aria-controls="customer-vehicle" aria-selected="true">Customer & Vehicle</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="parts-tasks-tab" data-bs-toggle="tab" data-bs-target="#parts-tasks" type="button" role="tab" aria-controls="parts-tasks" aria-selected="false">Parts & Tasks</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="photos-tab" data-bs-toggle="tab" data-bs-target="#photos" type="button" role="tab" aria-controls="photos" aria-selected="false">Photos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="status-history-tab" data-bs-toggle="tab" data-bs-target="#status-history" type="button" role="tab" aria-controls="status-history" aria-selected="false">Status History</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice-tab-panel" type="button" role="tab" aria-controls="invoice-tab-panel" aria-selected="false">Invoice</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="jobDetailsTabContent">
                            <div class="tab-pane fade show active" id="customer-vehicle" role="tabpanel" aria-labelledby="customer-vehicle-tab">
                                <h6 class="mb-3 text-secondary">Customer Info</h6>
                                <div class="info-item"><span class="info-label">Name:</span> <span class="info-value" id="detail_customer_name"></span></div>
                                <div class="info-item"><span class="info-label">Phone:</span> <span class="info-value" id="detail_customer_phone"></span></div>
                                <div class="info-item"><span class="info-label">Email:</span> <span class="info-value" id="detail_customer_email"></span></div>
                                <div class="info-item"><span class="info-label">Address:</span> <span class="info-value" id="detail_customer_address"></span></div>

                                <h6 class="mb-3 mt-4 text-secondary">Vehicle Info</h6>
                                <div class="info-item"><span class="info-label">Registration No.:</span> <span class="info-value" id="detail_vehicle_reg_no"></span></div>
                                <div class="info-item"><span class="info-label">Make & Model:</span> <span class="info-value" id="detail_vehicle_make_model"></span></div>
                                <div class="info-item"><span class="info-label">Year:</span> <span class="info-value" id="detail_vehicle_year"></span></div>
                                <div class="info-item"><span class="info-label">VIN:</span> <span class="info-value" id="detail_vehicle_vin"></span></div>
                                <div class="info-item"><span class="info-label">Engine No.:</span> <span class="info-value" id="detail_vehicle_engine_no"></span></div>
                                <div class="info-item"><span class="info-label">Chassis No.:</span> <span class="info-value" id="detail_vehicle_chassis_no"></span></div>
                                <div class="info-item"><span class="info-label">Fuel Type:</span> <span class="info-value" id="detail_vehicle_fuel_type"></span></div>
                                <div class="info-item"><span class="info-label">Transmission:</span> <span class="info-value" id="detail_vehicle_transmission"></span></div>
                                <div class="info-item"><span class="info-label">Color:</span> <span class="info-value" id="detail_vehicle_color"></span></div>
                            </div>

                            <div class="tab-pane fade" id="parts-tasks" role="tabpanel" aria-labelledby="parts-tasks-tab">
                                <h6 class="mb-3 text-secondary">Parts Required</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Part Name</th>
                                                <th>Qty.</th>
                                                <th>Est. Unit Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail_parts_list">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No parts recorded.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <h6 class="mb-3 mt-4 text-secondary">Labor Tasks</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Task Name</th>
                                                <th>Est. Hours</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail_tasks_list">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No tasks recorded.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="photos" role="tabpanel" aria-labelledby="photos-tab">
                                <h6 class="mb-3 text-secondary">Job Card Photos</h6>
                                <div id="detail_photos_gallery" class="row g-2">
                                    <div class="col-12 text-center text-muted">No photos available.</div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="status-history" role="tabpanel" aria-labelledby="status-history-tab">
                                <h6 class="mb-3 text-secondary">Status Transition History</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Changed By</th>
                                                <th>Date/Time</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail_status_history">
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Loading history...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="invoice-tab-panel" role="tabpanel" aria-labelledby="invoice-tab">
                                <h6 class="mb-3 text-secondary">Invoice</h6>
                                <div id="detail_invoice_content">
                                    <p class="text-muted text-center">Loading invoice data...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    // Function to open a modal with dynamic content
    function openModal(url, title = 'Form') {
        const modalElement = document.getElementById('actionModal');
        const modal = new bootstrap.Modal(modalElement);
        const modalTitle = modalElement.querySelector('.modal-title');
        const modalContent = document.getElementById('modalContent');

        modalContent.innerHTML = `
            <div class="d-flex justify-content-center align-items-center" style="min-height: 100px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        modalTitle.textContent = title;

        modal.show();

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.text();
            })
            .then(data => {
                modalContent.innerHTML = data;
            })
            .catch(error => {
                modalTitle.textContent = 'Error';
                modalContent.innerHTML = `<div class="alert alert-danger" role="alert">Error loading content: ${error.message}. Please try again.</div>`;
                console.error('Error loading modal content:', error);
            });
    }
    window.openModal = openModal;


    $(document).ready(function() {
        // --- Element References ---
        const addJobModal = $('#addJobModal');
        const jobDetailsModalElement = document.getElementById('jobDetailsModal');
        const jobDetailsModal = new bootstrap.Modal(jobDetailsModalElement);

        const jobIntakeForm = $('#jobIntakeForm');
        const searchInput = $('#search_input');
        const searchResults = $('#search_results');
        const customerStatusBadge = $('#customer_status');
        const vehicleStatusBadge = $('#vehicle_status');
        const photoPreviewContainer = $('#photo_preview_container');
        const photoUploadInput = $('#job_card_photos');

        // Customer Fields
        const customerId = $('#customer_id');
        const newCustomerFirstName = $('#new_customer_first_name');
        const newCustomerLastName = $('#new_customer_last_name');
        const newCustomerPhone = $('#new_customer_phone_number');
        const newCustomerEmail = $('#new_customer_email');
        const newCustomerAddress = $('#new_customer_address');
        const allCustomerInputs = [newCustomerFirstName, newCustomerLastName, newCustomerPhone, newCustomerEmail, newCustomerAddress];

        // Vehicle Fields
        const vehicleId = $('#vehicle_id');
        const newVehicleLicensePlate = $('#new_vehicle_license_plate');
        const newVehicleVIN = $('#new_vehicle_vin');
        const newVehicleMake = $('#new_vehicle_make');
        const newVehicleModel = $('#new_vehicle_model');
        const newVehicleYear = $('#new_vehicle_year');
        const newVehicleEngineNumber = $('#new_vehicle_engine_number');
        const newVehicleChassisNumber = $('#new_vehicle_chassis_number');
        const newVehicleTransmission = $('#new_vehicle_transmission');
        const newVehicleFuelType = $('#new_vehicle_fuel_type');
        const newVehicleColor = $('#new_vehicle_color');
        const allVehicleInputs = [newVehicleLicensePlate, newVehicleVIN, newVehicleMake, newVehicleModel, newVehicleYear, newVehicleEngineNumber, newVehicleChassisNumber, newVehicleTransmission, newVehicleFuelType, newVehicleColor];

        // Job Details Fields (for auto-population from existing vehicle)
        const reportedProblem = $('#reported_problem');
        const mileageIn = $('#mileage_in');
        const fuelLevel = $('#fuel_level');


        $('#jobsTable tbody').on('click', '.view-job', async function() {
            const jobId = $(this).data('id'); // Get job ID from data attribute

            // Clear previous data and show loading spinners/placeholders
            $('#detail_job_no').text('Loading...');
            // ... clear all other detail spans/elements ...
            jobDetailsModalElement.querySelectorAll('.info-value').forEach(el => el.textContent = '');
            $('#detail_parts_list').html('<tr><td colspan="3" class="text-center text-muted">Loading parts...</td></tr>');
            $('#detail_tasks_list').html('<tr><td colspan="3" class="text-center text-muted">Loading tasks...</td></tr>');
            $('#detail_photos_gallery').html('<div class="col-12 text-center text-muted">Loading photos...</div>');


            jobDetailsModal.show();

            try {
                const response = await fetch(`<?= base_url('admin/jobs/details/') ?>${jobId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch job details (Status: ${response.status})`);
                }

                const data = await response.json();

                // Populate Job Card Summary
                $('#detail_job_no').text(data.job_no || 'N/A');
                $('#detail_job_status').text(data.job_status || 'N/A');
                $('#detail_reported_problem').text(data.reported_problem || 'N/A');
                $('#detail_initial_damage_notes').text(data.initial_damage_notes || 'N/A');
                $('#detail_mileage_in').text(data.mileage_in ? `${data.mileage_in} km` : 'N/A');
                $('#detail_fuel_level').text(data.fuel_level || 'N/A');
                $('#detail_date_in').text(data.date_in || 'N/A');
                $('#detail_time_in').text(data.time_in || 'N/A');
                $('#detail_estimated_labor_hours').text(data.estimated_labor_hours ? `${data.estimated_labor_hours} hrs` : 'N/A');
                $('#detail_quote_status').text(data.quote_status || 'N/A');
                $('#detail_quote_amount').text(data.quote_amount ? `Ksh ${parseFloat(data.quote_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}` : 'N/A');
                $('#detail_assigned_service_advisor').text(data.assigned_service_advisor || 'N/A');
                $('#detail_assigned_mechanic').text(data.mechanic_name || 'Unassigned');
                $('#detail_diagnosis').text(data.diagnosis || 'N/A');
                $('#detail_job_summary').text(data.job_summary || 'N/A');

                // Populate dispatch mechanic dropdown
                const dispatchSelect = $('#dispatch_mechanic_id');
                dispatchSelect.empty().append('<option value="">-- Select Mechanic --</option>');
                if (data.mechanics && data.mechanics.length > 0) {
                    data.mechanics.forEach(function(mech) {
                        const selected = (mech.id == data.assigned_mechanic_id) ? ' selected' : '';
                        dispatchSelect.append(`<option value="${mech.id}"${selected}>${mech.first_name} ${mech.last_name}</option>`);
                    });
                }
                window._currentJobId = data.id;

                // Populate Customer & Vehicle Tab
                $('#detail_customer_name').text(data.customer.name || 'N/A');
                $('#detail_customer_phone').text(data.customer.phone || 'N/A');
                $('#detail_customer_email').text(data.customer.email || 'N/A');
                $('#detail_customer_address').text(data.customer.address || 'N/A');

                $('#detail_vehicle_reg_no').text(data.vehicle.registration_number || 'N/A');
                $('#detail_vehicle_make_model').text(`${data.vehicle.make || ''} ${data.vehicle.model || ''}`.trim() || 'N/A');
                $('#detail_vehicle_year').text(data.vehicle.year_of_manufacture || 'N/A');
                $('#detail_vehicle_vin').text(data.vehicle.vin || 'N/A');
                $('#detail_vehicle_engine_no').text(data.vehicle.engine_number || 'N/A');
                $('#detail_vehicle_chassis_no').text(data.vehicle.chassis_number || 'N/A');
                $('#detail_vehicle_fuel_type').text(data.vehicle.fuel_type || 'N/A');
                $('#detail_vehicle_transmission').text(data.vehicle.transmission || 'N/A');
                $('#detail_vehicle_color').text(data.vehicle.color || 'N/A');

                // Populate Parts & Tasks Tab
                const partsList = $('#detail_parts_list');
                partsList.empty();
                if (data.parts && data.parts.length > 0) {
                    data.parts.forEach(part => {
                        partsList.append(`
                            <tr>
                                <td>${part.name || 'N/A'} (${part.part_number || 'N/A'})</td>
                                <td>${part.quantity_required || '0'}</td>
                                <td>Ksh ${parseFloat(part.unit_price_at_estimate || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            </tr>
                        `);
                    });
                } else {
                    partsList.append('<tr><td colspan="3" class="text-center text-muted">No parts recorded.</td></tr>');
                }

                const tasksList = $('#detail_tasks_list');
                tasksList.empty();
                if (data.tasks && data.tasks.length > 0) {
                    data.tasks.forEach(task => {
                        tasksList.append(`
                            <tr>
                                <td>${task.task_name || 'N/A'}</td>
                                <td>${task.estimated_hours || '0'}</td>
                                <td>${task.notes || 'N/A'}</td>
                            </tr>
                        `);
                    });
                } else {
                    tasksList.append('<tr><td colspan="3" class="text-center text-muted">No tasks recorded.</td></tr>');
                }

                // Populate Photos Tab
                const photosGallery = $('#detail_photos_gallery');
                photosGallery.empty();
                if (data.photos && data.photos.length > 0) {
                    data.photos.forEach(photo => {
                        photosGallery.append(`
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a href="<?= base_url() ?>/${photo.file_path}" target="_blank">
                                    <img src="<?= base_url() ?>/${photo.file_path}" alt="${photo.file_name}" class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">
                                </a>
                            </div>
                        `);
                    });
                } else {
                    photosGallery.append('<div class="col-12 text-center text-muted">No photos available.</div>');
                }


                renderStatusSection(data.job_status, data.valid_transitions, data.id);

                // Populate Invoice Tab
                const invoiceContent = $('#detail_invoice_content');
                invoiceContent.empty();
                if (data.invoice) {
                    const inv = data.invoice;
                    const badgeMap = {
                        'Draft': 'bg-secondary', 'Sent': 'bg-primary',
                        'Partially Paid': 'bg-warning text-dark', 'Paid': 'bg-success',
                        'Overdue': 'bg-danger', 'Cancelled': 'bg-dark'
                    };
                    const badgeClass = badgeMap[inv.status] || 'bg-secondary';
                    invoiceContent.html(`
                        <div class="info-item"><span class="info-label">Invoice No:</span> <span class="info-value">${inv.invoice_no}</span></div>
                        <div class="info-item"><span class="info-label">Grand Total:</span> <span class="info-value">KSh ${parseFloat(inv.grand_total).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                        <div class="info-item"><span class="info-label">Balance Due:</span> <span class="info-value">KSh ${parseFloat(inv.balance_due).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                        <div class="info-item"><span class="info-label">Status:</span> <span class="info-value"><span class="badge ${badgeClass}">${inv.status}</span></span></div>
                        <div class="mt-3">
                            <a href="<?= base_url('admin/invoices/view/') ?>${inv.invoice_id}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View Invoice</a>
                        </div>
                    `);
                } else {
                    const jobId = data.id;
                    invoiceContent.html(`
                        <p class="text-muted">No invoice generated yet for this job.</p>
                        <a href="<?= base_url('admin/invoices/generate/') ?>${jobId}" class="btn btn-sm btn-primary" onclick="return confirm('Generate invoice from this job card?')">
                            <i class="bi bi-receipt"></i> Generate Invoice
                        </a>
                    `);
                }

            } catch (error) {
                const modalBody = jobDetailsModalElement.querySelector('.modal-body');
                modalBody.innerHTML = `<div class="alert alert-danger" role="alert">
                                            <i class="bi bi-exclamation-circle me-2"></i> Failed to load job details: ${error.message}
                                        </div>`;
                console.error('Error fetching job details:', error);
            }
        });


        // --- Helper Functions ---




        // Function to display form-specific error messages
        function displayFormError(fieldId, message) {
            const errorDiv = $('#error_' + fieldId);
            if (errorDiv.length) {
                errorDiv.text(message);
                $('#' + fieldId).addClass('is-invalid');
            }
        }

        // Function to clear all form error messages and invalid states
        function clearFormErrors() {
            $('.error-message').text('');
            $('input, select, textarea').removeClass('is-invalid');
        }

        // Function to reset customer section to 'new' state
        function resetCustomerSection() {
            customerId.val('new');
            customerStatusBadge.text('New').removeClass('bg-success').addClass('bg-secondary');
            allCustomerInputs.forEach(input => {
                input.val('');
                input.prop('disabled', false);
            });
        }

        // Function to reset vehicle section to 'new' state
        function resetVehicleSection() {
            vehicleId.val('new');
            vehicleStatusBadge.text('New').removeClass('bg-success').addClass('bg-secondary');
            allVehicleInputs.forEach(input => {
                input.val('');
                input.prop('disabled', false);
            });
        }

        // Function to populate customer fields from search result data
        function populateCustomerFields(customerData) {
            resetCustomerSection(); // Always reset before populating
            customerId.val(customerData.id);
            customerStatusBadge.text('Existing').removeClass('bg-secondary').addClass('bg-success');

            const nameParts = (customerData.name || '').split(' ');
            newCustomerFirstName.val(nameParts[0] || '').prop('disabled', true);
            newCustomerLastName.val(nameParts[1] || '').prop('disabled', true);
            newCustomerPhone.val(customerData.phone || '').prop('disabled', true);
            newCustomerEmail.val(customerData.email || '').prop('disabled', true);
            newCustomerAddress.val(customerData.address || '').prop('disabled', true);
        }

        // Function to populate vehicle fields from search result data
        function populateVehicleFields(vehicleData) {
            resetVehicleSection(); // Always reset before populating
            vehicleId.val(vehicleData.id);
            vehicleStatusBadge.text('Existing').removeClass('bg-secondary').addClass('bg-success');

            newVehicleLicensePlate.val(vehicleData.registration_number || '').prop('disabled', true);
            newVehicleVIN.val(vehicleData.vin || '').prop('disabled', true);
            newVehicleMake.val(vehicleData.make || '').prop('disabled', true);
            newVehicleModel.val(vehicleData.model || '').prop('disabled', true);
            newVehicleYear.val(vehicleData.year_of_manufacture || '').prop('disabled', true);
            newVehicleEngineNumber.val(vehicleData.engine_number || '').prop('disabled', true);
            newVehicleChassisNumber.val(vehicleData.chassis_number || '').prop('disabled', true);
            newVehicleTransmission.val(vehicleData.transmission || '').prop('disabled', true);
            newVehicleFuelType.val(vehicleData.fuel_type || '').prop('disabled', true);
            newVehicleColor.val(vehicleData.color || '').prop('disabled', true);

            // Auto-populate job details from existing vehicle data
            mileageIn.val(vehicleData.mileage || 0);
            reportedProblem.val(vehicleData.reported_problem || '');
        }

        // Reset entire form state on modal close or successful submission
        function resetEntireForm() {
            jobIntakeForm[0].reset(); // Resets all form fields
            resetCustomerSection();
            resetVehicleSection();
            photoPreviewContainer.empty();
            photoPreviewContainer.addClass('empty-state'); // Add empty state class
            searchResults.empty().hide();
            clearFormErrors();
        }

        // --- Event Listeners ---

        // Handle modal hidden event to reset form
        addJobModal.on('hidden.bs.modal', function() {
            resetEntireForm();
        });

        // Photo Upload Preview
        photoUploadInput.on('change', function() {
            photoPreviewContainer.empty();
            photoPreviewContainer.removeClass('empty-state');
            clearFormErrors(); // Clear errors for job_card_photos specifically

            const files = this.files;
            if (files.length === 0) {
                photoPreviewContainer.addClass('empty-state');
                return;
            }

            if (files.length > 10) {
                displayFormError('job_card_photos', "You can upload a maximum of 10 images.");
                this.value = ''; // Clear the input
                photoPreviewContainer.addClass('empty-state');
                return;
            }

            let allFilesValid = true;
            Array.from(files).forEach(file => {
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    displayFormError('job_card_photos', `File "${file.name}" is too large. Max 2MB allowed.`);
                    this.value = ''; // Clear the input
                    photoPreviewContainer.empty();
                    photoPreviewContainer.addClass('empty-state');
                    allFilesValid = false;
                    return;
                }
                if (!file.type.startsWith('image/')) {
                    displayFormError('job_card_photos', `File "${file.name}" is not an image.`);
                    this.value = ''; // Clear the input
                    photoPreviewContainer.empty();
                    photoPreviewContainer.addClass('empty-state');
                    allFilesValid = false;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreviewContainer.append(`<img src="${e.target.result}" class="preview-thumb" alt="Photo Preview">`);
                };
                reader.readAsDataURL(file);
            });

            if (!allFilesValid) {
                this.value = '';
                photoPreviewContainer.empty();
                photoPreviewContainer.addClass('empty-state');
            }
        });

        // Customer/Vehicle Search
        let searchTimeout;
        searchInput.on('keyup', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val().trim();
            clearFormErrors(); // Clear errors on new search input

            if (query.length < 2) {
                searchResults.empty().hide();
                resetCustomerSection(); // Reset sections if search query is too short
                resetVehicleSection();
                return;
            }

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: '<?= base_url("job_intake/search") ?>',
                    method: 'GET',
                    data: {
                        query: query
                    },
                    dataType: 'json',
                    success: function(response) {
                        searchResults.empty();
                        if (response.customers.length === 0 && response.vehicles.length === 0) {
                            searchResults.append('<div class="search-result-item disabled">No existing matches. Will create new.</div>');
                        } else {
                            response.customers.forEach(customer => {
                                searchResults.append(`
                                    <div class="search-result-item" data-type="customer" data-id="${customer.id}"
                                        data-name="${customer.name}" data-phone="${customer.phone}"
                                        data-email="${customer.email || ''}" data-address="${customer.address || ''}">
                                        <div class="result-title">Customer: ${customer.name}</div>
                                        <div class="result-subtitle">Phone: ${customer.phone}</div>
                                    </div>
                                `);
                            });
                            response.vehicles.forEach(vehicle => {
                                searchResults.append(`
                                    <div class="search-result-item" data-type="vehicle" data-id="${vehicle.id}"
                                        data-registration_number="${vehicle.registration_number}"
                                        data-vin="${vehicle.vin || ''}" data-make="${vehicle.make}" data-model="${vehicle.model}"
                                        data-year_of_manufacture="${vehicle.year_of_manufacture}"
                                        data-color="${vehicle.color || ''}" data-mileage="${vehicle.mileage || 0}"
                                        data-reported_problem="${vehicle.reported_problem || ''}"
                                        data-engine_number="${vehicle.engine_number || ''}" data-chassis_number="${vehicle.chassis_number || ''}"
                                        data-fuel_type="${vehicle.fuel_type || ''}" data-transmission="${vehicle.transmission || ''}"
                                        data-owner-id="${vehicle.owner_id}" data-owner-name="${vehicle.owner_name || ''}"
                                        data-owner-phone="${(vehicle.owner && vehicle.owner.phone) ? vehicle.owner.phone : ''}"
                                        data-owner-email="${(vehicle.owner && vehicle.owner.email) ? vehicle.owner.email : ''}"
                                        data-owner-address="${(vehicle.owner && vehicle.owner.address) ? vehicle.owner.address : ''}">
                                        <div class="result-title">Vehicle: ${vehicle.registration_number} (${vehicle.make} ${vehicle.model})</div>
                                        <div class="result-subtitle">Owner: ${vehicle.owner_name}</div>
                                    </div>
                                `);
                            });
                        }
                        searchResults.show();
                    },
                    error: function(xhr, status, error) {
                        searchResults.empty().hide();
                        displayFormError('search_input', 'Error performing search. Please try again.');
                    }
                });
            }, 300); // 300ms debounce
        });

        // Handle selection from search results
        searchResults.on('click', '.search-result-item:not(.disabled)', function() {
            const itemType = $(this).data('type');
            const itemData = $(this).data(); // Get all data- attributes as an object

            searchResults.empty().hide();
            searchInput.val($(this).find('.result-title').text()); // Set search input to selected item's title

            clearFormErrors(); // Clear errors on selection

            if (itemType === 'customer') {
                resetCustomerSection();
                populateCustomerFields({
                    id: itemData.id,
                    name: itemData.name,
                    phone: itemData.phone,
                    email: itemData.email,
                    address: itemData.address
                });
                // Ensure vehicle section is reset to new if only customer is selected
                resetVehicleSection();
            } else if (itemType === 'vehicle') {
                resetVehicleSection();
                // Pass data directly to populateVehicleFields, using the original names
                populateVehicleFields(itemData);

                // If vehicle has an owner, populate customer section with full details
                if (itemData.ownerId) {
                    populateCustomerFields({
                        id: itemData.ownerId,
                        name: itemData.ownerName,
                        phone: itemData.ownerPhone,
                        email: itemData.ownerEmail,
                        address: itemData.ownerAddress
                    });
                    // Disable customer fields when linked to an existing vehicle
                    allCustomerInputs.forEach(input => input.prop('disabled', true));
                } else {
                    // If vehicle has no owner, reset customer section to new
                    resetCustomerSection();
                }
            }
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search_input, #search_results').length) {
                searchResults.hide();
            }
        });

        // Form Submission
        jobIntakeForm.on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            clearFormErrors(); // Clear all previous errors

            const formData = new FormData(this); // Create FormData object for AJAX file upload

            // Add loading state to button
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...');

            $.ajax({
                url: '<?= base_url('job_intake/create_job_card') ?>',
                method: 'POST',
                data: formData,
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message + ' Job No: ' + response.job_no,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            resetEntireForm(); // Reset form and sections
                            addJobModal.modal('hide'); // Close the modal
                            window.location.reload(); // Reload the page to reflect new job
                        });
                    } else if (response.status === 'error') {
                        let errorMessage = 'Error: ' + (response.message || 'An unknown error occurred.');
                        if (response.errors) {
                            errorMessage = 'Validation failed. Please check the form.';
                            $.each(response.errors, function(key, value) {
                                displayFormError(key, value);
                            });
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'An unexpected server error occurred. Please try again.';
                    try {
                        const responseJson = JSON.parse(xhr.responseText);
                        if (responseJson.message) {
                            errorMessage = 'Server Error: ' + responseJson.message;
                        } else if (responseJson.errors) {
                            errorMessage = 'Validation Error: Please correct the following issues:<br>' + Object.values(responseJson.errors).join('<br>');
                            $.each(responseJson.errors, function(key, value) {
                                displayFormError(key, value);
                            });
                        }
                    } catch (e) {
                        // If response is not JSON, use generic message
                    }
                    Swal.fire({
                        title: 'Server Error!',
                        html: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    submitButton.prop('disabled', false).html('<i class="bi bi-plus-circle"></i> Create Job Card');
                }
            });
        });

        // Assign Mechanic button handler
        $(document).on('click', '#btnAssignMechanic', function() {
            const jobId = window._currentJobId;
            const mechanicId = $('#dispatch_mechanic_id').val();
            const $btn = $(this);
            const $msg = $('#dispatch_message');

            if (!mechanicId) {
                $msg.html('<span class="text-danger">Please select a mechanic.</span>');
                return;
            }

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Assigning...');
            $msg.html('');

            $.ajax({
                url: '<?= base_url('admin/jobs/assign_mechanic') ?>/' + jobId,
                method: 'POST',
                data: { mechanic_id: mechanicId },
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.status === 'success') {
                        $msg.html('<span class="text-success">' + response.message + '</span>');
                        Swal.fire('Assigned!', response.message, 'success').then(function() {
                            location.reload();
                        });
                    } else {
                        $msg.html('<span class="text-danger">' + (response.message || 'Error assigning mechanic') + '</span>');
                    }
                },
                error: function(xhr) {
                    let msg = 'Error assigning mechanic.';
                    try {
                        const r = JSON.parse(xhr.responseText);
                        msg = r.message || msg;
                    } catch(e) {}
                    $msg.html('<span class="text-danger">' + msg + '</span>');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Assign');
                }
            });
        });

        // Status transition button click handler
        $(document).on('click', '.btn-status-transition', function() {
            const jobId = $(this).data('job-id');
            const newStatus = $(this).data('new-status');
            const $btn = $(this);
            const $msg = $('#statusTransitionMessage');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            $msg.html('');

            $.ajax({
                url: BASE_URL + '/admin/jobs/update_status/' + jobId,
                method: 'POST',
                data: { new_status: newStatus },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $msg.html('<span class="text-success">' + response.message + '</span>');
                        renderStatusSection(response.new_status, response.valid_transitions, jobId);
                        $('#detail_job_status').text(response.new_status);
                    } else {
                        $msg.html('<span class="text-danger">' + (response.message || 'Error updating status') + '</span>');
                    }
                },
                error: function(xhr) {
                    let msg = 'Error updating status.';
                    try {
                        const r = JSON.parse(xhr.responseText);
                        msg = r.message || msg;
                    } catch(e) {}
                    $msg.html('<span class="text-danger">' + msg + '</span>');
                },
                complete: function() {
                    $btn.prop('disabled', false).text($btn.data('original-text') || 'Update');
                }
            });
        });

        // Load status history when tab is shown
        $(document).on('shown.bs.tab', '#status-history-tab', function() {
            const jobId = window._currentJobId;
            if (!jobId) return;
            loadStatusHistory(jobId);
        });

        function renderStatusSection(status, transitions, jobId) {
            const badge = $('#detail_status_badge');
            badge.text(status);
            badge.removeClass('bg-secondary bg-success bg-warning bg-danger bg-info bg-primary');
            const colorMap = {
                'Awaiting Assignment': 'bg-secondary',
                'Awaiting Diagnosis': 'bg-info',
                'Diagnosis Complete': 'bg-primary',
                'Quote Sent': 'bg-primary',
                'Approved': 'bg-success',
                'In Progress': 'bg-primary',
                'Awaiting Parts': 'bg-warning text-dark',
                'Quality Check': 'bg-info',
                'Ready for Invoice': 'bg-success',
                'Paid': 'bg-success',
                'Completed': 'bg-success',
                'On Hold': 'bg-warning text-dark',
                'Rework': 'bg-danger',
                'Cancelled': 'bg-danger'
            };
            badge.addClass(colorMap[status] || 'bg-secondary');

            const container = $('#transitionButtonsContainer');
            container.empty();

            if (transitions && transitions.length > 0) {
                const label = $('<div class="info-label mb-1">Actions:</div>');
                container.append(label);
                transitions.forEach(function(nextStatus) {
                    const btn = $('<button class="btn btn-sm btn-outline-primary me-1 mb-1 btn-status-transition"></button>');
                    btn.data('job-id', jobId);
                    btn.data('new-status', nextStatus);
                    btn.data('original-text', nextStatus);
                    btn.text(nextStatus);
                    container.append(btn);
                });
            } else {
                container.append('<span class="text-muted small">No status transitions available for your role.</span>');
            }
        }

        function loadStatusHistory(jobId) {
            const tbody = $('#detail_status_history');
            tbody.html('<tr><td colspan="5" class="text-center text-muted">Loading history...</td></tr>');

            $.ajax({
                url: BASE_URL + '/admin/jobs/status_history/' + jobId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    tbody.empty();
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(entry) {
                            const date = new Date(entry.created_at).toLocaleString();
                            tbody.append(`
                                <tr>
                                    <td>${entry.from_status}</td>
                                    <td>${entry.to_status}</td>
                                    <td>${entry.changed_by_name || 'N/A'}</td>
                                    <td>${date}</td>
                                    <td>${entry.notes || ''}</td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr><td colspan="5" class="text-center text-muted">No status history recorded.</td></tr>');
                    }
                },
                error: function() {
                    tbody.html('<tr><td colspan="5" class="text-center text-danger">Failed to load status history.</td></tr>');
                }
            });
        }
    });
</script>

<style>
    /* Add any specific styles for this form if needed, e.g., for input focus, button colors */
    .form-group label {
        font-weight: 500;
        color: #343a40;
        /* text-dark */
    }

    .form-control:focus,
    .form-select:focus,
    .form-control:focus-within {
        border-color: #007bff;
        /* primary-color */
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .card-header {
        background-color: #f8f9fa;
        /* bg-light */
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
        color: #343a40;
        padding: 1rem 1.5rem;
        /* Slightly reduced padding */
    }

    /* Search Results Dropdown */
    .search-results-dropdown {
        position: absolute;
        width: calc(100% - 2rem);
        /* Account for card padding */
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        z-index: 1000;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        left: 1rem;
        /* Align with card padding */
        right: 1rem;
    }

    .search-results-dropdown div {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f2f5;
    }

    .search-results-dropdown div:last-child {
        border-bottom: none;
    }

    .search-results-dropdown div:hover {
        background-color: #e9ecef;
    }

    .search-results-dropdown .result-title {
        font-weight: 600;
        color: #007bff;
    }

    .search-results-dropdown .result-subtitle {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .search-results-dropdown .disabled {
        cursor: not-allowed;
        color: #999;
        font-style: italic;
    }

    /* Photo Preview */
    .photo-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
        min-height: 110px;
        /* Give it a minimum height to avoid layout shifts */
        border: 1px dashed #ced4da;
        /* Visual cue for drop area */
        border-radius: 8px;
        padding: 10px;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .photo-preview-container.empty-state::before {
        content: "No photos selected. Click or drag to add.";
    }

    .photo-preview-container img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .error-message {
        color: #dc3545;
        /* danger-color */
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-control.is-invalid,
    .form-select.is-invalid,
    textarea.is-invalid {
        border-color: #dc3545 !important;
    }
</style>