function openModalById(id) {
    var modal = document.getElementById(id);
    var backdrop = document.getElementById(id + '-backdrop');
    if (modal) modal.classList.remove('hidden');
    if (backdrop) backdrop.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeModalById(id) {
    var modal = document.getElementById(id);
    var backdrop = document.getElementById(id + '-backdrop');
    if (modal) modal.classList.add('hidden');
    if (backdrop) backdrop.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

window.openModalById = openModalById;
window.closeModalById = closeModalById;

$(document).ready(function () {
    var selectAllCheckbox = document.getElementById('select_all_customers');
    var deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    var bulkActionForm = document.getElementById('bulkActionForm');

    var customerTable = FlowDesk.serverSideTable('#customerTable', {
        ajax: {
            url: BASE_URL + 'admin/customers/load',
            type: "POST"
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                render: function (data) {
                    return '<input type="checkbox" name="customers[]" value="' + data + '">';
                }
            },
            { data: "name" },
            { data: "phone" },
            { data: "email" },
            {
                data: "vehicle_count",
                render: function (data) {
                    return data > 0 ? data + ' vehicles' : '0 vehicles';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data) {
                    return '<div class="flex items-center gap-2">' +
                        '<button class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors view-customer" title="View Details" data-id="' + data.id + '">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>' +
                        '</button>' +
                        '<button class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors edit-customer" title="Edit Customer" data-id="' + data.id + '">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>' +
                        '</button>' +
                        '<button class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors delete-customer" title="Delete Customer" data-id="' + data.id + '">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
                        '</button>' +
                        '</div>';
                }
            }
        ],
        language: {
            searchPlaceholder: "Search customers..."
        }
    });

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            var checkboxes = customerTable.rows({ page: 'current' }).nodes().to$().find('input[type="checkbox"]');
            checkboxes.prop('checked', this.checked);
        });
    }

    // View customer details
    var customerDetailsModalElement = document.getElementById('customerDetailsModal');

    $('#customerTable tbody').on('click', '.view-customer', async function () {
        var customerId = $(this).data('id');

        $('#customer-profile-picture').attr('src', 'https://placehold.co/100x100/cccccc/333333?text=CS');
        $('#customer-fullname-modal').text('Loading...');
        $('#customer-phone-modal').text('');
        $('#customer-email-modal').text('');
        $('#customer-address-modal').text('');
        $('#customer-created-at').text('');

        $('#overview_fullname').text('Loading...');
        $('#overview_phone').text('');
        $('#overview_email').text('');
        $('#overview_address').text('');
        $('#overview_id').text('');

        $('#customer-vehicles-list').html('<tr><td colspan="7" class="px-3 py-2 text-sm text-gray-500 text-center">Loading vehicles...</td></tr>');
        $('#no-vehicles-message').hide();

        $('#customer-jobs-list').html('<tr><td colspan="5" class="px-3 py-2 text-sm text-gray-500 text-center">Loading job history...</td></tr>');
        $('#customer-invoices-list').html('<tr><td colspan="4" class="px-3 py-2 text-sm text-gray-500 text-center">Loading invoices...</td></tr>');
        $('#customer-communication-list').html('<div class="text-center text-gray-500 py-3 text-sm">Loading communication log...</div>');

        openModalById('customerDetailsModal');

        try {
            var response = await fetch(BASE_URL + 'admin/customers/details/' + customerId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch customer details (Status: ' + response.status + ')');
            }

            var data = await response.json();

            var initials = data.name ? data.name.split(' ').map(function(n) { return n[0]; }).join('').toUpperCase() : 'CS';
            $('#customer-profile-picture').attr('src', 'https://placehold.co/100x100/cccccc/333333?text=' + initials);
            $('#customer-fullname-modal').text(data.name || 'N/A');
            $('#customer-phone-modal').text(data.phone || 'N/A');
            $('#customer-email-modal').text(data.email || 'N/A');
            $('#customer-address-modal').text(data.address || 'N/A');
            $('#customer-created-at').text(data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A');

            $('#overview_fullname').text(data.name || 'N/A');
            $('#overview_phone').text(data.phone || 'N/A');
            $('#overview_email').text(data.email || 'N/A');
            $('#overview_address').text(data.address || 'N/A');
            $('#overview_id').text(data.id || 'N/A');

            var vehiclesList = $('#customer-vehicles-list');
            vehiclesList.empty();
            if (data.vehicles && data.vehicles.length > 0) {
                data.vehicles.forEach(function(vehicle) {
                    vehiclesList.append('<tr>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (vehicle.registration_number || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (vehicle.make || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (vehicle.model || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (vehicle.year_of_manufacture || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (vehicle.vin || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (vehicle.mileage || '0') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (vehicle.reported_problem || 'N/A') + '</td>' +
                        '</tr>');
                });
            } else {
                vehiclesList.append('<tr><td colspan="7" class="px-3 py-2 text-sm text-gray-500 text-center">No vehicles registered for this customer.</td></tr>');
            }

            var jobsList = $('#customer-jobs-list');
            jobsList.empty();
            if (data.jobs && data.jobs.length > 0) {
                data.jobs.forEach(function(job) {
                    jobsList.append('<tr>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (job.job_no || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (job.registration_number || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (job.date_in || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (job.job_status || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (job.diagnosis || 'N/A') + '</td>' +
                        '</tr>');
                });
            } else {
                jobsList.append('<tr><td colspan="5" class="px-3 py-2 text-sm text-gray-500 text-center">No job history available for this customer.</td></tr>');
            }

            var invoicesList = $('#customer-invoices-list');
            invoicesList.empty();
            if (data.invoices && data.invoices.length > 0) {
                data.invoices.forEach(function(invoice) {
                    invoicesList.append('<tr>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (invoice.invoice_no || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (invoice.invoice_date || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">Ksh ' + parseFloat(invoice.grand_total || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</td>' +
                        '<td class="px-3 py-2 text-sm text-gray-900">' + (invoice.status || 'N/A') + '</td>' +
                        '</tr>');
                });
            } else {
                invoicesList.append('<tr><td colspan="4" class="px-3 py-2 text-sm text-gray-500 text-center">No invoices available for this customer.</td></tr>');
            }

            var communicationList = $('#customer-communication-list');
            communicationList.empty();
            if (data.communication_log && data.communication_log.length > 0) {
                data.communication_log.forEach(function(log) {
                    var iconClass = log.type === 'call' ? 'bi-chat-dots-fill text-indigo-600' : 'bi-envelope-fill text-emerald-600';
                    communicationList.append('<div class="px-4 py-3 flex items-start gap-3">' +
                        '<i class="bi ' + iconClass + ' mt-1"></i>' +
                        '<div>' +
                        '<small class="text-gray-500">' + log.date + ' (' + log.agent + ')</small>' +
                        '<p class="text-sm text-gray-700">' + log.message + '</p>' +
                        '</div>' +
                        '</div>');
                });
            } else {
                communicationList.append('<div class="text-center text-gray-500 py-3 text-sm">No communication entries.</div>');
            }

        } catch (error) {
            var modalBody = customerDetailsModalElement.querySelector('.modal-body');
            modalBody.innerHTML = '<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">' +
                '<i class="bi bi-exclamation-circle me-2"></i> Failed to load customer details: ' + error.message +
                '</div>';
            console.error('Error fetching customer details:', error);
        }
    });

    // Edit Customer
    $('#customerTable tbody').on('click', '.edit-customer', function () {
        var customerId = $(this).data('id');
        openModal(BASE_URL + 'admin/customers/edit/' + customerId, 'Edit Customer Details');
    });

    // Delete Customer
    var customerIdToDelete = null;

    document.addEventListener('DOMContentLoaded', function() {
        var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        $('#customerTable tbody').on('click', '.delete-customer', function () {
            customerIdToDelete = $(this).data('id');
            openModalById('confirmDeleteModal');
        });

        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', async function () {
                closeModalById('confirmDeleteModal');

                if (customerIdToDelete) {
                    try {
                        var response = await fetch(BASE_URL + 'admin/customers/bulk_action', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ customers: [customerIdToDelete] })
                        });

                        var responseData = await response.json();

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
        }
    });

    // Bulk delete (placeholder)
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function () {
            var checkedCustomerIds = [];
            customerTable.rows().nodes().to$().find('input[name="customers[]"]:checked').each(function () {
                checkedCustomerIds.push($(this).val());
            });

            if (checkedCustomerIds.length === 0) {
                Swal.fire('No Selection', 'Please select at least one customer to delete.', 'info');
                return;
            }

            Swal.fire({
                title: 'Confirm Deletion',
                text: 'Are you sure you want to delete ' + checkedCustomerIds.length + ' selected customer(s)? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel',
                reverseButtons: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    performBulkDelete(checkedCustomerIds);
                }
            });
        });
    }

    async function performBulkDelete(customerIds) {
        try {
            var response = await fetch(BASE_URL + 'admin/customers/bulk_action', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ customers: customerIds })
            });

            var responseData = await response.json();

            if (response.ok && responseData.status === 'success') {
                Swal.fire('Deleted!', responseData.message, 'success');
                customerTable.ajax.reload();
                if (selectAllCheckbox) selectAllCheckbox.checked = false;
            } else {
                Swal.fire('Error!', responseData.message || 'Failed to perform bulk deletion.', 'error');
            }
        } catch (error) {
            console.error('Error during bulk deletion:', error);
            Swal.fire('Error!', 'An unexpected error occurred during bulk deletion.', 'error');
        }
    }
});
