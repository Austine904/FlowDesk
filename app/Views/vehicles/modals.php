<!-- Add Vehicle Modal -->
<div id="addVehicleModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addVehicleForm" action="<?= base_url('admin/vehicles/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Registration No</label>
                            <input type="text" name="registration_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Make</label>
                            <input type="text" name="make" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Model</label>
                            <input type="text" name="model" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Year</label>
                            <input type="number" name="year" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Color</label>
                            <input type="text" name="color" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Engine No</label>
                            <input type="text" name="engine_no" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Chassis No</label>
                            <input type="text" name="chassis_no" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Vehicle
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Vehicle Details Modal -->
<div id="viewVehicleModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vehicle Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Registration No:</strong> <span id="v_registration_no"></span></p>
                        <p><strong>Make:</strong> <span id="v_make"></span></p>
                        <p><strong>Model:</strong> <span id="v_model"></span></p>
                        <p><strong>Year:</strong> <span id="v_year"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Color:</strong> <span id="v_color"></span></p>
                        <p><strong>Engine No:</strong> <span id="v_engine_no"></span></p>
                        <p><strong>Chassis No:</strong> <span id="v_chassis_no"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-labelledby="editVehicleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editVehicleForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehicleLabel">Edit Vehicle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" id="edit_vehicle_id" name="id">

                    <div class="col-md-6">
                        <label>Registration Number</label>
                        <input type="text" class="form-control" id="edit_registration_number" name="registration_number">
                    </div>
                    <div class="col-md-6">
                        <label>Make</label>
                        <input type="text" class="form-control" id="edit_make" name="make">
                    </div>
                    <div class="col-md-6">
                        <label>Model</label>
                        <input type="text" class="form-control" id="edit_model" name="model">
                    </div>
                    <div class="col-md-6">
                        <label>Year of Manufature</label>
                        <input type="number" class="form-control" id="edit_year_of_manufacture" name="year_of_manufatcure">
                    </div>
                    <div class="col-md-6">
                        <label>Chassis Number</label>
                        <input type="text" class="form-control" id="edit_chassis_number" name="chassis_number">
                    </div>
                    <div class="col-md-6">
                        <label>Engine Number</label>
                        <input type="text" class="form-control" id="edit_engine_number" name="engine_number">
                    </div>
                    <div class="col-md-6">
                        <label>Fuel Type</label>
                        <select class="form-control" id="edit_fuel_type" name="fuel_type">
                            <option value="Petrol">Petrol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Electric">Electric</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Transmission</label>
                        <select class="form-control" id="edit_transmission" name="transmission">
                            <option value="Automatic">Automatic</option>
                            <option value="Manual">Manual</option>
                            <option value="Semi-Automatic">Semi-Automatic</option>
                            <option value="CVT">CVT</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Status</label>
                        <select class="form-control" id="edit_status" name="status">
                            <option value="On Job">On Job</option>
                            <option value="Available">Available</option>
                            <option value="Under Maintenance">Under Maintenance</option>
                            <option value="Written Off">Written Off</option>
                        </select>
                    </div>

                </div>

                <div class="col-md-6">
                    <label>Color</label>
                    <input type="text" class="form-control" id="edit_color" name="color">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
    </div>
    </form>
</div>
</div>