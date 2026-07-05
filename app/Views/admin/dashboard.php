<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

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
        /* Used for main cards */
        --success-color: #28a745;
        --success-hover-color: #218838;
        --warning-color: #ffc107;
        --warning-hover-color: #e0a800;
        --danger-color: #dc3545;
        --danger-hover-color: #c82333;
        --info-color: #17a2b8;
        --info-hover-color: #138496;
        --dark-color: #343a40;
        /* For petty cash button */
        --dark-hover-color: #23272b;
    }

    body {
        font-family: 'Inter', sans-serif;
        /* Ensure Inter font is applied */
        color: var(--text-dark);
        /* Consistent text color */
    }

    .container.mt-5 {
        margin-top: 3rem !important;
        /* Bootstrap's mt-5 value */
    }

    h3 {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 2rem !important;
        /* Consistent spacing */
    }

    /* Modern Card Styling */
    .card {
        border-radius: 15px;
        /* More rounded corners */
        border: none;
        /* Remove default card border */
        box-shadow: 0 10px 30px var(--shadow-medium);
        /* Softer, larger shadow */
        overflow: hidden;
        /* Ensure content respects border-radius */
    }

    /* Summary Card Specifics with subtle gradients */
    .card.bg-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-hover-color)) !important;
        /* Primary gradient */
        color: white !important;
    }

    .card.bg-success {
        background: linear-gradient(135deg, var(--success-color), var(--success-hover-color)) !important;
        /* Success gradient */
        color: white !important;
    }

    .card.bg-warning {
        background: linear-gradient(135deg, var(--warning-color), var(--warning-hover-color)) !important;
        /* Warning gradient */
        color: var(--text-dark) !important;
        /* Ensure text is readable on yellow */
    }

    .card.bg-danger {
        background: linear-gradient(135deg, var(--danger-color), var(--danger-hover-color)) !important;
        /* Danger gradient */
        color: white !important;
    }

    /* Ensure icons in summary cards have correct color */
    .card.bg-primary .bi,
    .card.bg-success .bi,
    .card.bg-danger .bi {
        color: white !important;
    }

    .card.bg-warning .bi {
        color: var(--text-dark) !important;
    }

    /* Card Header Styling (Recent Activity, Quick Actions, Charts, Alerts) */
    .card-header {
        background-color: var(--bg-light) !important;
        /* Use theme's light background */
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        /* Subtle border */
        font-weight: 600;
        /* Semi-bold for headings */
        color: var(--text-dark);
        /* Consistent text color */
        padding: 1.25rem 1.5rem;
        /* More generous padding */
        font-size: 1.1rem;
    }

    /* List Group Item Styling */
    .list-group-item {
        border-color: rgba(0, 0, 0, 0.05);
        /* Lighter borders */
        padding: 1rem 1.5rem;
        /* Consistent padding */
    }

    /* Quick Action Button Styling */
    .btn {
        border-radius: 8px;
        /* Consistent rounded corners */
        font-weight: 500;
        transition: all 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        /* Subtle lift on hover */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        /* More prominent shadow on hover */
    }

    /* Specific hover colors for outline buttons */
    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-outline-success:hover {
        background-color: var(--success-color);
        color: white;
    }

    .btn-outline-warning:hover {
        background-color: var(--warning-color);
        color: var(--text-dark);
        /* Keep text dark on warning hover */
    }

    .btn-outline-info:hover {
        background-color: var(--info-color);
        color: white;
    }

    .btn-outline-danger:hover {
        background-color: var(--danger-color);
        color: white;
    }

    .btn-outline-dark:hover {
        background-color: var(--dark-color);
        color: white;
    }

    /* === MODAL STYLING (Modernized) === */
    .modal-backdrop.fade {
        opacity: 0.7;
        /* Darker overlay */
    }

    .modal.fade .modal-dialog {
        transform: translate(0, -50px);
        /* Start slightly above center */
        opacity: 0;
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: translate(0, 0);
        /* Slide to center */
        opacity: 1;
    }

    .modal-content {
        border-radius: 15px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        /* Softer, larger shadow */
        border: none;
        overflow: hidden;
        /* Ensure content respects border-radius */
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem;
        background-color: var(--card-bg);
        /* Default light background for header */
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .modal-title {
        font-weight: 700;
        /* Bolder title */
        color: var(--primary-color);
        font-size: 1.6rem;
        /* Slightly larger title */
    }

    .modal-body {
        padding: 1.5rem;
        color: var(--text-dark);
    }

    .modal-footer {
        border-top: none;
        /* Remove default footer border */
        padding: 1rem 1.5rem;
        background-color: var(--card-bg);
    }

    .modal-footer .btn {
        padding: 0.6rem 1.2rem;
        /* Adjust button padding in footer */
        font-size: 0.95rem;
    }

    /* Critical Alerts specific styling */
    .alert-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-left: 4px solid;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        background-color: var(--bg-light);
    }

    .alert-item.alert-low-stock {
        border-color: var(--warning-color);
    }

    .alert-item.alert-overdue {
        border-color: var(--danger-color);
    }

    .alert-item.alert-pending {
        border-color: var(--info-color);
    }

    .alert-item .bi {
        font-size: 1.2rem;
    }

    .alert-item .bi-exclamation-triangle-fill {
        color: var(--warning-color);
    }

    .alert-item .bi-clock-fill {
        color: var(--danger-color);
    }

    .alert-item .bi-file-earmark-text-fill {
        color: var(--info-color);
    }

    /* Recent Activity Item Styling */
    .activity-item {
        display: flex;
        align-items: flex-start;
        /* Align icon to top of text */
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item .activity-icon {
        font-size: 1.1rem;
        color: var(--primary-color);
        flex-shrink: 0;
        /* Prevent icon from shrinking */
    }

    .activity-item .activity-details {
        flex-grow: 1;
    }

    .activity-item .activity-details .activity-text {
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .activity-item .activity-details .activity-meta {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.2rem;
    }

    .activity-item .activity-details .activity-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }

    .activity-item .activity-details .activity-link:hover {
        text-decoration: underline;
    }
</style>

<div class="container mt-5">
    <h3 class="mb-4">Dashboard</h3>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Total Users</h6>
                            <h3><?= $userCount ?? '0' ?></h3>
                        </div>
                        <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Vehicles</h6>
                            <h3><?= $vehicleCount ?? '0' ?></h3>
                        </div>
                        <i class="bi bi-truck" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Jobs in Progress</h6>
                            <h3><?= $activeJobs ?? '0' ?></h3>
                        </div>
                        <i class="bi bi-gear" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>This Month Revenue</h6>
                            <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($totalRevenue ?? 0, 0) ?></h3>
                        </div>
                        <i class="bi bi-cash-stack" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Outstanding Balance</h6>
                            <h3><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($outstandingBalance ?? 0, 0) ?></h3>
                        </div>
                        <i class="bi bi-credit-card-2-front" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5 g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong>Job Status Breakdown</strong>
                </div>
                <div class="card-body">
                    <canvas id="jobStatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong>Revenue Trends (Last 6 Months)</strong>
                </div>
                <div class="card-body">
                    <canvas id="revenueTrendsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <strong>Critical Alerts</strong>
                </div>
                <div class="card-body">
                    <div id="criticalAlertsList">
                        <div class="alert-item alert-low-stock">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Low Stock: Brake Pads (SKU: BP-123) are below minimum level. <a href="#" class="text-warning">Order Now</a></span>
                        </div>
                        <div class="alert-item alert-overdue">
                            <i class="bi bi-clock-fill"></i>
                            <span>Overdue Job: Job #456 for Customer X was due yesterday. <a href="#" class="text-danger">View Job</a></span>
                        </div>
                        <div class="alert-item alert-pending">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            <span>Pending LPO: LPO #789 for Supplier Y needs approval. <a href="#" class="text-info">Approve</a></span>
                        </div>
                        <?php if (empty($criticalAlerts ?? [])): ?>
                            <div class="text-muted text-center py-3">No critical alerts at the moment.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5 g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Recent Activity</strong>
                    <select class="form-select w-auto" id="activityFilter">
                        <option value="all">All Activity</option>
                        <option value="jobs">Job Updates</option>
                        <option value="users">New Users</option>
                        <option value="vehicles">Vehicle Registrations</option>
                    </select>
                </div>
                <ul class="list-group list-group-flush" id="recentActivityList">
                    <li class="list-group-item activity-item" data-type="jobs">
                        <?php if (!empty($recentActivity)): ?>
                            <?php foreach ($recentActivity as $activity): ?>
                    <li class=" list-group-item activity-item" data-type="<?= esc($activity['type']) ?>">
                        <i class="bi <?= esc($activity['icon']) ?> activity-icon"></i>
                        <div class="activity-details">
                            <div class="activity-text"><?= $activity['text'] ?></div>
                            <div class="activity-meta"><?= $activity['time'] ?></div>
                        </div>

                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-muted">No recent activity yet.</li>
            <?php endif; ?>
                </ul>
            </div>
        </div>



        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <strong>Quick Actions</strong>
                </div>
                <div class="card-body d-grid gap-3">
                    <button onclick="openModal('<?= base_url('admin/users/add') ?>', 'Add New User')" class="btn btn-outline-primary d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus"></i> Add User
                    </button>
                    <button onclick="openModal('<?= base_url('admin/vehicles/add') ?>', 'Register New Vehicle')" class="btn btn-outline-success d-flex align-items-center gap-2">
                        <i class="bi bi-car-front"></i> Register Vehicle
                    </button>
                    <button onclick="openModal('<?= base_url('admin/jobs/create') ?>', 'Create New Job')" class="btn btn-outline-warning d-flex align-items-center gap-2">
                        <i class="bi bi-briefcase"></i> Create Job
                    </button>
                    <button onclick="openModal('<?= base_url('admin/sublets/add') ?>', 'Add New Sublet')" class="btn btn-outline-info d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-repeat"></i> Add Sublet
                    </button>
                    <button onclick="openModal('<?= base_url('admin/lpos/add') ?>', 'Create New LPO')" class="btn btn-outline-danger d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-plus"></i> New LPO
                    </button>
                    <button onclick="openModal('<?= base_url('admin/pettycash/add') ?>', 'Add Petty Cash Entry')" class="btn btn-outline-dark d-flex align-items-center gap-2">
                        <i class="bi bi-cash"></i> Add Petty Cash
                    </button>
                </div>
            </div>
        </div>
    </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {


        // --- openModal Function ---
        function openModal(url, title = 'Form') {
            const modalElement = document.getElementById('actionModal');
            const modal = new bootstrap.Modal(modalElement);
            const modalTitle = modalElement.querySelector('.modal-title');
            const modalContent = document.getElementById('modalContent');

            modalContent.innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="min-height: 100px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            modalTitle.textContent = title;

            modal.show();

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
                    modalContent.innerHTML = data;
                })
                .catch(error => {
                    modalTitle.textContent = 'Error';
                    modalContent.innerHTML = `<div class="alert alert-danger" role="alert">Error loading content: ${error.message}. Please try again.</div>`;
                    console.error('Error loading modal content:', error);
                });
        }
        window.openModal = openModal;


        // --- Chart.js Initialization ---

        const ctx = document.getElementById('jobStatusChart');
        if (!ctx) {
            console.error('Job Status Chart canvas not found');
            return;
        }

        const revenueLabels = <?= $revenueLabels ?? '["Jan","Feb","Mar","Apr","May","Jun"]' ?>;
        const revenueData = <?= $revenueByMonth ?? '[0,0,0,0,0,0]' ?>;


        // Job Status Breakdown Chart
        const jobstatusData = <?= $jobStatusData ?? '{}' ?>;
        if (Object.keys(jobstatusData).length === 0) {
            console.warn('No job status data available');
            return;
        }
        // Ensure jobstatusData is an object with expected keys
        if (typeof jobstatusData !== 'object' || Array.isArray(jobstatusData)) {
            console.error('Invalid job status data format');
            return;
        }
        //log if jobstatusData is not empty
        console.log('Job Status Data:', jobstatusData);


        const jobStatusCtx = document.getElementById('jobStatusChart');
        if (jobStatusCtx) {
            new Chart(jobStatusCtx, {
                type: 'doughnut', // Doughnut chart for job status
                data: {
                    labels: Object.keys(jobstatusData),
                    datasets: [{
                        data: Object.values(jobstatusData),
                        backgroundColor: [
                            'rgba(255, 193, 7, 0.8)', // Warning (yellow)
                            'rgba(220, 53, 69, 0.8)', // Danger (red)
                            'rgba(255, 255, 255, 0.8)', // White for empty or unknown status
                            'rgba(52, 58, 64, 0.8)', // Dark (grey)
                            'rgb(186, 85, 255)',
                            'rgb(255, 99, 255)',
                            'rgba(255, 159, 64, 0.8)', // Orange
                            'rgba(0, 123, 255, 0.8)', // Primary (blue)
                            'rgba(23, 162, 184, 0.8)', // Info (teal)
                            'rgba(40, 167, 69, 0.8)', // Success (green)
                            'rgba(108, 117, 125, 0.8)' // Secondary (grey)
                        ],
                        borderColor: 'white',
                        borderWidth: 2
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: 'Inter',
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }

            });
        }

        // Revenue Trends Chart
        const revenueTrendsCtx = document.getElementById('revenueTrendsChart');
        if (revenueTrendsCtx) {
            new Chart(revenueTrendsCtx, {
                type: 'line',
                data: {
                    labels: revenueLabels,
                    datasets: [{
                        label: 'Revenue (<?= org_setting('currency_symbol', 'KSh') ?>)',
                        data: revenueData,
                        fill: true,
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        borderColor: 'var(--primary-color)',
                        tension: 0.4,
                        pointBackgroundColor: 'var(--primary-color)',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide legend for single dataset
                        },
                        title: {
                            display: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)' // Lighter grid lines
                            },
                            ticks: {
                                font: {
                                    family: 'Inter'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false // Hide x-axis grid lines
                            },
                            ticks: {
                                font: {
                                    family: 'Inter'
                                }
                            }
                        }
                    }
                }
            });
        }

        // --- Recent Activity Filtering ---
        const activityFilter = document.getElementById('activityFilter');
        const recentActivityList = document.getElementById('recentActivityList');
        const allActivityItems = recentActivityList.querySelectorAll('.activity-item');

        activityFilter.addEventListener('change', function() {
            const selectedType = this.value;

            allActivityItems.forEach(item => {
                const itemType = item.getAttribute('data-type');
                if (selectedType === 'all' || itemType === selectedType) {
                    item.style.display = 'flex'; // Show item
                } else {
                    item.style.display = 'none'; // Hide item
                }
            });

            // If no items are visible, display a "No activity" message (optional, but good UX)
            const visibleItems = Array.from(allActivityItems).filter(item => item.style.display !== 'none');
            if (visibleItems.length === 0) {
                // You might want to add a temporary message here or handle it in PHP if data is empty
                // For now, assuming some items are always present or handled by PHP's empty check
            }
        });
    });

    document.getElementById('activityFilter').addEventListener('change', function() {
        const selectedType = this.value;
        const items = document.querySelectorAll('.activity-item');

        items.forEach(item => {
            const match = selectedType === 'all' || item.dataset.type === selectedType;
            item.style.display = match ? 'flex' : 'none';
        });
    });
</script>
<?= $this->endSection(); ?>