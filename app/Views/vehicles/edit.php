<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle — FlowDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans p-4">
    <div class="max-w-3xl mx-auto py-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Edit Vehicle</h3>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <form method="POST" action="<?= base_url('admin/vehicles/update/' . $vehicle['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                            <input type="text" id="registration_number" name="registration_number" value="<?= esc($vehicle['registration_number']) ?>" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="vin" class="block text-sm font-medium text-gray-700 mb-1">VIN</label>
                            <input type="text" id="vin" name="vin" value="<?= esc($vehicle['vin'] ?? '') ?>"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="make" class="block text-sm font-medium text-gray-700 mb-1">Make</label>
                            <input type="text" id="make" name="make" value="<?= esc($vehicle['make']) ?>" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                            <input type="text" id="model" name="model" value="<?= esc($vehicle['model']) ?>" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="year_of_manufacture" class="block text-sm font-medium text-gray-700 mb-1">Year of Manufacture</label>
                            <input type="number" id="year_of_manufacture" name="year_of_manufacture" value="<?= esc($vehicle['year_of_manufacture'] ?? '') ?>" min="1900" max="<?= date('Y') + 1 ?>"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="engine_number" class="block text-sm font-medium text-gray-700 mb-1">Engine Number</label>
                            <input type="text" id="engine_number" name="engine_number" value="<?= esc($vehicle['engine_number'] ?? '') ?>"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="chassis_number" class="block text-sm font-medium text-gray-700 mb-1">Chassis Number</label>
                            <input type="text" id="chassis_number" name="chassis_number" value="<?= esc($vehicle['chassis_number'] ?? '') ?>"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-1">Fuel Type</label>
                            <select id="fuel_type" name="fuel_type"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Select Fuel Type</option>
                                <option value="Petrol" <?= ($vehicle['fuel_type'] ?? '') === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                                <option value="Diesel" <?= ($vehicle['fuel_type'] ?? '') === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                                <option value="Electric" <?= ($vehicle['fuel_type'] ?? '') === 'Electric' ? 'selected' : '' ?>>Electric</option>
                                <option value="Hybrid" <?= ($vehicle['fuel_type'] ?? '') === 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                            </select>
                        </div>
                        <div>
                            <label for="transmission" class="block text-sm font-medium text-gray-700 mb-1">Transmission</label>
                            <select id="transmission" name="transmission"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Select Transmission</option>
                                <option value="Manual" <?= ($vehicle['transmission'] ?? '') === 'Manual' ? 'selected' : '' ?>>Manual</option>
                                <option value="Automatic" <?= ($vehicle['transmission'] ?? '') === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                                <option value="CVT" <?= ($vehicle['transmission'] ?? '') === 'CVT' ? 'selected' : '' ?>>CVT</option>
                                <option value="Semi-Automatic" <?= ($vehicle['transmission'] ?? '') === 'Semi-Automatic' ? 'selected' : '' ?>>Semi-Automatic</option>
                            </select>
                        </div>
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input type="text" id="color" name="color" value="<?= esc($vehicle['color'] ?? '') ?>"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="mileage" class="block text-sm font-medium text-gray-700 mb-1">Mileage</label>
                            <input type="number" id="mileage" name="mileage" value="<?= esc($vehicle['mileage'] ?? '') ?>"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-1">Owner ID</label>
                            <input type="number" id="owner_id" name="owner_id" value="<?= esc($vehicle['owner_id'] ?? '') ?>"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">Update Vehicle</button>
                        <a href="<?= base_url('admin/vehicles') ?>" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
