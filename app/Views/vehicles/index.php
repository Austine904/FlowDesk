<?php $pageTitle = 'Vehicles'; ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Vehicle Management</h2>
            <p class="text-sm text-gray-500">Manage your vehicle records</p>
        </div>
        <button onclick="openAddVehicleModal()" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="bi bi-person-plus"></i> Add Vehicle
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Vehicles List</h4>
        </div>
        <div class="overflow-x-auto">
            <table id="vehicleTable" class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">ID</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Registration Number</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Owner ID</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Vehicle</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Vehicle Color</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Status</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php include('modals.php'); ?>

<script>
function openAddVehicleModal() {
    document.getElementById('addVehicleModal').classList.remove('hidden');
    document.getElementById('addVehicleModal-backdrop').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
</script>

<?= $this->section('scripts') ?>
<script>var BASE_URL = '<?= base_url() ?>';</script>
<script src="<?= base_url('public/assets/js/vehicles.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
