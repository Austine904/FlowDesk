<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FlowDesk</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #007bff; /* Bootstrap primary blue */
            --primary-hover-color: #0056b3;
            --text-dark: #343a40;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0eafc, #cfdef3); /* Subtle gradient background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Ensure it takes full viewport height */
            margin: 0;
            color: var(--text-dark);
        }

        .login-container {
            background: var(--card-bg);
            padding: 2.5rem; /* Increased padding */
            border-radius: 15px; /* More rounded corners */
            box-shadow: 0 10px 30px var(--shadow-medium); /* Softer, larger shadow */
            max-width: 450px; /* Slightly wider */
            width: 90%; /* Responsive width */
            text-align: center;
            animation: fadeIn 0.8s ease-out; /* Simple fade-in animation */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container h2 {
            margin-bottom: 2rem; /* More space below heading */
            font-weight: 600; /* Semi-bold */
            color: var(--primary-color); /* Use primary color for heading */
            font-size: 2rem; /* Larger heading */
        }

        .form-label {
            font-weight: 500; /* Medium weight for labels */
            color: var(--text-dark);
            text-align: left; /* Align labels to the left */
            display: block; /* Make label a block element */
            margin-bottom: 0.5rem;
        }

        .input-group-modern {
            position: relative;
            margin-bottom: 1.5rem; /* Consistent spacing */
        }

        .input-group-modern .form-control {
            padding-left: 3rem; /* Space for icon */
            border-radius: 8px; /* Rounded input fields */
            border: 1px solid #ced4da;
            height: 50px; /* Taller input fields */
            font-size: 1rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-group-modern .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .input-group-modern .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d; /* Icon color */
            font-size: 1.2rem;
            z-index: 2; /* Ensure icon is above input */
        }

        /* Show/Hide Password Toggle */
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

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem; /* Larger button */
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2); /* Button shadow */
            position: relative; /* For spinner */
            overflow: hidden; /* Hide spinner overflow */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover-color);
            border-color: var(--primary-hover-color);
            transform: translateY(-2px); /* Subtle lift on hover */
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        }

        /* Loading Indicator */
        .btn-primary .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
            margin-right: 0.5rem;
            color: white;
            display: none; /* Hidden by default */
        }

        .btn-primary.loading .spinner-border {
            display: inline-block;
        }

        .btn-primary.loading .button-text {
            visibility: hidden; /* Hide text when loading */
        }


        .alert-danger {
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-size: 0.95rem;
            text-align: left;
            padding: 1rem 1.25rem; /* Better padding for alerts */
        }

        .forgot-password-link {
            display: block;
            margin-top: 1rem;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            cursor: pointer;
        }

        .forgot-password-link:hover {
            color: var(--primary-hover-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>FlowDesk Login</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('login/auth') ?>" id="loginForm">
        <?= csrf_field() ?>
        <div class="input-group-modern">
            <label for="company_id" class="form-label visually-hidden">Company ID</label>
            <i class="bi bi-building input-icon"></i>
            <input type="text" name="company_id" id="company_id" class="form-control" placeholder="Company ID" required autofocus>
        </div>

        <div class="input-group-modern">
            <label for="password" class="form-label visually-hidden">Password</label>
            <i class="bi bi-lock input-icon"></i>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
            <i class="bi bi-eye-slash password-toggle" id="passwordToggle"></i> </div>

        <button type="submit" class="btn btn-primary w-100" id="loginButton">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="button-text">Login</span>
        </button>

        <a href="<?= base_url('forgot-password') ?>" class="forgot-password-link">Forgot Password?</a>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');

        // Show/Hide Password Toggle
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Toggle the eye icon
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Loading Indicator on Submit
        loginForm.addEventListener('submit', function() {
            loginButton.classList.add('loading'); // Add loading class to button
            loginButton.disabled = true; // Disable button to prevent multiple submits
            // The spinner will show and text will hide via CSS
        });

        // Optional: If you handle form submission via AJAX, you'd remove the loading state
        // and re-enable the button in your AJAX success/error callbacks.
        // For a standard form submit, the page will reload, resetting the state automatically.
    });
</script>
