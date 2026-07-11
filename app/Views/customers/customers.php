<?php $pageTitle = 'Customers'; ?>
<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Customers Management</h2>
            <p class="text-sm text-gray-500">Manage your customer records</p>
        </div>
        <button onclick="openModal('<?= base_url('admin/customers/add') ?>', 'Add New Customer')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="bi bi-person-plus"></i> Add Customer
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900">Customers List</h4>
        </div>
        <div class="overflow-x-auto">
            <table id="customerTable" class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left"><input type="checkbox" id="select_all_customers"></th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Name</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Phone</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Email</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Vehicles</th>
                        <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="actionModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('actionModal')"></div>
<div id="actionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900" id="actionModalLabel"></h5>
            <button type="button" onclick="closeModal('actionModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div id="modalContent" class="text-center py-5">
                <div class="inline-block w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('modals.php'); ?>

<?= $this->section('scripts') ?>
<script>var BASE_URL = '<?= base_url() ?>';</script>
<script src="<?= base_url('public/assets/js/customers.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
