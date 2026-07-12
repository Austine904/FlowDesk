window.editSupplier = function(id) {
    fetch(BASE_URL + 'admin/suppliers/edit/' + id, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('edit_supplier_id').value = data.id;
        document.getElementById('edit_supplier_name').value = data.name;
        window.openModal('editSupplierModal');
    })
    .catch(function(err) {
        alert('Failed to fetch supplier details: ' + err.message);
    });
};

$(document).ready(function() {
    // Add Supplier Form
    $('#addSupplierForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: BASE_URL + 'admin/suppliers/create',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    closeModal('addSupplierModal');
                    $('#suppliersTable').DataTable().ajax.reload();
                    Swal.fire('Success!', res.message || 'Supplier added successfully.', 'success');
                } else {
                    Swal.fire('Error!', res.message || 'Failed to save supplier.', 'error');
                }
            },
            error: function(xhr) {
                var msg = 'Failed to save supplier.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || msg;
                } catch(e) {}
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

    // Edit Supplier Form
    $('#editSupplierForm').on('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('edit_supplier_id').value;
        var formData = new FormData(this);
        $.ajax({
            url: BASE_URL + 'admin/suppliers/update/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    closeModal('editSupplierModal');
                    $('#suppliersTable').DataTable().ajax.reload();
                    Swal.fire('Success!', res.message || 'Supplier updated successfully.', 'success');
                } else {
                    Swal.fire('Error!', res.message || 'Failed to update supplier.', 'error');
                }
            },
            error: function(xhr) {
                var msg = 'Failed to update supplier.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || msg;
                } catch(e) {}
                Swal.fire('Error!', msg, 'error');
            }
        });
    });
});
