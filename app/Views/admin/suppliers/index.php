<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-truck-flatbed me-2"></i> Suppliers</h3>
        <a href="<?= base_url('admin/suppliers/add') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New Supplier
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table id="suppliersTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#suppliersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/suppliers/load') ?>',
            type: 'POST',
            data: function(d) {
                var csrf = getCsrfMeta();
                d[csrf.name] = csrf.hash;
            }
        },
        order: [[0, 'asc']],
        pageLength: 25,
        columns: [
            { data: 'name' },
            {
                data: 'id',
                orderable: false,
                render: function(data) {
                    return `
                        <a href="<?= base_url('admin/suppliers/edit/') ?>${data}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${data}"><i class="bi bi-trash"></i> Delete</button>
                    `;
                }
            }
        ]
    });

    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var csrf = getCsrfMeta();
        if (!confirm('Are you sure you want to delete this supplier?')) return;
        $.ajax({
            url: '<?= base_url('admin/suppliers/delete/') ?>' + id,
            type: 'POST',
            data: { [csrf.name]: csrf.hash },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#suppliersTable').DataTable().ajax.reload();
                } else {
                    alert(res.message || 'Delete failed.');
                }
            },
            error: function(xhr) {
                var msg = 'Delete failed.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || r.messages?.error || msg;
                } catch(e) {}
                alert(msg);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
