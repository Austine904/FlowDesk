<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto py-6 px-4">
    <h3 class="text-xl font-bold text-gray-900 mb-6">Job Intake Form</h3>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <form method="POST" action="<?= base_url('job_intake/create_job_card') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- CUSTOMER & VEHICLE -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="customer_search" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="customer_search" name="customer_search" placeholder="Search by phone or name">
                            <button type="button" class="px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors" id="toggle_new_customer">+ New</button>
                        </div>
                        <input type="hidden" name="customer_id" id="customer_id">
                    </div>
                    <div>
                        <label for="vehicle_search" class="block text-sm font-medium text-gray-700 mb-1">Vehicle</label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="vehicle_search" name="vehicle_search" placeholder="Search by registration number">
                            <button type="button" class="px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors" id="toggle_new_vehicle">+ New</button>
                        </div>
                        <input type="hidden" name="vehicle_id" id="vehicle_id">
                    </div>
                </div>

                <!-- NEW CUSTOMER FIELDS -->
                <div id="new_customer_fields" class="border border-gray-200 rounded-lg p-4 mb-4 hidden">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">New Customer</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="new_customer_first_name" class="block text-xs font-medium text-gray-600 mb-1">First Name</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_customer_first_name" name="new_customer_first_name" maxlength="50">
                        </div>
                        <div>
                            <label for="new_customer_last_name" class="block text-xs font-medium text-gray-600 mb-1">Last Name</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_customer_last_name" name="new_customer_last_name" maxlength="50">
                        </div>
                        <div>
                            <label for="new_customer_phone_number" class="block text-xs font-medium text-gray-600 mb-1">Phone Number</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_customer_phone_number" name="new_customer_phone_number" maxlength="15">
                        </div>
                        <div>
                            <label for="new_customer_email" class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                            <input type="email" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_customer_email" name="new_customer_email" maxlength="255">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="new_customer_address" class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                        <textarea class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_customer_address" name="new_customer_address" rows="2"></textarea>
                    </div>
                </div>

                <!-- NEW VEHICLE FIELDS -->
                <div id="new_vehicle_fields" class="border border-gray-200 rounded-lg p-4 mb-4 hidden">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">New Vehicle</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="new_vehicle_license_plate" class="block text-xs font-medium text-gray-600 mb-1">License Plate</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_license_plate" name="new_vehicle_license_plate" maxlength="20">
                        </div>
                        <div>
                            <label for="new_vehicle_vin" class="block text-xs font-medium text-gray-600 mb-1">VIN (17 characters)</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_vin" name="new_vehicle_vin" maxlength="17" minlength="17">
                        </div>
                        <div>
                            <label for="new_vehicle_make" class="block text-xs font-medium text-gray-600 mb-1">Make</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_make" name="new_vehicle_make" maxlength="50">
                        </div>
                        <div>
                            <label for="new_vehicle_model" class="block text-xs font-medium text-gray-600 mb-1">Model</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_model" name="new_vehicle_model" maxlength="50">
                        </div>
                        <div>
                            <label for="new_vehicle_year" class="block text-xs font-medium text-gray-600 mb-1">Year</label>
                            <input type="number" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_year" name="new_vehicle_year" min="1900" max="<?= date('Y') + 1 ?>">
                        </div>
                        <div>
                            <label for="new_vehicle_engine_number" class="block text-xs font-medium text-gray-600 mb-1">Engine Number</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_engine_number" name="new_vehicle_engine_number" maxlength="50">
                        </div>
                        <div>
                            <label for="new_vehicle_chassis_number" class="block text-xs font-medium text-gray-600 mb-1">Chassis Number</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_chassis_number" name="new_vehicle_chassis_number" maxlength="50">
                        </div>
                        <div>
                            <label for="new_vehicle_fuel_type" class="block text-xs font-medium text-gray-600 mb-1">Fuel Type</label>
                            <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_fuel_type" name="new_vehicle_fuel_type">
                                <option value="">Select</option>
                                <option value="Petrol">Petrol</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Electric">Electric</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div>
                            <label for="new_vehicle_transmission" class="block text-xs font-medium text-gray-600 mb-1">Transmission</label>
                            <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_transmission" name="new_vehicle_transmission">
                                <option value="">Select</option>
                                <option value="Manual">Manual</option>
                                <option value="Automatic">Automatic</option>
                                <option value="CVT">CVT</option>
                            </select>
                        </div>
                        <div>
                            <label for="new_vehicle_color" class="block text-xs font-medium text-gray-600 mb-1">Color</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="new_vehicle_color" name="new_vehicle_color" maxlength="30">
                        </div>
                    </div>
                </div>

                <!-- JOB DETAILS -->
                <div class="mb-4">
                    <label for="reported_problem" class="block text-sm font-medium text-gray-700 mb-1">Reported Problem</label>
                    <textarea class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="reported_problem" name="reported_problem" rows="3" minlength="10" required></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="mileage_in" class="block text-sm font-medium text-gray-700 mb-1">Mileage In</label>
                        <input type="number" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="mileage_in" name="mileage_in" min="0" required>
                    </div>
                    <div>
                        <label for="fuel_level" class="block text-sm font-medium text-gray-700 mb-1">Fuel Level</label>
                        <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="fuel_level" name="fuel_level" required>
                            <option value="">Select Fuel Level</option>
                            <option value="Empty">Empty</option>
                            <option value="1/4">1/4</option>
                            <option value="1/2">1/2</option>
                            <option value="3/4">3/4</option>
                            <option value="Full">Full</option>
                        </select>
                    </div>
                    <div>
                        <label for="assigned_service_advisor_id" class="block text-sm font-medium text-gray-700 mb-1">Service Advisor</label>
                        <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="assigned_service_advisor_id" name="assigned_service_advisor_id" required>
                            <option value="">Select Advisor</option>
                            <?php if (!empty($service_advisors)): ?>
                                <?php foreach ($service_advisors as $advisor): ?>
                                    <option value="<?= esc($advisor['id']) ?>"><?= esc($advisor['first_name'] . ' ' . $advisor['last_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="assigned_mechanic_id" class="block text-sm font-medium text-gray-700 mb-1">Assigned Mechanic <span class="text-gray-400 font-normal">(optional)</span></label>
                    <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="assigned_mechanic_id" name="assigned_mechanic_id">
                        <option value="">Unassigned</option>
                        <?php if (!empty($mechanics)): ?>
                            <?php foreach ($mechanics as $mechanic): ?>
                                <option value="<?= esc($mechanic['id']) ?>"><?= esc($mechanic['first_name'] . ' ' . $mechanic['last_name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="date_in" class="block text-sm font-medium text-gray-700 mb-1">Date In</label>
                        <input type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-gray-50 text-gray-500" id="date_in" value="<?= date('Y-m-d') ?>" disabled>
                    </div>
                    <div>
                        <label for="time_in" class="block text-sm font-medium text-gray-700 mb-1">Time In</label>
                        <input type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-gray-50 text-gray-500" id="time_in" value="<?= date('H:i') ?>" disabled>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="initial_damage_notes" class="block text-sm font-medium text-gray-700 mb-1">Initial Damage Notes</label>
                    <textarea class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" id="initial_damage_notes" name="initial_damage_notes" rows="3" maxlength="500"></textarea>
                </div>

                <div class="mb-6">
                    <label for="job_card_photos" class="block text-sm font-medium text-gray-700 mb-1">Photos</label>
                    <input type="file" id="job_card_photos" name="job_card_photos[]" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">Create Job Card</button>
                    <a href="<?= base_url('admin/jobs') ?>" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const customerIdInput = document.getElementById('customer_id');
    const vehicleIdInput = document.getElementById('vehicle_id');
    const newCustomerFields = document.getElementById('new_customer_fields');
    const newVehicleFields = document.getElementById('new_vehicle_fields');

    document.getElementById('toggle_new_customer').addEventListener('click', function () {
        const showing = !newCustomerFields.classList.contains('hidden');
        newCustomerFields.classList.toggle('hidden', showing);
        newCustomerFields.querySelectorAll('input, textarea').forEach(el => el.disabled = showing);
        customerIdInput.value = showing ? '' : 'new';
        this.textContent = showing ? '+ New' : 'Cancel';
    });

    document.getElementById('toggle_new_vehicle').addEventListener('click', function () {
        const showing = !newVehicleFields.classList.contains('hidden');
        newVehicleFields.classList.toggle('hidden', showing);
        newVehicleFields.querySelectorAll('input, select').forEach(el => el.disabled = showing);
        vehicleIdInput.value = showing ? '' : 'new';
        this.textContent = showing ? '+ New' : 'Cancel';
    });

    newCustomerFields.querySelectorAll('input, textarea').forEach(el => el.disabled = true);
    newVehicleFields.querySelectorAll('input, select').forEach(el => el.disabled = true);
});
</script>
<?= $this->endSection() ?>
