$(document).ready(function () {
    const selectAllCheckbox = $('#select_all');
    const deleteSelectedBtn = $('#deleteSelectedBtn');
    const bulkActionForm = $('#bulkActionForm');
    const statusFilter = $('#status-filter');

    // Modals
    const actionModalElement = document.getElementById('actionModal');
    const actionModal = new bootstrap.Modal(actionModalElement);
    const actionModalTitle = document.getElementById('actionModalLabel');
    const modalContentDiv = document.getElementById('modalContent');

    const subletDetailsModalElement = document.getElementById('subletDetailsModal');
    const subletDetailsModal = new bootstrap.Modal(subletDetailsModalElement);
    const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
    const confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement);
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // --- Helper function for status badge colors ---
    function getStatusBadgeClass(status) {
        switch (status) {
            case 'Pending': return 'status-badge-pending';
            case 'In Progress': return 'status-badge-in-progress';
            case 'Completed': return 'status-badge-completed';
            case 'Invoiced': return 'status-badge-invoiced';
            case 'Paid': return 'status-badge-paid';
            case 'Cancelled': return 'status-badge-cancelled';
            default: return 'bg-secondary text-white'; // Default Bootstrap grey
        }
    }

    // --- DataTables Initialization ---
    const subletTable = $('#subletTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `${BASE_URL}admin/sublets/load`,
            type: 'POST',
            data: function (d) {
                d.status_filter = statusFilter.val(); // Add status filter
            }
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                render: function (data, type, row) {
                    return `<input type="checkbox" name="sublets[]" value="${data}">`;
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
                    const badgeClass = getStatusBadgeClass(data);
                    return `<span class="status-badge ${badgeClass}">${data}</span>`;
                }
            },
            { data: 'date_sent' },
            { data: 'date_returned' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    // Pass the Job ID for the View Details button, which will fetch full details
                    return `
                        <button type="button" class="btn btn-primary btn-sm view-sublet" data-id="${data}">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button type="button" class="btn btn-info btn-sm edit-sublet ms-1" data-id="${data}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    `;
                }
            }
        ],
        dom: '<"top d-flex justify-content-between flex-wrap"<"mb-2"l><"mb-2"f>>rt<"bottom d-flex justify-content-between flex-wrap"<"mb-2"i><"mb-2"p>><"clear">',
        language: {
            search: "",
            searchPlaceholder: "Search sublets...",
        },
        initComplete: function () {
            // Move the custom status filter into the DataTables filter area
            $('#subletTable_filter').prepend(statusFilter.detach());
            statusFilter.addClass('ms-2'); // Add some margin

            // Apply filter on change
            statusFilter.on('change', function () {
                subletTable.ajax.reload();
            });
        }
    });

    // --- Checkbox Select All for DataTables ---
    selectAllCheckbox.on('change', function () {
        const checkboxes = subletTable.rows({ page: 'current' }).nodes().to$().find('input[type="checkbox"]');
        checkboxes.prop('checked', this.checked);
    });

    // --- Custom openModal Function for Add/Edit Forms ---
    window.openModal = function (url, title = 'Form') {
        actionModalTitle.textContent = title;
        modalContentDiv.innerHTML = `
            <div class="d-flex justify-content-center align-items-center" style="min-height: 150px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        actionModal.show();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.text();
            })
            .then(data => {
                modalContentDiv.innerHTML = data;
            })
            .catch(error => {
                actionModalTitle.textContent = 'Error';
                modalContentDiv.innerHTML = `<div class="alert alert-danger" role="alert">Error loading content: ${error.message}. Please try again.</div>`;
                console.error('Error loading modal content:', error);
            });
    }

    // --- View Sublet Details Modal ---
    $('#subletTable tbody').on('click', '.view-sublet', async function () {
        const subletId = $(this).data('id');

        // Clear previous data and show loading spinner or placeholder text
        $('#detail_id').text('Loading...');
        $('#detail_job_no').text('');
        $('#detail_vehicle_reg').text('');
        $('#detail_provider_name').text('');
        $('#detail_cost').text('');
        $('#detail_status').text('').removeClass().addClass('info-value'); // Reset status badge
        $('#detail_date_sent').text('');
        $('#detail_date_returned').text('');
        $('#detail_description').text('');
        $('#detail_notes').text('');
        $('#detail_created_at').text('');
        $('#detail_updated_at').text('');

        subletDetailsModal.show();

        try {
            const response = await fetch(BASE_URL + 'admin/sublets/details/' + subletId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch sublet details (Status: ${response.status})`);
            }

            const data = await response.text(); // Get raw HTML response
            // Directly inject the HTML into the modal body (assuming _details.php renders complete content)
            subletDetailsModalElement.querySelector('.modal-body').innerHTML = data;

            // Re-fetch elements and update data as necessary if _details.php doesn't fully populate
            // If _details.php already renders everything with PHP, then no further JS updates needed here.
            // However, if you need to apply JS logic to the newly loaded content, do it here.

        } catch (error) {
            const modalBody = subletDetailsModalElement.querySelector('.modal-body');
            modalBody.innerHTML = `<div class="alert alert-danger" role="alert">
                                        <i class="bi bi-exclamation-circle me-2"></i> Failed to load sublet details: ${error.message}
                                   </div>`;
            console.error('Error fetching sublet details:', error);
        }
    });

    // --- Edit Sublet Button ---
    $('#subletTable tbody').on('click', '.edit-sublet', function () {
        const subletId = $(this).data('id');
        openModal(BASE_URL + 'admin/sublets/edit/' + subletId, 'Edit Sublet');
    });

    // --- Bulk Delete Confirmation ---
    deleteSelectedBtn.on('click', function () {
        const checkedSublets = subletTable.rows().nodes().to$().find('input[name="sublets[]"]:checked');
        if (checkedSublets.length === 0) {
            Swal.fire({
                title: 'No Selection',
                text: 'Please select at least one sublet to delete.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
            return;
        }
        confirmDeleteModal.show();
    });

    // --- Confirm Delete Action ---
      $(confirmDeleteBtn).on('click', function() {
        confirmDeleteModal.hide();
        // Set hidden input in the form to tell the backend what action to perform
        bulkActionForm.append('<input type="hidden" name="action" value="delete">');
        // Get all checked IDs and append to form as hidden inputs
        subletTable.rows().nodes().to$().find('input[name="sublets[]"]:checked').each(function() {
            bulkActionForm.append(`<input type="hidden" name="ids[]" value="${$(this).val()}">`);
        });
        bulkActionForm.submit(); // Submit the form for bulk deletion
    });


    // --- Listen for custom event to reload DataTables after save ---
    document.addEventListener('subletSaved', function () {
        subletTable.ajax.reload(null, false); // Reload data, keep current page
    });
});
