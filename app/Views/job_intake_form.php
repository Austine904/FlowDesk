<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4">Job Intake Form</h3>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url('job_intake/create_job_card') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_search" class="form-label">Customer</label>
                        <input type="text" class="form-control" id="customer_search" name="customer_search" placeholder="Search by phone or name">
                        <input type="hidden" name="customer_id" id="customer_id">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vehicle_search" class="form-label">Vehicle</label>
                        <input type="text" class="form-control" id="vehicle_search" name="vehicle_search" placeholder="Search by registration number">
                        <input type="hidden" name="vehicle_id" id="vehicle_id">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="mileage_in" class="form-label">Mileage In</label>
                        <input type="number" class="form-control" id="mileage_in" name="mileage_in">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fuel_level" class="form-label">Fuel Level</label>
                        <select class="form-select" id="fuel_level" name="fuel_level">
                            <option value="">Select Fuel Level</option>
                            <option value="Empty">Empty</option>
                            <option value="1/4">1/4</option>
                            <option value="1/2">1/2</option>
                            <option value="3/4">3/4</option>
                            <option value="Full">Full</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date_in" class="form-label">Date In</label>
                        <input type="date" class="form-control" id="date_in" name="date_in" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="time_in" class="form-label">Time In</label>
                        <input type="time" class="form-control" id="time_in" name="time_in" value="<?= date('H:i') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="initial_damage_notes" class="form-label">Initial Damage Notes</label>
                    <textarea class="form-control" id="initial_damage_notes" name="initial_damage_notes" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="photos" class="form-label">Photos</label>
                    <input class="form-control" type="file" id="photos" name="photos[]" multiple accept="image/*">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Job Card</button>
                    <a href="<?= base_url('admin/jobs') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
