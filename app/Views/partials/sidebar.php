<?php
// Retrieve current URL segment and session data
$currentSegment = service('uri')->getSegment(2);
$session = session();
$name = $session->get('user_name');
$role = $session->get('role');
$userPhoto = $session->get('profile_picture');

/**
 * Helper function to determine if a navigation link should be active.
 *
 * @param string $segment The segment name to check against.
 * @param string $currentSegment The current URL segment.
 * @return string Returns 'active' if segments match, otherwise an empty string.
 */
function isActive($segment, $currentSegment)
{
    return ($currentSegment == $segment) ? 'active' : '';
}
?>

<style>
    /* Consistent theme variables (ensure these are defined in your main layout or a global CSS file) */
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
        --sidebar-bg: #f0f2f5;
        /* Lighter background for sidebar */
        --sidebar-active-bg: rgba(0, 123, 255, 0.1);
        /* Light primary tint for active */
        --sidebar-hover-bg: rgba(0, 123, 255, 0.05);
        /* Very light primary tint for hover */
    }

    .sidebar {
        width: 250px;
        /* Fixed width for the sidebar */
        background-color: var(--sidebar-bg);
        /* Use a consistent background */
        height: 100vh;
        /* Full viewport height */
        position: fixed;
        /* Fixed position */
        top: 0;
        left: 0;
        padding: 1.5rem 1rem;
        display: flex;
        flex-direction: column;
        box-shadow: 5px 0 15px var(--shadow-light);
        /* Soft shadow to the right */
        border-right: 1px solid rgba(0, 0, 0, 0.05);
        transition: width 0.3s ease;
        /* For future collapsible feature */
        font-family: 'Inter', sans-serif;
    }

    .sidebar .fs-4 {
        color: var(--primary-color);
        /* Primary color for brand name */
        font-weight: 700;
        /* Bolder brand name */
    }

    .sidebar nav {
        flex-grow: 1;
        min-height: 0;
        overflow-x: hidden;
        overflow-y: auto;
        scrollbar-width: thin;
        display: flex;
        flex-direction: column;
    }

    .sidebar nav ul {
        margin-top: 2rem;
        margin-bottom: 0;
    }

    .sidebar .nav-item {
        margin-bottom: 0.5rem;
    }

    .sidebar .nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        /* Space between icon and text */
        padding: 0.75rem 1rem;
        color: var(--text-dark);
        /* Default text color */
        border-radius: 8px;
        /* Rounded corners for nav links */
        transition: all 0.2s ease, transform 0.1s ease;
        position: relative;
        /* For active indicator */
        font-weight: 500;
        text-decoration: none;
        /* Remove underline */
        

    }

    .sidebar .nav-link .bi {
        font-size: 1.2rem;
        /* Slightly larger icons */
        flex-shrink: 0;
        /* Prevent icon from shrinking */
    }

    /* Hover Effect */
    .sidebar .nav-link.nav-hover:hover {
        background-color: var(--sidebar-hover-bg);
        color: var(--primary-color);
        transform: translateX(3px);
        /* Subtle slide effect */
    }

    /* Active Link Styling */
    .sidebar .nav-link.active {
        background-color: var(--sidebar-active-bg);
        color: var(--primary-color);
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
        /* Subtle shadow for active link */
    }

    .sidebar .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 80%;
        width: 4px;
        /* Left border indicator */
        background-color: var(--primary-color);
        border-radius: 0 5px 5px 0;
    }

    /* Profile item at bottom of sidebar */
    .sidebar-profile {
        flex-shrink: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        background-color: var(--sidebar-bg);
    }

    .sidebar-profile .nav-link {
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-dark);
        font-weight: 500;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .sidebar-profile .nav-link:hover {
        background-color: var(--sidebar-hover-bg);
        color: var(--primary-color);
    }

    .sidebar-profile .profile-picture {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--primary-color);
        flex-shrink: 0;
    }

    .sidebar-profile .profile-name {
        flex-grow: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar-profile .logout-icon {
        font-size: 1.1rem;
        flex-shrink: 0;
        opacity: 0.6;
        transition: opacity 0.2s;
    }

    .sidebar-profile .nav-link:hover .logout-icon {
        opacity: 1;
    }
</style>

<div class="sidebar">
    <div class="d-flex align-items-center mb-4">
        <span class="fs-4 fw-bold text-dark">FlowDesk</span>
    </div>

    <nav>
        <ul class="nav flex-column gap-3">
            <li class="nav-item">
                <a class="nav-link nav-hover <?= isActive('dashboard', $currentSegment) ?>"
                    href="<?= base_url('admin/dashboard') ?>"
                    <?= isActive('dashboard', $currentSegment) ? 'aria-current="page"' : '' ?>>
                    <i class="bi bi-house-door fs-6"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-hover <?= isActive('jobs', $currentSegment) ?>"
                    href="<?= base_url('admin/jobs') ?>"
                    <?= isActive('jobs', $currentSegment) ? 'aria-current="page"' : '' ?>>
                    <i class="bi bi-briefcase fs-6"></i>
                    <span>Jobs</span>
                </a>
            </li>
            <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('users', $currentSegment) ?>"
                        href="<?= base_url('admin/users') ?>"
                        <?= isActive('users', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-people fs-6"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('customers', $currentSegment) ?>"
                        href="<?= base_url('admin/customers') ?>"
                        <?= isActive('customers', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-person-bounding-box fs-6"></i>
                        <span>Customers</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link nav-hover <?= isActive('vehicles', $currentSegment) ?>"
                    href="<?= base_url('admin/vehicles') ?>"
                    <?= isActive('vehicles', $currentSegment) ? 'aria-current="page"' : '' ?>>
                    <i class="bi bi-car-front fs-6"></i>
                    <span>Vehicles</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link nav-hover <?= isActive('sublets', $currentSegment) ?>"
                    href="<?= base_url('admin/sublets') ?>"
                    <?= isActive('sublets', $currentSegment) ? 'aria-current="page"' : '' ?>>
                    <i class="bi bi-gear fs-6"></i>
                    <span>Sublets</span>
                </a>
            </li>
            <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('inventory', $currentSegment) ?>"
                        href="<?= base_url('admin/inventory') ?>"
                        <?= isActive('inventory', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-boxes fs-6"></i>
                        <span>Inventory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('suppliers', $currentSegment) ?>"
                        href="<?= base_url('admin/suppliers') ?>"
                        <?= isActive('suppliers', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-truck-flatbed fs-6"></i>
                        <span>Suppliers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('invoices', $currentSegment) ?>"
                        href="<?= base_url('admin/invoices') ?>"
                        <?= isActive('invoices', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-receipt fs-6"></i>
                        <span>Invoices</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('calendar', $currentSegment) ?>"
                        href="<?= base_url('admin/calendar') ?>"
                        <?= isActive('calendar', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-calendar-event fs-6"></i>
                        <span>Calendar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('lpos', $currentSegment) ?>"
                        href="<?= base_url('admin/lpos') ?>"
                        <?= isActive('lpos', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-file-earmark-text fs-6"></i>
                        <span>LPOs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('pettycash', $currentSegment) ?>"
                        href="<?= base_url('admin/pettycash') ?>"
                        <?= isActive('pettycash', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-cash-stack fs-6"></i>
                        <span>Petty Cash</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('reports', $currentSegment) ?>"
                        href="<?= base_url('admin/reports') ?>" <?= isActive('reports', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-bar-chart fs-6"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover <?= isActive('settings', $currentSegment) ?>"
                        href="<?= base_url('admin/settings') ?>"
                        <?= isActive('settings', $currentSegment) ? 'aria-current="page"' : '' ?>>
                        <i class="bi bi-gear-fill fs-6"></i>
                        <span>Settings</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="sidebar-profile">
        <a class="nav-link" href="<?= base_url('logout') ?>">
            <img src="<?= $userPhoto ? base_url($userPhoto) : 'https://placehold.co/32x32/cccccc/333333?text=JP' ?>" class="profile-picture" alt="">
            <span class="profile-name"><strong><?= esc($name) ?></strong> (<?= esc(ucfirst($role)) ?>)</span>
            <i class="bi bi-box-arrow-right logout-icon"></i>
        </a>
    </div>
</div>