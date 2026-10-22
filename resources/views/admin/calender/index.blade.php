@push('css')
    <style>
        .job-type-select {
            border: 1px solid #e0e0e0;
            border-radius: 13px;
            box-shadow: 12px 12px 5px #eee;
            cursor: pointer;
        }

        .select-job {
            border: 1px solid #242a62;
        }
        a.fc-event.fc-event-draggable.fc-event-resizable.fc-event-start.fc-event-end.fc-event-today.fc-event-future.fc-timegrid-event.fc-v-event.fc-event-primary {
            padding: 0px;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />


    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-calendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/admin-staff-calendar.css') }}?v=20" />
    <link rel="stylesheet" href="{{ asset('assets/css/admin-service-picker.css') }}?v=2" />


@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Calendar";
    </script>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Calendar</h4>
                <p>Schedule appointments and manage staff availability</p>
            </div>
        </div>

        <div class="card app-calendar-wrapper rounded-3 shadow-sm">
            <div class="row g-0 bc-cal-layout">
                <!-- Calendar Sidebar -->
                <div class="app-calendar-sidebar border-end" id="app-calendar-sidebar">
                    <div class="border-bottom p-6 my-sm-0 mb-4">
                        <button
                            class="btn btn-primary btn-toggle-sidebar w-100"
                           >
                            <i class="bi bi-plus-lg me-2"></i>
                            <span class="align-middle">Add Appointment</span>
                        </button>


                    </div>

                    <hr class="mb-6 mx-n4 mt-3" />
                    @if(\Illuminate\Support\Facades\Auth::user()->role === 'Admin' || \Illuminate\Support\Facades\Auth::user()->hasRole('Super-admin'))
                    <div class="mb-3 p-4">
                        <label for="staffFilter" class="form-label">Filter by Staff</label>
                        <select id="staffFilter" class="form-select">
                            <option value="">All Staff</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @elseif(\Illuminate\Support\Facades\Auth::user()->role === 'Staff')
                        <input type="hidden" value="{{\Illuminate\Support\Facades\Auth::id()}}" id="staffFilter" name="staffFilter">
                    @endif
                    <div class="px-3 pt-2">
                        <!-- inline calendar (flatpicker) -->
                        <div class="inline-calendar"></div>
                    </div>
                    <div class="px-6 pb-2 d-none">
                        <!-- Filter -->
                        <div>
                            <h5>Event Filters</h5>
                        </div>

                        <div class="form-check form-check-secondary mb-5 ms-2">
                            <input
                                class="form-check-input select-all"
                                type="checkbox"
                                id="selectAll"
                                data-value="all"
                                checked />
                            <label class="form-check-label" for="selectAll">View All</label>
                        </div>

                        <div class="app-calendar-events-filter text-heading">
                            <div class="form-check form-check-danger mb-5 ms-2">
                                <input
                                    class="form-check-input input-filter"
                                    type="checkbox"
                                    id="select-personal"
                                    data-value="personal"
                                    checked />
                                <label class="form-check-label" for="select-personal">Personal</label>
                            </div>
                            <div class="form-check mb-5 ms-2">
                                <input
                                    class="form-check-input input-filter"
                                    type="checkbox"
                                    id="select-business"
                                    data-value="business"
                                    checked />
                                <label class="form-check-label" for="select-business">Business</label>
                            </div>
                            <div class="form-check form-check-warning mb-5 ms-2">
                                <input
                                    class="form-check-input input-filter"
                                    type="checkbox"
                                    id="select-family"
                                    data-value="family"
                                    checked />
                                <label class="form-check-label" for="select-family">Family</label>
                            </div>
                            <div class="form-check form-check-success mb-5 ms-2">
                                <input
                                    class="form-check-input input-filter"
                                    type="checkbox"
                                    id="select-holiday"
                                    data-value="holiday"
                                    checked />
                                <label class="form-check-label" for="select-holiday">Holiday</label>
                            </div>
                            <div class="form-check form-check-info ms-2">
                                <input
                                    class="form-check-input input-filter"
                                    type="checkbox"
                                    id="select-etc"
                                    data-value="etc"
                                    checked />
                                <label class="form-check-label" for="select-etc">ETC</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Calendar Sidebar -->

                <!-- Calendar & Modal -->
                <div class="app-calendar-content">
                    <div class="card shadow-none border-0 h-100">
                        <div class="card-body pb-0">
                            <!-- Staff day calendar -->
                            <div id="calendar"></div>
                        </div>
                    </div>
                    <div class="app-overlay"></div>
                    <!-- FullCalendar Offcanvas -->
                    <div
                        class="offcanvas offcanvas-end event-sidebar"
                        tabindex="-1"
                        id="addEventSidebar"
                        aria-labelledby="addEventSidebarLabel">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                            <button
                                type="button"
                                class="btn-close text-reset"
                                data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>


                        <div class="offcanvas-body">
                            <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                <div class="mb-6">
                                    <label class="form-label" for="eventTitle">Title</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="eventTitle"
                                        name="eventTitle"
                                        placeholder="Event Title" />
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="eventLabel">Label</label>
                                    <select class="select2 select-event-label form-select" id="eventLabel" name="eventLabel">
                                        <option data-label="primary" value="Business" selected>Business</option>
                                        <option data-label="danger" value="Personal">Personal</option>
                                        <option data-label="warning" value="Family">Family</option>
                                        <option data-label="success" value="Holiday">Holiday</option>
                                        <option data-label="info" value="ETC">ETC</option>
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="eventStartDate">Start Date</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="eventStartDate"
                                        name="eventStartDate"
                                        placeholder="Start Date" />
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="eventEndDate">End Date</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="eventEndDate"
                                        name="eventEndDate"
                                        placeholder="End Date" />
                                </div>
                                <div class="mb-6">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input allDay-switch" id="allDaySwitch" />
                                        <label class="form-check-label" for="allDaySwitch">All Day</label>
                                    </div>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="eventURL">Event URL</label>
                                    <input
                                        type="url"
                                        class="form-control"
                                        id="eventURL"
                                        name="eventURL"
                                        placeholder="https://www.google.com" />
                                </div>
                                <div class="mb-4 select2-primary">
                                    <label class="form-label" for="eventGuests">Add Guests</label>
                                    <select
                                        class="select2 select-event-guests form-select"
                                        id="eventGuests"
                                        name="eventGuests"
                                        multiple>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->customer_id }}">{{ $customer->customer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="eventLocation">Location</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="eventLocation"
                                        name="eventLocation"
                                        placeholder="Enter Location" />
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="eventDescription">Description</label>
                                    <textarea class="form-control" name="eventDescription" id="eventDescription"></textarea>
                                </div>
                                <div class="d-flex justify-content-sm-between justify-content-start mt-6 gap-2">
                                    <div class="d-flex">
                                        <button type="submit" id="addEventBtn" class="btn btn-primary btn-add-event me-4">
                                            Add
                                        </button>
                                        <button
                                            type="reset"
                                            class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                            data-bs-dismiss="offcanvas">
                                            Cancel
                                        </button>
                                    </div>
                                    <button class="btn btn-label-danger btn-delete-event d-none">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Calendar & Modal -->
            </div>
        </div>
    </div>
    <!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addAppointmentLabel">Add Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="appointmentForm">
                        @csrf
                        <div class="row g-3">
                            <input type="hidden" name="appointment_id" id="appointment_id">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" inputmode="tel" class="form-control" id="phone" name="phone" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>

                            <div class="col-md-6">
                                <label for="servicePicker" class="form-label">Service</label>
                                <div class="bc-svc-picker" id="servicePicker">
                                    <button type="button" class="bc-svc-picker-toggle">
                                        <span class="bc-svc-picker-label">Select Service</span>
                                        <span class="bc-svc-picker-caret"></span>
                                    </button>
                                    <div class="bc-svc-picker-menu">
                                        <div class="bc-svc-picker-head">
                                            <span class="bc-svc-picker-count">0 selected</span>
                                            <span>
                                                <a href="#" data-action="all">Select all</a>
                                                <a href="#" data-action="clear">Clear</a>
                                            </span>
                                        </div>
                                        <div class="bc-svc-picker-list">
                                            @foreach($services as $service)
                                                <div class="bc-svc-item"
                                                     data-id="{{ $service->id }}"
                                                     data-duration="{{ $service->duration }}"
                                                     data-name="{{ $service->service_name }}">
                                                    <label class="bc-svc-item-label">
                                                        <input type="checkbox" class="bc-svc-check">
                                                        <span class="bc-svc-name">{{ $service->service_name }} <small>({{ $service->duration }} min)</small></span>
                                                        <span class="bc-svc-qty">
                                                            <button type="button" class="btn btn-outline-secondary bc-svc-minus">&minus;</button>
                                                            <span class="bc-svc-qty-val">1</span>
                                                            <button type="button" class="btn btn-outline-secondary bc-svc-plus">+</button>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="staff_id" class="form-label">Staff</label>
                                <select class="form-select" id="staff_id" name="staff_id" required>
                                    <option value="">Select Staff</option>
                                    {{-- options will be filled dynamically --}}
                                </select>
                            </div>


                            <div class="col-md-6">
                                <label for="appointment_date" class="form-label">Date</label>
                                <input type="datetime-local" class="form-control" id="appointment_date"  min="{{date('Y-m-d')}}" name="appointment_date" required>
                            </div>


                            <div class="col-md-12" id="staff-container">
                                <!-- Staff selects will be appended dynamically -->
                            </div>

                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancel</button>
                    <a href="#" id="calMakePaymentBtn" class="btn btn-success d-none">
                        <i class="bi bi-credit-card me-1"></i> Make Payment
                    </a>
                    <button type="submit" form="appointmentForm" class="btn btn-primary sav-appointment"><i class="bi bi-check-lg"></i> Save Appointment</button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="addBreakModal" tabindex="-1" aria-labelledby="addaddBreakModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addBreakModalLabel">Add Breaks</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="breakForm">
                        @csrf
                        <div class="row g-3">
                            <input type="hidden" name="break_type" id="break_type" value="2">
                            @if(\Illuminate\Support\Facades\Auth::user()->hasRole('Staff'))

                                <input type="hidden" value="{{\Illuminate\Support\Facades\Auth::id()}}"  id="break_staff_id" name="break_staff_id">

                            @else
                                <div class="col-md-12"  >
                                    <label for="break_staff_id" class="form-label">Staff</label>
                                    <select class="form-select" id="break_staff_id" name="break_staff_id" required>
                                        <option value="">Select Staff</option>
                                        @foreach($staffs as $staff)
                                            <option value="{{$staff->id}}">{{$staff->name}}</option>
                                        @endforeach
                                        {{-- options will be filled dynamically --}}
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <label for="break_date" class="form-label">Date</label>
                                <input type="date" id="break_date" name="break_date" class="form-control"
                                    min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-md-4 break-time-field">
                                <label for="break_start" class="form-label">Start time</label>
                                <input type="time" id="break_start" name="break_start" class="form-control" required>
                            </div>
                            <div class="col-md-4 break-time-field">
                                <label for="break_end" class="form-label">End time</label>
                                <input type="time" id="break_end" name="break_end" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="restoftheday" id="restoftheday" value="1">
                                    <label class="form-check-label" for="restoftheday">
                                        Rest of the Day?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancel</button>
                    <button type="submit" form="breakForm" class="btn btn-primary save-break"><i class="bi bi-check-lg"></i> Save Break</button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('script')


    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/admin-staff-calendar.js') }}?v=23"></script>
    <script src="{{ asset('assets/js/admin-service-picker.js') }}?v=1"></script>

    <!-- Main JS -->
    {{--    <script src="{{ asset('assets/js/main.js') }}"></script>--}}

    <!-- Page JS -->
    {{--    <script src="{{ asset('assets/js/app-calendar-events.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/app-calendar.js') }}"></script>--}}

    <script>
        /**
         * App Calendar
         */

        /**
         * ! If both start and end dates are same Full calendar will nullify the end date value.
         * ! Full calendar will end the event on a day before at 12:00:00AM thus, event won't extend to the end date.
         * ! We are getting events from a separate file named app-calendar-events.js. You can add or remove events from there.
         *
         **/

        'use strict';

        let direction = 'ltr';

        if (isRtl) {
            direction = 'rtl';
        }

        @php
            $defaultStaffAvatar = asset('assets/img/user.png');
            $staffResources = collect($staffs ?? [])->map(function ($s) use ($defaultStaffAvatar) {
                $avatar = $defaultStaffAvatar;
                if (!empty($s->profile_img)) {
                    $path = ltrim(str_replace('\\', '/', $s->profile_img), '/');
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                        $avatar = asset('storage/' . $path);
                    } elseif (file_exists(public_path('storage/' . $path))) {
                        $avatar = asset('storage/' . $path);
                    } elseif (file_exists(public_path($path))) {
                        $avatar = asset($path);
                    }
                }
                return [
                    'id' => (string) $s->id,
                    'title' => trim((string) ($s->name ?? '')) ?: 'Staff',
                    'name' => trim((string) ($s->name ?? '')) ?: 'Staff',
                    'avatar' => $avatar,
                ];
            })->values();
        @endphp
        const BC_APP_TIMEZONE = @json(config('app.timezone', 'America/Edmonton'));
        const BC_STAFF_DEFAULT_AVATAR = @json($defaultStaffAvatar);
        const BC_STAFF_RESOURCES = @json($staffResources);

        document.addEventListener('DOMContentLoaded', function () {
            (function () {
                const calendarEl = document.getElementById('calendar'),
                    appCalendarSidebar = document.querySelector('.app-calendar-sidebar'),
                    addEventSidebar = document.getElementById('addEventSidebar'),
                    appOverlay = document.querySelector('.app-overlay'),
                    calendarsColor = {
                        Business: 'primary',
                        Holiday: 'success',
                        Personal: 'danger',
                        Family: 'warning',
                        ETC: 'info'
                    },
                    offcanvasTitle = document.querySelector('.offcanvas-title'),
                    btnToggleSidebar = document.querySelector('.btn-toggle-sidebar'),
                    btnSubmit = document.querySelector('#addEventBtn'),
                    btnDeleteEvent = document.querySelector('.btn-delete-event'),
                    btnCancel = document.querySelector('.btn-cancel'),
                    eventTitle = document.querySelector('#eventTitle'),
                    eventStartDate = document.querySelector('#eventStartDate'),
                    eventEndDate = document.querySelector('#eventEndDate'),
                    eventUrl = document.querySelector('#eventURL'),
                    eventLabel = $('#eventLabel'), // ! Using jquery vars due to select2 jQuery dependency
                    eventGuests = $('#eventGuests'), // ! Using jquery vars due to select2 jQuery dependency
                    eventLocation = document.querySelector('#eventLocation'),
                    eventDescription = document.querySelector('#eventDescription'),
                    allDaySwitch = document.querySelector('.allDay-switch'),
                    selectAll = document.querySelector('.select-all'),
                    filterInput = [].slice.call(document.querySelectorAll('.input-filter')),
                    inlineCalendar = document.querySelector('.inline-calendar');

                let eventToUpdate,
                    currentEvents = [], // Assign app-calendar-events.js file events (assume events from API) to currentEvents (browser store/object) to manage and update calender events

                    inlineCalInstance;

                // Init event Offcanvas
                const bsAddEventSidebar = new bootstrap.Offcanvas(addEventSidebar);

                //! TODO: Update Event label and guest code to JS once select removes jQuery dependency
                // Event Label (select2)
                if (eventLabel.length) {
                    function renderBadges(option) {
                        if (!option.id) {
                            return option.text;
                        }
                        var $badge =
                            "<span class='badge badge-dot bg-" + $(option.element).data('label') + " me-2'> " + '</span>' + option.text;

                        return $badge;
                    }
                    eventLabel.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: eventLabel.parent(),
                        templateResult: renderBadges,
                        templateSelection: renderBadges,
                        minimumResultsForSearch: -1,
                        escapeMarkup: function (es) {
                            return es;
                        }
                    });
                }

                // Event Guests (select2)
                if (eventGuests.length) {
                    function renderGuestAvatar(option) {
                        if (!option.id) {
                            return option.text;
                        }
                        var $avatar =
                            "<div class='d-flex flex-wrap align-items-center'>" +
                            "<div class='avatar avatar-xs me-2'>" +
                            "<img src='" +
                            assetsPath +
                            'img/avatars/' +
                            $(option.element).data('avatar') +
                            "' alt='avatar' class='rounded-circle' />" +
                            '</div>' +
                            option.text +
                            '</div>';

                        return $avatar;
                    }
                    eventGuests.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: eventGuests.parent(),
                        closeOnSelect: false,
                        templateResult: renderGuestAvatar,
                        templateSelection: renderGuestAvatar,
                        escapeMarkup: function (es) {
                            return es;
                        }
                    });
                }

                // Event start (flatpicker)
                if (eventStartDate) {
                    var start = eventStartDate.flatpickr({
                        enableTime: true,
                        altFormat: 'Y-m-dTH:i:S',
                        onReady: function (selectedDates, dateStr, instance) {
                            if (instance.isMobile) {
                                instance.mobileInput.setAttribute('step', null);
                            }
                        }
                    });
                }

                // Event end (flatpicker)
                if (eventEndDate) {
                    var end = eventEndDate.flatpickr({
                        enableTime: true,
                        altFormat: 'Y-m-dTH:i:S',
                        onReady: function (selectedDates, dateStr, instance) {
                            if (instance.isMobile) {
                                instance.mobileInput.setAttribute('step', null);
                            }
                        }
                    });
                }

                // Inline sidebar calendar (flatpicker)
                if (inlineCalendar) {
                    inlineCalInstance = inlineCalendar.flatpickr({
                        monthSelectorType: 'static',
                        inline: true
                    });
                }

                function populateCalendarStaffOptions(selectedStaffId, serviceIds) {
                    var staffSelect = $('#staff_id');
                    var preferred = selectedStaffId ? String(selectedStaffId) : '';

                    function fillOptions(staffRows) {
                        staffSelect.empty().append('<option value="">Select Staff</option>');
                        (staffRows || []).forEach(function (staff) {
                            var id = String(staff.id);
                            staffSelect.append('<option value="' + id + '">' + staff.name + '</option>');
                        });

                        if (preferred && staffSelect.find('option[value="' + preferred + '"]').length === 0) {
                            var matched = (BC_STAFF_RESOURCES || []).find(function (staff) {
                                return String(staff.id) === preferred;
                            });
                            if (matched) {
                                staffSelect.append('<option value="' + matched.id + '">' + matched.name + '</option>');
                            }
                        }

                        if (preferred) {
                            staffSelect.val(preferred).trigger('change');
                        }
                    }

                    if (serviceIds && serviceIds.length) {
                        $.ajax({
                            url: "{{ route('admin.staffby.services') }}",
                            type: "GET",
                            data: { service_ids: serviceIds },
                            success: function (response) {
                                fillOptions(response.data || []);
                            },
                            error: function () {
                                fillOptions(BC_STAFF_RESOURCES || []);
                            }
                        });
                    } else {
                        fillOptions(BC_STAFF_RESOURCES || []);
                    }
                }

                // Event click function
                function eventClick(info) {
                    eventToUpdate = info.event;
                    if (eventToUpdate.url) {
                        info.jsEvent.preventDefault();
                        window.open(eventToUpdate.url, '_blank');
                    }
                    // Breaks / non-appointment events
                    if (!eventToUpdate.extendedProps || !eventToUpdate.extendedProps.appointment_id) {
                        return;
                    }
                    $('#appointment_id').val(eventToUpdate.extendedProps.appointment_id);
                    $('#name').val(eventToUpdate.extendedProps.appointment_name);
                    $('#phone').val(eventToUpdate.extendedProps.phone);
                    $('#email').val(eventToUpdate.extendedProps.email);
                    $('#appointment_date').val(eventToUpdate.extendedProps.start);
                    let serviceIds = eventToUpdate.extendedProps.service_id;

                    if (typeof serviceIds === "string") {
                        serviceIds = serviceIds.split(",");
                    }

                    let rawValues = (serviceIds || []).map(v => String(v).trim()).filter(Boolean);
                    let uniqueValues = [...new Set(rawValues)];
                    let picker = $('#servicePicker').data('picker');
                    if (rawValues.length) {
                        let countMap = {};
                        rawValues.forEach(function (id) {
                            countMap[id] = (countMap[id] || 0) + 1;
                        });
                        if (picker) picker.setItems(countMap, true);
                    } else if (picker) {
                        picker.clear(true);
                    }
                    populateCalendarStaffOptions(eventToUpdate.extendedProps.staffId, uniqueValues);

                    var appointmentId = eventToUpdate.extendedProps.appointment_id;
                    $('#calMakePaymentBtn')
                        .attr('href', "{{ route('admin.invoice.create-from-appointment', ['shop' => \App\Support\CurrentShop::slug(), 'id' => '__ID__']) }}".replace('__ID__', appointmentId))
                        .removeClass('d-none');

                    $('#addAppointmentModal').modal('show');
                    if (offcanvasTitle) {
                        offcanvasTitle.innerHTML = 'Update Event';
                    }
                    if (btnSubmit) {
                        btnSubmit.innerHTML = 'Update';
                        btnSubmit.classList.add('btn-update-event');
                        btnSubmit.classList.remove('btn-add-event');
                    }
                    if (btnDeleteEvent) btnDeleteEvent.classList.remove('d-none');

                    if (eventTitle) eventTitle.value = eventToUpdate.title;
                    if (typeof start !== 'undefined' && start && eventToUpdate.start) {
                        try { start.setDate(eventToUpdate.start, true, 'Y-m-d'); } catch (e) {}
                    }
                    if (allDaySwitch) {
                        eventToUpdate.allDay === true ? (allDaySwitch.checked = true) : (allDaySwitch.checked = false);
                    }
                    if (typeof end !== 'undefined' && end) {
                        try {
                            eventToUpdate.end !== null ?
                                end.setDate(eventToUpdate.end, true, 'Y-m-d') :
                                end.setDate(eventToUpdate.start, true, 'Y-m-d');
                        } catch (e) {}
                    }
                    if (eventLabel && eventLabel.length) {
                        eventLabel.val(eventToUpdate.extendedProps.calendar).trigger('change');
                    }
                }

                // Modify sidebar toggler
                function modifyToggler() {
                    const fcSidebarToggleButton = document.querySelector('.fc-sidebarToggle-button');
                    if (!fcSidebarToggleButton) return;
                    fcSidebarToggleButton.classList.remove('fc-button-primary');
                    fcSidebarToggleButton.classList.add('d-lg-none', 'd-inline-block', 'ps-0');
                    while (fcSidebarToggleButton.firstChild) {
                        fcSidebarToggleButton.firstChild.remove();
                    }
                    fcSidebarToggleButton.setAttribute('data-bs-toggle', 'sidebar');
                    fcSidebarToggleButton.setAttribute('data-overlay', '');
                    fcSidebarToggleButton.setAttribute('data-target', '#app-calendar-sidebar');
                    fcSidebarToggleButton.insertAdjacentHTML('beforeend', '<i class="bx bx-menu bx-lg text-heading"></i>');
                }

                // Filter events by calender
                function selectedCalendars() {
                    let selected = [],
                        filterInputChecked = [].slice.call(document.querySelectorAll('.input-filter:checked'));

                    filterInputChecked.forEach(item => {
                        selected.push(item.getAttribute('data-value'));
                    });

                    return selected;
                }

                // --------------------------------------------------------------------------------------------------
                // AXIOS: fetchEvents
                // * This will be called by fullCalendar to fetch events. Also this can be used to refetch events.
                // --------------------------------------------------------------------------------------------------
                function fetchEvents(info, successCallback, failureCallback) {
                    // alert('ds');

                    $.ajax({
                        url: "{{ route('admin.appointments.fetch') }}",
                        type: "GET",
                        data: {
                            start: info.startStr,
                            end: info.endStr,
                            staff_id: $('#staffFilter').val() // ✅ send staff filter
                        },
                        success: function (response) {
                            successCallback(response); // FullCalendar expects array of events
                        },
                        error: function (xhr) {
                            console.error("Error loading events", xhr);
                            failureCallback(xhr);
                        }
                    });
                }

                function fetchAvailability(params, successCallback, failureCallback) {
                    $.ajax({
                        url: "{{ route('admin.staff.availability') }}",
                        type: "GET",
                        data: {
                            date: params.date,
                            staff_id: $('#staffFilter').val() || ''
                        },
                        success: function (response) {
                            successCallback(response);
                        },
                        error: function () {
                            failureCallback();
                        }
                    });
                }

                $('#staffFilter').on('change', function () {
                    if (window.bcStaffCalendar) {
                        window.bcStaffCalendar.render();
                        window.bcStaffCalendar.refetchEvents();
                    } else if (typeof calendar !== 'undefined' && calendar.refetchEvents) {
                        calendar.refetchEvents();
                    }
                });

                // Init Staff Day Calendar (staff columns + live timeline)
                // ------------------------------------------------
                let calendar = new StaffDayCalendar(calendarEl, {
                    staff: BC_STAFF_RESOURCES,
                    defaultAvatar: BC_STAFF_DEFAULT_AVATAR,
                    timezone: BC_APP_TIMEZONE,
                    slotMinHour: 8,
                    slotMaxHour: 20,
                    hourHeight: 72,
                    getStaffFilter: function () {
                        return $('#staffFilter').val();
                    },
                    fetchEvents: fetchEvents,
                    fetchAvailability: fetchAvailability,
                    onUnavailableClick: function (info) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Staff Not Available',
                            text: 'This staff member is not available at the selected time.',
                            confirmButtonText: 'Ok',
                            confirmButtonColor: '#0f5132'
                        });
                    },
                    onEventClick: function (info) {
                        eventClick(info);
                    },
                    onSlotClick: function (info) {
                        $('#appointment_id').val('');
                        $('#calMakePaymentBtn').addClass('d-none').attr('href', '#');
                        $('#name').val('');
                        $('#phone').val('');
                        $('#email').val('');
                        var picker = $('#servicePicker').data('picker');
                        if (picker) picker.clear(true);
                        if (info && info.date) {
                            var d = info.date;
                            function pad(n) { return n < 10 ? '0' + n : String(n); }
                            $('#appointment_date').val(
                                d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate())
                                + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes())
                            );
                        }
                        if (info && info.staffId) {
                            populateCalendarStaffOptions(String(info.staffId), []);
                        } else {
                            populateCalendarStaffOptions('', []);
                        }
                        $('#addAppointmentModal').modal('show');
                    },
                    onBreakClick: function () {
                        $('#addBreakModal').modal('show');
                    },
                    onDateChange: function (date) {
                        if (inlineCalInstance) {
                            inlineCalInstance.setDate(date, false);
                        }
                    }
                });
                window.bcStaffCalendar = calendar;

                // Recalculate column widths on resize
                var bcResizeTimer = null;
                window.addEventListener('resize', function () {
                    clearTimeout(bcResizeTimer);
                    bcResizeTimer = setTimeout(function () {
                        if (window.bcStaffCalendar) window.bcStaffCalendar.render();
                    }, 150);
                });

                // Keep modifyToggler safe (no FullCalendar sidebar button)
                function modifyTogglerSafe() {
                    try { modifyToggler(); } catch (e) {}
                }
                modifyTogglerSafe();

                const eventForm = document.getElementById('eventForm');

                // Sidebar Toggle Btn
                if (btnToggleSidebar) {
                    btnToggleSidebar.addEventListener('click', e => {
                        $('#appointment_id').val('');
                        $('#calMakePaymentBtn').addClass('d-none').attr('href', '#');
                        var picker = $('#servicePicker').data('picker');
                        if (picker) picker.clear(true);
                        $('#addAppointmentModal').modal('show');
                    });
                }

                // Add Event
                // ------------------------------------------------
                function addEvent(eventData) {
                    currentEvents.push(eventData);
                    calendar.refetchEvents();
                }

                // Update Event
                // ------------------------------------------------
                function updateEvent(eventData) {
                    eventData.id = parseInt(eventData.id);
                    currentEvents[currentEvents.findIndex(el => el.id === eventData.id)] = eventData;
                    calendar.refetchEvents();
                }

                // Remove Event
                // ------------------------------------------------
                function removeEvent(eventId) {
                    currentEvents = currentEvents.filter(function (event) {
                        return event.id != eventId;
                    });
                    calendar.refetchEvents();
                }

                const updateEventInCalendar = () => {
                    calendar.refetchEvents();
                };

                function removeEventInCalendar() {
                    calendar.refetchEvents();
                }

                // Add new event (legacy offcanvas form — guarded)
                // ------------------------------------------------
                if (btnSubmit) btnSubmit.addEventListener('click', e => {

                    if (btnSubmit.classList.contains('btn-add-event')) {

                        let newEvent = {
                            id: (calendar.events ? calendar.events.length : 0) + 1,
                            title: eventTitle.value,
                            start: eventStartDate.value,
                            end: eventEndDate.value,
                            startStr: eventStartDate.value,
                            endStr: eventEndDate.value,
                            display: 'block',
                            extendedProps: {
                                location: eventLocation.value,
                                guests: eventGuests.val(),
                                calendar: eventLabel.val(),
                                description: eventDescription.value
                            }
                        };
                        if (eventUrl.value) {
                            newEvent.url = eventUrl.value;
                        }
                        if (allDaySwitch.checked) {
                            newEvent.allDay = true;
                        }
                        addEvent(newEvent);
                        bsAddEventSidebar.hide();

                    } else {
                        // Update event
                        // ------------------------------------------------

                        let eventData = {
                            id: eventToUpdate.id,
                            title: eventTitle.value,
                            start: eventStartDate.value,
                            end: eventEndDate.value,
                            url: eventUrl.value,
                            extendedProps: {
                                location: eventLocation.value,
                                guests: eventGuests.val(),
                                calendar: eventLabel.val(),
                                description: eventDescription.value
                            },
                            display: 'block',
                            allDay: allDaySwitch.checked ? true : false
                        };

                        updateEvent(eventData);
                        bsAddEventSidebar.hide();

                    }
                });

                // Call removeEvent function
                btnDeleteEvent.addEventListener('click', e => {
                    removeEvent(parseInt(eventToUpdate.id));
                    // eventToUpdate.remove();
                    bsAddEventSidebar.hide();
                });

                // Reset event form inputs values
                // ------------------------------------------------
                function resetValues() {
                    eventEndDate.value = '';
                    eventUrl.value = '';
                    eventStartDate.value = '';
                    eventTitle.value = '';
                    eventLocation.value = '';
                    allDaySwitch.checked = false;
                    eventGuests.val('').trigger('change');
                    eventDescription.value = '';
                }

                // When modal hides reset input values
                addEventSidebar.addEventListener('hidden.bs.offcanvas', function () {
                    resetValues();
                });

                // Hide left sidebar if the right sidebar is open
                // btnToggleSidebar.addEventListener('click', e => {
                //     if (offcanvasTitle) {
                //         offcanvasTitle.innerHTML = 'Add Event';
                //     }
                //     btnSubmit.innerHTML = 'Add';
                //     btnSubmit.classList.remove('btn-update-event');
                //     btnSubmit.classList.add('btn-add-event');
                //     btnDeleteEvent.classList.add('d-none');
                //     appCalendarSidebar.classList.remove('show');
                //     appOverlay.classList.remove('show');
                // });

                // Calender filter functionality
                // ------------------------------------------------
                if (selectAll) {
                    selectAll.addEventListener('click', e => {
                        if (e.currentTarget.checked) {
                            document.querySelectorAll('.input-filter').forEach(c => (c.checked = 1));
                        } else {
                            document.querySelectorAll('.input-filter').forEach(c => (c.checked = 0));
                        }
                        calendar.refetchEvents();
                    });
                }

                if (filterInput) {
                    filterInput.forEach(item => {
                        item.addEventListener('click', () => {
                            document.querySelectorAll('.input-filter:checked').length < document.querySelectorAll('.input-filter').length ?
                                (selectAll.checked = false) :
                                (selectAll.checked = true);
                            calendar.refetchEvents();
                        });
                    });
                }

                // Jump to date on sidebar(inline) calendar change
                if (inlineCalInstance && inlineCalInstance.config) {
                    inlineCalInstance.config.onChange.push(function (date) {
                        calendar.gotoDate(date[0]);
                        if (appCalendarSidebar) appCalendarSidebar.classList.remove('show');
                        if (appOverlay) appOverlay.classList.remove('show');
                    });
                }
            })();
        });
    </script>

    <script>
        $(document).ready(function () {
            {{--$('#service_id').on('change', function () {--}}
            {{--    let serviceIds = $(this).val(); // selected service IDs (array from select2)--}}

            {{--    if (serviceIds && serviceIds.length > 0) {--}}
            {{--        $.ajax({--}}
            {{--            url: "{{ route('admin.staffby.services') }}",--}}
            {{--            type: "GET",--}}
            {{--            data: { service_ids: serviceIds },--}}
            {{--            success: function (response) {--}}
            {{--                console.log(response);--}}

            {{--                // clear container where service+staff blocks will be appended--}}
            {{--                $('#staff-container').empty();--}}

            {{--                // loop each selected service--}}
            {{--                $.each(response.data, function (serviceName, staffs) {--}}
            {{--                    let staffOptions = '<option value="">Select Staff</option>';--}}
            {{--                    $.each(staffs, function (key, staff) {--}}
            {{--                        staffOptions += '<option value="' + staff.id + '">' + staff.name + '</option>';--}}
            {{--                    });--}}

            {{--                    // build block for each service--}}
            {{--                    let html = `--}}
            {{--            <div class="row mb-3">--}}
            {{--                <div class="col-md-6">--}}
            {{--                    <label class="form-label">Service</label>--}}
            {{--                    <input type="text" class="form-control" value="${serviceName}" readonly>--}}
            {{--                </div>--}}
            {{--                <div class="col-md-6">--}}
            {{--                    <label class="form-label">Staff for ${serviceName}</label>--}}
            {{--                    <select class="form-select" name="staff_ids[${serviceName}][]" required>--}}
            {{--                        ${staffOptions}--}}
            {{--                    </select>--}}
            {{--                </div>--}}
            {{--            </div>--}}
            {{--        `;--}}

            {{--                    $('#staff-container').append(html);--}}
            {{--                });--}}
            {{--            }--}}
            {{--        });--}}
            {{--    } else {--}}
            {{--        $('#staff-container').empty();--}}
            {{--    }--}}
            {{--});--}}

            $('#servicePicker').bcServicePicker({
                onChange: function (serviceIds, state) {
                    let selectedStaffId = $('#staff_id').val() || '';

                    if (serviceIds.length > 0) {
                        $.ajax({
                            url: "{{ route('admin.staffby.services') }}",
                            type: "GET",
                            data: { service_ids: serviceIds },
                            success: function (response) {
                                $('#staff_id').empty();
                                $('#staff_id').append('<option value="">Select Staff</option>');

                                $.each(response.data, function (key, staff) {
                                    $('#staff_id').append('<option value="' + staff.id + '">' + staff.name + '</option>');
                                });
                                if (selectedStaffId && $('#staff_id option[value="' + selectedStaffId + '"]').length === 0) {
                                    let matched = (BC_STAFF_RESOURCES || []).find(function (staff) {
                                        return String(staff.id) === String(selectedStaffId);
                                    });
                                    if (matched) {
                                        $('#staff_id').append('<option value="' + matched.id + '">' + matched.name + '</option>');
                                    }
                                }
                                if (selectedStaffId) {
                                    $('#staff_id').val(String(selectedStaffId)).trigger('change');
                                }
                            }
                        });
                    } else {
                        $('#staff_id').empty().append('<option value="">Select Staff</option>');
                        (BC_STAFF_RESOURCES || []).forEach(function (s) {
                            $('#staff_id').append('<option value="' + s.id + '">' + s.name + '</option>');
                        });
                        if (selectedStaffId) {
                            $('#staff_id').val(String(selectedStaffId)).trigger('change');
                        }
                    }
                }
            });


            $('#appointmentForm').on('submit', function (e) {
                e.preventDefault();
                let totalDuration = 0;
                let pickerState = (($('#servicePicker').data('picker')) || { getState: function () { return {}; } }).getState() || {};
                Object.keys(pickerState).forEach(function (sid) {
                    let $option = $('#servicePicker').find('.bc-svc-item[data-id="' + sid + '"]');
                    let duration = parseInt($option.data('duration')) || 0;
                    totalDuration += duration * (pickerState[sid].qty || 1);
                });

                if ($("#total_duration").length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'total_duration',
                        name: 'total_duration',
                        value: totalDuration
                    }).appendTo('#appointmentForm');
                } else {
                    $("#total_duration").val(totalDuration);
                }

                var oldhtml =$('.sav-appointment').html();
                $.ajax({
                    url: "{{ route('admin.calender.book.store') }}", // Laravel route
                    type: "POST",
                    data: $(this).serialize(), // Serialize form data
                    beforeSend: function () {
                        $('.sav-appointment').prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                     `);
                        // Optional: Disable submit button / show loader
                    },
                    success: function (response) {
                        $('#addAppointmentModal').modal('hide');
                        $('.sav-appointment').prop('disabled', false).html(oldhtml);
                        if (response.status==200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.msg,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire({
                                icon: 'Warning',
                                title: 'Warning',
                                text: response.msg,
                                showConfirmButton: true,
                                // timer: 2000/
                            });
                            $('.sav-appointment').prop('disabled', false).html(oldhtml);
                            // setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            msg="";
                            $.each(errors, function (key, value) {
                                msg+=value[0];
                            });

                            $('.sav-appointment').prop('disabled', false).html(oldhtml);
                            Swal.fire({
                                icon: 'Warning',
                                title: 'Warning',
                                text: msg,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'Warning',
                                title: 'Warning',
                                text: "Something went to wrong",
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    }
                });
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#restoftheday').on('change', function () {
                const allDay = this.checked;
                $('.break-time-field input').prop('disabled', allDay).prop('required', !allDay);
            });

            $('#addBreakModal').on('show.bs.modal', function () {
                $('#breakForm')[0].reset();
                $('#restoftheday').trigger('change');
            });

            $('#breakForm').on('submit', function (e) {
                e.preventDefault();
                var oldhtml = $('.save-break').html();

                $.ajax({
                    url: "{{ route('admin.calender.breakFormSubmit') }}", // Laravel route
                    type: "POST",
                    data: $(this).serialize(),
                    beforeSend: function () {

                        $('.save-break').prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                     `);
                        // Optional: Disable submit button / show loader
                    },
                    success: function (response) {
                        $('#addBreakModal').modal('hide');
                        if (response.status==200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.msg,
                                showConfirmButton: false,
                                timer: 2000
                            });

                            $('.save-break').prop('disabled', false).html(oldhtml);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire({
                                icon: 'Warning',
                                title: 'Warning',
                                text: response.msg,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            $('.save-break').prop('disabled', false).html(oldhtml);
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            msg="";
                            $.each(errors, function (key, value) {
                                msg+=value[0];
                            });

                            Swal.fire({
                                icon: 'Warning',
                                title: 'Warning',
                                text: msg,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            $('.save-break').prop('disabled', false).html(oldhtml);
                        } else {

                            $('.save-break').prop('disabled', false).html(oldhtml);
                            Swal.fire({
                                icon: 'Warning',
                                title: 'Warning',
                                text: "Something Went to Wrong",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            // toastr.error("Server error, please try again.");
                        }
                    }
                });
            });


        });
    </script>


@endpush
