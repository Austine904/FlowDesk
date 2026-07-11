<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'FlowDesk' ?> — FlowDesk</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables (Tailwind-compatible, no Bootstrap) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- Tailwind config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

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

        .flash-message { animation: slideDown 0.3s ease; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

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

        <!-- Topbar -->
        <header id="topbar" class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-xl font-semibold text-gray-900"><?= $pageTitle ?? 'Dashboard' ?></h1>
            </div>

            <div class="flex items-center gap-4">
                <button class="relative text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

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
        function getCsrfMeta() {
            return {
                name: document.querySelector('meta[name="csrf-name"]')?.getAttribute('content'),
                hash: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            };
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
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
