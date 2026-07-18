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
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Action</th>
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
    function viewJob(id) {
        window.location.href = "<?= base_url('admin/jobs/view/') ?>" + id;
    }

    function deleteJob(id) {
        if (!confirm('Are you sure you want to delete this job card? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: "<?= base_url('admin/jobs/delete/') ?>" + id,
            method: 'POST',
            data: {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#JobTable').DataTable().ajax.reload(null, false);
                    alert(res.message);
                } else {
                    alert(res.message || 'Failed to delete job.');
                }
            },
            error: function(xhr) {
                const res = xhr.responseJSON;
                alert((res && res.message) || 'Failed to delete job.');
            }
        });
    }
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
        const table = $('#JobTable').DataTable({
            "ajax": "<?= base_url('admin/jobs/fetch') ?>",
            "columns": [{
                    "data": "job_no"
                },
                {
                    "data": "registration_number"
                },
                {
                    "data": "diagnosis"
                },
                {
                    "data": "job_status"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div style="display: flex; justify-content: space-around;">
                                
                                <button class="icon-btn text-primary" title="View Details" onclick="viewJob(${data.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn text-danger" title="Delete" onclick="deleteJob(${data.id})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
    });
</script>
<?= $this->endSection() ?>
