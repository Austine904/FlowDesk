<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <h3 class="mb-4">User Management</h3>

    <!-- Add User Button -->
    <button onclick="openModal('<?= base_url('admin/users/add') ?>', 'Add New User')" class="btn btn-outline-primary d-flex align-items-center gap-2">
        <i class="bi bi-person-plus"></i> Add User
    </button>
  

    <!-- User Table -->
    <form method="POST" action="<?= base_url('admin/users/bulk_action') ?>" id="bulkActionForm">
        <?= csrf_field() ?>
        <button type="button" class="btn btn-danger mb-3 mt-5 " id="deleteSelectedBtn">
            <i class="bi bi-trash me-1"></i> Delete Selected
        </button>


        <div class="table-container">
            <div class="table-responsive rounded">
                <table id="userTable" class="table table-striped table-bordered" style="width:100%">

                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select_all"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </form>
</div>

<div id="actionModal" class="modal fade" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">
                    <i class="bi bi-person-circle me-2"></i> User Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="profile-card">
                            <img id="profile_picture" src="https://placehold.co/150x150/cccccc/333333?text=No+Photo" class="profile-picture" alt="User Photo">
                            <h5 class="card-title" id="user-fullname"></h5>
                            <div class="info-item"><span class="info-label">Company ID:</span> <span class="info-value" id="company_id"></span></div>
                            <div class="info-item"><span class="info-label">Role:</span> <span class="info-value" id="user-role"></span></div>
                            <div class="info-item"><span class="info-label">Phone:</span> <span class="info-value" id="user-phone"></span></div>
                            <div class="info-item"><span class="info-label">Email:</span> <span class="info-value" id="user-email"></span></div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <ul class="nav nav-tabs mb-3" id="userDetailsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">Personal Info</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="employment-tab" data-bs-toggle="tab" data-bs-target="#employment" type="button" role="tab" aria-controls="employment" aria-selected="false">Employment</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="kin-tab" data-bs-toggle="tab" data-bs-target="#kin" type="button" role="tab" aria-controls="kin" aria-selected="false">Next of Kin</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="userDetailsTabContent">
                            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                <div class="info-item"><span class="info-label">Date of Birth:</span> <span class="info-value" id="dob"></span></div>
                                <div class="info-item"><span class="info-label">National ID:</span> <span class="info-value" id="national_id"></span></div>
                                <div class="info-item"><span class="info-label">Address:</span> <span class="info-value" id="user-address"></span></div>
                            </div>
                            <div class="tab-pane fade" id="employment" role="tabpanel" aria-labelledby="employment-tab">
                                <div class="info-item"><span class="info-label">Employment Date:</span> <span class="info-value" id="date_of_employment"></span></div>
                                <div class="info-item"><span class="info-label">Department:</span> <span class="info-value" id="department">N/A</span></div>
                            </div>
                            <div class="tab-pane fade" id="kin" role="tabpanel" aria-labelledby="kin-tab">
                                <div class="info-item"><span class="info-label">Next of Kin Name:</span> <span class="info-value" id="kin_first_name"></span> <span class="info-value" id="kin_last_name"></span></div>
                                <div class="info-item"><span class="info-label">Next of Kin Phone:</span> <span class="info-value" id="kin_phone_number"></span></div>
                                <div class="info-item"><span class="info-label">Relationship:</span> <span class="info-value" id="kin_relationship">N/A</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
                <button type="button" onclick="openModal('<?= base_url('admin/users/edit/') ?>' + document.getElementById('company_id').innerText, 'Edit User Details')" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit User
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade custom-confirm-modal" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to delete the selected user(s)? This action cannot be undone.
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select_all');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        const bulkActionForm = document.getElementById('bulkActionForm');

        $(document).ready(function() {
            // Initialize DataTable

            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }


            const userTable = $('#userTable').DataTable({
                "processing": true,
                "searching": true,
                "ajax": {
                    "url": "<?= base_url('admin/users/fetch') ?>",
                    "type": "GET",
                    "data": function(d) {
                        // Add custom filters to the DataTables request
                        d.role_filter = $('#role-filter').val(); // Send role filter
                        // DataTables automatically sends search[value], order, length, start, draw
                    }
                },
                "columns": [{

                        "data": 'id',
                        "orderable": false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" name="users[]" value="${data}">`;
                        }
                    },
                    {
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": 'role',
                        render: function(data, type, row) {
                            return ucfirst(data);
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
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
        });


        // --- Checkbox Select All for DataTables ---
        selectAllCheckbox.addEventListener('change', function() {
            // Get all checkboxes in the current page of DataTables
            const checkboxes = userTable.rows({
                page: 'current'
            }).nodes().to$().find('input[type="checkbox"]');
            checkboxes.prop('checked', this.checked);
        });

        // --- Custom Confirmation Modal for Delete ---
        const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
        const confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement);
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        // deleteSelectedBtn.addEventListener('click', function() {

        //     const checkedUsers = userTable.rows().nodes().to$().find('input[name="users[]"]:checked');

        //     if (checkedUsers.length === 0) {
        //         // Use a custom message box instead of alert
        //         alert("Please select at least one user to delete."); // Temporary alert for demonstration

        //         // showInfoModal("No Selection", "Please select at least one user to delete.");
        //         return;
        //     }
        //     confirmDeleteModal.show();
        // });

        deleteSelectedBtn.addEventListener('click', function() {
            const checkedUsers = userTable.rows().nodes().to$().find('input[name="users[]"]:checked');

            if (checkedUsers.length === 0) {
                alert("Please select at least one user to delete.");
                return;
            }

            const userIds = [];
            checkedUsers.each(function() {
                userIds.push($(this).val());
            });

            // Optional: confirm first
            if (!confirm("Are you sure you want to delete the selected user(s)?")) {
                return;
            }

            // Send AJAX to backend
            $.post('/users/delete-multiple', {
                user_ids: userIds
            }, function(response) {
                alert(response.message);
                // Optionally reload the DataTable
                userTable.ajax.reload();
            }, 'json');
        });


        confirmDeleteBtn.addEventListener('click', function() {
            confirmDeleteModal.hide();
            bulkActionForm.submit(); // Submit the form if confirmed
        });

        // --- Unified openModal Function for Add/Edit ---
        const actionModalElement = document.getElementById('actionModal');
        const actionModal = new bootstrap.Modal(actionModalElement);
        const actionModalTitle = document.getElementById('actionModalLabel');
        const modalContentDiv = document.getElementById('modalContent');

        window.openModal = function(url, title = 'Form') { // Added title parameter
            actionModalTitle.textContent = title;
            modalContentDiv.innerHTML = `
                                 <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                 </div>`;

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
                    modalContentDiv.innerHTML = ` < div class = "alert alert-danger"
                                role = "alert" > Error loading content: $ {
                                    error.message
                                }.Please
                                try again. < /div>`;
                    console.error('Error loading modal content:', error);
                });
        }

        // --- View User Details Modal ---
        const userDetailsModalElement = document.getElementById('userDetailsModal');
        const userDetailsModal = new bootstrap.Modal(userDetailsModalElement);

        // Attach event listener for 'View' buttons using delegation
        // This is crucial because DataTables dynamically adds/removes rows.
        $('#userTable tbody').on('click', '.view-user', async function() {
            const userId = $(this).data('id');

            if (!userId) {
                console.error('User ID not found for this button.');
                return;
            }
            // Clear previous data and show loading spinner
            document.getElementById('user-fullname').innerText = 'Loading...';
            document.getElementById('company_id').innerText = '';
            document.getElementById('profile_picture').src = 'https://placehold.co/150x150/cccccc/333333?text=Loading...';
            // Clear other fields as well

            userDetailsModal.show(); // Show modal immediately with loading state

            try {
                const response = await fetch(`<?= base_url('admin/users/fetch/') ?>${userId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch user details (Status: ${response.status})`);
                }

                const data = await response.json();

                //echo all data
                    // console.log(data);

                // Populate Basic Details
                document.getElementById('profile_picture').src = data.profile_picture ? `<?= base_url() ?>/${data.profile_picture}` : 'https://placehold.co/150x150/cccccc/333333?text=No+Photo';
                document.getElementById('user-fullname').innerText = `${data.first_name || ''} ${data.last_name || ''}`;
                document.getElementById('company_id').innerText = data.company_id || 'N/A';
                document.getElementById('user-role').innerText = data.role ? ucfirst(data.role) : 'N/A';
                document.getElementById('user-phone').innerText = data.phone_number || 'N/A';
                document.getElementById('user-email').innerText = data.email || 'N/A';

                // Populate Personal Info Tab
                document.getElementById('dob').innerText = data.dob || 'N/A';
                document.getElementById('national_id').innerText = data.national_id || 'N/A';
                document.getElementById('user-address').innerText = data.address || 'N/A';

                // Populate Employment Tab
                document.getElementById('date_of_employment').innerText = data.date_of_employment || 'N/A';
                document.getElementById('department').innerText = data.department || 'N/A';

                // Populate Next of Kin Tab

                const kin = data.next_of_kin || {};

                document.getElementById('kin_first_name').innerText = `${kin.kin_first_name || ''} ${kin.kin_last_name || ''}`.trim() || 'N/A';
                document.getElementById('kin_last_name').innerText = kin.kin_last_name || '';
                document.getElementById('kin_phone_number').innerText = kin.kin_phone_number || 'N/A';
                document.getElementById('kin_relationship').innerText = kin.relationship || 'N/A';

            } catch (error) {
                // Display error message directly in the modal body or a dedicated alert area
                const modalBody = userDetailsModalElement.querySelector('.modal-body');
                modalBody.innerHTML = `<div class="alert alert-danger" role="alert">
                                            <i class="bi bi-exclamation-circle me-2"></i> Failed to load user details: ${error.message}
                                        </div>`;
                console.error('Error fetching user details:', error);
            }
        });

        // --- Helper for ucfirst in JS (if needed for roles) ---
        function ucfirst(str) {
            if (typeof str !== 'string' || str.length === 0) {
                return '';
            }
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    });
</script>
<?= $this->endSection(); ?>