// Make openModal function globally accessible if it's used in onclick attributes
function openModal(url, title = 'Form') {
    const modalElement = document.getElementById('actionModal');
    const modal = new bootstrap.Modal(modalElement);
    const modalTitle = modalElement.querySelector('.modal-title');
    const modalContent = document.getElementById('modalContent');

    modalContent.innerHTML = `
            <div class="d-flex justify-content-center align-items-center" style="min-height: 100px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
    modalTitle.textContent = title;

    modal.show();

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
            modalContent.innerHTML = data;
        })
        .catch(error => {
            modalTitle.textContent = 'Error';
            modalContent.innerHTML = `<div class="alert alert-danger" role="alert">Error loading content: ${error.message}. Please try again.</div>`;
            console.error('Error loading modal content:', error);
        });
}
window.openModal = openModal;


$(document).ready(function () {
    const selectAllCheckbox = document.getElementById('select_all_customers'); // Updated ID
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn'); // You might want to add this button if you have bulk delete for customers
    const bulkActionForm = document.getElementById('bulkActionForm'); // If you have bulk delete form

    // --- DataTables Initialization ---
    const customerTable = $('#customerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE_URL + 'admin/customers/load',
            type: "POST"
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                render: function (data, type, row) {
                    return `<input type="checkbox" name="customers[]" value="${data}">`;
                }
            },
            { data: "name" },
            { data: "phone" },
            { data: "email" },
            {
                data: "vehicle_count", // Assuming backend provides this
                render: function (data, type, row) {
                    return data > 0 ? `${data} vehicles` : '0 vehicles';
                }
            },
            {
                data: null, // For action buttons
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                            <div class="d-flex justify-content-around">
                                <button class="icon-btn text-primary view-customer" title="View Details" data-id="${row.id}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn text-info edit-customer" title="Edit Customer" data-id="${row.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="icon-btn text-danger delete-customer" title="Delete Customer" data-id="${row.id}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        `;
                }
            }
        ],
        dom: '<"top d-flex justify-content-between flex-wrap"<"mb-2"l><"mb-2"f>>rt<"bottom d-flex justify-content-between flex-wrap"<"mb-2"i><"mb-2"p>><"clear">',
        language: {
            search: "",
            searchPlaceholder: "Search customers...",
        }
    });

    // --- Checkbox Select All (Optional, if you implement bulk delete) ---
    if (selectAllCheckbox) { // Check if element exists before adding listener
        selectAllCheckbox.addEventListener('change', function () {
            const checkboxes = customerTable.rows({ page: 'current' }).nodes().to$().find('input[type="checkbox"]');
            checkboxes.prop('checked', this.checked);
        });
    }


    // --- Customer Details Modal ---
    const customerDetailsModalElement = document.getElementById('customerDetailsModal');

    let customerDetailsModal = null;

    if (customerDetailsModalElement) {
        customerDetailsModal = new bootstrap.Modal(customerDetailsModalElement);
    }


    $('#customerTable tbody').on('click', '.view-customer', async function () {
        const customerId = $(this).data('id');

        // Clear previous data and show loading spinners/placeholders
        $('#customer-profile-picture').attr('src', 'https://placehold.co/100x100/cccccc/333333?text=CS');
        $('#customer-fullname-modal').text('Loading...');
        $('#customer-phone-modal').text('');
        $('#customer-email-modal').text('');
        $('#customer-address-modal').text('');
        $('#customer-created-at').text('');

        // Overview tab
        $('#overview_fullname').text('Loading...');
        $('#overview_phone').text('');
        $('#overview_email').text('');
        $('#overview_address').text('');
        $('#overview_id').text('');

        // Vehicles tab
        $('#customer-vehicles-list').html('<tr><td colspan="7" class="text-center text-muted">Loading vehicles...</td></tr>');
        $('#no-vehicles-message').hide(); // Hide initially

        // Other tabs - placeholders
        $('#customer-jobs-list').html('<tr><td colspan="5" class="text-center text-muted">Loading job history...</td></tr>');
        $('#customer-invoices-list').html('<tr><td colspan="4" class="text-center text-muted">Loading invoices...</td></tr>');
        $('#customer-communication-list').html('<div class="text-center text-muted py-3">Loading communication log...</div>');


        if (customerDetailsModal) {
            customerDetailsModal.show();
        }


        try {
            const response = await fetch(`${BASE_URL}admin/customers/details/${customerId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });


            if (!response.ok) {
                throw new Error(`Failed to fetch customer details (Status: ${response.status})`);
            }

            const data = await response.json();

            // Populate Customer Summary (Left Column)
            // You might generate initials for the photo or use a default if no photo field exists
            const initials = data.name ? data.name.split(' ').map(n => n[0]).join('').toUpperCase() : 'CS';
            $('#customer-profile-picture').attr('src', `https://placehold.co/100x100/cccccc/333333?text=${initials}`);
            $('#customer-fullname-modal').text(data.name || 'N/A');
            $('#customer-phone-modal').text(data.phone || 'N/A');
            $('#customer-email-modal').text(data.email || 'N/A');
            $('#customer-address-modal').text(data.address || 'N/A');
            $('#customer-created-at').text(data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A');

            // Populate Overview Tab
            $('#overview_fullname').text(data.name || 'N/A');
            $('#overview_phone').text(data.phone || 'N/A');
            $('#overview_email').text(data.email || 'N/A');
            $('#overview_address').text(data.address || 'N/A');
            $('#overview_id').text(data.id || 'N/A');


            // Populate Vehicles Owned Tab
            const vehiclesList = $('#customer-vehicles-list');
            vehiclesList.empty(); // Clear loading message
            if (data.vehicles && data.vehicles.length > 0) {
                data.vehicles.forEach(vehicle => {
                    vehiclesList.append(`
                            <tr>
                                <td>${vehicle.registration_number || 'N/A'}</td>
                                <td>${vehicle.make || 'N/A'}</td>
                                <td>${vehicle.model || 'N/A'}</td>
                                <td>${vehicle.year_of_manufacture || 'N/A'}</td>
                                <td>${vehicle.vin || 'N/A'}</td>
                                <td>${vehicle.mileage || '0'}</td>
                                <td>${vehicle.reported_problem || 'N/A'}</td>
                            </tr>
                        `);
                });
            } else {
                vehiclesList.append('<tr><td colspan="7" class="text-center text-muted">No vehicles registered for this customer.</td></tr>');
            }

            // Populate Job History Tab (MOCK DATA for now)
            const jobsList = $('#customer-jobs-list');
            jobsList.empty();
            if (data.jobs && data.jobs.length > 0) {
                data.jobs.forEach(job => {
                    jobsList.append(`
                            <tr>
                                <td>${job.job_no || 'N/A'}</td>
                                <td>${job.registration_number || 'N/A'}</td>
                                <td>${job.date_in || 'N/A'}</td>
                                <td>${job.job_status || 'N/A'}</td>
                                <td>${job.diagnosis || 'N/A'}</td>
                            </tr>
                        `);
                });
            } else {
                jobsList.append('<tr><td colspan="5" class="text-center text-muted">No job history available for this customer.</td></tr>');
            }

            // Populate Invoices Tab (MOCK DATA for now)
            const invoicesList = $('#customer-invoices-list');
            invoicesList.empty();
            if (data.invoices && data.invoices.length > 0) {
                data.invoices.forEach(invoice => {
                    invoicesList.append(`
                            <tr>
                                <td>${invoice.invoice_no || 'N/A'}</td>
                                <td>${invoice.date || 'N/A'}</td>
                                <td>Ksh ${parseFloat(invoice.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                <td>${invoice.status || 'N/A'}</td>
                            </tr>
                        `);
                });
            } else {
                invoicesList.append('<tr><td colspan="4" class="text-center text-muted">No invoices available for this customer.</td></tr>');
            }

            // Populate Communication Log Tab (MOCK DATA for now)
            const communicationList = $('#customer-communication-list');
            communicationList.empty();
            if (data.communication_log && data.communication_log.length > 0) {
                data.communication_log.forEach(log => {
                    communicationList.append(`
                             <div class="list-group-item d-flex align-items-start gap-3">
                                <i class="bi ${log.type === 'call' ? 'bi-chat-dots-fill text-primary' : 'bi-envelope-fill text-success'} mt-1"></i>
                                <div>
                                    <small class="text-muted">${log.date} (${log.agent})</small>
                                    <p class="mb-1">${log.message}</p>
                                </div>
                            </div>
                        `);
                });
            } else {
                communicationList.append('<div class="text-center text-muted py-3">No communication entries.</div>');
            }


        } catch (error) {
            const modalBody = customerDetailsModalElement.querySelector('.modal-body');
            modalBody.innerHTML = `<div class="alert alert-danger" role="alert">
                                            <i class="bi bi-exclamation-circle me-2"></i> Failed to load customer details: ${error.message}
                                        </div>`;
            console.error('Error fetching customer details:', error);
        }
    });

    // --- Edit Customer Logic ---
    $('#customerTable tbody').on('click', '.edit-customer', function () {
        const customerId = $(this).data('id');
        openModal(`${BASE_URL}admin/customers/edit/${customerId}`, 'Edit Customer Details');
    });

    // --- Delete Customer Logic ---
    let customerIdToDelete = null;

    document.addEventListener('DOMContentLoaded', function() {
        const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        let confirmDeleteModal;

        if (confirmDeleteModalElement) {
            confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement);
        } else {
            console.warn('Confirm Delete Modal element not found. Ensure it exists in your HTML.');
            return;
        }

        // When delete icon is clicked in table
        $('#customerTable tbody').on('click', '.delete-customer', function () {
            customerIdToDelete = $(this).data('id');
            confirmDeleteModal.show();
        });

        // When delete is confirmed
        confirmDeleteBtn?.addEventListener('click', async function () {
            confirmDeleteModal.hide();

            if (customerIdToDelete) {
                try {
                    const response = await fetch(`${BASE_URL}admin/customers/bulk_action`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ customers: [customerIdToDelete] })
                    });

                    const responseData = await response.json();

                    if (response.ok && responseData.status === 'success') {
                        Swal.fire('Deleted!', responseData.message, 'success');
                        customerTable.ajax.reload();
                    } else {
                        Swal.fire('Error!', responseData.message || 'Failed to delete customer.', 'error');
                    }
                } catch (error) {
                    console.error('Error during deletion:', error);
                    Swal.fire('Error!', 'An unexpected error occurred during deletion.', 'error');
                } finally {
                    customerIdToDelete = null;
                }
            }
        });
    });

    // --- Placeholder for Bulk Delete button if you add it ---
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function () {
            const checkedCustomerIds = [];
            customerTable.rows().nodes().to$().find('input[name="customers[]"]:checked').each(function () {
                checkedCustomerIds.push($(this).val());
            });

            if (checkedCustomerIds.length === 0) {
                Swal.fire('No Selection', 'Please select at least one customer to delete.', 'info');
                return;
            }

            Swal.fire({
                title: 'Confirm Deletion',
                text: `Are you sure you want to delete ${checkedCustomerIds.length} selected customer(s)? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form with selected IDs for bulk action
                    // You'll need to create hidden inputs for these IDs or use AJAX to send
                    // For simplicity, directly calling the AJAX here, assuming backend handles array
                    performBulkDelete(checkedCustomerIds);
                }
            });
        });
    }

    async function performBulkDelete(customerIds) {
        try {
            const response = await fetch(`BASE_URL + 'admin/customers/bulk_action'`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ customers: customerIds })
            });

            const responseData = await response.json();

            if (response.ok && responseData.status === 'success') {
                Swal.fire('Deleted!', responseData.message, 'success');
                customerTable.ajax.reload();
                if (selectAllCheckbox) selectAllCheckbox.checked = false; // Uncheck select all
            } else {
                Swal.fire('Error!', responseData.message || 'Failed to perform bulk deletion.', 'error');
            }
        } catch (error) {
            console.error('Error during bulk deletion:', error);
            Swal.fire('Error!', 'An unexpected error occurred during bulk deletion.', 'error');
        }
    }

});
