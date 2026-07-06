<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-cash-stack me-2"></i> Petty Cash</h3>
        <div>
            <a href="<?= base_url('admin/pettycash/ledger') ?>" class="btn btn-outline-info me-2">
                <i class="bi bi-book"></i> View Ledger
            </a>
            <a href="<?= base_url('admin/pettycash/add') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Transaction
            </a>
        </div>
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

    <div class="row g-4 mb-4" id="summaryCards">
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Total Income</h6>
                            <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($summary['total_income'], 2) ?></h3>
                        </div>
                        <i class="bi bi-arrow-down-circle" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Total Expenses</h6>
                            <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($summary['total_expenses'], 2) ?></h3>
                        </div>
                        <i class="bi bi-arrow-up-circle" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white <?= $summary['current_balance'] >= 0 ? 'bg-primary' : 'bg-danger' ?>" id="balanceCard">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Current Balance</h6>
                            <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($summary['current_balance'], 2) ?></h3>
                        </div>
                        <i class="bi bi-wallet2" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="filterStartDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="filterStartDate">
                </div>
                <div class="col-md-4">
                    <label for="filterEndDate" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="filterEndDate">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" id="filterBtn"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Recent Transactions</strong></div>
                <div class="card-body">
                    <table id="pettyCashTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><strong>Category Breakdown</strong></div>
                <div class="card-body" id="categoryBreakdown">
                    <?php if (!empty($byCategory)): ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($byCategory as $cat): ?>
                                <tr>
                                    <td><?= esc($cat['category']) ?></td>
                                    <td><span class="badge <?= $cat['type'] === 'Income' ? 'bg-success' : 'bg-danger' ?>"><?= esc($cat['type']) ?></span></td>
                                    <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($cat['total'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">No transactions yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#pettyCashTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, 'desc']],
        ajax: {
            url: '<?= base_url('admin/pettycash/load') ?>',
            type: 'POST',
        },
        columnDefs: [
            {
                targets: 7,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var editUrl = '<?= base_url('admin/pettycash/edit/') ?>' + row.id;
                    return '<a href="' + editUrl + '" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>' +
                        '<button class="btn btn-sm btn-outline-danger delete-btn" data-id="' + row.id + '"><i class="bi bi-trash"></i></button>';
                }
            },
            {
                targets: 1,
                render: function(data) {
                    if (data === 'Income') {
                        return '<span class="badge bg-success">Income</span>';
                    } else {
                        return '<span class="badge bg-danger">Expense</span>';
                    }
                }
            },
            {
                targets: 4,
                render: function(data) {
                    return '<?= org_setting('currency_symbol', 'KSh') ?> ' + parseFloat(data).toFixed(2);
                }
            }
        ],
        columns: [
            { data: 'transaction_date' },
            { data: 'type' },
            { data: 'category' },
            { data: 'description' },
            { data: 'amount' },
            { data: 'reference_no', defaultContent: '' },
            { data: 'recorded_by_name' },
            { data: 'id' }
        ]
    });

    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This transaction will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/pettycash/delete/') ?>' + id,
                    type: 'POST',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success');
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Could not delete transaction.', 'error');
                    }
                });
            }
        });
    });

    $('#filterBtn').on('click', function() {
        var startDate = $('#filterStartDate').val();
        var endDate = $('#filterEndDate').val();

        if (!startDate || !endDate) {
            Swal.fire('Notice', 'Please select both start and end dates.', 'info');
            return;
        }

        $.ajax({
            url: '<?= base_url('admin/pettycash/filter') ?>',
            type: 'POST',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var s = response.summary;
                    var sym = '<?= org_setting('currency_symbol', 'KSh') ?> ';

                    $('#summaryCards').html(
                        '<div class="col-md-4"><div class="card text-white bg-success"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6>Total Income</h6><h3>' + sym + parseFloat(s.total_income).toFixed(2) + '</h3></div><i class="bi bi-arrow-down-circle" style="font-size: 2.5rem;"></i></div></div></div></div>' +
                        '<div class="col-md-4"><div class="card text-white bg-danger"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6>Total Expenses</h6><h3>' + sym + parseFloat(s.total_expenses).toFixed(2) + '</h3></div><i class="bi bi-arrow-up-circle" style="font-size: 2.5rem;"></i></div></div></div></div>' +
                        '<div class="col-md-4"><div class="card text-white ' + (s.current_balance >= 0 ? 'bg-primary' : 'bg-danger') + '"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6>Current Balance</h6><h3>' + sym + parseFloat(s.current_balance).toFixed(2) + '</h3></div><i class="bi bi-wallet2" style="font-size: 2.5rem;"></i></div></div></div></div>'
                    );

                    var catHtml = '<table class="table table-sm"><thead><tr><th>Category</th><th>Type</th><th>Total</th></tr></thead><tbody>';
                    if (response.by_category && response.by_category.length > 0) {
                        $.each(response.by_category, function(i, cat) {
                            var badge = cat.type === 'Income' ? 'bg-success' : 'bg-danger';
                            catHtml += '<tr><td>' + cat.category + '</td><td><span class="badge ' + badge + '">' + cat.type + '</span></td><td>' + sym + parseFloat(cat.total).toFixed(2) + '</td></tr>';
                        });
                    } else {
                        catHtml += '<tr><td colspan="3" class="text-muted text-center">No transactions in this period.</td></tr>';
                    }
                    catHtml += '</tbody></table>';
                    $('#categoryBreakdown').html(catHtml);
                }
            },
            error: function() {
                Swal.fire('Error', 'Could not filter data.', 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
