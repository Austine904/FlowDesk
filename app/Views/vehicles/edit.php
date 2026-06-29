<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Edit Vehicle</h3>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/vehicles/update/' . $vehicle['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="registration_number" class="form-label">Registration Number</label>
                            <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?= esc($vehicle['registration_number']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vin" class="form-label">VIN</label>
                            <input type="text" class="form-control" id="vin" name="vin" value="<?= esc($vehicle['vin'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="make" class="form-label">Make</label>
                            <input type="text" class="form-control" id="make" name="make" value="<?= esc($vehicle['make']) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model" value="<?= esc($vehicle['model']) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="year_of_manufacture" class="form-label">Year of Manufacture</label>
                            <input type="number" class="form-control" id="year_of_manufacture" name="year_of_manufacture" value="<?= esc($vehicle['year_of_manufacture'] ?? '') ?>" min="1900" max="<?= date('Y') + 1 ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="engine_number" class="form-label">Engine Number</label>
                            <input type="text" class="form-control" id="engine_number" name="engine_number" value="<?= esc($vehicle['engine_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="chassis_number" class="form-label">Chassis Number</label>
                            <input type="text" class="form-control" id="chassis_number" name="chassis_number" value="<?= esc($vehicle['chassis_number'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select" id="fuel_type" name="fuel_type">
                                <option value="">Select Fuel Type</option>
                                <option value="Petrol" <?= ($vehicle['fuel_type'] ?? '') === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                                <option value="Diesel" <?= ($vehicle['fuel_type'] ?? '') === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                                <option value="Electric" <?= ($vehicle['fuel_type'] ?? '') === 'Electric' ? 'selected' : '' ?>>Electric</option>
                                <option value="Hybrid" <?= ($vehicle['fuel_type'] ?? '') === 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="transmission" class="form-label">Transmission</label>
                            <select class="form-select" id="transmission" name="transmission">
                                <option value="">Select Transmission</option>
                                <option value="Manual" <?= ($vehicle['transmission'] ?? '') === 'Manual' ? 'selected' : '' ?>>Manual</option>
                                <option value="Automatic" <?= ($vehicle['transmission'] ?? '') === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                                <option value="CVT" <?= ($vehicle['transmission'] ?? '') === 'CVT' ? 'selected' : '' ?>>CVT</option>
                                <option value="Semi-Automatic" <?= ($vehicle['transmission'] ?? '') === 'Semi-Automatic' ? 'selected' : '' ?>>Semi-Automatic</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="color" name="color" value="<?= esc($vehicle['color'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mileage" class="form-label">Mileage</label>
                            <input type="number" class="form-control" id="mileage" name="mileage" value="<?= esc($vehicle['mileage'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="owner_id" class="form-label">Owner ID</label>
                            <input type="number" class="form-control" id="owner_id" name="owner_id" value="<?= esc($vehicle['owner_id'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Vehicle</button>
                        <a href="<?= base_url('admin/vehicles') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
