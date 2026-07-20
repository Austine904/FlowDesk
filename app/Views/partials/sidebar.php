<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-slate-900 flex flex-col z-20">

    <!-- Logo/Brand -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div>
            <span class="text-white font-semibold text-sm">FlowDesk</span>
            <p class="text-slate-400 text-xs">Management System</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-1">
        <?php
        $role = session()->get('role');
        $currentUrl = current_url();
        function isActive($path) {
            global $currentUrl;
            return str_contains($currentUrl, $path)
                ? 'bg-indigo-600 text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white';
        }
        function navItem($href, $label, $icon, $activePath = null) {
            global $currentUrl;
            $path = $activePath ?? $href;
            $active = str_contains($currentUrl, $path);
            $classes = $active
                ? 'bg-indigo-600 text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white';
            return "<a href=\"{$href}\" class=\"flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {$classes}\">
                <span class=\"w-5 h-5 flex-shrink-0\">{$icon}</span>
                {$label}
            </a>";
        }
        ?>

        <!-- Dashboard -->
        <?php if ($role !== 'customer'): ?>
        <?= navItem(base_url('admin/dashboard'), 'Dashboard', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>', 'admin/dashboard') ?>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <!-- Section: People -->
        <div class="pt-4 pb-1">
            <p class="px-3 text-xs font-medium text-slate-500 uppercase tracking-wider">People</p>
        </div>
        <?= navItem(base_url('admin/users'), 'Staff', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>', 'admin/users') ?>
        <?= navItem(base_url('admin/customers'), 'Customers', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'admin/customers') ?>
        <?php endif; ?>

        <?php if (in_array($role, ['admin', 'receptionist', 'mechanic'])): ?>
        <!-- Section: Operations -->
        <div class="pt-4 pb-1">
            <p class="px-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Operations</p>
        </div>
        <?= navItem(base_url('admin/jobs'), 'Job Cards', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>', 'admin/jobs') ?>
        <?= navItem(base_url('admin/vehicles'), 'Vehicles', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>', 'admin/vehicles') ?>
        <?= navItem(base_url('admin/calendar'), 'Calendar', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>', 'admin/calendar') ?>
        <?php if ($role !== 'mechanic'): ?>
        <?= navItem(base_url('admin/sublets'), 'Sublets', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>', 'admin/sublets') ?>
        <?php endif; ?>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <!-- Section: Inventory -->
        <div class="pt-4 pb-1">
            <p class="px-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Inventory</p>
        </div>
        <?= navItem(base_url('admin/inventory'), 'Parts & Inventory', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>', 'admin/inventory') ?>
        <?= navItem(base_url('admin/suppliers'), 'Suppliers', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>', 'admin/suppliers') ?>
        <?= navItem(base_url('admin/lpos'), 'LPOs', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>', 'admin/lpos') ?>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <!-- Section: Finance -->
        <div class="pt-4 pb-1">
            <p class="px-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Finance</p>
        </div>
        <?= navItem(base_url('admin/invoices'), 'Invoices', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>', 'admin/invoices') ?>
        <?= navItem(base_url('admin/payments'), 'Payments', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>', 'admin/payments') ?>
        <?= navItem(base_url('admin/pettycash'), 'Petty Cash', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>', 'admin/pettycash') ?>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <!-- Section: Analytics -->
        <div class="pt-4 pb-1">
            <p class="px-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Analytics</p>
        </div>
        <?= navItem(base_url('admin/reports'), 'Reports', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>', 'admin/reports') ?>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <!-- Section: System -->
        <div class="pt-4 pb-1">
            <p class="px-3 text-xs font-medium text-slate-500 uppercase tracking-wider">System</p>
        </div>
        <?= navItem(base_url('admin/settings'), 'Settings', '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'admin/settings') ?>
        <?php endif; ?>
    </nav>

    <!-- Sidebar footer: logged in user -->
    <div class="px-4 py-4 border-t border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                <span class="text-white font-medium text-xs">
                    <?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
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
