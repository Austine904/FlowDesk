<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Multi-Step Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.css">

    <style>
        /* Consistent theme variables */
        :root {
            --primary-color: #007bff;
            --primary-hover-color: #0056b3;
            --text-dark: #343a40;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-color: #343a40;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        .container.mt-5 {
            margin-top: 3rem !important;
        }

        h3, h5 {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.5rem; /* Consistent spacing */
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #ced4da;
            padding: 0.75rem 1rem;
            box-shadow: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        /* Password toggle specific styling */
        .input-group-modern {
            position: relative;
        }
        .input-group-modern .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            cursor: pointer;
            font-size: 1.2rem;
            z-index: 2;
            transition: color 0.2s ease;
        }
        .input-group-modern .password-toggle:hover {
            color: var(--primary-color);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            padding: 0.75rem 1.5rem;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover-color);
            border-color: var(--primary-hover-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2);
        }

        /* Loading Indicator */
        .btn .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
            margin-right: 0.5rem;
            color: white;
            display: none;
        }
        .btn.loading .spinner-border {
            display: inline-block;
        }
        .btn.loading .button-text {
            visibility: hidden;
        }

        /* Multi-Step Form Progress Bar */
        .progress-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 10px;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .progress-bar-multi {
            height: 10px;
            background-color: var(--primary-color);
            width: 0%;
            border-radius: 10px;
            transition: width 0.4s ease-in-out;
        }

        /* Step Content Styling */
        .step-content {
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px var(--shadow-medium);
            margin-bottom: 2rem;
        }

        /* Validation Feedback */
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--danger-color) !important;
        }
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h3>Add New User - Multi-Step Form</h3>

        <div class="progress-container">
            <div class="progress-bar-multi" id="progressBar" style="width: 33%;"></div>
        </div>

        <form id="multiStepForm" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>

            <div id="step1" class="step-content">
                <h5>Step 1: Company Information</h5>

                <div class="mb-3">
                    <label for="profile_picture" class="form-label">User Image <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                    <div class="invalid-feedback">Please upload a profile picture.</div>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" name="role" id="role" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="mechanic">Mechanic</option>
                        <option value="receptionist">Receptionist</option>
                    </select>
                    <div class="invalid-feedback">Please select a role.</div>
                </div>

                <div class="mb-3 input-group-modern">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    <i class="bi bi-eye-slash password-toggle" id="passwordToggle"></i>
                    <div class="invalid-feedback">Password is required and must be at least 6 characters.</div>
                </div>

                <div class="mb-3">
                    <label for="company_id" class="form-label">Company ID</label>
                    <input type="text" class="form-control" id="company_id" name="company_id" readonly required>
                    <small class="form-text text-muted">Autogenerated ID based on role.</small>
                    <div class="invalid-feedback">Company ID could not be generated. Please select a role.</div>
                </div>

                <div class="mb-3">
                    <label for="date_of_employment" class="form-label">Date of Employment</label>
                    <input type="text" class="form-control" id="date_of_employment" name="date_of_employment" readonly value="<?= date('Y-m-d') ?>">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" id="nextStep1" class="btn btn-primary">Next Step</button>
                </div>
            </div>

            <div id="step2" class="step-content" style="display: none;">
                <h5>Step 2: Personal Information</h5>

                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required />
                    <div class="invalid-feedback">First name is required.</div>
                </div>

                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required />
                    <div class="invalid-feedback">Last name is required.</div>
                </div>

                <div class="mb-3">
                    <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="dob" name="dob" required />
                    <div class="invalid-feedback">Date of Birth is required.</div>
                </div>

                <div class="mb-3">
                    <label for="national_id" class="form-label">National ID Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="national_id" name="national_id" required />
                    <div class="invalid-feedback">National ID is required.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender <span class="text-danger">*</span></label><br />
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="Male" id="genderMale" required />
                        <label class="form-check-label" for="genderMale">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="Female" id="genderFemale" />
                        <label class="form-check-label" for="genderFemale">Female</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="Other" id="genderOther" />
                        <label class="form-check-label" for="genderOther">Other</label>
                    </div>
                    <div class="invalid-feedback">Please select a gender.</div>
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" required pattern="[0-9]{10,15}" title="Phone number must be 10-15 digits. Example: 0712345678" />
                    <div class="invalid-feedback">Valid phone number is required (10-15 digits).</div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Home Address <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="address" name="address" required />
                    <div class="invalid-feedback">Home address is required.</div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required />
                    <div class="invalid-feedback">Valid email address is required.</div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" id="prevStep2" class="btn btn-secondary">Previous</button>
                    <button type="button" id="nextStep2" class="btn btn-primary">Next Step</button>
                </div>
            </div>

            <div id="step3" class="step-content" style="display: none;">
                <h5>Step 3: Next of Kin Information</h5>

                <div class="mb-3">
                    <label for="kin_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="kin_first_name" name="kin_first_name" required />
                    <div class="invalid-feedback">Next of kin's first name is required.</div>
                </div>

                <div class="mb-3">
                    <label for="kin_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="kin_last_name" name="kin_last_name" required />
                    <div class="invalid-feedback">Next of kin's last name is required.</div>
                </div>

                <div class="mb-3">
                    <label for="relationship" class="form-label">Relationship <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="relationship" name="relationship" required />
                    <div class="invalid-feedback">Relationship to next of kin is required.</div>
                </div>

                <div class="mb-3">
                    <label for="kin_phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="kin_phone_number" name="kin_phone_number" required pattern="[0-9]{10,15}" title="Phone number must be 10-15 digits. Example: 0712345678" />
                    <div class="invalid-feedback">Valid phone number for next of kin is required (10-15 digits).</div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" id="prevStep3" class="btn btn-secondary">Previous</button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="button-text">Submit</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const multiStepForm = document.getElementById('multiStepForm');
            const roleSelect = document.getElementById('role');
            const companyIdInput = document.getElementById('company_id');
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            const progressBar = document.getElementById('progressBar');
            const submitBtn = document.getElementById('submitBtn');

            let currentStep = 1;

            // --- Helper: Show only one step and update progress bar ---
            function showStep(step) {
                document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');
                document.getElementById('step' + step).style.display = 'block';
                currentStep = step;
                updateProgressBar();
            }

            // --- Update Progress Bar ---
            function updateProgressBar() {
                const totalSteps = 3; // Hardcoded for this form
                const progressPercentage = (currentStep / totalSteps) * 100;
                progressBar.style.width = progressPercentage + '%';
            }

            // --- Custom Validation for current step ---
            function validateStep(stepId) {
                const currentStepElement = document.getElementById(stepId);
                let isValid = true;
                const requiredInputs = currentStepElement.querySelectorAll('[required]');

                requiredInputs.forEach(input => {
                    // Skip radio buttons here as they are handled separately for gender group
                    if (input.type === 'radio' && input.name === 'gender') {
                        return;
                    }

                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        // console.log(`Validation failed for input: ${input.id || input.name}, value: "${input.value}"`); // Debugging line
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                // Special handling for radio buttons (gender)
                if (stepId === 'step2') {
                    const genderInputs = currentStepElement.querySelectorAll('input[name="gender"]');
                    const genderSelected = Array.from(genderInputs).some(radio => radio.checked);
                    if (!genderSelected) {
                        genderInputs.forEach(input => input.classList.add('is-invalid')); // Add to all for visual feedback
                        isValid = false;
                        // console.log(`Validation failed for gender selection`); // Debugging line
                    } else {
                        genderInputs.forEach(input => input.classList.remove('is-invalid'));
                    }
                }

                // console.log(`Step ${stepId} validation result: ${isValid}`); // Debugging line
                return isValid;
            }

            // --- Clear Validation Feedback for a step ---
            function clearValidationFeedback(stepId) {
                const stepElement = document.getElementById(stepId);
                stepElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                // Note: Bootstrap's .invalid-feedback visibility is tied to .is-invalid on input and .was-validated on form
                // So, no need to manually hide .invalid-feedback here if .is-invalid is removed.
            }


            // --- Show/Hide Password Toggle ---
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });

            // --- Fetch and generate company ID ---
            roleSelect.addEventListener('change', async function() {
                const role = this.value;
                const year = new Date().getFullYear().toString().slice(-2);
                const rolePrefixes = {
                    admin: 'ADM',
                    mechanic: 'MECH',
                    receptionist: 'RP'
                };

                companyIdInput.value = 'Generating...'; // Show loading state
                companyIdInput.classList.remove('is-invalid'); // Clear any previous validation

                try {
                    const response = await fetch(`<?= base_url('user/getLastId') ?>?role=${role}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        } 
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    // Ensure data.result is a valid number
                    const lastId = parseInt(data.result);
                    if (isNaN(lastId)) {
                        throw new Error('Invalid last ID received from server.');
                    }

                    const nextId = lastId + 1;
                    const formattedId = String(nextId).padStart(3, '0');
                    const companyId = `${rolePrefixes[role]}${year}${formattedId}`;

                    companyIdInput.value = companyId;
                    companyIdInput.setAttribute('readonly', true);
                    // Mark as valid if ID is generated
                    companyIdInput.classList.remove('is-invalid');

                } catch (error) {
                    console.error('Error generating Company ID:', error);
                    companyIdInput.value = 'Error';
                    companyIdInput.classList.add('is-invalid'); // Mark as invalid
                    // Provide user feedback via SweetAlert2
                    Swal.fire('Error', 'Failed to generate Company ID. Please try again or contact support.', 'error');
                }
            });


            // --- Navigation Logic ---
            document.getElementById('nextStep1').addEventListener('click', function() {
                // Apply 'was-validated' to the form to show all validation feedback immediately
                multiStepForm.classList.add('was-validated');
                if (validateStep('step1')) {
                    clearValidationFeedback('step1'); // Clear feedback if valid
                    multiStepForm.classList.remove('was-validated'); // Remove for next step, will be re-added if needed
                    showStep(2);
                }
                // If not valid, 'was-validated' remains, showing feedback
            });

            document.getElementById('nextStep2').addEventListener('click', function() {
                multiStepForm.classList.add('was-validated');
                if (validateStep('step2')) {
                    clearValidationFeedback('step2');
                    multiStepForm.classList.remove('was-validated');
                    showStep(3);
                }
            });

            document.getElementById('prevStep2').addEventListener('click', function() {
                clearValidationFeedback('step2');
                multiStepForm.classList.remove('was-validated'); // Remove on previous
                showStep(1);
            });

            document.getElementById('prevStep3').addEventListener('click', function() {
                clearValidationFeedback('step3');
                multiStepForm.classList.remove('was-validated'); // Remove on previous
                showStep(2);
            });

            // --- Form Submission ---
            multiStepForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Final validation of the current step (Step 3)
                multiStepForm.classList.add('was-validated'); // Ensure validation styling is active for final step
                if (!validateStep('step3')) {
                    return;
                }

                // Optional: Ensure all steps are valid before final submission (good for robustness)
                if (!validateStep('step1') || !validateStep('step2') || !validateStep('step3')) {
                    Swal.fire('Validation Error', 'Please ensure all required fields in all steps are filled correctly.', 'warning');
                    return;
                }

                submitBtn.classList.add('loading'); // Show loading spinner
                submitBtn.disabled = true; // Disable button

                const formData = new FormData(this);

                try {
                    const response = await fetch("<?= base_url('user/final_submit') ?>", {
                        method: "POST",
                        body: formData,
                        headers: {
                            // 'X-Requested-With': 'XMLHttpRequest' // Only needed if backend specifically checks for this header
                        }
                    });

                    const responseData = await response.json(); // Assuming backend always returns JSON

                    if (response.ok && responseData.success) { // Check both HTTP status and custom success flag
                        Swal.fire('Success', 'User successfully added!', 'success').then(() => {
                            // If this form is in a modal, you might want to close the modal and refresh the parent table
                            // For now, we'll just reload the page as per original code's intent
                            window.location.reload();
                        });
                    } else {
                        // Handle server-side validation errors or other backend errors
                        const errorMessage = responseData.message || 'Error saving user. Please try again.';
                        Swal.fire('Error', errorMessage, 'error');
                    }
                } catch (error) {
                    console.error('Server error during submission:', error);
                    Swal.fire('Error', 'Server error. Please try again later.', 'error');
                } finally {
                    submitBtn.classList.remove('loading'); // Hide loading spinner
                    submitBtn.disabled = false; // Re-enable button
                }
            });

            // Initial setup
            showStep(1);
            updateProgressBar(); // Set initial progress bar state
        });
    </script>

</body>
</html>
