<style>
    .search-results-dropdown {
        position: absolute;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #d1d5db;
        border-top: none;
        z-index: 1000;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }

    .search-results-dropdown div {
        padding: 0.6rem 0.9rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f2f5;
        font-size: 0.85rem;
    }

    .search-results-dropdown div:last-child {
        border-bottom: none;
    }

    .search-results-dropdown div:hover {
        background-color: #eef2ff;
    }

    .search-results-dropdown .result-title {
        font-weight: 600;
        color: #4f46e5;
    }

    .search-results-dropdown .result-subtitle {
        font-size: 0.78rem;
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
        min-height: 90px;
        border: 1px dashed #d1d5db;
        border-radius: 8px;
        padding: 10px;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: #6b7280;
        font-size: 0.85rem;
    }

    .photo-preview-container.empty-state::before {
        content: "No photos selected.";
    }

    .photo-preview-container img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .error-message {
        color: #dc2626;
        font-size: 0.8rem;
        margin-top: 0.2rem;
    }

    input.border-red-500,
    select.border-red-500,
    textarea.border-red-500 {
        border-color: #ef4444 !important;
    }
</style>

<!-- Add Job Modal -->
<div id="addJobModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('addJobModal')"></div>
<div id="addJobModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <!-- <h5 class="text-lg font-semibold text-gray-900">New Job Intake</h5> -->
            <button type="button" onclick="closeModal('addJobModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <form id="jobIntakeForm" method="POST" action="<?= base_url('job_intake/create_job_card') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer Section -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h6 class="text-sm font-semibold text-gray-700">Customer</h6>
                            <!-- <span id="customer_status" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">New</span> -->
                        </div>

                        <div class="mb-3 relative">
                            <label for="customer_search" class="block text-xs font-medium text-gray-600 mb-1">Search by phone or name</label>
                            <input type="text" id="customer_search" autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                            <div id="customer_search_results" class="search-results-dropdown hidden"></div>
                        </div>

                        <input type="hidden" name="customer_id" id="customer_id" value="new">

                        <div id="new_customer_fields" class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="new_customer_first_name" class="block text-xs font-medium text-gray-600 mb-1">First Name</label>
                                    <input type="text" id="new_customer_first_name" name="new_customer_first_name" maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_customer_first_name" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_customer_last_name" class="block text-xs font-medium text-gray-600 mb-1">Last Name</label>
                                    <input type="text" id="new_customer_last_name" name="new_customer_last_name" maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_customer_last_name" class="error-message"></div>
                                </div>
                            </div>
                            <div>
                                <label for="new_customer_phone_number" class="block text-xs font-medium text-gray-600 mb-1">Phone Number</label>
                                <input type="text" id="new_customer_phone_number" name="new_customer_phone_number" maxlength="15" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                <div id="error_new_customer_phone_number" class="error-message"></div>
                            </div>
                            <div>
                                <label for="new_customer_email" class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                                <input type="email" id="new_customer_email" name="new_customer_email" maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
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

                        <div class="mb-3 relative">
                            <label for="vehicle_search" class="block text-xs font-medium text-gray-600 mb-1">Search by registration number</label>
                            <input type="text" id="vehicle_search" autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                            <div id="vehicle_search_results" class="search-results-dropdown hidden"></div>
                        </div>

                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="new">

                        <div id="new_vehicle_fields" class="space-y-3">
                            <div>
                                <label for="new_vehicle_license_plate" class="block text-xs font-medium text-gray-600 mb-1">License Plate</label>
                                <input type="text" id="new_vehicle_license_plate" name="new_vehicle_license_plate" maxlength="20" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                <div id="error_new_vehicle_license_plate" class="error-message"></div>
                            </div>
                            <div>
                                <label for="new_vehicle_vin" class="block text-xs font-medium text-gray-600 mb-1">VIN (17 characters)</label>
                                <input type="text" id="new_vehicle_vin" name="new_vehicle_vin" maxlength="17" minlength="17" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                <div id="error_new_vehicle_vin" class="error-message"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="new_vehicle_make" class="block text-xs font-medium text-gray-600 mb-1">Make</label>
                                    <input type="text" id="new_vehicle_make" name="new_vehicle_make" maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_make" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_vehicle_model" class="block text-xs font-medium text-gray-600 mb-1">Model</label>
                                    <input type="text" id="new_vehicle_model" name="new_vehicle_model" maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_model" class="error-message"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label for="new_vehicle_year" class="block text-xs font-medium text-gray-600 mb-1">Year</label>
                                    <input type="number" id="new_vehicle_year" name="new_vehicle_year" min="1900" max="<?= date('Y') + 1 ?>" maxlength="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_year" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_vehicle_engine_number" class="block text-xs font-medium text-gray-600 mb-1">Engine No.</label>
                                    <input type="text" id="new_vehicle_engine_number" name="new_vehicle_engine_number" maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_engine_number" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_vehicle_chassis_number" class="block text-xs font-medium text-gray-600 mb-1">Chassis No.</label>
                                    <input type="text" id="new_vehicle_chassis_number" name="new_vehicle_chassis_number" maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_chassis_number" class="error-message"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
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
                                    </select>
                                    <div id="error_new_vehicle_transmission" class="error-message"></div>
                                </div>
                                <div>
                                    <label for="new_vehicle_color" class="block text-xs font-medium text-gray-600 mb-1">Color</label>
                                    <input type="text" id="new_vehicle_color" name="new_vehicle_color" maxlength="30" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                                    <div id="error_new_vehicle_color" class="error-message"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Details -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mt-4">
                    <h6 class="text-sm font-semibold text-gray-700 mb-3">Job Details</h6>

                    <div class="mb-3">
                        <label for="reported_problem" class="block text-xs font-medium text-gray-600 mb-1">Reported Problem</label>
                        <textarea id="reported_problem" name="reported_problem" rows="3" minlength="10" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none"></textarea>
                        <div id="error_reported_problem" class="error-message"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label for="mileage_in" class="block text-xs font-medium text-gray-600 mb-1">Mileage In</label>
                            <input type="number" id="mileage_in" name="mileage_in" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                            <div id="error_mileage_in" class="error-message"></div>
                        </div>
                        <div>
                            <label for="fuel_level" class="block text-xs font-medium text-gray-600 mb-1">Fuel Level</label>
                            <select id="fuel_level" name="fuel_level" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white">
                                <option value="">Select Fuel Level</option>
                                <option value="Empty">Empty</option>
                                <option value="1/4">1/4</option>
                                <option value="1/2">1/2</option>
                                <option value="3/4">3/4</option>
                                <option value="Full">Full</option>
                            </select>
                            <div id="error_fuel_level" class="error-message"></div>
                        </div>
                        <div>
                            <label for="assigned_service_advisor_id" class="block text-xs font-medium text-gray-600 mb-1">Service Advisor</label>
                            <select id="assigned_service_advisor_id" name="assigned_service_advisor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white">
                                <option value="">Select Advisor</option>
                                <?php if (!empty($service_advisors)): ?>
                                    <?php foreach ($service_advisors as $advisor): ?>
                                        <option value="<?= esc($advisor['id']) ?>"><?= esc($advisor['first_name'] . ' ' . $advisor['last_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div id="error_assigned_service_advisor_id" class="error-message"></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="assigned_mechanic_id" class="block text-xs font-medium text-gray-600 mb-1">Assigned Mechanic (optional)</label>
                        <select id="assigned_mechanic_id" name="assigned_mechanic_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white">
                            <option value="">Unassigned</option>
                            <?php if (!empty($mechanics)): ?>
                                <?php foreach ($mechanics as $mechanic): ?>
                                    <option value="<?= esc($mechanic['id']) ?>"><?= esc($mechanic['first_name'] . ' ' . $mechanic['last_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div id="error_assigned_mechanic_id" class="error-message"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Date In</label>
                            <input type="date" value="<?= date('Y-m-d') ?>" disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 text-gray-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Time In</label>
                            <input type="time" value="<?= date('H:i') ?>" disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 text-gray-500">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="initial_damage_notes" class="block text-xs font-medium text-gray-600 mb-1">Initial Damage Notes</label>
                        <textarea id="initial_damage_notes" name="initial_damage_notes" rows="2" maxlength="500" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none"></textarea>
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

<script>
$(document).ready(function () {

    const addJobModalEl = document.getElementById('addJobModal');
    const jobIntakeForm = $('#jobIntakeForm');
    const photoPreviewContainer = $('#photo_preview_container');
    const photoUploadInput = $('#job_card_photos');

    // ---------- Customer refs ----------
    const customerId = $('#customer_id');
    const customerSearch = $('#customer_search');
    const customerResults = $('#customer_search_results');
    const customerStatus = $('#customer_status');
    const newCustomerFields = $('#new_customer_fields');
    const customerInputs = newCustomerFields.find('input, textarea');

    // ---------- Vehicle refs ----------
    const vehicleId = $('#vehicle_id');
    const vehicleSearch = $('#vehicle_search');
    const vehicleResults = $('#vehicle_search_results');
    const vehicleStatus = $('#vehicle_status');
    const newVehicleFields = $('#new_vehicle_fields');
    const vehicleInputs = newVehicleFields.find('input, select');

    const mileageIn = $('#mileage_in');
    const reportedProblem = $('#reported_problem');

    // ---------- Helpers ----------
    function clearFormErrors() {
        $('.error-message').text('');
        jobIntakeForm.find('input, select, textarea').removeClass('border-red-500');
    }

    function displayFormError(fieldId, message) {
        const errorDiv = $('#error_' + fieldId);
        if (errorDiv.length) errorDiv.text(message);
        $('#' + fieldId).addClass('border-red-500');
    }

    function resetCustomerSection() {
        customerId.val('new');
        customerStatus.text('New').removeClass('bg-emerald-100 text-emerald-700').addClass('bg-gray-100 text-gray-700');
        customerInputs.each(function () {
            $(this).val('').prop('disabled', false);
        });
    }

    function resetVehicleSection() {
        vehicleId.val('new');
        vehicleStatus.text('New').removeClass('bg-emerald-100 text-emerald-700').addClass('bg-gray-100 text-gray-700');
        vehicleInputs.each(function () {
            $(this).val('').prop('disabled', false);
        });
    }

    function populateCustomerFields(c) {
        customerId.val(c.id);
        customerStatus.text('Existing').removeClass('bg-gray-100 text-gray-700').addClass('bg-emerald-100 text-emerald-700');
        const parts = (c.name || '').split(' ');
        $('#new_customer_first_name').val(parts[0] || '');
        $('#new_customer_last_name').val(parts.slice(1).join(' ') || '');
        $('#new_customer_phone_number').val(c.phone || '');
        $('#new_customer_email').val(c.email || '');
        $('#new_customer_address').val(c.address || '');
        customerInputs.prop('disabled', true);
    }

    function populateVehicleFields(v) {
        vehicleId.val(v.id);
        vehicleStatus.text('Existing').removeClass('bg-gray-100 text-gray-700').addClass('bg-emerald-100 text-emerald-700');
        $('#new_vehicle_license_plate').val(v.registration_number || '');
        $('#new_vehicle_vin').val(v.vin || '');
        $('#new_vehicle_make').val(v.make || '');
        $('#new_vehicle_model').val(v.model || '');
        $('#new_vehicle_year').val(v.year_of_manufacture || '');
        $('#new_vehicle_engine_number').val(v.engine_number || '');
        $('#new_vehicle_chassis_number').val(v.chassis_number || '');
        $('#new_vehicle_fuel_type').val(v.fuel_type || '');
        $('#new_vehicle_transmission').val(v.transmission || '');
        $('#new_vehicle_color').val(v.color || '');
        vehicleInputs.prop('disabled', true);

        if (v.mileage) mileageIn.val(v.mileage);
        if (v.reported_problem) reportedProblem.val(v.reported_problem);
    }

    function resetEntireForm() {
        jobIntakeForm[0].reset();
        resetCustomerSection();
        resetVehicleSection();
        photoPreviewContainer.empty().addClass('empty-state');
        customerResults.empty().hide();
        vehicleResults.empty().hide();
        clearFormErrors();
    }

    // Reset form whenever modal closes (backdrop click or X button)
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            if (m.type === 'attributes' && m.attributeName === 'class') {
                if (addJobModalEl.classList.contains('hidden')) {
                    resetEntireForm();
                }
            }
        });
    });
    observer.observe(addJobModalEl, { attributes: true });

    // ---------- Customer search ----------
    let customerTimeout;
    customerSearch.on('keyup', function () {
        clearTimeout(customerTimeout);
        const query = $(this).val().trim();

        if (query.length < 2) {
            customerResults.empty().hide();
            return;
        }

        customerTimeout = setTimeout(function () {
            $.ajax({
                url: '<?= base_url("job_intake/search") ?>',
                method: 'GET',
                data: { query: query, type: 'customer' },
                dataType: 'json',
                success: function (res) {
                    customerResults.empty();
                    const customers = res.customers || [];
                    if (customers.length === 0) {
                        customerResults.append('<div class="disabled">No matches. Will create new.</div>');
                    } else {
                        customers.forEach(function (c) {
                            const item = $('<div></div>')
                                .html(`<div class="result-title">${c.name}</div><div class="result-subtitle">${c.phone || ''}</div>`)
                                .data('customer', c);
                            customerResults.append(item);
                        });
                    }
                    customerResults.show();
                },
                error: function () {
                    customerResults.empty().hide();
                }
            });
        }, 300);
    });

    customerResults.on('click', 'div:not(.disabled)', function () {
        const c = $(this).data('customer');
        if (!c) return;
        customerSearch.val(c.name);
        customerResults.empty().hide();
        resetCustomerSection();
        populateCustomerFields(c);
    });

    // ---------- Vehicle search ----------
    let vehicleTimeout;
    vehicleSearch.on('keyup', function () {
        clearTimeout(vehicleTimeout);
        const query = $(this).val().trim();

        if (query.length < 2) {
            vehicleResults.empty().hide();
            return;
        }

        vehicleTimeout = setTimeout(function () {
            $.ajax({
                url: '<?= base_url("job_intake/search") ?>',
                method: 'GET',
                data: { query: query, type: 'vehicle' },
                dataType: 'json',
                success: function (res) {
                    vehicleResults.empty();
                    const vehicles = res.vehicles || [];
                    if (vehicles.length === 0) {
                        vehicleResults.append('<div class="disabled">No matches. Will create new.</div>');
                    } else {
                        vehicles.forEach(function (v) {
                            const item = $('<div></div>')
                                .html(`<div class="result-title">${v.registration_number}</div><div class="result-subtitle">${v.make || ''} ${v.model || ''} — ${v.owner_name || ''}</div>`)
                                .data('vehicle', v);
                            vehicleResults.append(item);
                        });
                    }
                    vehicleResults.show();
                },
                error: function () {
                    vehicleResults.empty().hide();
                }
            });
        }, 300);
    });

    vehicleResults.on('click', 'div:not(.disabled)', function () {
        const v = $(this).data('vehicle');
        if (!v) return;
        vehicleSearch.val(v.registration_number);
        vehicleResults.empty().hide();
        resetVehicleSection();
        populateVehicleFields(v);

        // Auto-fill owning customer, if provided by the search endpoint
        if (v.owner_id) {
            customerSearch.val(v.owner_name || '');
            resetCustomerSection();
            populateCustomerFields({
                id: v.owner_id,
                name: v.owner_name,
                phone: v.owner_phone,
                email: v.owner_email,
                address: v.owner_address
            });
        }
    });

    // Hide dropdowns when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#customer_search, #customer_search_results').length) {
            customerResults.hide();
        }
        if (!$(e.target).closest('#vehicle_search, #vehicle_search_results').length) {
            vehicleResults.hide();
        }
    });

    // ---------- Photo preview ----------
    photoUploadInput.on('change', function () {
        photoPreviewContainer.empty().removeClass('empty-state');
        clearFormErrors();

        const files = this.files;
        if (files.length === 0) {
            photoPreviewContainer.addClass('empty-state');
            return;
        }
        if (files.length > 10) {
            displayFormError('job_card_photos', 'Maximum 10 images allowed.');
            this.value = '';
            photoPreviewContainer.addClass('empty-state');
            return;
        }

        let valid = true;
        Array.from(files).forEach(function (file) {
            if (file.size > 2 * 1024 * 1024) {
                displayFormError('job_card_photos', `"${file.name}" exceeds 2MB.`);
                valid = false;
                return;
            }
            if (!file.type.startsWith('image/')) {
                displayFormError('job_card_photos', `"${file.name}" is not an image.`);
                valid = false;
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                photoPreviewContainer.append(`<img src="${e.target.result}" alt="preview">`);
            };
            reader.readAsDataURL(file);
        });

        if (!valid) {
            photoUploadInput.val('');
            photoPreviewContainer.empty().addClass('empty-state');
        }
    });

    // ---------- Form submission ----------
    jobIntakeForm.on('submit', function (e) {
        e.preventDefault();
        clearFormErrors();

        const formData = new FormData(this);
        const submitBtn = jobIntakeForm.find('button[type="submit"]');
        const originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="animate-spin rounded-full h-4 w-4 border-b-2 border-white inline-block"></span> Creating...');

        $.ajax({
            url: jobIntakeForm.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Success!', (res.message || 'Job created.') + (res.job_no ? ' Job No: ' + res.job_no : ''), 'success')
                            .then(function () {
                                resetEntireForm();
                                closeModal('addJobModal');
                                window.location.reload();
                            });
                    } else {
                        alert((res.message || 'Job created.') + (res.job_no ? ' Job No: ' + res.job_no : ''));
                        resetEntireForm();
                        closeModal('addJobModal');
                        window.location.reload();
                    }
                } else {
                    handleErrors(res);
                }
            },
            error: function (xhr) {
                let res = {};
                try { res = JSON.parse(xhr.responseText); } catch (e) {}
                handleErrors(res);
            },
            complete: function () {
                submitBtn.prop('disabled', false).html(originalHtml);
            }
        });

        function handleErrors(res) {
            const errors = (res.errors) || (res.messages && res.messages.errors) || null;
            const message = res.message || (res.messages && res.messages.message) || 'An error occurred.';

            if (errors) {
                $.each(errors, function (field, msg) {
                    displayFormError(field, msg);
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', errors ? 'Please correct the highlighted fields.' : message, 'error');
            } else {
                alert(errors ? 'Please correct the highlighted fields.' : message);
            }
        }
    });

});
</script>