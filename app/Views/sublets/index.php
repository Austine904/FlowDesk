<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <h3 class="mb-4">Sublet Management</h3>

    <div class="table-container" style="margin-top: 20px;">

        <div class="table-header d-flex justify-content-between align-items-center mb-6">
            <h4>Sublets List</h4>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="openModal('<?= base_url('admin/sublets/add') ?>', 'Add New Sublet')">
                    <i class="bi bi-plus-circle"></i> Add Sublet
                </button>
            </div>
        </div>

        <!-- <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                <select class="form-select" id="status-filter" style="min-width: 150px;">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Invoiced">Invoiced</option>
                    <option value="Paid">Paid</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <button type="button" class="btn btn-danger mb-3" id="deleteSelectedBtn">
                <i class="bi bi-trash me-1"></i> Delete Selected
            </button>
        </div> -->




        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('admin/sublets/bulkAction') ?>" id="bulkActionForm" class="mt-5">
            <?= csrf_field() ?>

            <div class="table-responsive rounded">
                <table class="table table-striped table-bordered " id="subletTable" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="select_all"></th>
                            <th>ID</th>
                            <th>Job No.</th>
                            <th>Description</th>
                            <th>Provider</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th>Date Sent</th>
                            <th>Date Returned</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this tbody -->
                    </tbody>
                </table>
            </div>

            <div id="pagination-links-container"></div>
        </form>
    </div>
</div>

<!-- modals -->

<?php include('modals.php'); ?>

<?= $this->endSection() ?>