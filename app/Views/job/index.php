<?= $this->extend('layouts/main') ?>

<?php $pageTitle = 'Job Cards'; ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Job List</h3>
            <div class="flex items-center gap-2">
                <button onclick="openModal('addJobModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Job Intake
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table id="JobTable" class="w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Job No</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Vehicle Reg</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Description</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Status</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div id="actionModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('actionModal')"></div>
<div id="actionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
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
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600 mx-auto"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<?php include('modals.php'); ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        const backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        const backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    $(document).ready(function() {
        const table = FlowDesk.serverSideTable('#JobTable', {
            ajax: {
                url: '<?= base_url('admin/jobs/fetch') ?>',
                type: 'GET'
            },
            columns: [
                { data: 'job_no' },
                { data: 'registration_number' },
                { data: 'diagnosis' },
                { data: 'job_status' },
                { data: null, orderable: false, render: function(data, type, row) {
                    return '<div class="flex items-center gap-2">' +
                        '<button onclick="editJob(' + data.id + ')" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>' +
                        '<button class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors view-job" title="View" data-id="' + data.id + '"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>' +
                        '<button onclick="deleteJob(' + data.id + ')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
                        '</div>';
                } }
            ]
        });
    });
</script>
<?= $this->endSection() ?>
