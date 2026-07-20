<?= $this->extend('layouts/main') ?>

<?php $pageTitle = 'Payments'; ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900"><i class="bi bi-cash-coin mr-2"></i> Payments</h1>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500" id="periodLabel"><?= date('M Y') ?></span>
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

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="summaryCards">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Received</p>
                    <p class="text-2xl font-bold text-emerald-600"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalReceived, 2) ?></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="bi bi-arrow-down-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Transactions</p>
                    <p class="text-2xl font-bold text-indigo-600"><?= number_format($totalTransactions) ?></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="bi bi-receipt text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Outstanding</p>
                    <p class="text-2xl font-bold <?= $outstandingBalance > 0 ? 'text-amber-600' : 'text-emerald-600' ?>"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($outstandingBalance, 2) ?></p>
                </div>
                <div class="w-10 h-10 rounded-lg <?= $outstandingBalance > 0 ? 'bg-amber-100' : 'bg-emerald-100' ?> flex items-center justify-center">
                    <i class="bi bi-clock-history <?= $outstandingBalance > 0 ? 'text-amber-600' : 'text-emerald-600' ?> text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Avg Payment</p>
                    <p class="text-2xl font-bold text-indigo-600"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($avgPayment, 2) ?></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="bi bi-bar-chart text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3" id="methodCards">
        <?php
        $methodColors = [
            'Cash' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'bi-cash'],
            'M-Pesa' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'bi-phone'],
            'Bank Transfer' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'bi-bank'],
            'Insurance' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'bi-shield-check'],
            'Credit' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'bi-credit-card'],
        ];
        foreach ($methodStats as $method => $stats):
            $colors = $methodColors[$method] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'bi-cash'];
        ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg <?= $colors['bg'] ?> flex items-center justify-center">
                    <i class="bi <?= $colors['icon'] ?> <?= $colors['text'] ?>"></i>
                </div>
                <span class="text-xs font-medium <?= $colors['text'] ?>"><?= $method ?></span>
            </div>
            <p class="text-sm font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($stats['total'], 2) ?></p>
            <p class="text-xs text-gray-500"><?= $stats['count'] ?> transactions</p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="filterStartDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="filterStartDate" value="<?= $monthStart ?>">
                </div>
                <div>
                    <label for="filterEndDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="filterEndDate" value="<?= $monthEnd ?>">
                </div>
                <div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors w-full inline-flex items-center justify-center gap-2" id="filterBtn">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
                <div>
                    <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors w-full inline-flex items-center justify-center gap-2" id="resetFilterBtn">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments DataTable -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-0">
            <div class="overflow-x-auto rounded-xl">
                <table id="paymentsTable" class="w-full divide-y divide-gray-200" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received By</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    var sym = '<?= org_setting('currency_symbol', 'KSh') ?> ';

    var table = FlowDesk.serverSideTable('#paymentsTable', {
        ajax: {
            url: '<?= base_url('admin/payments/load') ?>',
            type: 'POST',
            data: function(d) {
                d.start_date = $('#filterStartDate').val();
                d.end_date = $('#filterEndDate').val();
                var csrf = getCsrfMeta();
                if (csrf.name) d[csrf.name] = csrf.hash;
            }
        },
        order: [[0, 'desc']],
        columns: [
            { data: 'payment_date' },
            {
                data: null,
                render: function(data) {
                    return '<span class="font-medium text-gray-900">' + (data.customer_name || 'N/A') + '</span>' +
                           (data.customer_phone ? '<br><span class="text-xs text-gray-500">' + data.customer_phone + '</span>' : '');
                }
            },
            { data: 'invoice_no', defaultContent: '—' },
            { data: 'job_no', defaultContent: '—' },
            {
                data: 'amount',
                render: function(data) {
                    return sym + parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
                }
            },
            {
                data: 'payment_method',
                render: function(data) {
                    var map = {
                        'Cash': 'bg-gray-100 text-gray-700',
                        'M-Pesa': 'bg-emerald-100 text-emerald-700',
                        'Bank Transfer': 'bg-blue-100 text-blue-700',
                        'Insurance': 'bg-purple-100 text-purple-700',
                        'Credit': 'bg-amber-100 text-amber-700'
                    };
                    var cls = map[data] || 'bg-gray-100 text-gray-700';
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + cls + '">' + (data || '—') + '</span>';
                }
            },
            { data: 'reference_no', defaultContent: '—' },
            { data: 'received_by_name', defaultContent: '—' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    if (data.receipt_id) {
                        return '<a href="<?= base_url('admin/invoices/receipt/') ?>' + data.receipt_id + '" target="_blank" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-2 py-1 rounded-lg text-xs font-medium transition-colors"><i class="bi bi-printer"></i> Print</a>';
                    } else {
                        return '<a href="<?= base_url('admin/invoices/generate_receipt/') ?>' + data.id + '" class="inline-flex items-center gap-1 bg-gray-50 hover:bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-xs font-medium transition-colors">Generate</a>';
                    }
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<a href="<?= base_url('admin/invoices/view/') ?>' + data.invoice_id + '" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-2 py-1 rounded-lg text-xs font-medium transition-colors"><i class="bi bi-eye"></i> View Invoice</a>';
                }
            }
        ]
    });

    // Date range filter
    $('#filterBtn').on('click', function() {
        var startDate = $('#filterStartDate').val();
        var endDate = $('#filterEndDate').val();

        if (!startDate || !endDate) {
            Swal.fire('Notice', 'Please select both start and end dates.', 'info');
            return;
        }

        $.ajax({
            url: '<?= base_url('admin/payments/filter') ?>',
            type: 'POST',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update summary cards
                    $('#summaryCards').html(
                        '<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"><div class="flex items-center justify-between"><div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Received</p><p class="text-2xl font-bold text-emerald-600">' + sym + parseFloat(response.total_received).toFixed(2) + '</p></div><div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="bi bi-arrow-down-circle text-emerald-600 text-xl"></i></div></div></div>' +
                        '<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"><div class="flex items-center justify-between"><div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Transactions</p><p class="text-2xl font-bold text-indigo-600">' + response.total_transactions + '</p></div><div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center"><i class="bi bi-receipt text-indigo-600 text-xl"></i></div></div></div>' +
                        '<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"><div class="flex items-center justify-between"><div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Outstanding</p><p class="text-2xl font-bold ' + (response.outstanding_balance > 0 ? 'text-amber-600' : 'text-emerald-600') + '">' + sym + parseFloat(response.outstanding_balance).toFixed(2) + '</p></div><div class="w-10 h-10 rounded-lg ' + (response.outstanding_balance > 0 ? 'bg-amber-100' : 'bg-emerald-100') + ' flex items-center justify-center"><i class="bi bi-clock-history ' + (response.outstanding_balance > 0 ? 'text-amber-600' : 'text-emerald-600') + ' text-xl"></i></div></div></div>' +
                        '<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"><div class="flex items-center justify-between"><div><p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Avg Payment</p><p class="text-2xl font-bold text-indigo-600">' + sym + parseFloat(response.avg_payment).toFixed(2) + '</p></div><div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center"><i class="bi bi-bar-chart text-indigo-600 text-xl"></i></div></div></div>'
                    );

                    // Update method cards
                    var methodHtml = '';
                    var methodColors = {
                        'Cash': {bg: 'bg-gray-100', text: 'text-gray-700', icon: 'bi-cash'},
                        'M-Pesa': {bg: 'bg-emerald-100', text: 'text-emerald-700', icon: 'bi-phone'},
                        'Bank Transfer': {bg: 'bg-blue-100', text: 'text-blue-700', icon: 'bi-bank'},
                        'Insurance': {bg: 'bg-purple-100', text: 'text-purple-700', icon: 'bi-shield-check'},
                        'Credit': {bg: 'bg-amber-100', text: 'text-amber-700', icon: 'bi-credit-card'}
                    };
                    var methodDefaults = ['Cash', 'M-Pesa', 'Bank Transfer', 'Insurance', 'Credit'];
                    var methodData = {};
                    if (response.method_breakdown) {
                        $.each(response.method_breakdown, function(i, m) {
                            methodData[m.payment_method] = {total: parseFloat(m.total || 0), count: parseInt(m.count || 0)};
                        });
                    }
                    $.each(methodDefaults, function(i, m) {
                        var stats = methodData[m] || {total: 0, count: 0};
                        var colors = methodColors[m] || {bg: 'bg-gray-100', text: 'text-gray-700', icon: 'bi-cash'};
                        methodHtml += '<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4"><div class="flex items-center gap-2 mb-2"><div class="w-8 h-8 rounded-lg ' + colors.bg + ' flex items-center justify-center"><i class="bi ' + colors.icon + ' ' + colors.text + '"></i></div><span class="text-xs font-medium ' + colors.text + '">' + m + '</span></div><p class="text-sm font-bold text-gray-900">' + sym + stats.total.toFixed(2) + '</p><p class="text-xs text-gray-500">' + stats.count + ' transactions</p></div>';
                    });
                    $('#methodCards').html(methodHtml);

                    // Reload DataTable with new dates
                    table.ajax.reload();
                }
            },
            error: function() {
                Swal.fire('Error', 'Could not filter data.', 'error');
            }
        });
    });

    $('#resetFilterBtn').on('click', function() {
        $('#filterStartDate').val('<?= $monthStart ?>');
        $('#filterEndDate').val('<?= $monthEnd ?>');
        $('#filterBtn').trigger('click');
    });
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
