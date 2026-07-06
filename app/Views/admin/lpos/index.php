<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Local Purchase Orders</h3>
        <a href="<?= base_url('admin/lpos/add') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New LPO
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
            <table id="lposTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>LPO No.</th>
                        <th>Supplier</th>
                        <th>Job Ref</th>
                        <th>Date</th>
                        <th>Expected</th>
                        <th>Total</th>
                        <th>Status</th>
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
    $('#lposTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/lpos/load') ?>',
            type: 'POST',
            data: function(d) {
                var csrf = getCsrfMeta();
                d[csrf.name] = csrf.hash;
            }
        },
        order: [[0, 'desc']],
        pageLength: 25,
        columns: [
            { data: 'lpo_no' },
            { data: 'supplier_name', defaultContent: '—' },
            { data: 'job_no', defaultContent: '—' },
            {
                data: 'lpo_date',
                render: function(data) { return data ? data : '—'; }
            },
            {
                data: 'expected_date',
                render: function(data) { return data ? data : '—'; }
            },
            {
                data: 'total_amount',
                render: function(data) {
                    return '<?= org_setting('currency_symbol', 'KSh') ?> ' + parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
                }
            },
            {
                data: 'status',
                render: function(data) {
                    var map = {
                        'Draft': 'bg-secondary',
                        'Sent': 'bg-primary',
                        'Partially Received': 'bg-warning text-dark',
                        'Received': 'bg-success',
                        'Cancelled': 'bg-dark'
                    };
                    var cls = map[data] || 'bg-secondary';
                    return '<span class="badge ' + cls + '">' + (data || 'Draft') + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    var id = data.id;
                    var status = data.status || 'Draft';
                    var actions = '<a href="<?= base_url('admin/lpos/view/') ?>' + id + '" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>';
                    if (status === 'Draft') {
                        actions += ' <a href="<?= base_url('admin/lpos/edit/') ?>' + id + '" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>';
                        actions += ' <button class="btn btn-sm btn-outline-danger btn-delete-lpo" data-id="' + id + '"><i class="bi bi-trash"></i></button>';
                    }
                    if (status === 'Sent' || status === 'Partially Received') {
                        actions += ' <a href="<?= base_url('admin/lpos/receive/') ?>' + id + '" class="btn btn-sm btn-outline-success"><i class="bi bi-box-seam"></i> Receive</a>';
                    }
                    return actions;
                }
            }
        ]
    });

    $(document).on('click', '.btn-delete-lpo', function() {
        var id = $(this).data('id');
        var csrf = getCsrfMeta();
        if (!confirm('Are you sure you want to delete this LPO?')) return;
        $.ajax({
            url: '<?= base_url('admin/lpos/delete/') ?>' + id,
            type: 'POST',
            data: { [csrf.name]: csrf.hash },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#lposTable').DataTable().ajax.reload();
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
