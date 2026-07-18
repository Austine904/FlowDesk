<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Reports</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="<?= base_url('admin/reports/financial') ?>" class="block bg-white rounded-xl border border-indigo-200 shadow-sm hover:shadow-md transition-shadow p-6 text-center">
            <i class="bi bi-currency-dollar text-5xl text-indigo-600"></i>
            <h4 class="text-base font-semibold text-gray-900 mt-3">Financial Reports</h4>
            <p class="text-sm text-gray-500 mt-1">Revenue, invoices, payments, petty cash</p>
        </a>
        <a href="<?= base_url('admin/reports/operational') ?>" class="block bg-white rounded-xl border border-emerald-200 shadow-sm hover:shadow-md transition-shadow p-6 text-center">
            <i class="bi bi-gear-wide-connected text-5xl text-emerald-600"></i>
            <h4 class="text-base font-semibold text-gray-900 mt-3">Operational Reports</h4>
            <p class="text-sm text-gray-500 mt-1">Jobs, mechanics, turnaround time</p>
        </a>
        <a href="<?= base_url('admin/reports/inventory') ?>" class="block bg-white rounded-xl border border-amber-200 shadow-sm hover:shadow-md transition-shadow p-6 text-center">
            <i class="bi bi-box-seam text-5xl text-amber-500"></i>
            <h4 class="text-base font-semibold text-gray-900 mt-3">Inventory Reports</h4>
            <p class="text-sm text-gray-500 mt-1">Stock levels, parts usage, inventory value</p>
        </a>
        <a href="<?= base_url('admin/reports/customers') ?>" class="block bg-white rounded-xl border border-cyan-200 shadow-sm hover:shadow-md transition-shadow p-6 text-center">
            <i class="bi bi-people text-5xl text-cyan-600"></i>
            <h4 class="text-base font-semibold text-gray-900 mt-3">Customer Reports</h4>
            <p class="text-sm text-gray-500 mt-1">Top customers, visits, outstanding balances</p>
        </a>
        <a href="<?= base_url('admin/reports/staff') ?>" class="block bg-white rounded-xl border border-gray-300 shadow-sm hover:shadow-md transition-shadow p-6 text-center">
            <i class="bi bi-person-badge text-5xl text-gray-600"></i>
            <h4 class="text-base font-semibold text-gray-900 mt-3">Staff Reports</h4>
            <p class="text-sm text-gray-500 mt-1">Advisor performance, activity log</p>
        </a>
    </div>
</div>
<?= $this->endSection() ?>
