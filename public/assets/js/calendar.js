document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const filterSelect = document.getElementById('eventTypeFilter');
    const searchInput = document.getElementById('eventSearchInput');
    const addEventBtn = document.getElementById('addEventBtn');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    const eventCountDisplay = document.getElementById('eventCount');
    const addEventForm = document.getElementById('addEventForm');

    let calendar;
    let allEvents = [];

    function getSelectedType() {
        return filterSelect.value;
    }

    function getSearchTerm() {
        return searchInput.value.toLowerCase();
    }

    function getStartDate() {
        return startDateInput.value ? new Date(startDateInput.value) : null;
    }

    function getEndDate() {
        return endDateInput.value ? new Date(endDateInput.value) : null;
    }

    function filterEvents(event) {
        const selectedType = getSelectedType();
        const searchTerm = getSearchTerm();
        const startDate = getStartDate();
        const endDate = getEndDate();

        const matchesType = selectedType === 'all' || event.extendedProps.type === selectedType;
        const matchesSearch = event.title.toLowerCase().includes(searchTerm);
        const eventStart = new Date(event.start);
        const withinStart = !startDate || eventStart >= startDate;
        const withinEnd = !endDate || eventStart <= endDate;

        return matchesType && matchesSearch && withinStart && withinEnd;
    }

    function populateEventModal(event) {
        const props = event.extendedProps;
        const safe = (val) => val ?? 'N/A';

        document.getElementById('event_type').textContent = safe(props.type);
        document.getElementById('event_title').textContent = safe(event.title);
        document.getElementById('event_start').textContent = event.start?.toLocaleString() ?? 'N/A';
        document.getElementById('event_end').textContent = event.end?.toLocaleString() ?? 'N/A';
        document.getElementById('event_status').textContent = safe(props.status);
        document.getElementById('event_job_no').textContent = safe(props.job_no);
        document.getElementById('event_vehicle').textContent = safe(props.vehicle);
        document.getElementById('event_customer').textContent = safe(props.customer);
        document.getElementById('event_mechanic').textContent = safe(props.mechanic);
        document.getElementById('event_description').textContent = safe(props.description);
    }

    function updateEventCount(count) {
        if (eventCountDisplay) {
            eventCountDisplay.textContent = count;
        }
    }

    function updateEventDate(id, start, end) {
        fetch(`${BASE_URL}admin/calendar/updateEventDate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id, start, end })
        })
            .then(response => response.json())
            .then(result => {
                if (!result.success) throw new Error(result.message);
            })
            .catch(error => {
                console.error('Error updating event:', error);
                Swal.fire('Update Failed', 'Could not update event. Please try again.', 'error');
            });
    }

    function renderCalendar() {
        if (calendarEl) {
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                editable: true,
                selectable: true,
                dayMaxEvents: true,

                events: function (fetchInfo, successCallback, failureCallback) {
                    fetch(`${BASE_URL}admin/calendar/getEvents?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                        .then(res => res.json())
                        .then(data => {
                            allEvents = Array.isArray(data) ? data : [];
                            const filtered = allEvents.filter(filterEvents);
                            successCallback(filtered);
                            updateEventCount(filtered.length);
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            failureCallback(error);
                        });
                },

                eventDidMount: function (info) {
                    const { status, type } = info.event.extendedProps;
                    if (status) info.el.setAttribute('data-status', status);
                    if (type) info.el.setAttribute('data-type', type);
                },

                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    populateEventModal(info.event);
                    openModal('actionModal');
                },

                select: function (info) {
                    openModal('addEventModal');
                    var startInput = document.getElementById('eventStartTimeInput');
                    var endInput = document.getElementById('eventEndTimeInput');
                    if (startInput) startInput.value = info.startStr.slice(0, 16);
                    if (endInput && info.endStr) endInput.value = info.endStr.slice(0, 16);
                }
            });

            calendar.render();
        }
    }

    // Re-filter without reloading all events
    function refilterEvents() {
        const filtered = allEvents.filter(filterEvents);
        calendar.removeAllEvents();
        calendar.addEventSource(filtered);
        updateEventCount(filtered.length);
    }

    // Attach event listeners to filters
    [filterSelect, searchInput, startDateInput, endDateInput].forEach(input => {
        if (input) {
            input.addEventListener('input', refilterEvents);
        }
    });

    resetFiltersBtn?.addEventListener('click', () => {
        filterSelect.value = 'all';
        searchInput.value = '';
        startDateInput.value = '';
        endDateInput.value = '';
        refilterEvents();
    });

    if (addEventForm && addEventBtn) {
        addEventBtn.addEventListener('click', function () {
            addEventForm.reset();
            openModal('addEventModal');
        });
    }

    renderCalendar();
});
