<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'FlowDesk' ?> — FlowDesk</title>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/css/tailwind.css') ?>">

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables (vendored locally, Tailwind-compatible) -->
    <link rel="stylesheet" href="<?= base_url('public/assets/vendor/datatables/jquery.dataTables.min.css') ?>">
    <script src="<?= base_url('public/assets/js/datatable-config.js') ?>"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>   

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>


    <!-- CSRF meta tags -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <!-- Custom DataTables Tailwind overrides -->
    <style>
        body { font-family: 'Inter', sans-serif; }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            outline: none;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99,102,241,0.2);
        }
        table.dataTable thead th {
            background-color: #f9fafb;
            color: #6b7280;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        table.dataTable tbody td {
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
        }
        table.dataTable tbody tr:hover {
            background-color: #f9fafb;
        }
        table.dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.375rem;
            padding: 0.25rem 0.625rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important;
            color: white !important;
            border: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6 !important;
            color: #111827 !important;
            border: none !important;
        }

        #sidebar { transition: width 0.2s ease; }
        #notificationDropdown { transition: opacity 0.15s ease; }

        .flash-message { animation: slideDown 0.3s ease; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* SweetAlert2 button overrides — Tailwind preflight resets button bg/color */
        .swal2-popup .swal2-styled.swal2-confirm { background-color: #4f46e5 !important; color: #fff !important; }
        .swal2-popup .swal2-styled.swal2-cancel { background-color: #6b7280 !important; color: #fff !important; }
        .swal2-popup .swal2-title,
        .swal2-popup .swal2-html-container { color: #111827 !important; }

        @media print {
            #sidebar, #topbar, .no-print { display: none !important; }
            #main-content { margin: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="h-full flex font-sans">

    <!-- Sidebar -->
    <?= view('partials/sidebar') ?>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col min-h-screen ml-64">

        <?php
        // Smart page title fallback from URL
        if (!isset($pageTitle)):
            $url = current_url();
            $titleFallback = match(true) {
                str_contains($url, 'admin/dashboard') => 'Dashboard',
                str_contains($url, 'admin/users')     => 'Staff',
                str_contains($url, 'admin/customers') => 'Customers',
                str_contains($url, 'admin/vehicles')  => 'Vehicles',
                str_contains($url, 'admin/jobs')      => 'Job Cards',
                str_contains($url, 'job_intake')      => 'Job Intake',
                str_contains($url, 'admin/sublets')   => 'Sublets',
                str_contains($url, 'admin/inventory') => 'Parts & Inventory',
                str_contains($url, 'admin/suppliers') => 'Suppliers',
                str_contains($url, 'admin/lpos')      => 'LPOs',
                str_contains($url, 'admin/invoices')  => 'Invoices',
                str_contains($url, 'admin/pettycash') => 'Petty Cash',
                str_contains($url, 'admin/reports')   => 'Reports',
                str_contains($url, 'admin/calendar')  => 'Calendar',
                str_contains($url, 'admin/settings')  => 'Settings',
                str_contains($url, 'mechanic')        => 'Mechanic',
                str_contains($url, 'receptionist')    => 'Receptionist',
                str_contains($url, 'customer')        => 'Customer Portal',
                default => 'FlowDesk'
            };
        endif;
        ?>

        <!-- Topbar -->
        <header id="topbar" class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900"><?= $pageTitle ?? $titleFallback ?></h1>
                    <nav class="text-xs text-gray-400 mt-0.5 space-x-1">
                        <?php
                        $bcSegments = service('uri')->getSegments();
                        foreach ($bcSegments as $i => $seg):
                            $label = ucwords(str_replace(['-', '_'], ' ', $seg));
                            if ($i > 0) echo '<span class="text-gray-300">/</span>';
                        ?>
                        <span class="<?= $i === array_key_last($bcSegments) ? 'text-gray-500 font-medium' : '' ?>"><?= $label ?></span>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </div>

            <?php if (str_contains(current_url(), 'admin/dashboard')): ?>
            <div class="flex-1 flex justify-center px-4">
                <div class="relative w-full max-w-md">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="globalSearch" type="text" placeholder="Search jobs, customers, vehicles..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                    <div id="searchResults" class="hidden absolute top-full left-0 mt-1 w-96 bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto"></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="flex items-center gap-4">
                <div class="relative" id="notificationArea">
                    <button onclick="toggleNotifications()" class="relative p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notificationBadge" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">0</span>
                    </button>
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">Notifications</p>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-6 text-center text-sm text-gray-400">No new notifications</div>
                        </div>
                    </div>
                </div>
                <div class="relative" id="userDropdown">
                    <button onclick="document.getElementById('userMenu').classList.toggle('hidden')"
                            class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-600 font-medium text-xs">
                                <?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
                            </span>
                        </div>
                        <span class="font-medium hidden sm:block"><?= session()->get('user_name') ?? 'User' ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900"><?= session()->get('user_name') ?></p>
                            <p class="text-xs text-gray-500 capitalize"><?= session()->get('role') ?></p>
                        </div>
                        <a href="<?= base_url('admin/settings') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Settings
                        </a>
                        <a href="<?= base_url('logout') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="px-6 pt-4">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="flash-message flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-4">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
            <div class="flash-message flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Page content -->
        <main id="main-content" class="flex-1 p-6">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer -->
        <footer class="px-6 py-4 border-t border-gray-100">
            <p class="text-xs text-gray-400">&copy; <?= date('Y') ?> FlowDesk. All rights reserved.</p>
        </footer>
    </div>

    <!-- Global JS -->
    <script>
        var BASE_URL = '<?= base_url() ?>';

        function getCsrfMeta() {
            return {
                name: document.querySelector('meta[name="csrf-name"]')?.getAttribute('content'),
                hash: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            };
        }

        function loadChartJS() {
            return new Promise(function(resolve, reject) {
                if (window.Chart) { resolve(window.Chart); return; }
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                s.onload = function() { resolve(window.Chart); };
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }

        function toggleNotifications() {
            document.getElementById('notificationDropdown').classList.toggle('hidden');
        }

        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (['POST','PUT','DELETE'].includes(settings.type?.toUpperCase())) {
                    var csrf = getCsrfMeta();
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf.hash);
                    if (typeof settings.data === 'string' && settings.data.length > 0) {
                        settings.data += '&' + csrf.name + '=' + csrf.hash;
                    } else if (typeof settings.data === 'object' && settings.data !== null) {
                        settings.data[csrf.name] = csrf.hash;
                    }
                }
            },
            complete: function(xhr) {
                var newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (newToken) {
                    document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', newToken);
                }
            }
        });

        document.addEventListener('click', function(e) {
            if (!document.getElementById('userDropdown')?.contains(e.target)) {
                document.getElementById('userMenu')?.classList.add('hidden');
            }
            if (!document.getElementById('notificationArea')?.contains(e.target)) {
                document.getElementById('notificationDropdown')?.classList.add('hidden');
            }
        });
    </script>

    <script src="<?= base_url('public/assets/js/dashboard-refresh.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/notifications.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/global-search.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
