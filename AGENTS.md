# AGENTS.md — FlowDesk Organizational Management System

## 1. PROJECT OVERVIEW

FlowDesk is a garage/shop management system for tracking job cards, vehicles, customers, users, sublets, and calendar events. Built as a single CodeIgniter 4 application with role-based dashboards.

**Stack:**
- Framework: CodeIgniter 4 (v4.x, PHP 8.1+)
- PHP version: 8.1+
- Database: MySQL 5.7+ / MariaDB, database name `flowdesk` (note: `app/Config/Database.php` says `flowdesk` — this is stale; `.env` has `flowdesk` which is the actual database)
- Frontend: Bootstrap 5.3, jQuery 3.6, DataTables 1.13, FullCalendar 6.1, Chart.js, SweetAlert2, Select2, Font Awesome / Bootstrap Icons
- Web server: Apache with mod_rewrite (XAMPP)

**Local dev URL:** `http://localhost/FlowDesk/`
**Root folder:** `C:\xampp\htdocs\FlowDesk`

**Entry point:** `index.php` → `app/Config/Paths.php` → CodeIgniter boot
**Routing:** `app/Config/Routes.php` — explicit routes only (no auto-routing). Route groups with `filter` for auth.

## 2. ARCHITECTURE

```
C:\xampp\htdocs\FlowDesk\
├── app/
│   ├── Config/          # CI4 config files (App, Database, Routes, Filters, etc.)
│   ├── Controllers/     # All controllers (11 files)
│   ├── Database/
│   │   ├── Migrations/  # EMPTY — no migration files exist
│   │   └── Seeds/       # EMPTY — no seed files exist
│   ├── Filters/
│   │   └── AuthFilter.php
│   └── Views/
│       ├── admin/       # dashboard.php, users.php, add_user.php, users/user_list.php
│       ├── calendar/    # calendar.php, modals.php
│       ├── customers/   # customers.php, modals.php
│       ├── errors/      # unauthorized.php, html/ and cli/ error pages
│       ├── job/         # index.php, modals.php
│       ├── layouts/     # main.php
│       ├── partials/    # sidebar.php
│       ├── sublets/     # index.php, form.php, _details.php, modals.php
│       ├── user/        # add_step1/2/3.php, preview.php, success.php, getLastId.php
│       ├── vehicles/    # index.php, modals.php
│       └── *.php        # login.php, dashboard variants, welcome_message.php
├── public/
│   ├── assets/js/       # vehicles.js, job_intake.js, customers.js, calendar.js, sublets.js
│   └── css/             # Per-module CSS files
├── uploads/users/       # Profile picture uploads
├── vendor/              # Composer dependencies
├── writable/            # CI4 cache/logs/session
├── .htaccess            # RewriteBase /FlowDesk/
└── index.php            # Front controller
```

**Controllers, Views, Filters, Routes:**
- **Controllers** extend `BaseController` (which extends `CodeIgniter\Controller`). They call `\Config\Database::connect()` for raw DB access. No models — all DB queries are `$db->table('name')->...` or raw SQL.
- **Views** are loaded with `return view('path', $data)` and frequently extend `layouts/main` (which includes sidebar, Bootstrap, DataTables, etc.). Views that don't extend `layouts/main` (like `admin/add_user.php`) are standalone.
- **Auth** is enforced two ways: (1) route groups with `'filter' => 'auth:role'` in `Routes.php`, and (2) manual `session()->get('isLoggedIn')` checks inside controller methods.
- **Routes** are defined in `app/Config/Routes.php`. Public routes are top-level. Protected routes are in named groups (`admin`, `receptionist`, `mechanic`, `customer`) with `filter` applied.

**Auth system:**
- `AuthFilter` (`app/Filters/AuthFilter.php`) checks `session()->get('isLoggedIn')` and, if role arguments are provided, validates `session()->get('role')` is in the allowed list.
- Login writes these session keys: `user_id`, `user_name`, `role`, `company_id`, `profile_picture`, `isLoggedIn`.
- Logout destroys the session and redirects to `/login`.
- Roles: `admin`, `mechanic`, `receptionist`, `customer`.

**No models exist.** All DB access is raw `$db->table('table_name')` query builder calls or `$db->query('SELECT ...')` inside controllers. There are no Eloquent-like models or repository classes.

## 3. DATABASE

Database: **flowdesk** (MySQL). No migrations exist — schema lives in the database only.

### Tables

#### users
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| company_id | varchar(20) | NOT NULL, UNIQUE |
| first_name | varchar(50) | NOT NULL |
| last_name | varchar(50) | NOT NULL |
| name | varchar(101) | NULL, STORED GENERATED (first_name + last_name) |
| email | varchar(255) | NULL |
| phone_number | varchar(20) | NULL |
| phone | varchar(20) | NULL |
| password | varchar(255) | NOT NULL |
| role | enum('admin','mechanic','receptionist','customer') | NOT NULL, INDEX, default 'mechanic' |
| profile_picture | varchar(255) | NULL |
| date_of_employment | date | NULL |
| dob | date | NULL |
| national_id | varchar(50) | NULL |
| gender | enum('Male','Female','Other') | NULL |
| address | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |
| updated_at | datetime | NULL, on update current_timestamp() |
| deleted_at | datetime | NULL, INDEX (soft delete) |

#### next_of_kin
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| user_id | int(10) unsigned | FK → users.id |
| kin_first_name | varchar(50) | NOT NULL |
| kin_last_name | varchar(50) | NOT NULL |
| relationship | varchar(50) | NOT NULL |
| kin_phone_number | varchar(20) | NOT NULL |

#### vehicles
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| owner_id | int(10) unsigned | FK → customers.id |
| registration_number | varchar(20) | NOT NULL, UNIQUE |
| vin | varchar(17) | NULL, UNIQUE |
| make | varchar(50) | NOT NULL |
| model | varchar(50) | NOT NULL |
| year_of_manufacture | year(4) | NULL |
| engine_number | varchar(50) | NULL, UNIQUE |
| chassis_number | varchar(50) | NULL, UNIQUE |
| fuel_type | enum('Petrol','Diesel','Electric','Hybrid') | NULL |
| transmission | enum('Manual','Automatic','CVT','Semi-Automatic') | NULL |
| color | varchar(30) | NULL |
| mileage | int(11) | NULL |
| reported_problem | text | NULL |
| status | varchar(50) | NOT NULL, INDEX, default 'On Job' |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

#### customers
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| name | varchar(100) | NOT NULL, INDEX |
| phone | varchar(20) | NOT NULL, UNIQUE |
| email | varchar(255) | NULL |
| address | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

#### job_cards (the real jobs table)
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_no | varchar(30) | NOT NULL, UNIQUE |
| customer_id | int(10) unsigned | FK → customers.id |
| vehicle_id | int(10) unsigned | FK → vehicles.id |
| date_in | date | NOT NULL, INDEX |
| time_in | time | NULL |
| start_date | date | NULL |
| end_date | date | NULL |
| diagnosis | text | NULL |
| initial_damage_notes | text | NULL |
| job_status | varchar(30) | NOT NULL, INDEX, default 'Awaiting Diagnosis' |
| mileage_in | int(11) | NULL |
| fuel_level | enum('Empty','1/4','1/2','3/4','Full') | NULL |
| estimated_labor_hours | decimal(8,2) | NULL |
| assigned_service_advisor_id | int(10) unsigned | FK → users.id |
| job_summary | text | NULL |
| quote_amount | decimal(12,2) | NULL |
| quote_status | varchar(30) | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |
| updated_at | datetime | NULL, on update current_timestamp() |

#### job_card_photos
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| file_path | varchar(255) | NOT NULL |
| file_name | varchar(255) | NULL |

#### job_card_parts_required
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| inventory_id | int(10) unsigned | FK → inventory.id |
| quantity_required | int(11) | NOT NULL, default 1 |
| unit_price_at_estimate | decimal(12,2) | NOT NULL, default 0.00 |

#### job_card_labor_tasks
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| task_name | varchar(255) | NOT NULL |
| estimated_hours | decimal(8,2) | NOT NULL, default 0.00 |
| notes | text | NULL |

#### inventory
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| name | varchar(255) | NOT NULL, INDEX |
| part_number | varchar(100) | NULL, INDEX |
| unit_price | decimal(12,2) | NOT NULL, default 0.00 |

#### suppliers
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| name | varchar(255) | NOT NULL |

#### sublets
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| sublet_provider_id | int(10) unsigned | FK → suppliers.id |
| description | text | NOT NULL |
| cost | decimal(12,2) | NOT NULL, default 0.00 |
| status | enum('Pending','In Progress','Completed','Invoiced','Paid','Cancelled') | NOT NULL, INDEX, default 'Pending' |
| date_sent | date | NOT NULL |
| date_returned | date | NULL |
| notes | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |
| updated_at | datetime | NULL, on update current_timestamp() |

#### calendar_events
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| title | varchar(255) | NOT NULL |
| description | text | NULL |
| start_time | datetime | NOT NULL, INDEX |
| end_time | datetime | NULL |
| all_day | tinyint(1) | NOT NULL, default 0 |
| event_type | varchar(50) | NOT NULL, default 'general' |
| color | varchar(7) | NULL, default '#007bff' |
| related_table | varchar(50) | NULL, INDEX |
| related_id | int(10) unsigned | NULL |
| priority | enum('low','medium','high') | NOT NULL, default 'medium' |
| created_by_user_id | int(10) unsigned | FK → users.id |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

#### jobs (legacy table, mostly unused)
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_name | varchar(255) | NOT NULL |
| description | text | NULL |
| status | enum('pending','completed','cancelled') | NOT NULL |
| assigned_to | int(10) unsigned | FK → users.id |
| created_at | datetime | NOT NULL |
| updated_at | datetime | NULL |
| deleted_at | datetime | NULL |

This table exists alongside `job_cards`. The FK `jobs.assigned_to -> users.id` exists in information_schema, but **the codebase now uses `job_cards` exclusively**. `jobs` is a legacy table. Do not write to it.

### FK Relationships

| Source | Target | Constraint |
|--------|--------|------------|
| next_of_kin.user_id | users.id | fk_nok_user |
| vehicles.owner_id | customers.id | fk_vehicles_owner |
| job_cards.customer_id | customers.id | fk_jc_customer |
| job_cards.vehicle_id | vehicles.id | fk_jc_vehicle |
| job_cards.assigned_service_advisor_id | users.id | fk_jc_advisor |
| job_card_photos.job_card_id | job_cards.id | fk_jcp_job_card |
| job_card_parts_required.job_card_id | job_cards.id | fk_jcpr_job_card |
| job_card_parts_required.inventory_id | inventory.id | fk_jcpr_inventory |
| job_card_labor_tasks.job_card_id | job_cards.id | fk_jclt_job_card |
| sublets.job_card_id | job_cards.id | fk_sublets_job_card |
| sublets.sublet_provider_id | suppliers.id | fk_sublets_provider |
| calendar_events.created_by_user_id | users.id | fk_ce_created_by |
| jobs.assigned_to | users.id | fk_jobs_assigned (legacy) |

### Known Mismatches

- `app/Config/Database.php` says database is `flowdesk` — the actual database is `flowdesk` (set in `.env`).
- `jobs` table exists but is **legacy/unused** — all current code uses `job_cards`.
- `vehicles.registration_number` is the actual column name, but some old code or comments may reference `vehicle_number` — always use `registration_number`.
- `users.name` is a STORED GENERATED column (concatenation of `first_name` + `last_name`), but some code references `first_name`/`last_name` directly.

## 4. MODULE STATUS

### Auth (Login/Logout)
- **Status:** Complete
- **What works:** Login with company_id + password, session creation, role-based redirect, logout/destroy.
- **Broken/missing:** Customer login redirect exists in code but there's no customer dashboard route implemented (only `customer/` group exists in routes; `restrictTo('customer', 'customer_dashboard')` loads a view but the route works).

### Dashboard
- **Status:** Complete
- **What works:** Admin dashboard with summary cards (users, vehicles, jobs in progress, pending LPOs), job status doughnut chart, revenue line chart (mock data), recent activity feed, quick action buttons. All view variables are passed correctly.
- **Broken/missing:** Revenue chart uses hardcoded mock data; critical alerts are static HTML; activity filter dropdown JS works but is duplicated.

### Users
- **Status:** Complete (with caveats)
- **What works:** CRUD (index, add via multi-step wizard, edit, soft-delete, bulk delete via AJAX), DataTable listing with search, user details modal, role-based filters, `fetchUsers` JSON endpoint. Edit view exists at `admin/edit_user.php`.
- **Broken/missing:** `UsersController::__construct()` sets `$this->session` but `index()` and `add()` use `session()->` directly (inconsistent); the `deleteMultiple()` method exists but route uses `/users/delete-multiple` (not under admin prefix); `UsersController::submit()` method referenced in routes but not defined in the controller.

### Vehicles
- **Status:** Partial
- **What works:** DataTable listing, AJAX fetch, add/store, edit (view loads), delete, details JSON endpoint, `get()` single-vehicle endpoint. Add and edit views created at `vehicles/add.php` and `vehicles/edit.php`.
- **Broken/missing:** `VehicleController::add()` and `store()` are separate (add returns view, store saves via AJAX — confusing); `VehicleController::index()` does not use `layouts/main` (no sidebar/menu); route has typo `vechicles/edit/(:num)` (line 83 of Routes.php) — note the typo "vechicles" vs "vehicles". This route would never match.

### Customers
- **Status:** Partial
- **What works:** DataTable listing with server-side processing (`load()`), details modal with vehicles and jobs, add/edit forms exist at `admin/forms/add_customer_form.php` and `admin/forms/edit_customer_form.php`, bulk delete with transaction.
- **Broken/missing:** No `store()` or `update()` routes defined for customers — forms will submit to non-existent endpoints.

### Job Intake
- **Status:** Partial
- **What works:** Search customers/vehicles, create new customers+vehicles inline, create job card with photos, validation, transaction safety, unique job number generation (`JOB-YYYYMMDD-NNN`), mechanic diagnosis view, parts/tasks assignment, inventory search. Intake form and mechanic diagnosis form views exist.
- **Broken/missing:** `mechanic_view()`, `search_parts()`, and `save_diagnosis()` exist in `JobIntake` controller but have **no routes** defined — they cannot be accessed via URL.

### Jobs (Job Cards Management)
- **Status:** Partial
- **What works:** DataTable listing (with vehicle join via AJAX), add form, edit form, update, soft delete. Add/Edit views exist at `jobs/add.php` and `jobs/edit.php`. Jobs list view exists at `admin/jobs/jobs_list.php`.
- **Broken/missing:** `create()` method is referenced in routes but does not exist in `JobsController`. `JobsController::index()` joins `vehicles` for `registration_number` but the `index()` view (`job/index.php`) displays it via DataTable's AJAX endpoint which works.

### Calendar
- **Status:** Complete
- **What works:** FullCalendar integration, fetching job events by date range (drop-offs, estimated completions, completed), custom calendar event CRUD (add event with validation), drag-drop event date update (`updateEventDate` route exists). All view variables ($users_for_notification, $loggedInUserId) are passed correctly.
- **Broken/missing:** `updateEventDate()` method is referenced in routes but does not exist in `CalendarController`; events from `calendar_events` table are fetched but `addEvent` works; no event edit/delete routes.

### Sublets
- **Status:** Complete
- **What works:** DataTable with server-side processing, server-side search, status filter, add/edit form with validation, details modal (view path fixed), single delete, bulk delete, joins with job_cards and suppliers.
- **Broken/missing:** `fetchSublets()` method referenced in routes but does NOT exist in `SubletsController`; the `load()` endpoint handles all DataTable data; `edit()` and `update()` methods referenced in routes do not exist in `SubletsController` (only `add($id)` handles both add and edit form loading; `save()` handles both create and update).

## 5. KNOWN BUGS

| ID | File | Line | Description | Severity |
|----|------|------|-------------|----------|
| BUG-010 | `app/Config/Routes.php` | 83 | Route `vechicles/edit/(:num)` — typo "vechicles" should be "vehicles" | Low |
| BUG-011 | Various | — | Two different database names: `app/Config/Database.php` says `flowdesk`, `.env` says `flowdesk`. Actual DB is `flowdesk` | Medium |
| BUG-012 | `app/Controllers/JobsController.php` | — | `create()` method referenced in route `admin/jobs/create` does not exist in controller | Critical |
| BUG-013 | `app/Controllers/CalendarController.php` | — | `updateEventDate()` referenced in route `admin/calendar/updateEventDate` does not exist | Medium |
| BUG-014 | `app/Controllers/SubletsController.php` | — | `fetchSublets()`, `edit()`, `update()` methods referenced in routes do not exist | Medium |
| BUG-019 | `app/Config/Routes.php` | 40, 74 | Route `admin/users/bulk_action` and `user/add_step1` reference `UsersController` methods that exist, but `submit()` route (line 40) references `UsersController::submit()` which does not exist in the controller | Medium |
| BUG-020 | `app/Controllers/JobIntake.php` | 337-384 | `mechanic_view()`, `search_parts()`, `save_diagnosis()` exist but have no routes defined | Medium |
| BUG-021 | `app/Config/Database.php` | 32 | `database` key says `flowdesk` but actual database is `flowdesk` | Low |

## 6. MISSING FEATURES

Features referenced in the sidebar (`app/Views/partials/sidebar.php`) but with **no controller, no routes, and no views**:

| Feature | Sidebar link | Notes |
|---------|-------------|-------|
| **Inventory** | `/admin/inventory` | No routes, no controller. Table `inventory` exists in DB and is used by JobIntake for parts lookup. |
| **Suppliers** | `/admin/suppliers` | No routes, no controller. Table `suppliers` exists and is used by Sublets module. |
| **Invoices** | `/admin/invoices` | No routes, no controller. No invoices table in DB. |
| **LPOs** | `/admin/lpos` | No routes, no controller. Quick action button on dashboard opens `/admin/lpos/add`. No LPO table in DB. |
| **Petty Cash** | `/admin/pettycash` | No routes, no controller. Quick action button opens `/admin/pettycash/add`. No petty cash table in DB. |
| **Reports** | `/admin/reports` | No routes, no controller. |
| **Settings** | `/admin/settings` | No routes, no controller. |
| **Profile** | `/admin/profile` | No routes, no controller. Link in sidebar footer. |

Features referenced in dashboard quick actions but not implemented:
- `admin/lpos/add` — New LPO button
- `admin/pettycash/add` — Add Petty Cash button

## 7. CODING CONVENTIONS

- **Controllers** extend `BaseController`. They use `\Config\Database::connect()` to get a DB instance (or `$this->db` set in `__construct()`).
- **AJAX responses** use `$this->response->setJSON($data)` or `$this->respond($data)` (via `ResponseTrait`). Some controllers use `CodeIgniter\API\ResponseTrait`.
- **Views** are loaded with `return view('path', $data)`. Views that need the sidebar/layout extend `layouts/main` via `$this->extend('layouts/main')` and use `$this->section('content')`. Standalone views (like `admin/add_user.php`) do not extend the layout.
- **Auth** is applied via route groups: `['filter' => 'auth:admin']` in `Routes.php`. Some controllers also duplicate auth checks inside methods with `if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin')`.
- **File uploads** go to `uploads/users/` for profile pictures and `public/uploads/job_card_photos/` for job card photos. Always use `$file->getRandomName()` and `$file->move()`.
- **No models** exist. All queries are raw `$db->table('table')->...` or `$db->query('SELECT ...')` directly in controllers.
- **Soft deletes** use a `deleted_at` datetime column (users, job_cards).
- **No CSRF protection** globally — CSRF filter is commented out in `app/Config/Filters.php`.

## 8. ROUTE PATTERNS

### Public routes (no auth filter)
```
GET  /                    -> Home::index
GET  /login               -> LoginController::index
POST /login/auth          -> LoginController::auth
GET  /logout              -> LoginController::logout
GET  /unauthorized        -> DashboardController::unauthorized
```

### User registration wizard (no auth)
```
GET  /user/add_step1       -> UsersController::addStep1
POST /user/add_step1       -> UsersController::add_step1
GET  /user/add_step2       -> UsersController::addStep2
POST /user/add_step2       -> UsersController::add_step2
GET  /user/add_step3       -> UsersController::addStep3
POST /user/add_step3       -> UsersController::addUserStep3
POST /user/addUserStep3    -> UsersController::addUserStep3
GET  /user/preview         -> UsersController::preview
GET  /user/saveUser        -> UsersController::saveUser
POST /save-step-data/(:num)-> UsersController::saveStepData/$1
POST /final-submit         -> UsersController::finalSubmit
GET  /user/getLastId       -> UsersController::getLastId
POST /user/submit          -> UsersController::submit
GET  /user/success         -> UsersController::success
GET  /user/failure         -> UsersController::failure
POST /admin/users/bulk_action -> UsersController::bulk_action
```

### Job Intake group (filter: auth:admin,receptionist)
```
GET  /job_intake/                     -> JobIntake::index
GET  /job_intake/search               -> JobIntake::search
POST /job_intake/create_job_card      -> JobIntake::create_job_card
GET  /job_intake/create_job_card      -> JobIntake::create_job_card
POST /job_intake/fetch_vehicle_details -> JobIntake::fetch_vehicle_details
POST /job_intake/fetch_customer_details -> JobIntake::fetch_customer_details
```

### Admin group (filter: auth:admin)
```
GET    /admin/                                 -> DashboardController::admin
GET    /admin/dashboard                        -> DashboardController::admin
GET    /admin/users                            -> UsersController::index
GET    /admin/users/add                        -> UsersController::add
GET    /admin/users/(:num)                     -> UsersController::details/$1
GET    /admin/users/edit/(:num)                -> UsersController::edit/$1
POST   /admin/users/update/(:num)              -> UsersController::update/$1
GET    /admin/users/delete/(:num)              -> UsersController::delete/$1
POST   /admin/users/bulk_action                -> UsersController::bulk_action
GET    /admin/users/fetch/(:num)               -> UsersController::details/$1
GET    /admin/users/fetch                      -> UsersController::fetchUsers
GET    /admin/vehicles                         -> VehicleController::index
GET    /admin/vehicles/fetch                   -> VehicleController::fetchVehicles
GET    /admin/vehicles/fetch/(:num)            -> VehicleController::fetchVehicles
GET    /admin/vechicles/edit/(:num)            -> VehicleController::edit/$1 (typo)
POST   /admin/vehicles/store                   -> VehicleController::store
POST   /admin/vehicles/update/(:num)           -> VehicleController::update/$1
POST   /admin/vehicles/delete/(:num)           -> VehicleController::delete/$1
POST   /admin/vehicles/add                     -> VehicleController::add
GET    /admin/vehicles/edit/(:num)             -> VehicleController::edit/$1
GET    /admin/vehicles/delete/(:num)           -> VehicleController::delete/$1
GET    /admin/vehicles/details/(:num)          -> VehicleController::details/$1
GET    /admin/jobs                             -> JobsController::index
GET    /admin/jobs/fetch                       -> JobsController::fetchJobs
GET    /admin/jobs/add                         -> JobsController::add
POST   /admin/jobs/create                      -> JobsController::create (NOT IMPLEMENTED)
GET    /admin/jobs/(:num)                      -> JobsController::details/$1
GET    /admin/jobs/edit/(:num)                 -> JobsController::edit/$1
POST   /admin/jobs/update/(:num)               -> JobsController::update/$1
GET    /admin/jobs/delete/(:num)               -> JobsController::delete/$1
POST   /admin/jobs/bulk_action                 -> JobsController::bulk_action
GET    /admin/job_intake                       -> JobIntake::index
GET    /admin/job_intake/search                -> JobIntake::search
POST   /admin/job_intake/create_job_card       -> JobIntake::create_job_card
GET    /admin/job_intake/create_job_card       -> JobIntake::create_job_card
POST   /admin/job_intake/fetch_vehicle_details -> JobIntake::fetch_vehicle_details
POST   /admin/job_intake/fetch_customer_details-> JobIntake::fetch_customer_details
GET    /admin/customers                        -> CustomersController::index
POST   /admin/customers/load                   -> CustomersController::load
GET    /admin/customers/load                   -> CustomersController::load
GET    /admin/customers/details/(:num)         -> CustomersController::details/$1
GET    /admin/customers/add                    -> CustomersController::add
GET    /admin/customers/edit/(:num)            -> CustomersController::edit/$1
POST   /admin/customers/bulk_action            -> CustomersController::bulk_action
GET    /admin/calendar                         -> CalendarController::index
GET    /admin/calendar/getEvents               -> CalendarController::getEvents
GET    /admin/calendar/addEvent                -> CalendarController::addEvent
POST   /admin/calendar/addEvent                -> CalendarController::addEvent
POST   /admin/calendar/updateEventDate         -> CalendarController::updateEventDate (NOT IMPLEMENTED)
GET    /admin/sublets                          -> SubletsController::index
GET    /admin/sublets/add                      -> SubletsController::add
POST   /admin/sublets/load                     -> SubletsController::load
GET    /admin/sublets/edit/(:num)              -> SubletsController::add/$1
POST   /admin/sublets/save                     -> SubletsController::save
GET    /admin/sublets/details/(:num)           -> SubletsController::details/$1
POST   /admin/sublets/delete/(:num)            -> SubletsController::delete/$1
POST   /admin/sublets/bulkAction               -> SubletsController::bulkAction
GET    /admin/sublets/fetch                    -> SubletsController::fetchSublets (NOT IMPLEMENTED)
GET    /admin/sublets/fetch/(:num)             -> SubletsController::fetchSublets/$1 (NOT IMPLEMENTED)
GET    /admin/sublets/(:num)                   -> SubletsController::details/$1
GET    /admin/sublets/(:num)/edit              -> SubletsController::edit/$1 (NOT IMPLEMENTED)
POST   /admin/sublets/(:num)/update            -> SubletsController::update/$1 (NOT IMPLEMENTED)
```

### Role-specific groups
```
GET /receptionist/  (filter: auth:receptionist) -> DashboardController::receptionist
GET /mechanic/      (filter: auth:mechanic)     -> DashboardController::mechanic
GET /customer/      (filter: auth:customer)     -> DashboardController::customer
```

### AJAX endpoint pattern
AJAX endpoints under `admin/` typically return JSON via `$this->response->setJSON()` or `$this->respond()`. The `fetch` pattern is used for DataTable data (`/admin/{module}/fetch`). The `load` pattern is used for DataTables with server-side processing (`/admin/customers/load`, `/admin/sublets/load`).

## 9. GOTCHAS & WARNINGS

1. **`jobs` table vs `job_cards` table**: The `jobs` table exists in the database but is **legacy/unused**. All current code operates on the `job_cards` table. Never write to the `jobs` table.

2. **UserController vs UsersController**: The correct controller is `UsersController`. In early development, some routes referenced `UserController` (without 's') — these have been fixed.

3. **Admin\CustomersController does not exist**: There is no `App\Controllers\Admin\CustomersController`. The customer routes point to `App\Controllers\CustomersController`. The old namespace `Admin\CustomersController::load` has been fixed.

4. **VehicleController field names**: The column in the `vehicles` table is `registration_number`, NOT `vehicle_number`. Some old code referenced `vehicle_number`.

5. **No CSRF globally**: The CSRF filter is commented out in `app/Config/Filters.php` (`// 'csrf'`). Do not add CSRF protection without updating all AJAX POST requests to include the CSRF token.

6. **No migrations — schema lives in database only**: The `app/Database/Migrations/` directory is empty. The schema was created manually (likely via phpMyAdmin or direct SQL). Any schema changes must be applied directly.

7. **`baseURL` must be `http://localhost/FlowDesk/`**: Set in `app/Config/App.php` line 19. Change this if deploying elsewhere.

8. **`.htaccess RewriteBase` must be `/FlowDesk/`**: Set in `.htaccess` line 12. Must match the `baseURL` path.

9. **Two database configs**: `app/Config/Database.php` has `'database' => 'flowdesk'` but `.env` has `database.default.database = flowdesk`. The `.env` value wins. The actual database is `flowdesk`.
