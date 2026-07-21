<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Supplier Payments</h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-2xl font-bold text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalPaidThisMonth, 2) ?></p>
            <p class="text-sm text-gray-500 mt-1">Paid This Month</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-2xl font-bold text-amber-600"><?= $pendingCount ?></p>
            <p class="text-sm text-gray-500 mt-1">Pending Approval (<?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($pendingAmount, 2) ?>)</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-2xl font-bold text-emerald-600"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalPaidAllTime, 2) ?></p>
            <p class="text-sm text-gray-500 mt-1">Total Paid (All Time)</p>
        </div>
    </div>

    <?php if ($pendingCount > 0): ?>
    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-6">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-amber-800"><?= $pendingCount ?> supplier payment<?= $pendingCount > 1 ? 's' : '' ?> pending approval</p>
            <p class="text-xs text-amber-600 mt-1">Total amount: <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($pendingAmount, 2) ?></p>
        </div>
        <a href="#payments-table" class="ml-auto text-sm font-medium text-amber-700 hover:text-amber-800 hover:underline">Review now →</a>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table id="supplierPaymentsTable" class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LPO No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raised By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="actionModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('actionModal')"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div id="actionModalContent" class="p-6">
            </div>
        </div>
    </div>
</div>

<script>
window.openModal = function(url, title) {
    var modal = document.getElementById('actionModal');
    var content = document.getElementById('actionModalContent');
    modal.classList.remove('hidden');
    content.innerHTML = '<div class="text-center py-8"><svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><p class="text-sm text-gray-500 mt-2">Loading...</p></div>';
    $.get(url, function(html) {
        content.html(html);
    });
};

window.closeModal = function(id) {
    document.getElementById(id).classList.add('hidden');
};
</script>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    var table = $('#supplierPaymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/supplier_payments/load') ?>',
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: function(d) {
                var csrf = getCsrfMeta();
                d[csrf.name] = csrf.hash;
            }
        },
        columns: [
            { data: 'payment_ref' },
            { data: 'supplier_name' },
            { data: 'lpo_no' },
            {
                data: 'amount',
                render: function(data) {
                    return '<?= org_setting('currency_symbol', 'KSh') ?> ' + parseFloat(data).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { data: 'payment_method' },
            {
                data: 'status',
                render: function(data) {
                    var map = {
                        'Pending Approval': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending Approval</span>',
                        'Approved': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Approved</span>',
                        'Paid': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Paid</span>',
                        'Rejected': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>',
                    };
                    return map[data] || data;
                }
            },
            { data: 'raised_by_name' },
            { data: 'approved_by_name' },
            {
                data: 'payment_date',
                render: function(data) { return data || '-'; }
            },
            {
                data: null,
                orderable: false,
                render: function(row) {
                    var html = '<a href="<?= base_url('admin/supplier_payments/view/') ?>' + row.id + '" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-2">View</a>';
                    if (row.status === 'Pending Approval') {
                        html += '<button onclick="approvePayment(' + row.id + ')" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium mr-2">Approve</button>';
                        html += '<button onclick="rejectPayment(' + row.id + ')" class="text-red-600 hover:text-red-800 text-xs font-medium">Reject</button>';
                    } else if (row.status === 'Approved') {
                        html += '<button onclick="markPaid(' + row.id + ')" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Mark Paid</button>';
                    }
                    return html;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: { search: "Search:", lengthMenu: "Show _MENU_ entries", info: "Showing _START_ to _END_ of _TOTAL_ entries" }
    });

    window.approvePayment = function(id) {
        if (!confirm('Approve this supplier payment?')) return;
        var csrf = getCsrfMeta();
        var data = {};
        data[csrf.name] = csrf.hash;
        $.post('<?= base_url('admin/supplier_payments/approve/') ?>' + id, data, function(res) {
            if (res.status === 'success') {
                table.ajax.reload();
                showAlert(res.message, 'success');
            } else {
                showAlert(res.message || 'Error', 'error');
            }
        }).fail(function(xhr) {
            showAlert('Error approving payment.', 'error');
        });
    };

    window.rejectPayment = function(id) {
        var reason = prompt('Enter rejection reason:');
        if (!reason) return;
        var csrf = getCsrfMeta();
        var data = { rejection_reason: reason };
        data[csrf.name] = csrf.hash;
        $.post('<?= base_url('admin/supplier_payments/reject/') ?>' + id, data, function(res) {
            if (res.status === 'success') {
                table.ajax.reload();
                showAlert(res.message, 'success');
            } else {
                showAlert(res.message || 'Error', 'error');
            }
        }).fail(function(xhr) {
            showAlert('Error rejecting payment.', 'error');
        });
    };

    window.markPaid = function(id) {
        var date = prompt('Enter payment date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
        if (!date) return;
        var ref = prompt('Enter reference number (optional):', '');
        var csrf = getCsrfMeta();
        var data = { payment_date: date, reference_no: ref || '' };
        data[csrf.name] = csrf.hash;
        $.post('<?= base_url('admin/supplier_payments/mark_paid/') ?>' + id, data, function(res) {
            if (res.status === 'success') {
                table.ajax.reload();
                showAlert(res.message, 'success');
            } else {
                showAlert(res.message || 'Error', 'error');
            }
        }).fail(function(xhr) {
            showAlert('Error marking payment as paid.', 'error');
        });
    };

    window.showAlert = function(message, type) {
        var cls = type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800';
        var html = '<div class="flex items-center gap-3 ' + cls + ' px-4 py-3 rounded-lg mb-6">' +
            '<span>' + message + '</span>' +
            '<button onclick="this.parentElement.remove()" class="ml-auto text-sm">&times;</button></div>';
        $('.max-w-7xl > .flex.items-center.justify-between').after(html);
    };
});
</script>
<?= $this->endSection() ?>
