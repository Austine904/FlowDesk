$(document).ready(function () {
    var table = $('#vehicleTable').DataTable({
        "ajax": BASE_URL + 'admin/vehicles/fetch',
        "columns": [{
            "data": "id"
        },
        {
            "data": "registration_number"
        },
        {
            "data": "owner_id"
        },
        {
            "data": "vehicle"
        },
        {
            "data": "color"
        },
        {
            "data": "status"
        },
        {
            "data": null,
            "render": function (data) {
                return '<div class="flex items-center gap-2">' +
                    '<button onclick="editVehicle(' + data.id + ')" title="Edit" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">' +
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>' +
                    '</button>' +
                    '<button onclick="viewVehicleDetails(' + data.id + ')" title="View" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">' +
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>' +
                    '</button>' +
                    '<button onclick="deleteVehicle(' + data.id + ')" title="Delete" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">' +
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
                    '</button>' +
                '</div>';
            }
        }
        ]
    });

    window.closeModal = function(id) {
        document.getElementById(id).classList.add('hidden');
        var backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    window.openModal = function(id) {
        document.getElementById(id).classList.remove('hidden');
        var backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    // Add Vehicle form submission
    $('#addVehicleForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                alert('Vehicle added successfully!');
                closeModal('addVehicleModal');
                table.ajax.reload();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Failed to add vehicle.');
            }
        });
    });

    // View Vehicle Details
    window.viewVehicleDetails = function (id) {
        $.ajax({
            url: BASE_URL + 'admin/vehicles/details/' + id,
            method: 'GET',
            success: function (data) {
                document.getElementById('v_registration_no').textContent = data.registration_number ?? 'N/A';
                document.getElementById('v_make').textContent = data.make ?? 'N/A';
                document.getElementById('v_model').textContent = data.model ?? 'N/A';
                document.getElementById('v_year').textContent = data.year_of_manufacture ?? 'N/A';
                document.getElementById('v_color').textContent = data.color ?? 'N/A';
                document.getElementById('v_engine_no').textContent = data.engine_number ?? 'N/A';
                document.getElementById('v_chassis_no').textContent = data.chassis_number ?? 'N/A';
                document.getElementById('v_vin').textContent = data.vin ?? 'N/A';
                document.getElementById('v_fuel_type').textContent = data.fuel_type ?? 'N/A';
                document.getElementById('v_transmission').textContent = data.transmission ?? 'N/A';
                document.getElementById('v_mileage').textContent = data.mileage ? data.mileage + ' km' : 'N/A';
                document.getElementById('v_status').textContent = data.status ?? 'N/A';

                openModal('viewVehicleModal');
            },
            error: function () {
                alert('Failed to fetch vehicle details.');
            }
        });
    };

    // Edit Vehicle
    window.editVehicle = function (id) {
        $.ajax({
            url: BASE_URL + 'admin/vehicles/fetch/' + id,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#edit_registration_number').val(data.registration_number);
                $('#edit_make').val(data.make);
                $('#edit_model').val(data.model);
                $('#edit_year_of_manufacture').val(data.year_of_manufacture);
                $('#edit_chassis_number').val(data.chassis_number);
                $('#edit_engine_number').val(data.engine_number);
                $('#edit_color').val(data.color);
                $('#edit_fuel_type').val(data.fuel_type);
                $('#edit_transmission').val(data.transmission);
                $('#edit_status').val(data.status);
                $('#edit_vin').val(data.vin);
                $('#edit_mileage').val(data.mileage);

                document.getElementById('editVehicleForm').action = BASE_URL + 'admin/vehicles/update/' + id;
                openModal('editVehicleModal');
            }
        });
    };

    // Edit Vehicle form submission
    $(document).on('submit', '#editVehicleForm', function (e) {
        e.preventDefault();
        var id = $('#edit_vehicle_id').val();
        var formData = $(this).serialize();

        $.ajax({
            url: BASE_URL + 'admin/vehicles/update/' + id,
            method: 'POST',
            data: formData,
            success: function () {
                closeModal('editVehicleModal');
                table.ajax.reload();
                alert('Vehicle updated successfully!');
            },
            error: function () {
                alert('Failed to update vehicle');
            }
        });
    });

    // Delete Vehicle
    window.deleteVehicle = function (id) {
        if (confirm('Are you sure you want to delete this vehicle?')) {
            $.ajax({
                url: BASE_URL + 'admin/vehicles/delete/' + id,
                method: 'POST',
                success: function () {
                    table.ajax.reload();
                },
                error: function () {
                    alert('Failed to delete vehicle.');
                }
            });
        }
    };
});
