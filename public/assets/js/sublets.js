$(document).ready(function () {
    const selectAllCheckbox = $('#select_all');
    const deleteSelectedBtn = $('#deleteSelectedBtn');
    const bulkActionForm = $('#bulkActionForm');
    const statusFilter = $('#status-filter');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    function openModalFn(name) {
        document.getElementById(name).classList.remove('hidden');
        var backdrop = document.getElementById(name + '-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModalFn(name) {
        document.getElementById(name).classList.add('hidden');
        var backdrop = document.getElementById(name + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function getStatusBadgeClass(status) {
        switch (status) {
            case 'Pending': return 'bg-amber-100 text-amber-700';
            case 'In Progress': return 'bg-blue-100 text-blue-700';
            case 'Completed': return 'bg-emerald-100 text-emerald-700';
            case 'Invoiced': return 'bg-blue-100 text-blue-700';
            case 'Paid': return 'bg-emerald-100 text-emerald-700';
            case 'Cancelled': return 'bg-red-100 text-red-700';
            default: return 'bg-gray-100 text-gray-700';
        }
    }

    const subletTable = $('#subletTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE_URL + 'admin/sublets/load',
            type: 'POST',
            data: function (d) {
                d.status_filter = statusFilter.val();
                var csrf = getCsrfMeta();
                d[csrf.name] = csrf.hash;
            }
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" name="sublets[]" value="' + data + '">';
                }
            },
            { data: 'id' },
            { data: 'job_no' },
            { data: 'description' },
            { data: 'provider_name' },
            {
                data: 'cost',
                render: $.fn.dataTable.render.number(',', '.', 2, 'KES ')
            },
            {
                data: 'status',
                render: function (data, type, row) {
                    var badgeClass = getStatusBadgeClass(data);
                    return '<span class="text-xs font-medium px-2.5 py-0.5 rounded-full ' + badgeClass + '">' + data + '</span>';
                }
            },
            { data: 'date_sent' },
            { data: 'date_returned' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors view-sublet" data-id="' + data + '"><i class="bi bi-eye"></i> View</button> <button type="button" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors edit-sublet ms-1" data-id="' + data + '"><i class="bi bi-pencil"></i> Edit</button>';
                }
            }
        ],
        dom: '<"top d-flex justify-content-between flex-wrap"<"mb-2"l><"mb-2"f>>rt<"bottom d-flex justify-content-between flex-wrap"<"mb-2"i><"mb-2"p>><"clear">',
        language: {
            search: "",
            searchPlaceholder: "Search sublets...",
        },
        initComplete: function () {
            $('#subletTable_filter').prepend(statusFilter.detach());
            statusFilter.addClass('ms-2');
            statusFilter.on('change', function () {
                subletTable.ajax.reload();
            });
        }
    });

    selectAllCheckbox.on('change', function () {
        var checkboxes = subletTable.rows({ page: 'current' }).nodes().to$().find('input[type="checkbox"]');
        checkboxes.prop('checked', this.checked);
    });

    $('#subletTable tbody').on('click', '.view-sublet', async function () {
        var subletId = $(this).data('id');
        var body = document.getElementById('subletDetailsBody');
        if (body) {
            body.innerHTML = '<div class="flex justify-center items-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';
        }
        openModalFn('subletDetailsModal');

        try {
            var response = await fetch(BASE_URL + 'admin/sublets/details/' + subletId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('Failed to fetch sublet details (Status: ' + response.status + ')');
            var data = await response.text();
            if (body) body.innerHTML = data;
        } catch (error) {
            if (body) body.innerHTML = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg"><i class="bi bi-exclamation-circle me-2"></i> Failed to load sublet details: ' + error.message + '</div>';
            console.error('Error fetching sublet details:', error);
        }
    });

    $('#subletTable tbody').on('click', '.edit-sublet', function () {
        var subletId = $(this).data('id');
        editSublet(subletId);
    });

    deleteSelectedBtn.on('click', function () {
        var checkedSublets = subletTable.rows().nodes().to$().find('input[name="sublets[]"]:checked');
        if (checkedSublets.length === 0) {
            Swal.fire({ title: 'No Selection', text: 'Please select at least one sublet to delete.', icon: 'info', confirmButtonText: 'OK' });
            return;
        }
        openModalFn('confirmDeleteModal');
    });

    $(confirmDeleteBtn).on('click', function() {
        closeModalFn('confirmDeleteModal');
        bulkActionForm.append('<input type="hidden" name="action" value="delete">');
        subletTable.rows().nodes().to$().find('input[name="sublets[]"]:checked').each(function() {
            bulkActionForm.append('<input type="hidden" name="ids[]" value="' + $(this).val() + '">');
        });
        bulkActionForm.submit();
    });

    document.addEventListener('subletSaved', function () {
        subletTable.ajax.reload(null, false);
    });
});

window.editSublet = function(id) {
    fetch(BASE_URL + 'admin/sublets/edit/' + id, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('edit_sublet_id').value = data.id;
        document.getElementById('edit_sublet_job_card_id').value = data.job_card_id;
        document.getElementById('edit_sublet_provider_id').value = data.sublet_provider_id;
        document.getElementById('edit_sublet_description').value = data.description;
        document.getElementById('edit_sublet_cost').value = data.cost;
        document.getElementById('edit_sublet_status').value = data.status;
        document.getElementById('edit_sublet_date_sent').value = data.date_sent;
        document.getElementById('edit_sublet_date_returned').value = data.date_returned || '';
        document.getElementById('edit_sublet_notes').value = data.notes || '';
        openModalFn('editSubletModal');
    })
    .catch(function(err) {
        Swal.fire('Error!', 'Failed to fetch sublet details: ' + err.message, 'error');
    });
};

function submitSubletForm(formId, modalId) {
    var form = document.getElementById(formId);
    var formData = new FormData(form);
    fetch(BASE_URL + 'admin/sublets/save', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(result) {
        if (result.status === 'success') {
            closeModalFn(modalId);
            document.dispatchEvent(new CustomEvent('subletSaved'));
            Swal.fire('Success!', result.message, 'success');
        } else {
            Swal.fire('Error!', result.message || 'Failed to save sublet.', 'error');
        }
    })
    .catch(function(err) {
        Swal.fire('Error!', 'An unexpected error occurred: ' + err.message, 'error');
    });
}

$(document).ready(function() {
    $('#addSubletForm').on('submit', function(e) {
        e.preventDefault();
        submitSubletForm('addSubletForm', 'addSubletModal');
    });
    $('#editSubletForm').on('submit', function(e) {
        e.preventDefault();
        submitSubletForm('editSubletForm', 'editSubletModal');
    });
});
