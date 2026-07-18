<style>
    .search-results-dropdown {
        position: absolute;
        width: calc(100% - 3rem);
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #d1d5db;
        border-top: none;
        z-index: 1000;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        left: 1.5rem;
        right: 1.5rem;
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
        color: #4f46e5;
    }

    .search-results-dropdown .result-subtitle {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .search-results-dropdown .disabled {
        cursor: not-allowed;
        color: #999;
        font-style: italic;
    }

    .photo-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
        min-height: 110px;
        border: 1px dashed #d1d5db;
        border-radius: 8px;
        padding: 10px;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: #6b7280;
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
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    input.border-red-500,
    select.border-red-500,
    textarea.border-red-500 {
        border-color: #ef4444 !important;
        ring: 1px solid #ef4444;
    }
</style>

<!-- Add Job Modal -->
<div id="addJobModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('addJobModal')"></div>
<div id="addJobModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">New Job Intake</h5>
            <button type="button" onclick="closeModal('addJobModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <form id="jobIntakeForm" method="POST" action="<?= base_url('job_intake/create_job_card') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Customer/Vehicle Search -->
                <div class="mb-4">
                    <label for="search_input" class="block text-sm font-medium text-gray-700 mb-1">Search Existing Customer or Vehicle</label>
                    <input type="text" id="search_input" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" placeholder="Search by phone, name, or registration number..." autocomplete="off">
                    <div id="search_results" class="search-results-dropdown hidden"></div>
                    <div id="error_search_input" class="error-message"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer Section -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h6 class="text-sm font-semibold text-gray-700">Customer</h6>
                            <span id="customer_status" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">New</span>
                        </div>
                        <input type="hidden" id="customer_id" name="customer_id" value="new">
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="new_customer_first_name" class="block text-xs font-medium text-gray-600 mb-1">First Name</label>
                                    <input type="text" id="new_customer_first_name" name="new_customer_first_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_customer_first_name" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_customer_last_name" class="block text-xs font-medium text-gray-600 mb-1">Last Name</label>
                                    <input type="text" id="new_customer_last_name" name="new_customer_last_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_customer_last_name" class="error-message"></div>
                                </div>
                            </div>
                            <div>
                                <label for="new_customer_phone_number" class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                                <input type="text" id="new_customer_phone_number" name="new_customer_phone_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                <div id="error_new_customer_phone_number" class="error-message"></div>
                            </div>
                            <div>
                                <label for="new_customer_email" class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                                <input type="email" id="new_customer_email" name="new_customer_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                <div id="error_new_customer_email" class="error-message"></div>
                            </div>
                            <div>
                                <label for="new_customer_address" class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                                <textarea id="new_customer_address" name="new_customer_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none"></textarea>
                                <div id="error_new_customer_address" class="error-message"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Section -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h6 class="text-sm font-semibold text-gray-700">Vehicle</h6>
                            <span id="vehicle_status" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">New</span>
                        </div>
                        <input type="hidden" id="vehicle_id" name="vehicle_id" value="new">
                        <div class="space-y-3">
                            <div>
                                <label for="new_vehicle_license_plate" class="block text-xs font-medium text-gray-600 mb-1">Registration No.</label>
                                <input type="text" id="new_vehicle_license_plate" name="new_vehicle_license_plate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                <div id="error_new_vehicle_license_plate" class="error-message"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="new_vehicle_make" class="block text-xs font-medium text-gray-600 mb-1">Make</label>
                                    <input type="text" id="new_vehicle_make" name="new_vehicle_make" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_make" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_vehicle_model" class="block text-xs font-medium text-gray-600 mb-1">Model</label>
                                    <input type="text" id="new_vehicle_model" name="new_vehicle_model" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_model" class="error-message"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="new_vehicle_year" class="block text-xs font-medium text-gray-600 mb-1">Year</label>
                                    <input type="text" id="new_vehicle_year" name="new_vehicle_year" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_year" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_vehicle_color" class="block text-xs font-medium text-gray-600 mb-1">Color</label>
                                    <input type="text" id="new_vehicle_color" name="new_vehicle_color" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_color" class="error-message"></div>
                                </div>
                            </div>
                            <div>
                                <label for="new_vehicle_vin" class="block text-xs font-medium text-gray-600 mb-1">VIN</label>
                                <input type="text" id="new_vehicle_vin" name="new_vehicle_vin" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                <div id="error_new_vehicle_vin" class="error-message"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="new_vehicle_engine_number" class="block text-xs font-medium text-gray-600 mb-1">Engine No.</label>
                                    <input type="text" id="new_vehicle_engine_number" name="new_vehicle_engine_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_engine_number" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_vehicle_chassis_number" class="block text-xs font-medium text-gray-600 mb-1">Chassis No.</label>
                                    <input type="text" id="new_vehicle_chassis_number" name="new_vehicle_chassis_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_chassis_number" class="error-message"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="new_vehicle_fuel_type" class="block text-xs font-medium text-gray-600 mb-1">Fuel Type</label>
                                    <select id="new_vehicle_fuel_type" name="new_vehicle_fuel_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white">
                                        <option value="">Select</option>
                                        <option value="Petrol">Petrol</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Electric">Electric</option>
                                        <option value="Hybrid">Hybrid</option>
                                    </select>
                                    <div id="error_new_vehicle_fuel_type" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_vehicle_transmission" class="block text-xs font-medium text-gray-600 mb-1">Transmission</label>
                                    <select id="new_vehicle_transmission" name="new_vehicle_transmission" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white">
                                        <option value="">Select</option>
                                        <option value="Manual">Manual</option>
                                        <option value="Automatic">Automatic</option>
                                        <option value="CVT">CVT</option>
                                        <option value="Semi-Automatic">Semi-Automatic</option>
                                    </select>
                                    <div id="error_new_vehicle_transmission" class="error-message"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Details -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mt-4">
                    <h6 class="text-sm font-semibold text-gray-700 mb-3">Job Details</h6>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label for="date_in" class="block text-xs font-medium text-gray-600 mb-1">Date In</label>
                            <input type="date" id="date_in" name="date_in" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                            <div id="error_date_in" class="error-message"></div>
                        </div>
                        <div>
                            <label for="time_in" class="block text-xs font-medium text-gray-600 mb-1">Time In</label>
                            <input type="time" id="time_in" name="time_in" value="<?= date('H:i') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                            <div id="error_time_in" class="error-message"></div>
                        </div>
                        <div>
                            <label for="mileage_in" class="block text-xs font-medium text-gray-600 mb-1">Mileage In</label>
                            <input type="number" id="mileage_in" name="mileage_in" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                            <div id="error_mileage_in" class="error-message"></div>
                        </div>
                        <div>
                            <label for="fuel_level" class="block text-xs font-medium text-gray-600 mb-1">Fuel Level</label>
                            <select id="fuel_level" name="fuel_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white">
                                <option value="">Select</option>
                                <option value="Empty">Empty</option>
                                <option value="1/4">1/4</option>
                                <option value="1/2">1/2</option>
                                <option value="3/4">3/4</option>
                                <option value="Full">Full</option>
                            </select>
                            <div id="error_fuel_level" class="error-message"></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="reported_problem" class="block text-xs font-medium text-gray-600 mb-1">Reported Problem</label>
                        <input type="text" id="reported_problem" name="reported_problem" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                        <div id="error_reported_problem" class="error-message"></div>
                    </div>
                    <div class="mt-3">
                        <label for="initial_damage_notes" class="block text-xs font-medium text-gray-600 mb-1">Initial Damage Notes</label>
                        <textarea id="initial_damage_notes" name="initial_damage_notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none"></textarea>
                        <div id="error_initial_damage_notes" class="error-message"></div>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mt-4">
                    <h6 class="text-sm font-semibold text-gray-700 mb-3">Job Card Photos</h6>
                    <input type="file" id="job_card_photos" name="job_card_photos[]" multiple accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <div id="photo_preview_container" class="photo-preview-container empty-state mt-2"></div>
                    <div id="error_job_card_photos" class="error-message"></div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal('addJobModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                        <i class="bi bi-plus-circle"></i> Create Job Card
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Job details modal -->
<div id="jobDetailsModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('jobDetailsModal')"></div>
<div id="jobDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">
                <i class="bi bi-briefcase me-2"></i> Job Details - <span id="detail_job_no"></span>
            </h5>
            <button type="button" onclick="closeModal('jobDetailsModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-5 space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                        <h6 class="text-sm font-semibold text-indigo-600 mb-3">Job Card Summary</h6>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Status:</span> <span class="text-sm text-gray-900" id="detail_job_status"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Reported Problem:</span> <span class="text-sm text-gray-900" id="detail_reported_problem"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Initial Damage Notes:</span> <span class="text-sm text-gray-900" id="detail_initial_damage_notes"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Mileage In:</span> <span class="text-sm text-gray-900" id="detail_mileage_in"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Fuel Level:</span> <span class="text-sm text-gray-900" id="detail_fuel_level"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Date In:</span> <span class="text-sm text-gray-900" id="detail_date_in"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Time In:</span> <span class="text-sm text-gray-900" id="detail_time_in"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Estimated Labor Hours:</span> <span class="text-sm text-gray-900" id="detail_estimated_labor_hours"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Quote Status:</span> <span class="text-sm text-gray-900" id="detail_quote_status"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Quote Amount:</span> <span class="text-sm text-gray-900" id="detail_quote_amount"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Assigned Service Advisor:</span> <span class="text-sm text-gray-900" id="detail_assigned_service_advisor"></span></div>
                        <div class="mb-3">
                            <span class="text-xs font-medium text-gray-500 block">Assigned Mechanic:</span>
                            <span class="text-sm text-gray-900" id="detail_assigned_mechanic"></span>
                            <div class="mt-2" id="dispatch_section">
                                <div class="flex gap-2">
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="dispatch_mechanic_id">
                                        <option value="">-- Select Mechanic --</option>
                                    </select>
                                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap" type="button" id="btnAssignMechanic">Assign</button>
                                </div>
                                <div id="dispatch_message" class="mt-1"></div>
                            </div>
                        </div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Diagnosis:</span> <span class="text-sm text-gray-900" id="detail_diagnosis"></span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Job Summary:</span> <span class="text-sm text-gray-900" id="detail_job_summary"></span></div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4" id="statusActionsCard">
                        <h6 class="text-sm font-semibold text-indigo-600 mb-3">Status</h6>
                        <div class="mb-3">
                            <span class="text-xs font-medium text-gray-500 block">Current Status:</span>
                            <span class="text-sm text-gray-900"><span class="text-xs font-medium px-2.5 py-0.5 rounded-full" id="detail_status_badge"></span></span>
                        </div>
                        <div class="mt-2" id="transitionButtonsContainer"></div>
                        <div class="mt-2" id="statusTransitionMessage"></div>
                    </div>
                </div>

                <div class="md:col-span-7">
                    <div class="flex border-b border-gray-200" id="jobDetailsTab">
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors text-indigo-600 border-indigo-600" onclick="switchTab('customer-vehicle', this)" data-tab="customer-vehicle">Customer & Vehicle</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors text-gray-500 border-transparent hover:text-gray-700" onclick="switchTab('parts-tasks', this)" data-tab="parts-tasks">Parts & Tasks</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors text-gray-500 border-transparent hover:text-gray-700" onclick="switchTab('photos', this)" data-tab="photos">Photos</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors text-gray-500 border-transparent hover:text-gray-700" onclick="switchTab('status-history', this)" data-tab="status-history">Status History</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors text-gray-500 border-transparent hover:text-gray-700" onclick="switchTab('invoice-tab-panel', this)" data-tab="invoice-tab-panel">Invoice</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors text-gray-500 border-transparent hover:text-gray-700" onclick="switchTab('lpos-tab-panel', this)" data-tab="lpos-tab-panel">LPOs</button>
                    </div>

                    <div class="mt-4">
                        <div id="customer-vehicle" class="tab-panel">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Customer Info</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Name:</span> <span class="text-sm text-gray-900" id="detail_customer_name"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Phone:</span> <span class="text-sm text-gray-900" id="detail_customer_phone"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Email:</span> <span class="text-sm text-gray-900" id="detail_customer_email"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Address:</span> <span class="text-sm text-gray-900" id="detail_customer_address"></span></div>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-500 mb-3 mt-4">Vehicle Info</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Registration No.:</span> <span class="text-sm text-gray-900" id="detail_vehicle_reg_no"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Make & Model:</span> <span class="text-sm text-gray-900" id="detail_vehicle_make_model"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Year:</span> <span class="text-sm text-gray-900" id="detail_vehicle_year"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">VIN:</span> <span class="text-sm text-gray-900" id="detail_vehicle_vin"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Engine No.:</span> <span class="text-sm text-gray-900" id="detail_vehicle_engine_no"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Chassis No.:</span> <span class="text-sm text-gray-900" id="detail_vehicle_chassis_no"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Fuel Type:</span> <span class="text-sm text-gray-900" id="detail_vehicle_fuel_type"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Transmission:</span> <span class="text-sm text-gray-900" id="detail_vehicle_transmission"></span></div>
                                <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Color:</span> <span class="text-sm text-gray-900" id="detail_vehicle_color"></span></div>
                            </div>
                        </div>

                        <div id="parts-tasks" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Parts Required</h6>
                            <div class="overflow-x-auto rounded-xl border border-gray-200 mb-4">
                                <table class="w-full divide-y divide-gray-200 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Part Name</th>
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Qty.</th>
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Est. Unit Price</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail_parts_list" class="divide-y divide-gray-200">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No parts recorded.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-500 mb-3 mt-4">Labor Tasks</h6>
                            <div class="overflow-x-auto rounded-xl border border-gray-200">
                                <table class="w-full divide-y divide-gray-200 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Task Name</th>
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Est. Hours</th>
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail_tasks_list" class="divide-y divide-gray-200">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No tasks recorded.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="photos" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Job Card Photos</h6>
                            <div id="detail_photos_gallery" class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <div class="col-span-full text-center text-gray-500 text-sm">No photos available.</div>
                            </div>
                        </div>

                        <div id="status-history" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Status Transition History</h6>
                            <div class="overflow-x-auto rounded-xl border border-gray-200">
                                <table class="w-full divide-y divide-gray-200 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">From</th>
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">To</th>
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Changed By</th>
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Date/Time</th>
                                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail_status_history" class="divide-y divide-gray-200">
                                        <tr>
                                            <td colspan="5" class="px-4 py-3 text-sm text-gray-500 text-center">Loading history...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="invoice-tab-panel" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Invoice</h6>
                            <div id="detail_invoice_content">
                                <p class="text-sm text-gray-500 text-center">Loading invoice data...</p>
                            </div>
                        </div>

                        <div id="lpos-tab-panel" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Purchase Orders for this Job</h6>
                            <div id="detail_lpos_content">
                                <p class="text-sm text-gray-500 text-center">No LPOs linked to this job.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeModal('jobDetailsModal')" class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <i class="bi bi-x-circle me-1"></i> Close
            </button>
        </div>
    </div>
</div>

<script>
    var BASE_URL = '<?= base_url() ?>';

    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('text-indigo-600', 'border-indigo-600');
            b.classList.add('text-gray-500', 'border-transparent');
        });
        btn.classList.remove('text-gray-500', 'border-transparent');
        btn.classList.add('text-indigo-600', 'border-indigo-600');
    }

    // Function to open a modal with dynamic content
    function openModal(url, title = 'Form') {
        const modalElement = document.getElementById('actionModal');
        const modalTitle = document.getElementById('actionModalLabel');
        const modalContent = document.getElementById('modalContent');

        modalContent.innerHTML = `
            <div class="flex justify-center items-center" style="min-height: 100px;">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600" role="status"></div>
            </div>
        `;
        modalTitle.textContent = title;

        openModal('actionModal');

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
                modalContent.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg" role="alert">Error loading content: ${error.message}. Please try again.</div>`;
                console.error('Error loading modal content:', error);
            });
    }
    window.openModal = openModal;

    $(document).ready(function() {
        // --- Element References ---
        const addJobModalEl = document.getElementById('addJobModal');
        const jobDetailsModalEl = document.getElementById('jobDetailsModal');

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

        // Handle modal hidden event to reset form
        function onAddJobModalClose() {
            resetEntireForm();
        }

        // Observe modal close via backdrop/close button
        function setupModalCloseObserver() {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (addJobModalEl.classList.contains('hidden')) {
                            onAddJobModalClose();
                        }
                    }
                });
            });
            observer.observe(addJobModalEl, { attributes: true });
        }
        setupModalCloseObserver();

        $('#JobTable tbody').on('click', '.view-job', async function() {
            const jobId = $(this).data('id');

            // Clear previous data and show loading spinners/placeholders
            $('#detail_job_no').text('Loading...');
            jobDetailsModalEl.querySelectorAll('.text-gray-900').forEach(el => {
                if (el.id && el.id !== 'detail_job_no') el.textContent = '';
            });
            $('#detail_parts_list').html('<tr><td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">Loading parts...</td></tr>');
            $('#detail_tasks_list').html('<tr><td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">Loading tasks...</td></tr>');
            $('#detail_photos_gallery').html('<div class="col-span-full text-center text-gray-500 text-sm">Loading photos...</div>');

            openModal('jobDetailsModal');

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
                            <tr class="divide-y divide-gray-200">
                                <td class="px-4 py-3 text-sm text-gray-900">${part.name || 'N/A'} (${part.part_number || 'N/A'})</td>
                                <td class="px-4 py-3 text-sm text-gray-900">${part.quantity_required || '0'}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Ksh ${parseFloat(part.unit_price_at_estimate || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            </tr>
                        `);
                    });
                } else {
                    partsList.append('<tr class="divide-y divide-gray-200"><td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No parts recorded.</td></tr>');
                }

                const tasksList = $('#detail_tasks_list');
                tasksList.empty();
                if (data.tasks && data.tasks.length > 0) {
                    data.tasks.forEach(task => {
                        tasksList.append(`
                            <tr class="divide-y divide-gray-200">
                                <td class="px-4 py-3 text-sm text-gray-900">${task.task_name || 'N/A'}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">${task.estimated_hours || '0'}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">${task.notes || 'N/A'}</td>
                            </tr>
                        `);
                    });
                } else {
                    tasksList.append('<tr class="divide-y divide-gray-200"><td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No tasks recorded.</td></tr>');
                }

                // Populate Photos Tab
                const photosGallery = $('#detail_photos_gallery');
                photosGallery.empty();
                if (data.photos && data.photos.length > 0) {
                    data.photos.forEach(photo => {
                        photosGallery.append(`
                            <div>
                                <a href="<?= base_url() ?>/${photo.file_path}" target="_blank">
                                    <img src="<?= base_url() ?>/${photo.file_path}" alt="${photo.file_name}" class="max-w-full h-auto rounded shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">
                                </a>
                            </div>
                        `);
                    });
                } else {
                    photosGallery.append('<div class="col-span-full text-center text-gray-500 text-sm">No photos available.</div>');
                }

                renderStatusSection(data.job_status, data.valid_transitions, data.id);

                // Populate Invoice Tab
                const invoiceContent = $('#detail_invoice_content');
                invoiceContent.empty();
                if (data.invoice) {
                    const inv = data.invoice;
                    const badgeMap = {
                        'Draft': 'bg-gray-100 text-gray-700', 'Sent': 'bg-blue-100 text-blue-700',
                        'Partially Paid': 'bg-amber-100 text-amber-700', 'Paid': 'bg-emerald-100 text-emerald-700',
                        'Overdue': 'bg-red-100 text-red-700', 'Cancelled': 'bg-red-100 text-red-700'
                    };
                    const badgeClass = badgeMap[inv.status] || 'bg-gray-100 text-gray-700';
                    invoiceContent.html(`
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Invoice No:</span> <span class="text-sm text-gray-900">${inv.invoice_no}</span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Grand Total:</span> <span class="text-sm text-gray-900">KSh ${parseFloat(inv.grand_total).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Balance Due:</span> <span class="text-sm text-gray-900">KSh ${parseFloat(inv.balance_due).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                        <div class="mb-3"><span class="text-xs font-medium text-gray-500 block">Status:</span> <span class="text-sm text-gray-900"><span class="text-xs font-medium px-2.5 py-0.5 rounded-full ${badgeClass}">${inv.status}</span></span></div>
                        <div class="mt-3">
                            <a href="<?= base_url('admin/invoices/view/') ?>${inv.invoice_id}" class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1"><i class="bi bi-eye"></i> View Invoice</a>
                        </div>
                    `);
                } else {
                    const jobId = data.id;
                    invoiceContent.html(`
                        <p class="text-sm text-gray-500">No invoice generated yet for this job.</p>
                        <a href="<?= base_url('admin/invoices/generate/') ?>${jobId}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1 mt-2" onclick="return confirm('Generate invoice from this job card?')">
                            <i class="bi bi-receipt"></i> Generate Invoice
                        </a>
                    `);
                }

                // Populate LPOs Tab
                var lposContent = $('#detail_lpos_content');
                lposContent.empty();
                if (data.lpos && data.lpos.length > 0) {
                    var html = '<div class="overflow-x-auto rounded-xl border border-gray-200 mb-3"><table class="w-full divide-y divide-gray-200 text-sm"><thead><tr class="bg-gray-50"><th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">LPO No.</th><th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Supplier</th><th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Total</th><th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Status</th><th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left"></th></tr></thead><tbody class="divide-y divide-gray-200">';
                    data.lpos.forEach(function(lpo) {
                        var badgeMap = {
                            'Draft': 'bg-gray-100 text-gray-700', 'Sent': 'bg-blue-100 text-blue-700',
                            'Partially Received': 'bg-amber-100 text-amber-700', 'Received': 'bg-emerald-100 text-emerald-700',
                            'Cancelled': 'bg-red-100 text-red-700'
                        };
                        var badgeClass = badgeMap[lpo.status] || 'bg-gray-100 text-gray-700';
                        html += '<tr>' +
                            '<td class="px-4 py-3 text-sm text-gray-900">' + lpo.lpo_no + '</td>' +
                            '<td class="px-4 py-3 text-sm text-gray-900">' + (lpo.supplier_name || 'N/A') + '</td>' +
                            '<td class="px-4 py-3 text-sm text-gray-900">KSh ' + parseFloat(lpo.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) + '</td>' +
                            '<td class="px-4 py-3 text-sm"><span class="text-xs font-medium px-2.5 py-0.5 rounded-full ' + badgeClass + '">' + lpo.status + '</span></td>' +
                            '<td class="px-4 py-3 text-sm"><a href="<?= base_url('admin/lpos/view/') ?>' + lpo.id + '" class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-2 py-1 rounded text-xs font-medium transition-colors inline-flex items-center gap-1"><i class="bi bi-eye"></i></a></td>' +
                            '</tr>';
                    });
                    html += '</tbody></table></div>';
                    html += '<a href="<?= base_url('admin/lpos/add?job_card_id=') ?>' + data.id + '" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1"><i class="bi bi-plus-lg"></i> Raise LPO</a>';
                    lposContent.html(html);
                } else {
                    lposContent.html(`
                        <p class="text-sm text-gray-500">No LPOs linked to this job.</p>
                        <a href="<?= base_url('admin/lpos/add?job_card_id=') ?>${data.id}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1 mt-2"><i class="bi bi-plus-lg"></i> Raise LPO</a>
                    `);
                }

            } catch (error) {
                const modalBody = jobDetailsModalEl.querySelector('.p-6');
                modalBody.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg" role="alert">
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
                $('#' + fieldId).addClass('border-red-500');
            }
        }

        // Function to clear all form error messages and invalid states
        function clearFormErrors() {
            $('.error-message').text('');
            $('input, select, textarea').removeClass('border-red-500');
        }

        // Function to reset customer section to 'new' state
        function resetCustomerSection() {
            customerId.val('new');
            customerStatusBadge.text('New').removeClass('bg-emerald-100 text-emerald-700').addClass('bg-gray-100 text-gray-700');
            allCustomerInputs.forEach(input => {
                input.val('');
                input.prop('disabled', false);
            });
        }

        // Function to reset vehicle section to 'new' state
        function resetVehicleSection() {
            vehicleId.val('new');
            vehicleStatusBadge.text('New').removeClass('bg-emerald-100 text-emerald-700').addClass('bg-gray-100 text-gray-700');
            allVehicleInputs.forEach(input => {
                input.val('');
                input.prop('disabled', false);
            });
        }

        // Function to populate customer fields from search result data
        function populateCustomerFields(customerData) {
            resetCustomerSection();
            customerId.val(customerData.id);
            customerStatusBadge.text('Existing').removeClass('bg-gray-100 text-gray-700').addClass('bg-emerald-100 text-emerald-700');

            const nameParts = (customerData.name || '').split(' ');
            newCustomerFirstName.val(nameParts[0] || '').prop('disabled', true);
            newCustomerLastName.val(nameParts[1] || '').prop('disabled', true);
            newCustomerPhone.val(customerData.phone || '').prop('disabled', true);
            newCustomerEmail.val(customerData.email || '').prop('disabled', true);
            newCustomerAddress.val(customerData.address || '').prop('disabled', true);
        }

        // Function to populate vehicle fields from search result data
        function populateVehicleFields(vehicleData) {
            resetVehicleSection();
            vehicleId.val(vehicleData.id);
            vehicleStatusBadge.text('Existing').removeClass('bg-gray-100 text-gray-700').addClass('bg-emerald-100 text-emerald-700');

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

            mileageIn.val(vehicleData.mileage || 0);
            reportedProblem.val(vehicleData.reported_problem || '');
        }

        // Reset entire form state on modal close or successful submission
        function resetEntireForm() {
            jobIntakeForm[0].reset();
            resetCustomerSection();
            resetVehicleSection();
            photoPreviewContainer.empty();
            photoPreviewContainer.addClass('empty-state');
            searchResults.empty().hide();
            clearFormErrors();
        }

        // --- Event Listeners ---

        // Photo Upload Preview
        photoUploadInput.on('change', function() {
            photoPreviewContainer.empty();
            photoPreviewContainer.removeClass('empty-state');
            clearFormErrors();

            const files = this.files;
            if (files.length === 0) {
                photoPreviewContainer.addClass('empty-state');
                return;
            }

            if (files.length > 10) {
                displayFormError('job_card_photos', "You can upload a maximum of 10 images.");
                this.value = '';
                photoPreviewContainer.addClass('empty-state');
                return;
            }

            let allFilesValid = true;
            Array.from(files).forEach(file => {
                if (file.size > 2 * 1024 * 1024) {
                    displayFormError('job_card_photos', `File "${file.name}" is too large. Max 2MB allowed.`);
                    this.value = '';
                    photoPreviewContainer.empty();
                    photoPreviewContainer.addClass('empty-state');
                    allFilesValid = false;
                    return;
                }
                if (!file.type.startsWith('image/')) {
                    displayFormError('job_card_photos', `File "${file.name}" is not an image.`);
                    this.value = '';
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
            clearFormErrors();

            if (query.length < 2) {
                searchResults.empty().hide();
                resetCustomerSection();
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
            }, 300);
        });

        // Handle selection from search results
        searchResults.on('click', '.search-result-item:not(.disabled)', function() {
            const itemType = $(this).data('type');
            const itemData = $(this).data();

            searchResults.empty().hide();
            searchInput.val($(this).find('.result-title').text());

            clearFormErrors();

            if (itemType === 'customer') {
                resetCustomerSection();
                populateCustomerFields({
                    id: itemData.id,
                    name: itemData.name,
                    phone: itemData.phone,
                    email: itemData.email,
                    address: itemData.address
                });
                resetVehicleSection();
            } else if (itemType === 'vehicle') {
                resetVehicleSection();
                populateVehicleFields(itemData);

                if (itemData.ownerId) {
                    populateCustomerFields({
                        id: itemData.ownerId,
                        name: itemData.ownerName,
                        phone: itemData.ownerPhone,
                        email: itemData.ownerEmail,
                        address: itemData.ownerAddress
                    });
                    allCustomerInputs.forEach(input => input.prop('disabled', true));
                } else {
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
            e.preventDefault();
            clearFormErrors();

            const formData = new FormData(this);

            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white inline-block"></div> Creating...');

            $.ajax({
                url: '<?= base_url('job_intake/create_job_card') ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message + ' Job No: ' + response.job_no,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            resetEntireForm();
                            closeModal('addJobModal');
                            window.location.reload();
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
                    } catch (e) {}
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
                $msg.html('<span class="text-red-600">Please select a mechanic.</span>');
                return;
            }

            $btn.prop('disabled', true).html('<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white inline-block"></div> Assigning...');
            $msg.html('');

            $.ajax({
                url: '<?= base_url('admin/jobs/assign_mechanic') ?>/' + jobId,
                method: 'POST',
                data: { mechanic_id: mechanicId },
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.status === 'success') {
                        $msg.html('<span class="text-emerald-600">' + response.message + '</span>');
                        Swal.fire('Assigned!', response.message, 'success').then(function() {
                            location.reload();
                        });
                    } else {
                        $msg.html('<span class="text-red-600">' + (response.message || 'Error assigning mechanic') + '</span>');
                    }
                },
                error: function(xhr) {
                    let msg = 'Error assigning mechanic.';
                    try {
                        const r = JSON.parse(xhr.responseText);
                        msg = r.message || msg;
                    } catch(e) {}
                    $msg.html('<span class="text-red-600">' + msg + '</span>');
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

            $btn.prop('disabled', true).html('<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600 inline-block"></div>');
            $msg.html('');

            $.ajax({
                url: BASE_URL + '/admin/jobs/update_status/' + jobId,
                method: 'POST',
                data: { new_status: newStatus },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $msg.html('<span class="text-emerald-600">' + response.message + '</span>');
                        renderStatusSection(response.new_status, response.valid_transitions, jobId);
                        $('#detail_job_status').text(response.new_status);
                    } else {
                        $msg.html('<span class="text-red-600">' + (response.message || 'Error updating status') + '</span>');
                    }
                },
                error: function(xhr) {
                    let msg = 'Error updating status.';
                    try {
                        const r = JSON.parse(xhr.responseText);
                        msg = r.message || msg;
                    } catch(e) {}
                    $msg.html('<span class="text-red-600">' + msg + '</span>');
                },
                complete: function() {
                    $btn.prop('disabled', false).text($btn.data('original-text') || 'Update');
                }
            });
        });

        // Load status history when tab is shown
        $(document).on('click', '[data-tab="status-history"]', function() {
            const jobId = window._currentJobId;
            if (!jobId) return;
            loadStatusHistory(jobId);
        });

        function renderStatusSection(status, transitions, jobId) {
            const badge = $('#detail_status_badge');
            badge.text(status);
            badge.removeClass('bg-gray-100 text-gray-700 bg-blue-100 text-blue-700 bg-emerald-100 text-emerald-700 bg-amber-100 text-amber-700 bg-red-100 text-red-700');
            const colorMap = {
                'Awaiting Assignment': 'bg-amber-100 text-amber-700',
                'Awaiting Diagnosis': 'bg-blue-100 text-blue-700',
                'Diagnosis Complete': 'bg-blue-100 text-blue-700',
                'Quote Sent': 'bg-blue-100 text-blue-700',
                'Approved': 'bg-emerald-100 text-emerald-700',
                'In Progress': 'bg-blue-100 text-blue-700',
                'Awaiting Parts': 'bg-amber-100 text-amber-700',
                'Quality Check': 'bg-blue-100 text-blue-700',
                'Ready for Invoice': 'bg-emerald-100 text-emerald-700',
                'Paid': 'bg-emerald-100 text-emerald-700',
                'Completed': 'bg-emerald-100 text-emerald-700',
                'On Hold': 'bg-amber-100 text-amber-700',
                'Rework': 'bg-red-100 text-red-700',
                'Cancelled': 'bg-red-100 text-red-700'
            };
            badge.addClass(colorMap[status] || 'bg-gray-100 text-gray-700');

            const container = $('#transitionButtonsContainer');
            container.empty();

            if (transitions && transitions.length > 0) {
                const label = $('<div class="text-xs font-medium text-gray-500 mb-1">Actions:</div>');
                container.append(label);
                transitions.forEach(function(nextStatus) {
                    const btn = $('<button class="bg-white border border-gray-300 border-solid text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1 me-1 mb-1 btn-status-transition"></button>');
                    btn.data('job-id', jobId);
                    btn.data('new-status', nextStatus);
                    btn.data('original-text', nextStatus);
                    btn.text(nextStatus);
                    container.append(btn);
                });
            } else {
                container.append('<span class="text-xs text-gray-500">No status transitions available for your role.</span>');
            }
        }

        function loadStatusHistory(jobId) {
            const tbody = $('#detail_status_history');
            tbody.html('<tr class="divide-y divide-gray-200"><td colspan="5" class="px-4 py-3 text-sm text-gray-500 text-center">Loading history...</td></tr>');

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
                                <tr class="divide-y divide-gray-200">
                                    <td class="px-4 py-3 text-sm text-gray-900">${entry.from_status}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${entry.to_status}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${entry.changed_by_name || 'N/A'}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${date}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${entry.notes || ''}</td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr class="divide-y divide-gray-200"><td colspan="5" class="px-4 py-3 text-sm text-gray-500 text-center">No status history recorded.</td></tr>');
                    }
                },
                error: function() {
                    tbody.html('<tr class="divide-y divide-gray-200"><td colspan="5" class="px-4 py-3 text-sm text-red-600 text-center">Failed to load status history.</td></tr>');
                }
            });
        }
    });
</script>
