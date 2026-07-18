<?= $this->extend('layouts/main') ?>

<?php $pageTitle = 'Job Cards'; ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Job List</h3>
            <div class="flex items-center gap-2">
                <button onclick="openModal('addJobModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Job Intake
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table id="JobTable" class="w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Job No</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Vehicle Reg</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Description</th>
                            <th class="text-gray-500 text-xs font-medium uppercase tracking-wider px-4 py-3 text-left">Status</th>
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
    function viewJob(id) {
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

    function populateJobModal(d) {
        var fmt = new Intl.NumberFormat('en-KE', { style: 'currency', currency: 'KES' });

        document.getElementById('jobDetailsModalLabel').innerHTML = '<i class="bi bi-wrench mr-2"></i> Job: ' + d.job_no;

        // Header
        document.getElementById('jd-job-no').textContent = d.job_no;
        var badge = document.getElementById('jd-job-status-badge');
        badge.textContent = d.job_status;
        badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 ' + statusBadgeClass(d.job_status);

        // Dates
        document.getElementById('jd-date-in').textContent = d.date_in || '—';
        document.getElementById('jd-time-in').textContent = d.time_in ? d.time_in.substr(0, 5) : '—';

        // Customer
        var c = d.customer || {};
        document.getElementById('jd-customer-name').textContent = c.name || '—';
        document.getElementById('jd-customer-phone').textContent = c.phone || '—';
        document.getElementById('jd-customer-email').textContent = c.email || '—';
        document.getElementById('jd-customer-address').textContent = c.address || '—';

        // Vehicle
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

        // Diagnosis
        document.getElementById('jd-diagnosis').textContent = d.diagnosis || '—';
        document.getElementById('jd-diagnosis-category').textContent = d.diagnosis_category || '—';
        document.getElementById('jd-damage-notes').textContent = d.initial_damage_notes || '—';

        // Assignment
        document.getElementById('jd-advisor').textContent = d.assigned_service_advisor || '—';
        document.getElementById('jd-mechanic').textContent = d.mechanic_name || 'Unassigned';
        document.getElementById('jd-est-hours').textContent = d.estimated_labor_hours != null ? d.estimated_labor_hours + ' hrs' : '—';
        document.getElementById('jd-quote-amount').textContent = d.quote_amount != null ? fmt.format(d.quote_amount) : '—';
        document.getElementById('jd-quote-status').textContent = d.quote_status || '—';
        document.getElementById('jd-summary').textContent = d.job_summary || '—';

        // Parts
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

        // Labor
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

        // Photos
        var photosGrid = document.getElementById('jd-photos-grid');
        if (d.photos && d.photos.length) {
            var html = '';
            d.photos.forEach(function(p) {
                var src = "<?= base_url('') ?>" + p.file_path;
                html += '<a href="' + src + '" target="_blank" class="block aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100 hover:opacity-80 transition-opacity">' +
                    '<img src="' + src + '" alt="' + escHtml(p.file_name || 'Photo') + '" class="w-full h-full object-cover">' +
                    '</a>';
            });
            photosGrid.innerHTML = html;
        } else {
            photosGrid.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-8">No photos uploaded.</p>';
        }

        // LPOs
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

        // Invoice
        var invoiceSection = document.getElementById('jd-invoice-section');
        if (d.invoice) {
            var inv = d.invoice;
            invoiceSection.innerHTML =
                '<div class="flex items-center justify-between">' +
                '<div class="space-y-1 text-sm">' +
                '<div><span class="text-gray-500">Invoice No:</span> <span class="font-medium text-gray-900">' + escHtml(inv.invoice_no) + '</span></div>' +
                '<div><span class="text-gray-500">Grand Total:</span> <span class="font-medium text-gray-900">' + fmt.format(inv.grand_total) + '</span></div>' +
                '<div><span class="text-gray-500">Balance Due:</span> <span class="font-medium text-gray-900">' + fmt.format(inv.balance_due) + '</span></div>' +
                '</div>' +
                '<div><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + statusBadgeClass(inv.status) + '">' + escHtml(inv.status) + '</span></div>' +
                '</div>' +
                '<div class="mt-3"><a href="<?= base_url('admin/invoices/view/') ?>' + inv.invoice_id + '" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Invoice <i class="bi bi-arrow-right"></i></a></div>';
        } else {
            invoiceSection.innerHTML = '<p class="text-sm text-gray-500">No invoice generated for this job.</p>';
        }

        // Status History
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

        // Status transition buttons
        var actionsDiv = document.getElementById('jd-status-actions');
        if (d.valid_transitions && d.valid_transitions.length) {
            var html = '<span class="text-xs text-gray-500 mr-1">Update:</span>';
            d.valid_transitions.forEach(function(ts) {
                html += '<button onclick="updateJobStatus(' + d.id + ', \'' + ts + '\')" class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">' + ts + '</button>';
            });
            actionsDiv.innerHTML = html;
        } else {
            actionsDiv.innerHTML = '<span class="text-xs text-gray-500">No status transitions available.</span>';
        }
    }

    function statusBadgeClass(status) {
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

    function escHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function updateJobStatus(jobId, newStatus) {
        if (!confirm('Change status to "' + newStatus + '"?')) return;
        var token = document.querySelector('meta[name="csrf-token"]');
        var name = document.querySelector('meta[name="csrf-name"]');
        var data = { new_status: newStatus };
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
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Updated!', 'Status changed to ' + res.new_status, 'success');
                    } else {
                        alert('Status updated to ' + res.new_status);
                    }
                    viewJob(jobId);
                    $('#JobTable').DataTable().ajax.reload(null, false);
                } else {
                    alert(res.message || 'Failed to update status.');
                }
            },
            error: function(xhr) {
                var res = xhr.responseJSON;
                alert((res && res.message) || 'Failed to update status.');
            }
        });
    }

    function switchJobTab(tabId, btn) {
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

    function deleteJob(id) {
        if (!confirm('Are you sure you want to delete this job card? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: "<?= base_url('admin/jobs/delete/') ?>" + id,
            method: 'POST',
            data: {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#JobTable').DataTable().ajax.reload(null, false);
                    alert(res.message);
                } else {
                    alert(res.message || 'Failed to delete job.');
                }
            },
            error: function(xhr) {
                const res = xhr.responseJSON;
                alert((res && res.message) || 'Failed to delete job.');
            }
        });
    }
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        const backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        const backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    $(document).ready(function() {        
        const table = $('#JobTable').DataTable({
            "ajax": "<?= base_url('admin/jobs/fetch') ?>",
            "columns": [{
                    "data": "job_no"
                },
                {
                    "data": "registration_number"
                },
                {
                    "data": "diagnosis"
                },
                {
                    "data": "job_status"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div style="display: flex; justify-content: space-around;">
                                
                                <button class="icon-btn text-primary" title="View Details" onclick="viewJob(${data.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn text-danger" title="Delete" onclick="deleteJob(${data.id})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
    });
</script>
<?= $this->endSection() ?>
