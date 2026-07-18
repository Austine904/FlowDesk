<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4">Job Intake Form</h3>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url('job_intake/create_job_card') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- CUSTOMER -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_search" class="form-label">Customer</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="customer_search" name="customer_search" placeholder="Search by phone or name">
                            <button type="button" class="btn btn-outline-secondary" id="toggle_new_customer">+ New</button>
                        </div>
                        <input type="hidden" name="customer_id" id="customer_id">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vehicle_search" class="form-label">Vehicle</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="vehicle_search" name="vehicle_search" placeholder="Search by registration number">
                            <button type="button" class="btn btn-outline-secondary" id="toggle_new_vehicle">+ New</button>
                        </div>
                        <input type="hidden" name="vehicle_id" id="vehicle_id">
                    </div>
                </div>

                <!-- NEW CUSTOMER FIELDS -->
                <div id="new_customer_fields" class="border rounded p-3 mb-3 d-none">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_customer_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="new_customer_first_name" name="new_customer_first_name" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_customer_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="new_customer_last_name" name="new_customer_last_name" maxlength="50">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_customer_phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="new_customer_phone_number" name="new_customer_phone_number" maxlength="15">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="new_customer_email" name="new_customer_email" maxlength="255">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_customer_address" class="form-label">Address</label>
                        <textarea class="form-control" id="new_customer_address" name="new_customer_address" rows="2"></textarea>
                    </div>
                </div>

                <!-- NEW VEHICLE FIELDS -->
                <div id="new_vehicle_fields" class="border rounded p-3 mb-3 d-none">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_vehicle_license_plate" class="form-label">License Plate</label>
                            <input type="text" class="form-control" id="new_vehicle_license_plate" name="new_vehicle_license_plate" maxlength="20">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_vehicle_vin" class="form-label">VIN (17 characters)</label>
                            <input type="text" class="form-control" id="new_vehicle_vin" name="new_vehicle_vin" maxlength="17" minlength="17">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_vehicle_make" class="form-label">Make</label>
                            <input type="text" class="form-control" id="new_vehicle_make" name="new_vehicle_make" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_vehicle_model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="new_vehicle_model" name="new_vehicle_model" maxlength="50">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="new_vehicle_year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="new_vehicle_year" name="new_vehicle_year"
                                   min="1900" max="<?= date('Y') + 1 ?>" maxlength="4">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="new_vehicle_engine_number" class="form-label">Engine Number</label>
                            <input type="text" class="form-control" id="new_vehicle_engine_number" name="new_vehicle_engine_number" maxlength="50">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="new_vehicle_chassis_number" class="form-label">Chassis Number</label>
                            <input type="text" class="form-control" id="new_vehicle_chassis_number" name="new_vehicle_chassis_number" maxlength="50">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="new_vehicle_fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select" id="new_vehicle_fuel_type" name="new_vehicle_fuel_type">
                                <option value="">Select</option>
                                <option value="Petrol">Petrol</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Electric">Electric</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="new_vehicle_transmission" class="form-label">Transmission</label>
                            <select class="form-select" id="new_vehicle_transmission" name="new_vehicle_transmission">
                                <option value="">Select</option>
                                <option value="Manual">Manual</option>
                                <option value="Automatic">Automatic</option>
                                <option value="CVT">CVT</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="new_vehicle_color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="new_vehicle_color" name="new_vehicle_color" maxlength="30">
                        </div>
                    </div>
                </div>

                <!-- JOB DETAILS -->
                <div class="mb-3">
                    <label for="reported_problem" class="form-label">Reported Problem</label>
                    <textarea class="form-control" id="reported_problem" name="reported_problem" rows="3" minlength="10" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="mileage_in" class="form-label">Mileage In</label>
                        <input type="number" class="form-control" id="mileage_in" name="mileage_in" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fuel_level" class="form-label">Fuel Level</label>
                        <select class="form-select" id="fuel_level" name="fuel_level" required>
                            <option value="">Select Fuel Level</option>
                            <option value="Empty">Empty</option>
                            <option value="1/4">1/4</option>
                            <option value="1/2">1/2</option>
                            <option value="3/4">3/4</option>
                            <option value="Full">Full</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="assigned_service_advisor_id" class="form-label">Service Advisor</label>
                        <select class="form-select" id="assigned_service_advisor_id" name="assigned_service_advisor_id" required>
                            <option value="">Select Advisor</option>
                            <?php if (!empty($service_advisors)): ?>
                                <?php foreach ($service_advisors as $advisor): ?>
                                    <option value="<?= esc($advisor['id']) ?>"><?= esc($advisor['first_name'] . ' ' . $advisor['last_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="assigned_mechanic_id" class="form-label">Assigned Mechanic (optional)</label>
                    <select class="form-select" id="assigned_mechanic_id" name="assigned_mechanic_id">
                        <option value="">Unassigned</option>
                        <?php if (!empty($mechanics)): ?>
                            <?php foreach ($mechanics as $mechanic): ?>
                                <option value="<?= esc($mechanic['id']) ?>"><?= esc($mechanic['first_name'] . ' ' . $mechanic['last_name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- date_in / time_in kept for display only -- controller sets these server-side, not read from POST -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date_in" class="form-label">Date In</label>
                        <input type="date" class="form-control" id="date_in" value="<?= date('Y-m-d') ?>" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="time_in" class="form-label">Time In</label>
                        <input type="time" class="form-control" id="time_in" value="<?= date('H:i') ?>" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="initial_damage_notes" class="form-label">Initial Damage Notes</label>
                    <textarea class="form-control" id="initial_damage_notes" name="initial_damage_notes" rows="3" maxlength="500"></textarea>
                </div>

                <div class="mb-3">
                    <label for="job_card_photos" class="form-label">Photos</label>
                    <input class="form-control" type="file" id="job_card_photos" name="job_card_photos[]" multiple accept="image/*">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Job Card</button>
                    <a href="<?= base_url('admin/jobs') ?>" class="btn btn-secondary">Cancel</a>
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
        const showing = !newCustomerFields.classList.contains('d-none');
        newCustomerFields.classList.toggle('d-none', showing);
        newCustomerFields.querySelectorAll('input, textarea').forEach(el => el.disabled = showing);
        customerIdInput.value = showing ? '' : 'new';
        this.textContent = showing ? '+ New' : 'Cancel';
    });

    document.getElementById('toggle_new_vehicle').addEventListener('click', function () {
        const showing = !newVehicleFields.classList.contains('d-none');
        newVehicleFields.classList.toggle('d-none', showing);
        newVehicleFields.querySelectorAll('input, select').forEach(el => el.disabled = showing);
        vehicleIdInput.value = showing ? '' : 'new';
        this.textContent = showing ? '+ New' : 'Cancel';
    });

    // init: new_* fields disabled until "+ New" clicked
    newCustomerFields.querySelectorAll('input, textarea').forEach(el => el.disabled = true);
    newVehicleFields.querySelectorAll('input, select').forEach(el => el.disabled = true);
});
</script>
<?= $this->endSection() ?>