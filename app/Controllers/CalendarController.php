<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CalendarEventModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;

class CalendarController extends BaseController
{
    use ResponseTrait;

    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
    }

    /**
     * Displays the main calendar view.
     */
    public function index()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'You do not have permission to access this page.');
        }

        $users_for_notification = $this->db->table('users')->where('deleted_at', null)->get()->getResultArray();
        $loggedInUserId = $this->session->get('user_id');

        // Upcoming custom events
        $calendarEventModel = new CalendarEventModel();
        $upcomingEvents = $calendarEventModel->getUpcoming(5);

        // Active jobs (non-terminal statuses)
        $activeStatuses = ['Awaiting Assignment', 'Awaiting Diagnosis', 'Diagnosis Complete', 'Approved', 'Quote Sent', 'In Progress', 'Awaiting Parts', 'Quality Check', 'Ready for Invoice', 'On Hold', 'Rework'];
        $upcomingJobs = $this->db->table('job_cards')
            ->select('job_cards.job_no, job_cards.date_in, job_cards.time_in, job_cards.end_date, job_cards.job_status, vehicles.registration_number, customers.name as customer_name')
            ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
            ->join('customers', 'customers.id = job_cards.customer_id', 'left')
            ->whereIn('job_cards.job_status', $activeStatuses)
            ->orderBy('job_cards.date_in', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Today's event counts
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');

        $todayEventsCount = $this->db->table('calendar_events')
            ->where('start_time >=', $todayStart)
            ->where('start_time <=', $todayEnd)
            ->countAllResults();

        $todayJobsCount = $this->db->table('job_cards')
            ->where('date_in', date('Y-m-d'))
            ->countAllResults();

        $activeJobsCount = $this->db->table('job_cards')
            ->whereIn('job_status', $activeStatuses)
            ->countAllResults();

        return view('calendar/calendar', [
            'users_for_notification' => $users_for_notification,
            'loggedInUserId' => $loggedInUserId,
            'upcomingEvents' => $upcomingEvents,
            'upcomingJobs' => $upcomingJobs,
            'todayEventsCount' => $todayEventsCount,
            'todayJobsCount' => $todayJobsCount,
            'activeJobsCount' => $activeJobsCount,
        ]);
    }

    /**
     * AJAX endpoint to fetch events for FullCalendar.
     * Events include job drop-offs, estimated completions, etc.
     *
     * FullCalendar sends 'start' and 'end' parameters (ISO 8601 strings).
     */
    public function getEvents()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->failUnauthorized('Unauthorized access to calendar events.');
        }

        $request = $this->request;
        $start_str = $request->getGet('start');
        $end_str = $request->getGet('end');

        if (empty($start_str) || empty($end_str)) {
            $start_str = date('Y-m-d', strtotime('-1 month'));
            $end_str = date('Y-m-d', strtotime('+2 months'));
        }

        $events = [];

        try {
            $jobsBuilder = $this->db->table('job_cards')
                ->select('
                    job_cards.id,
                    job_cards.job_no,
                    job_cards.diagnosis,
                    job_cards.job_status,
                    job_cards.date_in,
                    job_cards.time_in,
                    job_cards.end_date,
                    job_cards.job_summary,
                    vehicles.registration_number,
                    customers.name as customer_name
                ')
                ->join('vehicles', 'vehicles.id = job_cards.vehicle_id', 'left')
                ->join('customers', 'customers.id = job_cards.customer_id', 'left')
                ->groupStart()
                ->where('job_cards.date_in >=', $start_str)
                ->where('job_cards.date_in <=', $end_str)
                ->groupEnd()
                ->orGroupStart()
                ->where('job_cards.end_date >=', $start_str)
                ->where('job_cards.end_date <=', $end_str)
                ->groupEnd();
            $jobs = $jobsBuilder->get()->getResultArray();

            foreach ($jobs as $job) {
                // Event for Job Drop-off
                if (!empty($job['date_in'])) {
                    $startDateTime = $job['date_in'];
                    if (!empty($job['time_in'])) {
                        $startDateTime .= 'T' . $job['time_in']; // Combine date and time for full datetime
                    }

                    $events[] = [
                        'id'    => 'job-in-' . $job['id'],
                        'title' => 'Drop-off: ' . ($job['registration_number'] ?: 'N/A'),
                        'start' => $startDateTime,
                        'allDay' => empty($job['time_in']), // Set allDay if no specific time
                        'color' => '#007bff', // Primary blue for drop-offs
                        'extendedProps' => [
                            'type' => 'Job Drop-off',
                            'job_no' => $job['job_no'],
                            'status' => $job['job_status'],
                            'diagnosis' => $job['diagnosis'],
                            'vehicle' => $job['registration_number'],
                            'customer' => $job['customer_name'],
                            // Mechanic details will be fetched in the modal via job_details endpoint
                            'mechanic' => 'Loading...', // Placeholder
                            'description' => $job['job_summary'] ?: $job['diagnosis'],
                            'job_card_id' => $job['id'], // Pass job ID for modal fetching
                        ]
                    ];
                }

                // Event for Estimated Completion Date
                if (!empty($job['end_date']) && $job['job_status'] !== 'Completed' && $job['job_status'] !== 'Cancelled') {
                    $events[] = [
                        'id'    => 'job-est-' . $job['id'],
                        'title' => 'Est. Complete: ' . ($job['registration_number'] ?: 'N/A'),
                        'start' => $job['end_date'],
                        'allDay' => true, // Assuming estimated completion is usually a whole day
                        'color' => '#ffc107', // Warning yellow for estimated completion
                        'extendedProps' => [
                            'type' => 'Estimated Completion',
                            'job_no' => $job['job_no'],
                            'status' => $job['job_status'],
                            'diagnosis' => $job['diagnosis'],
                            'vehicle' => $job['registration_number'],
                            'customer' => $job['customer_name'],
                            // Mechanic details will be fetched in the modal via job_details endpoint
                            'mechanic' => 'Loading...', // Placeholder
                            'description' => 'Estimated completion for: ' . ($job['job_summary'] ?: $job['diagnosis']),
                            'job_card_id' => $job['id'], // Pass job ID for modal fetching
                        ]
                    ];
                }

                // Event for Completed Jobs (optional, to show recent completions)
                if ($job['job_status'] === 'Completed' && !empty($job['end_date'])) {
                    $events[] = [
                        'id'    => 'job-comp-' . $job['id'],
                        'title' => 'Completed: ' . ($job['registration_number'] ?: 'N/A'),
                        'start' => $job['end_date'], // Or actual_completion_date if available
                        'allDay' => true,
                        'color' => '#28a745', // Success green
                        'extendedProps' => [
                            'type' => 'Job Completed',
                            'job_no' => $job['job_no'],
                            'status' => $job['job_status'],
                            'diagnosis' => $job['diagnosis'],
                            'vehicle' => $job['registration_number'],
                            'customer' => $job['customer_name'],
                            // Mechanic details will be fetched in the modal via job_details endpoint
                            'mechanic' => 'Loading...', // Placeholder
                            'description' => 'Job successfully completed: ' . ($job['job_summary'] ?: $job['diagnosis']),
                            'job_card_id' => $job['id'], // Pass job ID for modal fetching
                        ]
                    ];
                }
            }

            // --- Fetch events from `calendar_events` table (if implemented) ---
            $calendarEvents = $this->db->table('calendar_events')
                ->where('start_time >=', $start_str)
                ->where('start_time <=', $end_str)
                ->get()
                ->getResultArray();

            foreach ($calendarEvents as $calEvent) {
                $events[] = [
                    'id'    => 'cal-' . $calEvent['id'], // Prefix to avoid ID collision
                    'title' => $calEvent['title'],
                    'start' => $calEvent['start_time'],
                    'end'   => $calEvent['end_time'],
                    'allDay' => (bool)$calEvent['all_day'],
                    'color' => $calEvent['color'],
                    'extendedProps' => [
                        'type' => $calEvent['event_type'],
                        'description' => $calEvent['description'],
                        'related_table' => $calEvent['related_table'],
                        'related_id' => $calEvent['related_id'],
                        // Other relevant fields for the modal display
                        'job_no' => ($calEvent['related_table'] === 'job_cards' && !empty($calEvent['related_id'])) ? 'Job ID: ' . $calEvent['related_id'] : 'N/A', // Example conditional data
                    ]
                ];
            }


            return $this->respond($events);
        } catch (DatabaseException $e) {
            // Log the specific database error for debugging
            log_message('error', 'Database error fetching calendar events: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not retrieve calendar events.');
        } catch (Exception $e) {
            log_message('error', 'Error fetching calendar events: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred while fetching calendar events.');
        }
    }

    public function updateEventDate()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->failUnauthorized('Unauthorized');
        }

        $input = $this->request->getJSON(true);
        if (empty($input['id']) || empty($input['start'])) {
            return $this->failValidationErrors('Missing required fields: id, start');
        }

        $id = $input['id'];
        $start = $input['start'];
        $end = $input['end'] ?? null;

        $db = \Config\Database::connect();

        try {
            // Calendar event (cal-{id})
            if (strpos($id, 'cal-') === 0) {
                $eventId = (int) substr($id, 4);
                $updateData = ['start_time' => $start];
                if ($end !== null) {
                    $updateData['end_time'] = $end;
                }
                $db->table('calendar_events')->update($updateData, ['id' => $eventId]);
                if ($db->affectedRows() === 0) {
                    return $this->failNotFound('Calendar event not found');
                }
                log_activity('event_date_updated', 'calendar_event', $eventId, "Event date updated");
                return $this->respond(['success' => true, 'message' => 'Event date updated']);
            }

            // Job event (job-in-{id}, job-est-{id}, job-comp-{id})
            if (strpos($id, 'job-in-') === 0) {
                $jobId = (int) substr($id, 7);
                $dateOnly = substr($start, 0, 10);
                $timeOnly = null;
                if (strpos($start, 'T') !== false) {
                    $parts = explode('T', $start);
                    $dateOnly = $parts[0];
                    $timeOnly = substr($parts[1], 0, 8);
                }
                $updateData = ['date_in' => $dateOnly];
                if ($timeOnly !== null) {
                    $updateData['time_in'] = $timeOnly;
                }
                $db->table('job_cards')->update($updateData, ['id' => $jobId]);
                if ($db->affectedRows() === 0) {
                    return $this->failNotFound('Job card not found');
                }
                log_activity('job_date_updated', 'job_card', $jobId, "Drop-off date updated via calendar");
                return $this->respond(['success' => true, 'message' => 'Job drop-off date updated']);
            }

            if (strpos($id, 'job-est-') === 0 || strpos($id, 'job-comp-') === 0) {
                $prefix = strpos($id, 'job-est-') === 0 ? 'job-est-' : 'job-comp-';
                $jobId = (int) substr($id, strlen($prefix));
                $dateOnly = substr($start, 0, 10);
                $db->table('job_cards')->update(['end_date' => $dateOnly], ['id' => $jobId]);
                if ($db->affectedRows() === 0) {
                    return $this->failNotFound('Job card not found');
                }
                $eventType = $prefix === 'job-est-' ? 'Estimated completion' : 'Completion';
                log_activity('job_date_updated', 'job_card', $jobId, "{$eventType} date updated via calendar");
                return $this->respond(['success' => true, 'message' => 'Job date updated']);
            }

            return $this->failNotFound('Unknown event type');
        } catch (DatabaseException $e) {
            log_message('error', 'Database error updating event date: ' . $e->getMessage());
            return $this->failServerError('Database error updating event date');
        } catch (Exception $e) {
            log_message('error', 'Error updating event date: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred');
        }
    }

    public function addEvent()
    {
        if (!$this->session->get('isLoggedIn') || ($this->session->get('role') !== 'admin' && $this->session->get('role') !== 'receptionist')) {
            return $this->failForbidden('Forbidden: Insufficient permissions to add calendar events.');
        }

        $input = $this->request->getPost();

        // Input validation rules
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'start_time' => 'required|valid_date',
            'end_time' => 'permit_empty|valid_date', // Permit empty if optional
            'all_day' => 'permit_empty|in_list[0,1]',
            'event_type' => 'permit_empty|max_length[50]',
            'color' => 'permit_empty|regex_match[/^#[0-9a-fA-F]{6}$/]',
            'description' => 'permit_empty',
            // 'related_table' => 'permit_empty|max_length[50]',
            // 'related_id' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            $data = [
                'title'         => $input['title'],
                'description'   => $input['description'] ?? null,
                'start_time'    => $input['start_time'],
                'end_time'      => $input['end_time'] ?? null,
                'all_day'       => $input['all_day'] ?? 0,
                'event_type'    => $input['event_type'] ?? 'general',
                'color'         => $input['color'] ?? '#007bff',
                // 'related_table' => $input['related_table'] ?? null,
                // 'related_id'    => $input['related_id'] ?? null,
                'created_by_user_id' => $this->session->get('user_id'), // Get logged-in user ID
            ];

            $this->db->table('calendar_events')->insert($data);
            $newEventId = $this->db->insertID();

            if ($newEventId) {
                return $this->respondCreated(['status' => 'success', 'message' => 'Event added successfully!', 'id' => $newEventId]);
            } else {
                return $this->failServerError('Failed to insert event into database.');
            }
        } catch (DatabaseException $e) {
            log_message('error', 'Database error adding calendar event: ' . $e->getMessage());
            return $this->failServerError('Database error: Could not add calendar event.');
        } catch (Exception $e) {
            log_message('error', 'Error adding calendar event: ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred while adding the event.');
        }
    }
}
