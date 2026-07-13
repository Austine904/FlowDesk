function toggleStockFields(prefix) {
    var isStocked = document.getElementById(prefix + '_inv_is_stocked').checked;
    var stockFields = document.getElementById(prefix + '_stock_fields');
    if (stockFields) stockFields.classList.toggle('hidden', !isStocked);
}

window.editInventory = function(id) {
    fetch(BASE_URL + 'admin/inventory/fetch/' + id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('edit_inv_id').value = data.id;
            document.getElementById('edit_inv_name').value = data.name;
            document.getElementById('edit_inv_part_number').value = data.part_number ?? '';
            document.getElementById('edit_inv_unit_price').value = data.unit_price;
            document.getElementById('edit_inv_unit').value = data.unit ?? 'piece';
            document.getElementById('edit_inv_is_stocked').checked = data.is_stocked == 1;
            document.getElementById('edit_inv_quantity_in_hand').value = data.quantity_in_hand ?? 0;
            document.getElementById('edit_inv_reorder_level').value = data.reorder_level ?? 0;
            toggleStockFields('edit');
            window.openModal('editInventoryModal');
        })
        .catch(function(err) {
            alert('Failed to fetch part details: ' + err.message);
        });
};

$(document).ready(function() {
    // Add Inventory Form
    $('#addInventoryForm').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        $.ajax({
            url: BASE_URL + 'admin/inventory/create',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    closeModal('addInventoryModal');
                    $('#inventoryTable').DataTable().ajax.reload();
                    Swal.fire('Success!', res.message || 'Part added successfully.', 'success');
                } else {
                    Swal.fire('Error!', res.message || 'Failed to save part.', 'error');
                }
            },
            error: function(xhr) {
                var msg = 'Failed to save part.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || msg;
                } catch(e) {}
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

    // Edit Inventory Form
    $('#editInventoryForm').on('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('edit_inv_id').value;
        var formData = new FormData(this);
        $.ajax({
            url: BASE_URL + 'admin/inventory/update/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    closeModal('editInventoryModal');
                    $('#inventoryTable').DataTable().ajax.reload();
                    Swal.fire('Success!', res.message || 'Part updated successfully.', 'success');
                } else {
                    Swal.fire('Error!', res.message || 'Failed to update part.', 'error');
                }
            },
            error: function(xhr) {
                var msg = 'Failed to update part.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || msg;
                } catch(e) {}
                Swal.fire('Error!', msg, 'error');
            }
        });
    });
});
