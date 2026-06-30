
$(document).ready(function () {
    // Initialize DataTable
    const table = $('#vehicleTable').DataTable({
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
            "render": function (data, type, row) {
                return `
            <div style="display: flex; justify-content: space-around;">
                <button class="icon-btn text-info" title="Edit" onclick="editVehicle(${data.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="icon-btn text-primary" title="View" onclick="viewVehicleDetails(${data.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="icon-btn text-danger" title="Delete" onclick="deleteVehicle(${data.id})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;
            }
        }
        ]
    });



    // Add Vehicle AJAX
    $('#addVehicleForm').on('click', function () {
        const formData = $('#addVehicleForm').serialize();
        $.post(`${BASE_URL}admin/vehicles/add`, formData, function (response) {
            $('#vehicleModal').modal('hide');
            table.ajax.reload();
        });
    });

    // Edit Vehicle AJAX
    window.editVehicle = function (id) {
        $.get(`${BASE_URL}admin/vehicles/fetch/${id}`, function (data) {
            $('#vehicle_id').val(data.id);
            $('#vehicle_number').val(data.vehicle_number);
            $('#make').val(data.make);
            $('#model').val(data.model);
            $('#year').val(data.year);
            $('#color').val(data.color);
            $('#vehicleModal').modal('show');
        });
    };

    // Delete Vehicle AJAX
    window.deleteVehicle = function (id) {
        if (confirm("Are you sure you want to delete this vehicle?")) {
            $.post(`<?= base_url('admin/vehicles/delete') ?>/${id}`, function (response) {
                table.ajax.reload();
            });
        }
    };

    // Clear form when modal closes
    $('#vehicleModal').on('hidden.bs.modal', function () {
        $('#vehicleForm')[0].reset();
    });


    function openAddModal() {
        const modal = new bootstrap.Modal(document.getElementById('vehicleModal'));
        modal.show();
    }

    $('#addVehicleForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                alert('Vehicle added successfully!');
                $('#addVehicleModal').modal('hide');
                $('#vehiclesTable').DataTable().ajax.reload();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Failed to add vehicle.');
            }
        });
    });


    function viewVehicleDetails(id) {
        $.ajax({
            url: BASE_URL + 'vehicles/details/' + id,
            method: 'GET',
            success: function (data) {
                $('#viewVehicleModal .modal-body').html(`
                    
                    <p><strong>Make:</strong> ${data.make}</p>
                    <p><strong>Year of Manufacture:</strong> ${data.year_of_manufacture}</p>
                    <p><strong>Registration Number:</strong> ${data.registration_number}</p>
                    <p><strong>Model:</strong> ${data.model}</p>
                    <p><strong>Color:</strong> ${data.color}</p>
                    <p><strong>Engine Number:</strong> ${data.engine_number}</p>
                    <p><strong>Chassis Number:</strong> ${data.chassis_number}</p>
                    <p><strong>Fuel Type:</strong> ${data.fuel_type}</p>
                  
                `);
                $('#viewVehicleModal').modal('show');
            },
            error: function (error) {
                alert('Failed to fetch vehicle details.');
                console.error(error);
            }
        });
    }

    function deleteVehicle(id) {
        if (confirm('Are you sure you want to delete this vehicle?')) {
            $.ajax({
                url: BASE_URL + 'vehicles/delete' + id,
                method: 'DELETE',
                success: function (response) {
                    alert('Vehicle deleted successfully!');
                    // Reload DataTable
                    $('#vehiclesTable').DataTable().ajax.reload();
                },
                error: function (error) {
                    alert('Failed to delete vehicle.');
                    console.error(error);
                }
            });
        }
    }

    function editVehicle(id) {
        $.ajax({
            url: BASE_URL + 'admin/vehicles/get' + id,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#edit_registration_number').val(data.registration_number);
                $('#edit_make').val(data.make);
                $('#edit_model').val(data.model);
                $('#edit_year_of_manufature').val(data.year_of_manufature);
                $('#edit_chassis_number').val(data.chassis_number);
                $('#edit_engine_number').val(data.engine_number);
                $('#edit_color').val(data.color);
                $('#edit_fuel_type').val(data.fuel_type);
                $('#edit_transmission').val(data.transmission);
                $('#edit_status').val(data.status);

                $('#editVehicleModal').modal('show');
            }
        });

        $('#editVehicleForm').submit(function (e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: BASE_URL + 'vehicles/update' + id,
                method: 'POST',
                data: formData,
                success: function (res) {
                    $('#editVehicleModal').modal('hide');
                    $('#vehicleTable').DataTable().ajax.reload();
                    alert('Vehicle updated successfully!');
                },
                error: function () {
                    alert('Failed to update vehicle');
                }
            });
        });

    }
});