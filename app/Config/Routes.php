<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// --- Public routes ---
$routes->get('login', 'LoginController::index');
$routes->post('login/auth', 'LoginController::auth');
$routes->get('logout', 'LoginController::logout');
$routes->get('unauthorized', 'DashboardController::unauthorized');



//Add user routes
$routes->get('user/add_step1', 'UsersController::addStep1');
$routes->post('user/add_step1', 'UsersController::add_step1');
// $routes->get('user/getLastId/(:segment)', 'UsersController::getLastId/$1');

$routes->get('user/add_step2', 'UsersController::addStep2');
$routes->post('user/add_step2', 'UsersController::add_step2');

$routes->get('user/add_step3', 'UsersController::addStep3');
$routes->post('user/add_step3', 'UsersController::addUserStep3');
$routes->post('user/addUserStep3', 'UsersController::addUserStep3');

$routes->get('user/preview', 'UsersController::preview');
$routes->get('user/saveUser', 'UsersController::saveUser');

// $routes->post('/save-step-data/(:num)', 'UsersController::saveStepData/$1'); // REMOVED: method does not exist

// $routes->post('/final-submit', 'UsersController::finalSubmit'); // REMOVED: method does not exist

// $routes->get('user/getLastId/(:any)', 'UsersController::getLastId/$1');
$routes->get('user/getLastId', 'UsersController::getLastId');

$routes->get('user/preview', 'UsersController::preview');

// $routes->post('user/submit', 'UsersController::submit'); // REMOVED: method does not exist

$routes->get('user/success', 'UsersController::success');
$routes->get('user/failure', 'UsersController::failure');

// $routes->post('admin/users/bulk_action', 'UsersController::bulk_action'); // REMOVED: method is commented out

// --- Job Intake routes ---
$routes->group('job_intake', ['filter' => 'auth:admin,receptionist'], function ($routes) {
    $routes->get('/', 'JobIntake::index');
    $routes->get('search', 'JobIntake::search');
    $routes->post('create_job_card', 'JobIntake::create_job_card');
    $routes->get('create_job_card', 'JobIntake::create_job_card');
    $routes->post('fetch_vehicle_details', 'JobIntake::fetch_vehicle_details');
    $routes->post('fetch_customer_details', 'JobIntake::fetch_customer_details');
});


// Protected routes

// Admin-only
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {

    // Dashboard
    $routes->get('/', 'DashboardController::admin');
    $routes->get('dashboard', 'DashboardController::admin');

    // Users
    $routes->get('users', 'UsersController::index');
    $routes->get('users/add', 'UsersController::add');
    $routes->get('users/(:num)', 'UsersController::details/$1');
    $routes->get('users/edit/(:num)', 'UsersController::edit/$1');
    $routes->post('users/update/(:num)', 'UsersController::update/$1');
    $routes->get('users/delete/(:num)', 'UsersController::delete/$1');
    $routes->post('users/bulk_action', 'UsersController::bulk_action');
    $routes->get('users/fetch/(:num)', 'UsersController::details/$1');
    $routes->get('users/fetch', 'UsersController::fetchUsers');

    // Vehicles
    $routes->get('vehicles', 'VehicleController::index');
    $routes->get('vehicles/fetch', 'VehicleController::fetchVehicles');
    $routes->get('vehicles/fetch/(:num)', 'VehicleController::fetchVehicles');

    $routes->get('vehicles/edit/(:num)', 'VehicleController::edit/$1');
    $routes->post('vehicles/store', 'VehicleController::store');
    $routes->post('vehicles/update/(:num)', 'VehicleController::update/$1');
    $routes->post('vehicles/delete/(:num)', 'VehicleController::delete/$1');
    $routes->get('vehicles/details/(:num)', 'VehicleController::details/$1');


    // Jobs
    $routes->get('jobs', 'JobsController::index');
    $routes->get('jobs/fetch', 'JobsController::fetchJobs');
    $routes->get('jobs/add', 'JobsController::add');
    // $routes->post('jobs/create', 'JobsController::create'); // REMOVED: method does not exist
    $routes->get('jobs/(:num)', 'JobsController::details/$1');
    $routes->get('jobs/edit/(:num)', 'JobsController::edit/$1');
    $routes->post('jobs/update/(:num)', 'JobsController::update/$1');
    $routes->get('jobs/delete/(:num)', 'JobsController::delete/$1');
    // $routes->post('jobs/bulk_action', 'JobsController::bulk_action'); // REMOVED: method does not exist
    $routes->post('jobs/assign_mechanic/(:num)', 'JobsController::assign_mechanic/$1');
    $routes->post('jobs/update_status/(:num)', 'JobsController::update_status/$1');
    $routes->get('jobs/status_history/(:num)', 'JobsController::status_history/$1');
    // $routes->get('job/job_intake_form', 'JobIntake::index');

    // Customers
    $routes->get('customers', 'CustomersController::index');
    $routes->post('customers/load', 'CustomersController::load');
    $routes->get('customers/load', 'CustomersController::load');
    $routes->get('customers/details/(:num)', 'CustomersController::details/$1');
    $routes->get('customers/add', 'CustomersController::add');
    $routes->get('customers/edit/(:num)', 'CustomersController::edit/$1');
    $routes->post('customers/bulk_action', 'CustomersController::bulk_action');

    // Calendar
    $routes->get('calendar', 'CalendarController::index');
    $routes->get('calendar/getEvents', 'CalendarController::getEvents');
    $routes->get('calendar/addEvent', 'CalendarController::addEvent');
    $routes->post('calendar/addEvent', 'CalendarController::addEvent');
    $routes->post('calendar/updateEventDate', 'CalendarController::updateEventDate');

    // Settings
    $routes->get('settings', 'SettingsController::index');
    $routes->post('settings/update', 'SettingsController::update');

    // Invoices
    $routes->get('invoices', 'InvoicesController::index');
    $routes->get('invoices/load', 'InvoicesController::load');
    $routes->get('invoices/view/(:num)', 'InvoicesController::view/$1');
    $routes->get('invoices/generate/(:num)', 'InvoicesController::generate/$1');
    $routes->post('invoices/record_payment/(:num)', 'InvoicesController::recordPayment/$1');
    $routes->get('invoices/mark_overdue', 'InvoicesController::markOverdue');

    // Inventory
    $routes->get('inventory', 'InventoryController::index');
    $routes->get('inventory/load', 'InventoryController::load');
    $routes->get('inventory/add', 'InventoryController::add');
    $routes->post('inventory/create', 'InventoryController::create');
    $routes->get('inventory/edit/(:num)', 'InventoryController::edit/$1');
    $routes->post('inventory/update/(:num)', 'InventoryController::update/$1');
    $routes->post('inventory/delete/(:num)', 'InventoryController::delete/$1');
    $routes->get('inventory/fetch/(:num)', 'InventoryController::fetch/$1');
    $routes->get('inventory/search', 'InventoryController::search');

    // Suppliers
    $routes->get('suppliers', 'SuppliersController::index');
    $routes->get('suppliers/load', 'SuppliersController::load');
    $routes->get('suppliers/add', 'SuppliersController::add');
    $routes->post('suppliers/create', 'SuppliersController::create');
    $routes->get('suppliers/edit/(:num)', 'SuppliersController::edit/$1');
    $routes->post('suppliers/update/(:num)', 'SuppliersController::update/$1');
    $routes->post('suppliers/delete/(:num)', 'SuppliersController::delete/$1');
    $routes->get('suppliers/all', 'SuppliersController::getAll');

    // Sublets
    $routes->get('sublets', 'SubletsController::index');
    $routes->get('sublets/add', 'SubletsController::add');
    $routes->post('sublets/load', 'SubletsController::load');
    $routes->get('sublets/edit/(:num)', 'SubletsController::add/$1');
    $routes->post('sublets/save', 'SubletsController::save');
    $routes->get('sublets/details/(:num)', 'SubletsController::details/$1');
    $routes->post('sublets/delete/(:num)', 'SubletsController::delete/$1');
    $routes->post('sublets/bulkAction', 'SubletsController::bulkAction');
    $routes->get('sublets/fetch', 'SubletsController::fetchSublets');
    $routes->get('sublets/fetch/(:num)', 'SubletsController::fetchSublets/$1');
    $routes->get('sublets/(:num)', 'SubletsController::details/$1');
    $routes->get('sublets/(:num)/edit', 'SubletsController::edit/$1');
    $routes->post('sublets/(:num)/update', 'SubletsController::update/$1');

    // LPOs
    $routes->get('lpos', 'LpoController::index');
    $routes->get('lpos/load', 'LpoController::load');
    $routes->get('lpos/add', 'LpoController::add');
    $routes->post('lpos/create', 'LpoController::create');
    $routes->get('lpos/view/(:num)', 'LpoController::view/$1');
    $routes->get('lpos/edit/(:num)', 'LpoController::edit/$1');
    $routes->post('lpos/update/(:num)', 'LpoController::update/$1');
    $routes->post('lpos/update_status/(:num)', 'LpoController::updateStatus/$1');
    $routes->get('lpos/receive/(:num)', 'LpoController::receive/$1');
    $routes->post('lpos/process_receive/(:num)', 'LpoController::processReceive/$1');
    $routes->post('lpos/delete/(:num)', 'LpoController::delete/$1');

    // Reports
    $routes->get('reports', 'ReportsController::index');
    $routes->get('reports/financial', 'ReportsController::financial');
    $routes->get('reports/operational', 'ReportsController::operational');
    $routes->get('reports/inventory', 'ReportsController::inventory');
    $routes->get('reports/customers', 'ReportsController::customers');
    $routes->get('reports/staff', 'ReportsController::staff');
    $routes->get('reports/export/(:segment)/(:segment)', 'ReportsController::export/$1/$2');

    // Petty Cash
    $routes->get('pettycash', 'PettyCashController::index');
    $routes->get('pettycash/load', 'PettyCashController::load');
    $routes->get('pettycash/add', 'PettyCashController::add');
    $routes->post('pettycash/create', 'PettyCashController::create');
    $routes->get('pettycash/edit/(:num)', 'PettyCashController::edit/$1');
    $routes->post('pettycash/update/(:num)', 'PettyCashController::update/$1');
    $routes->post('pettycash/delete/(:num)', 'PettyCashController::delete/$1');
    $routes->get('pettycash/ledger', 'PettyCashController::ledger');
    $routes->post('pettycash/filter', 'PettyCashController::filter');
    
});

// Receptionist-only
$routes->group('receptionist', ['filter' => 'auth:receptionist'], function ($routes) {
    $routes->get('/', 'DashboardController::receptionist');
    $routes->get('dashboard', 'DashboardController::receptionist');
});

// Mechanic-only
$routes->group('mechanic', ['filter' => 'auth:mechanic'], function ($routes) {
    $routes->get('/', 'DashboardController::mechanic');
    $routes->get('dashboard', 'DashboardController::mechanic');
    $routes->get('jobs', 'JobIntake::mechanic_jobs');
    $routes->get('jobs/(:num)', 'JobIntake::mechanic_view/$1');
    $routes->post('save_diagnosis', 'JobIntake::save_diagnosis');
    $routes->get('search_parts', 'JobIntake::search_parts');
    $routes->post('jobs/update_status/(:num)', 'JobsController::update_status/$1');
    $routes->get('inventory/search', 'InventoryController::search');
});

// Customer-only
$routes->group('customer', ['filter' => 'auth:customer'], function ($routes) {
    $routes->get('/', 'DashboardController::customer');
    $routes->get('dashboard', 'DashboardController::customer');
});
