<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Customer Portal' ?> — <?= org_setting('org_name', 'FlowDesk') ?></title>

    <link rel="stylesheet" href="<?= base_url('public/assets/css/tailwind.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Top Bar -->
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-900"><?= org_setting('org_name', 'FlowDesk') ?></span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="<?= base_url('customer/dashboard') ?>" class="text-sm text-gray-600 hover:text-indigo-600 font-medium transition-colors <?= current_url() === base_url('customer/dashboard') ? 'text-indigo-600' : '' ?>">Dashboard</a>
                    <a href="<?= base_url('customer/jobs') ?>" class="text-sm text-gray-600 hover:text-indigo-600 font-medium transition-colors <?= str_contains(current_url(), 'customer/jobs') ? 'text-indigo-600' : '' ?>">My Jobs</a>
                    <a href="<?= base_url('customer/invoices') ?>" class="text-sm text-gray-600 hover:text-indigo-600 font-medium transition-colors <?= str_contains(current_url(), 'customer/invoices') ? 'text-indigo-600' : '' ?>">Invoices</a>
                    <a href="<?= base_url('logout') ?>" class="text-sm text-red-600 hover:text-red-700 font-medium ml-2">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <?php if (session()->getFlashdata('success')): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-4 text-sm">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Page Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="border-t border-gray-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs text-gray-400">&copy; <?= date('Y') ?> <?= org_setting('org_name', 'FlowDesk') ?>. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
