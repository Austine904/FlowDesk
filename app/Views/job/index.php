<?= $this->extend('layouts/main') ?>

<?php $pageTitle = 'Job Cards'; ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Job List</h3>
            <div class="flex items-center gap-2">
                <button onclick="exportCSV()" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </button>
                <button onclick="openModal('addJobModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Job Intake
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 border-b border-gray-100 space-y-3">
            <div class="flex flex-wrap items-center gap-2" id="statusFilters">
                <span class="text-xs font-medium text-gray-500 mr-1">Status:</span>
                <button data-status="" class="status-filter active px-3 py-1.5 rounded-full text-xs font-medium border transition-colors bg-indigo-600 text-white border-indigo-600">All</button>
                <button data-status="Awaiting Assignment" class="status-filter px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">Awaiting Assignment</button>
                <button data-status="Awaiting Diagnosis" class="status-filter px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">Awaiting Diagnosis</button>
                <button data-status="Diagnosis Complete" class="status-filter px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">Diagnosis Complete</button>
                <button data-status="In Progress" class="status-filter px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">In Progress</button>
                <button data-status="Quality Check" class="status-filter px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">Quality Check</button>
                <button data-status="Ready for Invoice" class="status-filter px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">Ready for Invoice</button>
                <button data-status="Completed" class="status-filter px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">Completed</button>
                <button data-status="Cancelled" class="status-filter px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">Cancelled</button>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-500">From:</label>
                    <input type="date" id="dateFrom" class="text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-500">To:</label>
                    <input type="date" id="dateTo" class="text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button onclick="clearFilters()" class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Clear Filters</button>
            </div>
        </div>

        <div class="p-6">
            <!-- Bulk Action Bar -->
            <div id="bulkActionBar" class="hidden mb-3 flex items-center gap-3 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-lg">
                <span class="text-sm text-indigo-700"><span id="selectedCount">0</span> selected</span>
                <button onclick="bulkDelete()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Delete Selected</button>
                <button onclick="clearSelection()" class="text-gray-500 hover:text-gray-700 text-xs font-medium">Clear</button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table id="JobTable" class="w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="w-10 px-4 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Job No</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Customer</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Vehicle</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Date In</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Description</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Status</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Progress</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div id="actionModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('actionModal')"></div>
<div id="actionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900" id="actionModalLabel"></h5>
            <button type="button" onclick="closeModal('actionModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div id="modalContent" class="text-center py-5">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600 mx-auto"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<?php include('modals.php'); ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.viewJob = function(id) {
        var modal = document.getElementById('jobDetailsModal');
        var backdrop = document.getElementById('jobDetailsModal-backdrop');
        var modalTitle = document.getElementById('jobDetailsModalLabel');

        modalTitle.innerHTML = '<i class="bi bi-wrench mr-2"></i> Loading Job...';
        modal.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        fetch("<?= base_url('admin/jobs/') ?>" + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(resp) {
            if (!resp.ok) throw new Error('Failed to load job details');
            return resp.json();
        })
        .then(function(data) {
            populateJobModal(data);
        })
        .catch(function(err) {
            modalTitle.innerHTML = '<i class="bi bi-exclamation-triangle mr-2"></i> Error';
            document.getElementById('jobDetailsModalLabel').textContent = 'Error loading job';
            console.error('Error:', err);
        });
    }

    window.populateJobModal = function(d) {
        var fmt = new Intl.NumberFormat('en-KE', { style: 'currency', currency: 'KES' });

        document.getElementById('jobDetailsModalLabel').innerHTML = '<i class="bi bi-wrench mr-2"></i> Job: ' + d.job_no;

        document.getElementById('jd-job-no').textContent = d.job_no;
        var badge = document.getElementById('jd-job-status-badge');
        badge.textContent = d.job_status;
        badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 ' + statusBadgeClass(d.job_status);

        document.getElementById('jd-date-in').textContent = d.date_in || '—';
        document.getElementById('jd-time-in').textContent = d.time_in ? d.time_in.substr(0, 5) : '—';

        var c = d.customer || {};
        document.getElementById('jd-customer-name').textContent = c.name || '—';
        document.getElementById('jd-customer-phone').textContent = c.phone || '—';
        document.getElementById('jd-customer-email').textContent = c.email || '—';
        document.getElementById('jd-customer-address').textContent = c.address || '—';

        var v = d.vehicle || {};
        document.getElementById('jd-vehicle-reg').textContent = v.registration_number || '—';
        document.getElementById('jd-vehicle-make-model').textContent = (v.make || '') + ' ' + (v.model || '');
        document.getElementById('jd-vehicle-vin').textContent = v.vin || '—';
        document.getElementById('jd-vehicle-year').textContent = v.year_of_manufacture || '—';
        document.getElementById('jd-vehicle-color').textContent = v.color || '—';
        document.getElementById('jd-vehicle-transmission').textContent = v.transmission || '—';
        document.getElementById('jd-vehicle-fuel').textContent = v.fuel_type || '—';
        document.getElementById('jd-vehicle-engine').textContent = v.engine_number || '—';
        document.getElementById('jd-vehicle-chassis').textContent = v.chassis_number || '—';
        document.getElementById('jd-vehicle-mileage').textContent = v.mileage != null ? v.mileage.toLocaleString() : '—';

        document.getElementById('jd-diagnosis').textContent = d.diagnosis || '—';
        document.getElementById('jd-diagnosis-category').textContent = d.diagnosis_category || '—';
        document.getElementById('jd-damage-notes').textContent = d.initial_damage_notes || '—';

        document.getElementById('jd-advisor').textContent = d.assigned_service_advisor || '—';
        document.getElementById('jd-mechanic').textContent = d.mechanic_name || 'Unassigned';
        document.getElementById('jd-est-hours').textContent = d.estimated_labor_hours != null ? d.estimated_labor_hours + ' hrs' : '—';
        document.getElementById('jd-quote-amount').textContent = d.quote_amount != null ? fmt.format(d.quote_amount) : '—';
        document.getElementById('jd-quote-status').textContent = d.quote_status || '—';
        document.getElementById('jd-summary').textContent = d.job_summary || '—';

        var partsTbody = document.getElementById('jd-parts-list');
        if (d.parts && d.parts.length) {
            var html = '';
            d.parts.forEach(function(p) {
                var total = (p.quantity_required || 0) * (p.unit_price_at_estimate || 0);
                html += '<tr class="border-b border-gray-100 hover:bg-gray-50">' +
                    '<td class="px-3 py-2 text-sm text-gray-900">' + escHtml(p.name || '—') + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-500">' + escHtml(p.part_number || '—') + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-900 text-right">' + (p.quantity_required || 0) + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-900 text-right">' + fmt.format(p.unit_price_at_estimate || 0) + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-900 text-right font-medium">' + fmt.format(total) + '</td>' +
                    '</tr>';
            });
            partsTbody.innerHTML = html;
        } else {
            partsTbody.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-sm text-gray-500 text-center">No parts recorded.</td></tr>';
        }

        var laborTbody = document.getElementById('jd-labor-list');
        if (d.tasks && d.tasks.length) {
            var html = '';
            d.tasks.forEach(function(t) {
                html += '<tr class="border-b border-gray-100 hover:bg-gray-50">' +
                    '<td class="px-3 py-2 text-sm text-gray-900">' + escHtml(t.task_name || '') + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-900 text-right">' + (t.estimated_hours || 0) + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-900 text-right">' + fmt.format(t.rate_per_hour || 0) + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-900 text-right font-medium">' + fmt.format(t.labor_cost || 0) + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-500">' + escHtml(t.notes || '') + '</td>' +
                    '</tr>';
            });
            laborTbody.innerHTML = html;
        } else {
            laborTbody.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-sm text-gray-500 text-center">No labor tasks recorded.</td></tr>';
        }

        var photosGrid = document.getElementById('jd-photos-grid');
        if (d.photos && d.photos.length) {
            var html = '';
            d.photos.forEach(function(p) {
                var src = "<?= base_url('') ?>" + p.file_path;
                html += '<div onclick="openLightbox(\'' + src + '\')" class="block aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100 hover:opacity-80 transition-opacity cursor-pointer">' +
                    '<img src="' + src + '" alt="' + escHtml(p.file_name || 'Photo') + '" class="w-full h-full object-cover">' +
                    '</div>';
            });
            photosGrid.innerHTML = html;
        } else {
            photosGrid.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-8">No photos uploaded.</p>';
        }

        var lpoTbody = document.getElementById('jd-lpos-list');
        if (d.lpos && d.lpos.length) {
            var html = '';
            d.lpos.forEach(function(l) {
                html += '<tr class="border-b border-gray-100 hover:bg-gray-50">' +
                    '<td class="px-3 py-2 text-sm text-gray-900">' + escHtml(l.lpo_no || '') + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-500">' + escHtml(l.supplier_name || '—') + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-500">' + (l.lpo_date || '—') + '</td>' +
                    '<td class="px-3 py-2 text-sm text-gray-900 text-right">' + fmt.format(l.total_amount || 0) + '</td>' +
                    '<td class="px-3 py-2 text-sm"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' + statusBadgeClass(l.status) + '">' + escHtml(l.status || '') + '</span></td>' +
                    '</tr>';
            });
            lpoTbody.innerHTML = html;
        } else {
            lpoTbody.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-sm text-gray-500 text-center">No LPOs for this job.</td></tr>';
        }

        var invoiceSection = document.getElementById('jd-invoice-section');
        if (d.invoice) {
            var inv = d.invoice;
            var paymentForm = '';
            if (inv.status !== 'Paid' && inv.status !== 'Cancelled') {
                paymentForm = '<div class="mt-4 pt-4 border-t border-gray-200">' +
                    '<h6 class="text-sm font-semibold text-gray-700 mb-2">Record Payment</h6>' +
                    '<form id="quickPaymentForm" onsubmit="return recordQuickPayment(' + inv.invoice_id + ', this)">' +
                    '<?= csrf_field() ?>' +
                    '<div class="grid grid-cols-3 gap-2">' +
                    '<div><input type="number" name="amount" step="0.01" min="0.01" max="' + inv.balance_due + '" placeholder="Amount" required class="w-full text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-indigo-500"></div>' +
                    '<div><select name="payment_method" required class="w-full text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-indigo-500"><option value="">Method</option><option>Cash</option><option>M-Pesa</option><option>Bank Transfer</option><option>Insurance</option><option>Credit</option></select></div>' +
                    '<div><input type="text" name="reference_no" placeholder="Ref. No" class="w-full text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-indigo-500"></div>' +
                    '</div>' +
                    '<button type="submit" class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Record Payment</button>' +
                    '</form></div>';
            }
            invoiceSection.innerHTML =
                '<div class="flex items-center justify-between">' +
                '<div class="space-y-1 text-sm">' +
                '<div><span class="text-gray-500">Invoice No:</span> <span class="font-medium text-gray-900">' + escHtml(inv.invoice_no) + '</span></div>' +
                '<div><span class="text-gray-500">Grand Total:</span> <span class="font-medium text-gray-900">' + fmt.format(inv.grand_total) + '</span></div>' +
                '<div><span class="text-gray-500">Balance Due:</span> <span class="font-medium text-gray-900">' + fmt.format(inv.balance_due) + '</span></div>' +
                '</div>' +
                '<div><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + statusBadgeClass(inv.status) + '">' + escHtml(inv.status) + '</span></div>' +
                '</div>' +
                '<div class="mt-3 flex items-center gap-2"><a href="<?= base_url('admin/invoices/view/') ?>' + inv.invoice_id + '" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Invoice <i class="bi bi-arrow-right"></i></a></div>' +
                paymentForm;
        } else {
            invoiceSection.innerHTML = '<p class="text-sm text-gray-500">No invoice generated for this job.</p>';
        }

        var historyTbody = document.getElementById('jd-history-list');
        fetch("<?= base_url('admin/jobs/status_history/') ?>" + d.id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(hdata) {
            var items = hdata.data || [];
            if (items.length) {
                var html = '';
                items.forEach(function(h) {
                    html += '<tr class="border-b border-gray-100 hover:bg-gray-50">' +
                        '<td class="px-3 py-2 text-sm text-gray-500">' + escHtml(h.from_status || '—') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900 font-medium">' + escHtml(h.to_status || '—') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-500">' + escHtml(h.changed_by_name || '—') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-500">' + escHtml(h.notes || '') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-500">' + (h.created_at || '—') + '</td>' +
                        '</tr>';
                });
                historyTbody.innerHTML = html;
            } else {
                historyTbody.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-sm text-gray-500 text-center">No status history recorded.</td></tr>';
            }
        })
        .catch(function() {
            historyTbody.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-sm text-gray-500 text-center">Failed to load history.</td></tr>';
        });

        var actionsDiv = document.getElementById('jd-status-actions');
        if (d.valid_transitions && d.valid_transitions.length) {
            var html = '<span class="text-xs text-gray-500 mr-1">Update:</span>';
            d.valid_transitions.forEach(function(ts) {
                    html += '<button onclick="updateJobStatus(' + d.id + ', \'' + ts + '\')" class="border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 hover:border-indigo-300 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">' + ts + '</button>';
            });
            actionsDiv.innerHTML = html;
        } else {
            actionsDiv.innerHTML = '<span class="text-xs text-gray-500">No status transitions available.</span>';
        }
    }

    window.statusBadgeClass = function(status) {
        var map = {
            'Completed': 'text-emerald-600 bg-emerald-50',
            'Paid': 'text-emerald-600 bg-emerald-50',
            'Received': 'text-emerald-600 bg-emerald-50',
            'In Progress': 'text-blue-600 bg-blue-50',
            'Diagnosis Complete': 'text-blue-600 bg-blue-50',
            'Quality Check': 'text-purple-600 bg-purple-50',
            'Approved': 'text-indigo-600 bg-indigo-50',
            'Ready for Invoice': 'text-indigo-600 bg-indigo-50',
            'Partially Paid': 'text-amber-600 bg-amber-50',
            'Partially Received': 'text-amber-600 bg-amber-50',
            'Awaiting Diagnosis': 'text-amber-600 bg-amber-50',
            'Awaiting Assignment': 'text-amber-600 bg-amber-50',
            'Awaiting Parts': 'text-amber-600 bg-amber-50',
            'Quote Sent': 'text-amber-600 bg-amber-50',
            'On Hold': 'text-orange-600 bg-orange-50',
            'Rework': 'text-orange-600 bg-orange-50',
            'Overdue': 'text-red-600 bg-red-50',
            'Cancelled': 'text-red-600 bg-red-50',
            'Draft': 'text-gray-600 bg-gray-50',
            'Sent': 'text-blue-600 bg-blue-50',
        };
        return map[status] || 'text-gray-600 bg-gray-100';
    }

    window.escHtml = function(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    window.openLightbox = function(src) {
        document.getElementById('lightboxImage').src = src;
        document.getElementById('lightboxModal').classList.remove('hidden');
        document.getElementById('lightboxModal-backdrop').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    window.closeLightbox = function() {
        document.getElementById('lightboxModal').classList.add('hidden');
        document.getElementById('lightboxModal-backdrop').classList.add('hidden');
        document.getElementById('lightboxImage').src = '';
        document.body.classList.remove('overflow-hidden');
    }

    window.updateJobStatus = function(jobId, newStatus) {
        if (newStatus === 'Ready for Invoice') {
            Swal.fire({
                title: 'Ready for Invoice',
                html:
                    '<div class="text-left">' +
                    '<label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>' +
                    '<input id="swal-discount" type="number" step="0.01" min="0" value="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-3">' +
                    '<label class="block text-sm font-medium text-gray-700 mb-1">Other Charges</label>' +
                    '<input id="swal-other-charges" type="number" step="0.01" min="0" value="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-3">' +
                    '<label class="block text-sm font-medium text-gray-700 mb-1">Other Charges Description</label>' +
                    '<input id="swal-other-charges-desc" type="text" maxlength="255" placeholder="e.g. Admin fee, disposal fee" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">' +
                    '</div>',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Generate Invoice',
                preConfirm: function() {
                    return {
                        discount: parseFloat(document.getElementById('swal-discount').value) || 0,
                        other_charges: parseFloat(document.getElementById('swal-other-charges').value) || 0,
                        other_charges_description: document.getElementById('swal-other-charges-desc').value || ''
                    };
                }
            }).then(function(result) {
                if (!result.isConfirmed) return;
                var data = {
                    new_status: newStatus,
                    discount: result.value.discount,
                    other_charges: result.value.other_charges,
                    other_charges_description: result.value.other_charges_description
                };
                var token = document.querySelector('meta[name="csrf-token"]');
                var name = document.querySelector('meta[name="csrf-name"]');
                if (token && name) {
                    data[name.getAttribute('content')] = token.getAttribute('content');
                }
                $.ajax({
                    url: "<?= base_url('admin/jobs/update_status/') ?>" + jobId,
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Updated!', res.message || 'Status changed to ' + res.new_status, 'success');
                            viewJob(jobId);
                            $('#JobTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Failed to update status.', 'error');
                        }
                    },
                    error: function(xhr) {
                        var res = xhr.responseJSON;
                        Swal.fire('Error', (res && res.message) || 'Failed to update status.', 'error');
                    }
                });
            });
        } else {
            Swal.fire({
                title: 'Change status?',
                text: 'Move to "' + newStatus + '"?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Yes, update'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                var data = { new_status: newStatus };
                var token = document.querySelector('meta[name="csrf-token"]');
                var name = document.querySelector('meta[name="csrf-name"]');
                if (token && name) {
                    data[name.getAttribute('content')] = token.getAttribute('content');
                }
                $.ajax({
                    url: "<?= base_url('admin/jobs/update_status/') ?>" + jobId,
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Updated!', res.message || 'Status changed to ' + res.new_status, 'success');
                            viewJob(jobId);
                            $('#JobTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Failed to update status.', 'error');
                        }
                    },
                    error: function(xhr) {
                        var res = xhr.responseJSON;
                        Swal.fire('Error', (res && res.message) || 'Failed to update status.', 'error');
                    }
                });
            });
        }
    }

    window.switchJobTab = function(tabId, btn) {
        document.querySelectorAll('.job-tab-panel').forEach(function(p) {
            p.classList.add('hidden');
            p.classList.remove('block');
        });
        document.querySelectorAll('.job-tab-btn').forEach(function(b) {
            b.classList.remove('text-indigo-600', 'border-indigo-600');
            b.classList.add('text-gray-500', 'border-transparent');
        });
        document.getElementById(tabId).classList.remove('hidden');
        document.getElementById(tabId).classList.add('block');
        btn.classList.add('text-indigo-600', 'border-indigo-600');
        btn.classList.remove('text-gray-500', 'border-transparent');
    }

    window.deleteJob = function(id) {
        Swal.fire({
            title: 'Delete job?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Yes, delete'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: "<?= base_url('admin/jobs/delete/') ?>" + id,
                method: 'POST',
                data: { <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#JobTable').DataTable().ajax.reload(null, false);
                        Swal.fire('Deleted!', res.message, 'success');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to delete job.', 'error');
                    }
                },
                error: function(xhr) {
                    var res = xhr.responseJSON;
                    Swal.fire('Error', (res && res.message) || 'Failed to delete job.', 'error');
                }
            });
        });
    }

    window.openModal = function(id) {
        document.getElementById(id).classList.remove('hidden');
        var backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    window.closeModal = function(id) {
        document.getElementById(id).classList.add('hidden');
        var backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    window.recordQuickPayment = function(invoiceId, form) {
        var formData = new FormData(form);
        var token = document.querySelector('meta[name="csrf-token"]');
        var name = document.querySelector('meta[name="csrf-name"]');
        if (token && name) {
            formData.append(name.getAttribute('content'), token.getAttribute('content'));
        }
        $.ajax({
            url: "<?= base_url('admin/invoices/record_payment/') ?>" + invoiceId,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Payment Recorded!', '', 'success');
                    form.reset();
                } else {
                    Swal.fire('Error', res.message || 'Failed to record payment.', 'error');
                }
            },
            error: function(xhr) {
                var res = xhr.responseJSON;
                Swal.fire('Error', (res && res.message) || 'Failed to record payment.', 'error');
            }
        });
        return false;
    }

    window.openAssignMechanic = function(jobId, jobNo) {
        document.getElementById('assignMechanicJobId').value = jobId;
        document.getElementById('assignMechanicJobNo').textContent = jobNo;
        var select = document.getElementById('assignMechanicSelect');
        select.innerHTML = '<option value="">Loading mechanics...</option>';

        fetch("<?= base_url('admin/users/fetch') ?>?length=1000", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var users = data.data || data;
            var html = '<option value="">Select Mechanic</option>';
            users.forEach(function(u) {
                if (u.role === 'mechanic') {
                    html += '<option value="' + u.id + '">' + escHtml(u.name) + ' (' + escHtml(u.company_id) + ')</option>';
                }
            });
            select.innerHTML = html;
        })
        .catch(function() {
            select.innerHTML = '<option value="">Failed to load mechanics</option>';
        });

        openModal('assignMechanicModal');
    }

    window.submitAssignMechanic = function() {
        var id = document.getElementById('assignMechanicJobId').value;
        var mechanicId = document.getElementById('assignMechanicSelect').value;
        if (!mechanicId) {
            Swal.fire('Please select a mechanic.', '', 'warning');
            return;
        }
        var token = document.querySelector('meta[name="csrf-token"]');
        var name = document.querySelector('meta[name="csrf-name"]');
        var data = { mechanic_id: mechanicId };
        if (token && name) {
            data[name.getAttribute('content')] = token.getAttribute('content');
        }
        $.ajax({
            url: "<?= base_url('admin/jobs/assign_mechanic/') ?>" + id,
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Assigned!', 'Mechanic assigned successfully.', 'success');
                    closeModal('assignMechanicModal');
                    $('#JobTable').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Error', res.message || 'Failed to assign mechanic.', 'error');
                }
            },
            error: function(xhr) {
                var res = xhr.responseJSON;
                Swal.fire('Error', (res && res.message) || 'Failed to assign mechanic.', 'error');
            }
        });
    }

    window.bulkDelete = function() {
        var ids = [];
        document.querySelectorAll('.row-checkbox:checked').forEach(function(cb) {
            ids.push(cb.getAttribute('data-id'));
        });
        if (!ids.length) return;

        Swal.fire({
            title: 'Delete ' + ids.length + ' job(s)?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Yes, delete'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            var token = document.querySelector('meta[name="csrf-token"]');
            var name = document.querySelector('meta[name="csrf-name"]');
            var data = { ids: ids };
            if (token && name) {
                data[name.getAttribute('content')] = token.getAttribute('content');
            }
            $.ajax({
                url: "<?= base_url('admin/jobs/bulk_delete') ?>",
                method: 'POST',
                data: data,
                dataType: 'json',
                traditional: true,
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Deleted!', res.message, 'success');
                        clearSelection();
                        $('#JobTable').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message || 'Failed to delete.', 'error');
                    }
                },
                error: function(xhr) {
                    var res = xhr.responseJSON;
                    Swal.fire('Error', (res && res.message) || 'Failed to delete.', 'error');
                }
            });
        });
    }

    window.clearSelection = function() {
        document.querySelectorAll('.row-checkbox:checked').forEach(function(cb) {
            cb.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        document.getElementById('bulkActionBar').classList.add('hidden');
    }

    window.clearFilters = function() {
        document.querySelectorAll('.status-filter').forEach(function(b) {
            b.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
            b.classList.add('border-gray-300', 'text-gray-600', 'hover:bg-gray-100');
        });
        document.querySelector('.status-filter[data-status=""]').classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        document.querySelector('.status-filter[data-status=""]').classList.remove('border-gray-300', 'text-gray-600', 'hover:bg-gray-100');
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        $('#JobTable').DataTable().ajax.reload();
    }

    window.exportCSV = function() {
        var status = document.querySelector('.status-filter.bg-indigo-600');
        var s = status ? status.getAttribute('data-status') : '';
        var from = document.getElementById('dateFrom').value;
        var to = document.getElementById('dateTo').value;
        var search = $('#JobTable').DataTable().search();
        var params = new URLSearchParams();
        if (s) params.set('status', s);
        if (from) params.set('date_from', from);
        if (to) params.set('date_to', to);
        if (search) params.set('search', search);
        var qs = params.toString();
        window.location.href = "<?= base_url('admin/jobs/export_csv') ?>" + (qs ? '?' + qs : '');
    }

    $(document).ready(function() {
        var table = $('#JobTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('admin/jobs/fetch') ?>",
                "data": function(d) {
                    d.status = document.querySelector('.status-filter.bg-indigo-600') ? document.querySelector('.status-filter.bg-indigo-600').getAttribute('data-status') : '';
                    d.date_from = document.getElementById('dateFrom').value;
                    d.date_to = document.getElementById('dateTo').value;
                }
            },
            "columns": [
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return '<input type="checkbox" class="row-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-id="' + data.id + '">';
                    }
                },
                { "data": "job_no" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return '<div class="text-sm"><div class="font-medium text-gray-900">' + escHtml(data.customer_name || '—') + '</div><div class="text-xs text-gray-500">' + escHtml(data.customer_phone || '') + '</div></div>';
                    }
                },
                { "data": "registration_number" },
                {
                    "data": "date_in",
                    "render": function(data, type, row) {
                        if (!data) return '—';
                        var parts = data.split('-');
                        if (parts.length !== 3) return data;
                        var d = new Date(parts[0], parts[1] - 1, parts[2]);
                        var now = new Date();
                        var diff = Math.floor((now - d) / (1000 * 60 * 60 * 24));
                        var label = '';
                        if (diff === 0) label = ' (Today)';
                        else if (diff === 1) label = ' (Yesterday)';
                        else if (diff <= 7) label = ' (' + diff + ' days ago)';
                        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                        return '<span class="text-sm text-gray-900" title="' + data + '">' + parts[2] + ' ' + months[parseInt(parts[1])-1] + ' ' + parts[0] + '</span><span class="text-xs text-gray-500 ml-1">' + label + '</span>';
                    }
                },
                { "data": "diagnosis" },
                {
                    "data": "job_status",
                    "render": function(data, type, row) {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + statusBadgeClass(data) + '">' + data + '</span>';
                    }
                },
                {
                    "data": "progress",
                    "orderable": false,
                    "render": function(data, type, row) {
                        if (data < 0) return '<span class="text-xs text-gray-400">—</span>';
                        var color = data < 25 ? 'bg-red-500' : data < 50 ? 'bg-amber-500' : data < 75 ? 'bg-blue-500' : 'bg-emerald-500';
                        return '<div class="flex items-center gap-2"><div class="w-20 bg-gray-200 rounded-full h-2 overflow-hidden"><div class="' + color + ' h-2 rounded-full transition-all" style="width:' + data + '%"></div></div><span class="text-xs text-gray-500">' + data + '%</span></div>';
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return '<div class="flex justify-around items-center">' +
                            '<button class="text-indigo-600 hover:text-indigo-800 p-1" title="View Details" onclick="viewJob(' + data.id + ')">' +
                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>' +
                            '</button>' +
                            '<button class="text-amber-600 hover:text-amber-800 p-1" title="Assign Mechanic" onclick="openAssignMechanic(' + data.id + ', \'' + data.job_no + '\')">' +
                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' +
                            '</button>' +
                            '<button class="text-red-600 hover:text-red-800 p-1" title="Delete" onclick="deleteJob(' + data.id + ')">' +
                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
                            '</button>' +
                        '</div>';
                    }
                }
            ],
            "order": [[1, 'desc']],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "search": "Search:",
                "processing": '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>'
            },
            "drawCallback": function() {
                // Re-bind select-all checkbox
                var selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    selectAll.onchange = function() {
                        var checked = this.checked;
                        document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                            cb.checked = checked;
                        });
                        updateBulkActionBar();
                    };
                }
                document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                    cb.onchange = function() {
                        updateBulkActionBar();
                    };
                });
            },
            "initComplete": function() {
                // Bind status filter clicks
                document.querySelectorAll('.status-filter').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.status-filter').forEach(function(b) {
                            b.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                            b.classList.add('border-gray-300', 'text-gray-600', 'hover:bg-gray-100');
                        });
                        this.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                        this.classList.remove('border-gray-300', 'text-gray-600', 'hover:bg-gray-100');
                        $('#JobTable').DataTable().ajax.reload();
                    });
                });
                // Bind date filter inputs
                document.getElementById('dateFrom').addEventListener('change', function() {
                    $('#JobTable').DataTable().ajax.reload();
                });
                document.getElementById('dateTo').addEventListener('change', function() {
                    $('#JobTable').DataTable().ajax.reload();
                });
            }
        });
    });

    function updateBulkActionBar() {
        var checked = document.querySelectorAll('.row-checkbox:checked').length;
        var bar = document.getElementById('bulkActionBar');
        var count = document.getElementById('selectedCount');
        if (checked > 0) {
            bar.classList.remove('hidden');
            count.textContent = checked;
        } else {
            bar.classList.add('hidden');
        }
    }
</script>
<?= $this->endSection() ?>
