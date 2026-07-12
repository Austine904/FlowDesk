<!-- Add Vehicle Modal -->
<div id="addVehicleModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('addVehicleModal')"></div>
<div id="addVehicleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">Add New Vehicle</h5>
            <button type="button" onclick="closeModal('addVehicleModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="addVehicleForm" action="<?= base_url('admin/vehicles/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registration No</label>
                        <input type="text" name="registration_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Owner</label>
                        <select name="owner_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" required>
                            <option value="">-- Select Owner --</option>
                            <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?> — <?= esc($c['phone']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Make</label>
                        <input type="text" name="make" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <input type="text" name="model" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year of Manufacture</label>
                        <input type="number" name="year_of_manufacture" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="text" name="color" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">VIN</label>
                        <input type="text" name="vin" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mileage (km)</label>
                        <input type="number" name="mileage" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Engine Number</label>
                        <input type="text" name="engine_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chassis Number</label>
                        <input type="text" name="chassis_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fuel Type</label>
                        <select name="fuel_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white">
                            <option value="">-- Select --</option>
                            <option value="Petrol">Petrol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Electric">Electric</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transmission</label>
                        <select name="transmission" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white">
                            <option value="">-- Select --</option>
                            <option value="Manual">Manual</option>
                            <option value="Automatic">Automatic</option>
                            <option value="CVT">CVT</option>
                            <option value="Semi-Automatic">Semi-Automatic</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <i class="bi bi-check-circle"></i> Save Vehicle
                </button>
                <button type="button" onclick="closeModal('addVehicleModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Vehicle Details Modal -->
<div id="viewVehicleModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('viewVehicleModal')"></div>
<div id="viewVehicleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">Vehicle Details</h5>
            <button type="button" onclick="closeModal('viewVehicleModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Registration No:</span> <span class="text-sm text-gray-900" id="v_registration_no"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Make:</span> <span class="text-sm text-gray-900" id="v_make"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Model:</span> <span class="text-sm text-gray-900" id="v_model"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Year:</span> <span class="text-sm text-gray-900" id="v_year"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">VIN:</span> <span class="text-sm text-gray-900" id="v_vin"></span></p>
                </div>
                <div>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Color:</span> <span class="text-sm text-gray-900" id="v_color"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Engine No:</span> <span class="text-sm text-gray-900" id="v_engine_no"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Chassis No:</span> <span class="text-sm text-gray-900" id="v_chassis_no"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Fuel Type:</span> <span class="text-sm text-gray-900" id="v_fuel_type"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Transmission:</span> <span class="text-sm text-gray-900" id="v_transmission"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Mileage:</span> <span class="text-sm text-gray-900" id="v_mileage"></span></p>
                    <p class="mb-2"><span class="text-xs font-medium text-gray-500">Status:</span> <span class="text-sm text-gray-900" id="v_status"></span></p>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeModal('viewVehicleModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div id="editVehicleModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('editVehicleModal')"></div>
<div id="editVehicleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900" id="editVehicleLabel">Edit Vehicle</h5>
            <button type="button" onclick="closeModal('editVehicleModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="editVehicleForm" action="" method="POST">
            <?= csrf_field() ?>
            <div class="p-6">
                <input type="hidden" id="edit_vehicle_id" name="id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_registration_number" name="registration_number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Make</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_make" name="make">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_model" name="model">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year of Manufacture</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_year_of_manufacture" name="year_of_manufacture">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chassis Number</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_chassis_number" name="chassis_number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Engine Number</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_engine_number" name="engine_number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fuel Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="edit_fuel_type" name="fuel_type">
                            <option value="Petrol">Petrol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Electric">Electric</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transmission</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="edit_transmission" name="transmission">
                            <option value="Automatic">Automatic</option>
                            <option value="Manual">Manual</option>
                            <option value="Semi-Automatic">Semi-Automatic</option>
                            <option value="CVT">CVT</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="edit_status" name="status">
                            <option value="On Job">On Job</option>
                            <option value="Available">Available</option>
                            <option value="Under Maintenance">Under Maintenance</option>
                            <option value="Written Off">Written Off</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_color" name="color">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">VIN</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_vin" name="vin">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mileage (km)</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_mileage" name="mileage">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
                <button type="button" onclick="closeModal('editVehicleModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
if (typeof window.closeModal !== 'function') {
    window.closeModal = function(id) {
        document.getElementById(id).classList.add('hidden');
        var backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };
}
</script>
