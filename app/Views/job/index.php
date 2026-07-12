<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <h3 class="mb-4">Jobs Management</h3>

    <!-- Vehicle Table -->
    <div class="table-container" style="margin-top: 20px;">
        <div class="table-header d-flex justify-content-between align-items-center mb-3">
            <h4>Job List</h4>
            <div class="d-flex gap-2">
                <a href="<?= base_url('job_intake') ?>" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square"></i> Job Intake
                </a>
                <button class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="$('#addJobModal').modal('show')">
                    <i class="bi bi-wrench"></i> Add New Job
                </button>
            </div>
        </div>
        <div class="table-responsive rounded">
            <table id="JobTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Job No</th>
                        <th>Vehicle Reg</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>


<!-- Re-using existing actionModal for Add/Edit forms -->
<div id="actionModal" class="modal fade" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modals -->
<?php include('modals.php'); ?>



<script>
    $(document).ready(function() {
        // Initialize DataTable
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
                <button class="icon-btn text-info" title="Edit" onclick="editJob(${data.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="icon-btn text-primary view-job" title="View Details" data-id="${row.id}">
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