<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-boxes me-2"></i> Inventory / Parts</h3>
        <a href="<?= base_url('admin/inventory/add') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New Part
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

    <div id="lowStockBanner" class="alert alert-warning d-none">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong id="lowStockCount"></strong> item(s) are low on stock or out of stock.
        <a href="#inventoryTable" class="alert-link">Scroll to view</a>.
    </div>

    <div class="card">
        <div class="card-body">
            <table id="inventoryTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Part Name</th>
                        <th>Part Number</th>
                        <th>Unit Price</th>
                        <th>Unit</th>
                        <th>Stock Status</th>
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
    var table = $('#inventoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/inventory/load') ?>',
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
            { data: 'part_number', defaultContent: '—' },
            {
                data: 'unit_price',
                render: function(data) {
                    return '<?= org_setting('currency_symbol', 'KSh') ?> ' + parseFloat(data).toFixed(2);
                }
            },
            { data: 'unit', defaultContent: 'piece' },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    if (data.is_stocked == 0) {
                        return '<span class="badge bg-secondary">Catalog Only</span>';
                    }
                    var qty = parseFloat(data.quantity_in_hand);
                    var reorder = parseFloat(data.reorder_level);
                    if (qty <= 0) {
                        return '<span class="badge bg-danger">Out of Stock</span>';
                    } else if (qty <= reorder) {
                        return '<span class="badge bg-warning text-dark">Low Stock (' + qty + ')</span>';
                    } else {
                        return '<span class="badge bg-success">In Stock (' + qty + ')</span>';
                    }
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function(data) {
                    return `
                        <a href="<?= base_url('admin/inventory/edit/') ?>${data}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${data}"><i class="bi bi-trash"></i> Delete</button>
                    `;
                }
            }
        ],
        drawCallback: function(settings) {
            var api = this.api();
            var data = api.rows({ filter: 'applied' }).data().toArray();
            var lowCount = 0;
            data.forEach(function(row) {
                if (row.is_stocked == 1 && parseFloat(row.quantity_in_hand) <= parseFloat(row.reorder_level)) {
                    lowCount++;
                }
            });
            if (lowCount > 0) {
                $('#lowStockBanner').removeClass('d-none');
                $('#lowStockCount').text(lowCount);
            } else {
                $('#lowStockBanner').addClass('d-none');
            }
        }
    });

    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var csrf = getCsrfMeta();
        if (!confirm('Are you sure you want to delete this part?')) return;
        $.ajax({
            url: '<?= base_url('admin/inventory/delete/') ?>' + id,
            type: 'POST',
            data: { [csrf.name]: csrf.hash },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#inventoryTable').DataTable().ajax.reload();
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
