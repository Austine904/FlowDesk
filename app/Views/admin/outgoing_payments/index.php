<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $pageTitle = 'Outgoing Payments'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Outgoing Payments</h1>
        <a href="<?= base_url('admin/outgoing_payments/raise') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Payment
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('info')): ?>
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm"><?= esc(session()->getFlashdata('info')) ?></div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Paid This Month</p>
            <p class="text-2xl font-bold text-red-600">KSh <?= number_format($totalPaidMonth ?? 0, 2) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Pending Approval</p>
            <p class="text-2xl font-bold text-amber-600"><?= $pendingCount ?? 0 ?> · KSh <?= number_format($pendingAmount ?? 0, 2) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Total Paid (All Time)</p>
            <p class="text-2xl font-bold text-gray-700">KSh <?= number_format($totalPaidAll ?? 0, 2) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">By Type</p>
            <div class="space-y-1">
                <?php foreach (['LPO','Sublet','Expense','Staff Reimbursement','Ad-hoc'] as $t): ?>
                <?php if (isset($breakdown[$t])): ?>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600"><?= $t ?></span>
                    <span class="font-medium text-gray-900"><?= $breakdown[$t]['count'] ?> / KSh <?= number_format($breakdown[$t]['amount'], 0) ?></span>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Pending Approvals Banner -->
    <?php if (($pendingCount ?? 0) > 0): ?>
    <div class="flex items-start gap-3 p-4 bg-amber-50 rounded-lg border border-amber-200">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="text-sm font-medium text-amber-800"><?= $pendingCount ?> payment(s) awaiting approval</p>
            <a href="#paymentsTable" class="text-xs text-amber-600 hover:underline">Review below →</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- DataTable -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">All Payments</h3>
        </div>
        <div class="p-6">
            <table id="outgoingPaymentsTable" class="w-full divide-y divide-gray-200" style="width:100%">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Ref</th>
                        <th>Type</th>
                        <th>Source Doc</th>
                        <th>Payee</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Raised By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Payment</h3>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to approve this payment?</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('approveModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</button>
            <button id="confirmApproveBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Approve</button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Payment</h3>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
            <textarea id="rejectionReason" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" rows="3" minlength="10" required></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('rejectModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</button>
            <button id="confirmRejectBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Reject</button>
        </div>
    </div>
</div>

<!-- Mark Paid Modal -->
<div id="markPaidModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="markPaidModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mark as Paid</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                <input type="date" id="paidDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference No.</label>
                <input type="text" id="paidRef" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Optional reference">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeModal('markPaidModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancel</button>
            <button id="confirmPaidBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Mark as Paid</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const table = $('#outgoingPaymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/outgoing_payments/load') ?>',
            type: 'POST',
            data: function(d) {
                d.type = '<?= service('request')->getGet('type') ?? '' ?>';
                d.status = '<?= service('request')->getGet('status') ?? '' ?>';
            }
        },
        order: [[0, 'desc']],
        columns: [
            { data: 'payment_ref', className: 'text-sm font-medium text-gray-900' },
            { data: 'payment_type', className: 'text-sm', render: function(data) {
                const map = {'LPO':'bg-blue-100 text-blue-700','Sublet':'bg-purple-100 text-purple-700','Expense':'bg-orange-100 text-orange-700','Staff Reimbursement':'bg-teal-100 text-teal-700','Ad-hoc':'bg-gray-100 text-gray-700'};
                const cls = map[data] || 'bg-gray-100 text-gray-700';
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + cls + '">' + data + '</span>';
            }},
            { data: 'source_ref', className: 'text-sm text-gray-600', defaultContent: '-' },
            { data: 'payee_name', className: 'text-sm text-gray-600', defaultContent: '-' },
            { data: 'amount', className: 'text-sm text-gray-900 font-medium', render: function(d) { return 'KSh ' + parseFloat(d).toLocaleString(undefined, {minimumFractionDigits:2}); } },
            { data: 'payment_method', className: 'text-sm text-gray-600' },
            { data: 'status', className: 'text-sm', render: function(data) {
                const map = {'Pending Approval':'bg-amber-100 text-amber-800','Approved':'bg-blue-100 text-blue-800','Paid':'bg-emerald-100 text-emerald-800','Rejected':'bg-red-100 text-red-800'};
                const cls = map[data] || 'bg-gray-100 text-gray-700';
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + cls + '">' + data + '</span>';
            }},
            { data: 'raised_by_name', className: 'text-sm text-gray-600' },
            { data: 'payment_date', className: 'text-sm text-gray-500', defaultContent: '-' },
            { data: null, className: 'text-sm', orderable: false, render: function(data, type, row) {
                let btns = '<a href="<?= base_url('admin/outgoing_payments/view/') ?>' + row.id + '" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-2">View</a>';
                if (row.status === 'Pending Approval') {
                    btns += '<button onclick="openApprove(' + row.id + ')" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium mr-2">Approve</button>';
                    btns += '<button onclick="openReject(' + row.id + ')" class="text-red-600 hover:text-red-800 text-xs font-medium">Reject</button>';
                } else if (row.status === 'Approved') {
                    btns += '<button onclick="openMarkPaid(' + row.id + ')" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Mark Paid</button>';
                }
                return btns;
            }}
        ],
        language: { emptyTable: 'No outgoing payments found.' }
    });

    window.openApprove = function(id) {
        document.getElementById('approveModal').classList.remove('hidden');
        document.getElementById('approveModal-backdrop').classList.remove('hidden');
        document.getElementById('confirmApproveBtn').onclick = function() {
            $.ajax({
                url: '<?= base_url('admin/outgoing_payments/approve/') ?>' + id,
                type: 'POST',
                data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(r) { if (r.status === 'success') { table.ajax.reload(); closeModal('approveModal'); } else { alert(r.message); } },
                error: function() { alert('Error approving payment.'); }
            });
        };
    };

    window.openReject = function(id) {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal-backdrop').classList.remove('hidden');
        document.getElementById('confirmRejectBtn').onclick = function() {
            var reason = document.getElementById('rejectionReason').value;
            if (reason.length < 10) { alert('Reason must be at least 10 characters.'); return; }
            $.ajax({
                url: '<?= base_url('admin/outgoing_payments/reject/') ?>' + id,
                type: 'POST',
                data: { rejection_reason: reason, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(r) { if (r.status === 'success') { table.ajax.reload(); closeModal('rejectModal'); } else { alert(r.message); } },
                error: function() { alert('Error rejecting payment.'); }
            });
        };
    };

    window.openMarkPaid = function(id) {
        document.getElementById('markPaidModal').classList.remove('hidden');
        document.getElementById('markPaidModal-backdrop').classList.remove('hidden');
        document.getElementById('confirmPaidBtn').onclick = function() {
            var date = document.getElementById('paidDate').value;
            if (!date) { alert('Payment date is required.'); return; }
            var ref = document.getElementById('paidRef').value;
            $.ajax({
                url: '<?= base_url('admin/outgoing_payments/mark_paid/') ?>' + id,
                type: 'POST',
                data: { payment_date: date, reference_no: ref, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(r) { if (r.status === 'success') { table.ajax.reload(); closeModal('markPaidModal'); } else { alert(r.message); } },
                error: function() { alert('Error marking payment as paid.'); }
            });
        };
    };
});
</script>
<?= $this->endSection() ?>
