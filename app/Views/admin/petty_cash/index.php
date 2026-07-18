<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            <i class="bi bi-cash-stack mr-2"></i> Petty Cash
        </h1>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/pettycash/ledger') ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <i class="bi bi-book"></i> View Ledger
            </a>
            <a href="<?= base_url('admin/pettycash/add') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <i class="bi bi-plus-circle"></i> Add Transaction
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6">
            <i class="bi bi-check-circle"></i>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <i class="bi bi-exclamation-triangle"></i>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <ul class="mb-0 list-disc list-inside">
                <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="summaryCards">
        <div>
            <div class="rounded-xl shadow-sm p-6 text-white bg-emerald-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-white/80 uppercase tracking-wider mb-1">Total Income</p>
                        <p class="text-2xl font-bold text-white"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($summary['total_income'], 2) ?></p>
                    </div>
                    <i class="bi bi-arrow-down-circle text-4xl"></i>
                </div>
            </div>
        </div>
        <div>
            <div class="rounded-xl shadow-sm p-6 text-white bg-red-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-white/80 uppercase tracking-wider mb-1">Total Expenses</p>
                        <p class="text-2xl font-bold text-white"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($summary['total_expenses'], 2) ?></p>
                    </div>
                    <i class="bi bi-arrow-up-circle text-4xl"></i>
                </div>
            </div>
        </div>
        <div>
            <div class="rounded-xl shadow-sm p-6 text-white <?= $summary['current_balance'] >= 0 ? 'bg-indigo-600' : 'bg-red-600' ?>" id="balanceCard">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-white/80 uppercase tracking-wider mb-1">Current Balance</p>
                        <p class="text-2xl font-bold text-white"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($summary['current_balance'], 2) ?></p>
                    </div>
                    <i class="bi bi-wallet2 text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="filterStartDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="filterStartDate">
                </div>
                <div>
                    <label for="filterEndDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="filterEndDate">
                </div>
                <div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors w-full inline-flex items-center justify-center gap-2" id="filterBtn"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Recent Transactions</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table id="pettyCashTable" class="w-full divide-y divide-gray-200" style="width:100%">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Category Breakdown</h2>
                </div>
                <div class="p-6" id="categoryBreakdown">
                    <?php if (!empty($byCategory)): ?>
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($byCategory as $cat): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($cat['category']) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $cat['type'] === 'Income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>"><?= esc($cat['type']) ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($cat['total'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-500 text-center">No transactions yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = FlowDesk.serverSideTable('#pettyCashTable', {
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
                    return '<a href="' + editUrl + '" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-2 py-1 text-xs rounded-lg font-medium transition-colors mr-1"><i class="bi bi-pencil"></i></a>' +
                        '<button class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-2 py-1 text-xs rounded-lg font-medium transition-colors delete-btn" data-id="' + row.id + '"><i class="bi bi-trash"></i></button>';
                }
            },
            {
                targets: 1,
                render: function(data) {
                    if (data === 'Income') {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Income</span>';
                    } else {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Expense</span>';
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
                        '<div><div class="rounded-xl shadow-sm p-6 text-white bg-emerald-600"><div class="flex items-center justify-between"><div><p class="text-xs font-medium text-white/80 uppercase tracking-wider mb-1">Total Income</p><p class="text-2xl font-bold text-white">' + sym + parseFloat(s.total_income).toFixed(2) + '</p></div><i class="bi bi-arrow-down-circle text-4xl"></i></div></div></div>' +
                        '<div><div class="rounded-xl shadow-sm p-6 text-white bg-red-600"><div class="flex items-center justify-between"><div><p class="text-xs font-medium text-white/80 uppercase tracking-wider mb-1">Total Expenses</p><p class="text-2xl font-bold text-white">' + sym + parseFloat(s.total_expenses).toFixed(2) + '</p></div><i class="bi bi-arrow-up-circle text-4xl"></i></div></div></div>' +
                        '<div><div class="rounded-xl shadow-sm p-6 text-white ' + (s.current_balance >= 0 ? 'bg-indigo-600' : 'bg-red-600') + '"><div class="flex items-center justify-between"><div><p class="text-xs font-medium text-white/80 uppercase tracking-wider mb-1">Current Balance</p><p class="text-2xl font-bold text-white">' + sym + parseFloat(s.current_balance).toFixed(2) + '</p></div><i class="bi bi-wallet2 text-4xl"></i></div></div></div>'
                    );

                    var catHtml = '<table class="w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th></tr></thead><tbody class="divide-y divide-gray-200">';
                    if (response.by_category && response.by_category.length > 0) {
                        $.each(response.by_category, function(i, cat) {
                            var badge = cat.type === 'Income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
                            catHtml += '<tr class="hover:bg-gray-50"><td class="px-4 py-3 text-sm text-gray-900">' + cat.category + '</td><td class="px-4 py-3 text-sm text-gray-900"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + badge + '">' + cat.type + '</span></td><td class="px-4 py-3 text-sm text-gray-900">' + sym + parseFloat(cat.total).toFixed(2) + '</td></tr>';
                        });
                    } else {
                        catHtml += '<tr><td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No transactions in this period.</td></tr>';
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
