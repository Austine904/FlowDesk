<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>FlowDesk</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.css" />

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('public/css/vehicles.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/sidebar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/login.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/job_intake.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/customers.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/calendar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/sublets.css') ?>">

    <!-- Global JS Variables -->
    <script>
        const BASE_URL = "<?= base_url() ?>";
    </script>
</head>

<body>

    <!-- Sidebar Navigation -->
    <?= $this->include('partials/sidebar'); ?>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/locales-all.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/main.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.11/index.global.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <!-- Custom JS (after all libraries) -->
    <script src="<?= base_url('public/assets/js/vehicles.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/job_intake.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/customers.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/calendar.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/sublets.js') ?>"></script>



    <!-- Main Content -->
    <div style="margin-left: 250px;">
        <?= $this->renderSection('content'); ?>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-dark text-center py-3" style="margin-left: 200px;">
        <div class="container">
            <p>Developed by Austiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiine</p>
            <small>&copy; <?= date('Y') ?> FlowDesk. All rights reserved.</small>
        </div>
    </footer>

</body>

</html>