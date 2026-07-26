<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Technician Portal' ?> — FlowDesk</title>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/css/tailwind.css') ?>">

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
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

        .flash-message { animation: slideDown 0.3s ease; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .swal2-popup .swal2-styled.swal2-confirm { background-color: #4f46e5 !important; color: #fff !important; }
        .swal2-popup .swal2-styled.swal2-cancel { background-color: #6b7280 !important; color: #fff !important; }
        .swal2-popup .swal2-title,
        .swal2-popup .swal2-html-container { color: #111827 !important; }

        @media print {
            #sidebar, #topbar, #bottomTabBar, .no-print { display: none !important; }
            #main-content { margin: 0 !important; padding: 0 !important; }
        }

        #sidebar { transition: width 0.2s ease; }
    </style>
</head>
<body class="h-full flex font-sans">

    <!-- ============================================================ -->
    <!-- Bottom Tab Bar (mobile, < md)                               -->
    <!-- ============================================================ -->
    <?php
    $currentUrl = current_url();
    $uri = service('uri');
    $segments = $uri->getSegments();
    $lastSegment = end($segments);

    function isTechActive($needle): string {
        global $currentUrl;
        return str_contains($currentUrl, $needle) ? 'bg-indigo-600 text-white' : 'text-gray-500';
    }

    function techNavItem(string $href, string $label, string $faIcon, string $activePath = null): string {
        global $currentUrl;
        $path = $activePath ?? $href;
        $active = str_contains($currentUrl, $path);
        $classes = $active
            ? 'bg-indigo-600 text-white'
            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100';
        return '
        <a href="' . $href . '" class="' . $classes . ' flex flex-col items-center justify-center flex-1 py-2 rounded-lg text-xs font-medium transition-colors">
            <i class="' . $faIcon . ' text-lg mb-0.5"></i>
            <span>' . $label . '</span>
        </a>';
    }
    ?>

    <nav id="bottomTabBar" class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 shadow-lg">
        <div class="flex items-center justify-around px-2 py-1">
            <?= techNavItem(base_url('mechanic/dashboard'), 'Dashboard', 'fas fa-chart-pie', 'mechanic/dashboard') ?>
            <?= techNavItem(base_url('mechanic/jobs'), 'My Jobs', 'fas fa-wrench', 'mechanic/jobs') ?>
            <?= techNavItem(base_url('mechanic/history'), 'History', 'fas fa-clock-rotate', 'mechanic/history') ?>
            <?php
            $notifBadge = '<span id="tabNotifBadge" data-unread-count="0" class="hidden absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">0</span>';
            ?>
            <a href="<?= base_url('mechanic/notifications') ?>" class="<?= isTechActive('mechanic/notifications') ?> flex flex-col items-center justify-center flex-1 py-2 rounded-lg text-xs font-medium transition-colors relative hover:text-gray-700 hover:bg-gray-100">
                <i class="fas fa-bell text-lg mb-0.5"></i>
                <?= $notifBadge ?>
                <span>Notifications</span>
            </a>
            <?= techNavItem(base_url('admin/profile'), 'Profile', 'fas fa-user', 'admin/profile') ?>
        </div>
    </nav>

    <!-- ============================================================ -->
    <!-- Sidebar (md and above)                                      -->
    <!-- ============================================================ -->
    <aside id="sidebar" class="hidden md:flex fixed top-0 left-0 h-full w-64 bg-slate-900 flex-col z-20">

        <!-- Logo/Brand -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <span class="text-white font-semibold text-sm">FlowDesk</span>
                <p class="text-slate-400 text-xs">Technician Portal</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-1">
            <?php
            function techSidebarItem(string $href, string $label, string $iconSvg, string $activePath = null): string {
                global $currentUrl;
                $path = $activePath ?? $href;
                $active = str_contains($currentUrl, $path);
                $classes = $active
                    ? 'bg-indigo-600 text-white'
                    : 'text-slate-300 hover:bg-slate-800 hover:text-white';
                return '
                <a href="' . $href . '" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors ' . $classes . '">
                    <span class="w-5 h-5 flex-shrink-0">' . $iconSvg . '</span>
                    ' . $label . '
                </a>';
            }
            ?>

            <?= techSidebarItem(base_url('mechanic/dashboard'), 'Dashboard',
                '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
                'mechanic/dashboard') ?>

            <?= techSidebarItem(base_url('mechanic/jobs'), 'My Jobs',
                '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
                'mechanic/jobs') ?>

            <?= techSidebarItem(base_url('mechanic/history'), 'History',
                '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'mechanic/history') ?>
            <?php
            $sidebarNotifBadge = '<span id="sidebarNotifBadge" data-unread-count="0" class="hidden w-2 h-2 bg-red-500 rounded-full absolute top-1 right-1"></span>';
            ?>
            <a href="<?= base_url('mechanic/notifications') ?>" class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= str_contains($currentUrl, 'mechanic/notifications') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <span class="w-5 h-5 flex-shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </span>
                Notifications
                <?= $sidebarNotifBadge ?>
            </a>

            <?= techSidebarItem(base_url('admin/profile'), 'Profile',
                '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                'admin/profile') ?>
        </nav>

        <!-- Sidebar footer: logged in user -->
        <div class="px-4 py-4 border-t border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-medium text-xs">
                        <?= strtoupper(substr(session()->get('user_name') ?? 'T', 0, 1)) ?>
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="<?= base_url('admin/profile') ?>" class="text-sm font-medium text-white truncate hover:text-indigo-300 transition-colors"><?= session()->get('user_name') ?></a>
                    <p class="text-xs text-slate-400 capitalize"><?= session()->get('role') ?></p>
                </div>
                <a href="<?= base_url('logout') ?>" class="text-slate-400 hover:text-red-400 transition-colors" title="Logout">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- ============================================================ -->
    <!-- Main content area                                            -->
    <!-- ============================================================ -->
    <div class="flex-1 flex flex-col min-h-screen md:ml-64 pb-16 md:pb-0">

        <!-- Top bar -->
        <header id="topbar" class="bg-white border-b border-gray-200 px-4 md:px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 class="text-xl font-semibold text-gray-900"><?= $title ?? 'Technician Portal' ?></h1>
                <nav class="text-xs text-gray-400 mt-0.5 space-x-1">
                    <?php
                    $bcSegments = $uri->getSegments();
                    foreach ($bcSegments as $i => $seg):
                        $label = ucwords(str_replace(['-', '_'], ' ', $seg));
                        if ($i > 0) echo '<span class="text-gray-300">/</span>';
                    ?>
                    <span class="<?= $i === array_key_last($bcSegments) ? 'text-gray-500 font-medium' : '' ?>"><?= $label ?></span>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <!-- Notification bell -->
                <a href="<?= base_url('mechanic/notifications') ?>" class="relative p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span id="topbarNotifBadge" data-unread-count="0" class="hidden absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">0</span>
                </a>

                <!-- Profile avatar -->
                <a href="<?= base_url('admin/profile') ?>" class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-medium text-xs">
                            <?= strtoupper(substr(session()->get('user_name') ?? 'T', 0, 1)) ?>
                        </span>
                    </div>
                    <span class="font-medium hidden sm:block"><?= session()->get('user_name') ?? 'Technician' ?></span>
                </a>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="px-4 md:px-6 pt-4">
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
        <main id="main-content" class="flex-1 px-4 md:px-6 py-6 max-w-6xl mx-auto w-full">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer -->
        <footer class="px-4 md:px-6 py-4 border-t border-gray-100">
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

    <script src="<?= base_url('public/assets/js/notifications-poll.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
