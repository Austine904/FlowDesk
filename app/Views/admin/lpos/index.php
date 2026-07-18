<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Local Purchase Orders</h1>
        <a href="<?= base_url('admin/lpos/add') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New LPO
        </a>
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
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="list-disc list-inside text-sm"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="lposTable" class="w-full divide-y divide-gray-200" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LPO No.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Ref</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = FlowDesk.serverSideTable('#lposTable', {
        ajax: {
            url: '<?= base_url('admin/lpos/load') ?>'
        },
        order: [[0, 'desc']],
        columns: [
            { data: 'lpo_no' },
            { data: 'supplier_name', defaultContent: '—' },
            { data: 'job_no', defaultContent: '—' },
            { data: 'lpo_date', render: function(data) { return data ? data : '—'; } },
            { data: 'expected_date', render: function(data) { return data ? data : '—'; } },
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
                        'Draft': 'bg-gray-100 text-gray-700',
                        'Sent': 'bg-blue-100 text-blue-700',
                        'Partially Received': 'bg-amber-100 text-amber-700',
                        'Received': 'bg-emerald-100 text-emerald-700',
                        'Cancelled': 'bg-red-100 text-red-700'
                    };
                    var cls = map[data] || 'bg-gray-100 text-gray-700';
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + cls + '">' + data + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    var id = data.id;
                    var status = data.status || 'Draft';
                    var actions = '<a href="<?= base_url('admin/lpos/view/') ?>' + id + '" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> View</a>';
                    if (status === 'Draft') {
                        actions += ' <a href="<?= base_url('admin/lpos/edit/') ?>' + id + '" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit</a>';
                        actions += ' <button class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg font-medium bg-red-50 hover:bg-red-100 text-red-700 btn-delete-lpo" data-id="' + id + '"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Delete</button>';
                    }
                    if (status === 'Sent' || status === 'Partially Received') {
                        actions += ' <a href="<?= base_url('admin/lpos/receive/') ?>' + id + '" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg font-medium bg-emerald-50 hover:bg-emerald-100 text-emerald-700"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Receive</a>';
                    }
                    return actions;
                }
            }
        ]
    });

    $(document).on('click', '.btn-delete-lpo', function() {
        var id = $(this).data('id');
        var csrf = getCsrfMeta();
        Swal.fire({
            title: 'Confirm Delete',
            text: 'Are you sure you want to delete this LPO?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '<?= base_url('admin/lpos/delete/') ?>' + id,
                type: 'POST',
                data: { [csrf.name]: csrf.hash },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Deleted!', res.message || 'LPO deleted successfully.', 'success');
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error!', res.message || 'Delete failed.', 'error');
                    }
                },
                error: function(xhr) { FlowDesk.handleAjaxError(xhr, 'delete'); }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
