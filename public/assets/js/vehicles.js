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
                return '<div class="flex justify-around">' +
                    '<button class="icon-btn text-info" title="Edit" onclick="editVehicle(' + data.id + ')"><i class="fas fa-edit"></i></button>' +
                    '<button class="icon-btn text-primary" title="View" onclick="viewVehicleDetails(' + data.id + ')"><i class="fas fa-eye"></i></button>' +
                    '<button class="icon-btn text-danger" title="Delete" onclick="deleteVehicle(' + data.id + ')"><i class="fas fa-trash-alt"></i></button>' +
                    '</div>';
            }
        }
        ]
    });

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        var backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        var backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

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
                var body = '';
                body += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                body += '<div><p class="mb-2"><span class="text-xs font-medium text-gray-500">Make:</span> <span class="text-sm text-gray-900">' + (data.make || 'N/A') + '</span></p></div>';
                body += '<div><p class="mb-2"><span class="text-xs font-medium text-gray-500">Year of Manufacture:</span> <span class="text-sm text-gray-900">' + (data.year_of_manufacture || 'N/A') + '</span></p></div>';
                body += '<div><p class="mb-2"><span class="text-xs font-medium text-gray-500">Registration Number:</span> <span class="text-sm text-gray-900">' + (data.registration_number || 'N/A') + '</span></p></div>';
                body += '<div><p class="mb-2"><span class="text-xs font-medium text-gray-500">Model:</span> <span class="text-sm text-gray-900">' + (data.model || 'N/A') + '</span></p></div>';
                body += '<div><p class="mb-2"><span class="text-xs font-medium text-gray-500">Color:</span> <span class="text-sm text-gray-900">' + (data.color || 'N/A') + '</span></p></div>';
                body += '<div><p class="mb-2"><span class="text-xs font-medium text-gray-500">Engine Number:</span> <span class="text-sm text-gray-900">' + (data.engine_number || 'N/A') + '</span></p></div>';
                body += '<div><p class="mb-2"><span class="text-xs font-medium text-gray-500">Chassis Number:</span> <span class="text-sm text-gray-900">' + (data.chassis_number || 'N/A') + '</span></p></div>';
                body += '<div><p class="mb-2"><span class="text-xs font-medium text-gray-500">Fuel Type:</span> <span class="text-sm text-gray-900">' + (data.fuel_type || 'N/A') + '</span></p></div>';
                body += '</div>';

                var modalBody = document.querySelector('#viewVehicleModal .p-6');
                if (modalBody) {
                    modalBody.innerHTML = body;
                }
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
