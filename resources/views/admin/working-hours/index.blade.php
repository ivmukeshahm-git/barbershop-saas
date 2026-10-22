@push('css')
    <style>
        .week-schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 32px;
            margin: 18px 12px;
        }

        .day-card {
            background: #fff;
            border-radius: 11px;
            box-shadow: 0 2px 14px rgba(30, 30, 30, 0.07);
            padding: 16px 18px 18px 18px;
            min-height: 138px;
            display: flex;
            flex-direction: column;
            border: 1px solid #dddddd;
        }

        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .day-name {
            font-weight: 600;
            font-size: 1.18em;
            color: #232323;
        }

        .add-btn {
            display: inline-block;
            background: #f7f7f7;
            border-radius: 11px;
            padding: 6px 18px;
            color: #232323;
            font-weight: 500;
            font-size: 1em;
            border: none;
            box-shadow: 0 2px 7px rgba(40, 40, 40, 0.05);
            cursor: pointer;
            transition: background 0.17s;
        }

        .add-btn:hover {
            background: #eaeaea;
        }

        .total-hours {
            font-size: 1.04em;
            color: #656565;
            margin-bottom: 5px;
        }

        .hours-list {
            margin-top: 9px;
        }

        .hours-row {
            display: flex;
            align-items: center;
            background: #fafafa;
            border-radius: 10px;
            padding: 11px 13px 11px 14px;
            margin-bottom: 9px;
            font-size: 1.08em;
            box-shadow: 0 1px 5px rgba(220, 220, 220, 0.05);
            border: 1px solid #eee;
        }

        .hour-icon {
            color: #232323;
            font-size: 1.14em;
            margin-right: 13px;
        }

        .hour-range {
            flex: 1;
            color: #212121;
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 9px;
            cursor: pointer;
            font-size: 1.13em;
            background: none;
            border: none;
            padding: 2px 7px;
            border-radius: 7px;
            transition: background 0.18s;
        }

        .edit-icon {
            color: #212121;
        }

        .delete-icon {
            color: #dc3b36;
        }

        .icon-btn:hover {
            background: #ededed;
        }

        .day-off {
            color: #aaa;
            font-size: 1.07em;
            padding: 19px 2px;
            text-align: center;
            letter-spacing: 0.02em;
        }
    </style>
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(34, 34, 34, 0.10);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-dialog.modal-timeslot {
            background: #fff;
            border-radius: 18px;
            max-width: 420px;
            width: 96vw;
            box-shadow: 0 8px 28px rgba(44, 44, 44, 0.13);
            padding: 28px 26px 24px 26px;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            animation: modalpop 0.23s cubic-bezier(.22, .95, .57, .99);
        }

        @keyframes modalpop {
            from {
                opacity: 0;
                transform: scale(.98);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.26em;
            letter-spacing: 0.01em;
        }

        .modal-close {
            border: none;
            background: transparent;
            font-size: 1.48em;
            color: #232323;
            cursor: pointer;
            padding: 2px 8px;
            margin: -6px -11px 0 0;
        }

        .modal-desc {
            font-size: 1.07em;
            margin: 11px 0 16px 0;
            color: #323232;
        }

        .section-label {
            font-weight: 500;
            font-size: 1em;
            margin-bottom: 7px;
            display: block;
            color: #252525;
        }

        .quick-select-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 5px;
        }

        .quick-slot-btn {
            background: #fff;
            border: 2px solid #222;
            border-radius: 10px;
            padding: 10px 13px 6px 13px;
            font-size: 1.07em;
            text-align: left;
            transition: background 0.18s, border 0.16s;
            cursor: pointer;
            font-family: inherit;
            box-sizing: border-box;
            font-weight: 500;
        }

        .quick-slot-btn:hover,
        .quick-slot-btn:focus {
            background: #f5f5f5;
            border: 2px solid #2d2d2d;
        }

        .quick-slot-btn small {
            color: #747474;
            font-size: .97em;
        }

        .custom-time-row {
            display: flex;
            gap: 18px;
            margin-top: 7px;
        }

        .form-group {
            flex: 1;
            min-width: 0;
        }

        .form-group label {
            font-size: .99em;
            font-weight: 500;
            margin-bottom: 4px;
            display: block;
            color: #222;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
            margin-top: 2px;
        }

        .time-input {
            width: 100%;
            border: 1.5px solid #ececec;
            padding: 9px 15px 9px 15px;
            border-radius: 9px;
            font-size: 1.07em;
            background: #f9f9f9;
            outline: none;
            box-sizing: border-box;
            transition: border 0.14s;
        }

        .time-input:focus {
            border-color: #72beff;
        }

        .time-icon {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.19em;
            color: #bbbbbb;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            margin-top: 29px;
        }

        .action-btn {
            padding: 11px 26px;
            font-size: 1.10em;
            border-radius: 9px;
            border: 2px solid #232323;
            font-weight: 600;
            background: #fff;
            color: #232323;
            transition: background 0.18s, color 0.15s;
            cursor: pointer;
        }

        .btn-add {
            background: #232323;
            color: #fff;
            border-color: #232323;
        }

        .btn-add:hover {
            background: #414141;
        }

        .btn-cancel:hover {
            background: #eaeaea;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "WorkingHours";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Working Hours</h4>
                <p>Manage your availability and breaks for appointments</p>
            </div>
        </div>

        <div class="card rounded-3 shadow-sm">
            <div class="card-body table-responsive p-4">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="week-schedule-grid">
                            <!-- Repeat for each day -->
                            @foreach ($daysWithBreaks as $day)
                                <div class="day-card">
                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                        <div>
                                            <span class="day-name">{{ $day['day_name'] }}</span>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="add-btn mb-2" onclick="AddTime('{{ $day['slot_id'] }}')">+
                                                Add</span>
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="days[{{ $day['day_name'] }}][is_day_off]"
                                                    value="0" />
                                                <input class="form-check-input day-off-switch" type="checkbox"
                                                    id="dayOff{{ $day['slot_id'] }}"
                                                    name="days[{{ $day['day_name'] }}][is_day_off]"
                                                    value="{{ $day['slot_id'] }}" data-day="{{ $day['day_name'] }}"
                                                    {{ $day['is_day_off'] ? 'checked' : '' }} />
                                                <label class="form-check-label ms-2" for="dayOff{{ $day['slot_id'] }}">Day
                                                    Off</label>
                                            </div>
                                        </div>
                                    </div>



                                    @if ($day['is_day_off'] == 0)
                                        <div class="hours-list">
                                            @if($day['breaks']->isNotEmpty())
                                                @php
                                                    $totalMinutes = 0;
                                                    foreach ($day['breaks'] as $break) {
                                                        $start = \Carbon\Carbon::parse($break->break_start);
                                                        $end = \Carbon\Carbon::parse($break->break_end);
                                                        $totalMinutes += $end->diffInMinutes($start);
                                                    }
                                                    $totalHours = round($totalMinutes / 60, 2);
                                                @endphp
                                                
                                                <div class="total-hours mb-2">
                                                    <strong>Total Working Hours:</strong> {{ $totalHours }} hours
                                                </div>
                                                
                                                @foreach ($day['breaks'] as $break)
                                                    <div class="hours-row">
                                                        <span class="hour-icon">🕐</span>
                                                        <span class="hour-range">
                                                            {{ \Carbon\Carbon::parse($break->break_start)->format('g:i A') }} –
                                                            {{ \Carbon\Carbon::parse($break->break_end)->format('g:i A') }}
                                                        </span>
                                                        <span class="icon-btn">
                                                            <span class="edit-icon"
                                                                onclick="EditWorkTime('{{ $break->id }}','{{ $break->break_start }}','{{ $break->break_end }}')">&#9998;</span>
                                                        </span>
                                                        <span class="icon-btn" onclick="DeleteWorkTime({{ $break->id }})">
                                                            <span class="delete-icon">&#128465;</span>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <h5 style="padding: 20px;text-align: center;background: #eee;font-weight: 600;">
                                                    No Working Hours Set</h5>
                                            @endif
                                        </div>
                                    @else
                                        <h5 style="padding: 20px;text-align: center;background: #eee;font-weight: 600;">
                                            No Hours Set (Day Off)</h5>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <style>
        .quick-slot-btn {
            padding: 10px 15px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        .quick-slot-btn.active {
            /* background: #007bff; */
            /* color: #fff; */
            border: 2px solid #007bff;
        }
    </style>

    <div class="modal fade" id="StaffTimeSlotModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form id="add-working-hours" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Set Working Hours / Add Break</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-desc">
                        Configure your schedule for <strong id="selectedDayName">Monday</strong>
                    </div>

                    <div class="mb-4">
                        <div class="section-label">Quick select:</div>
                        <div class="quick-select-list">
                            <button type="button" class="quick-slot-btn" data-start="09:00" data-end="13:00">
                                Morning (9:00 AM - 1:00 PM)<br><small>Working Hours</small>
                            </button>
                            <button type="button" class="quick-slot-btn" data-start="14:00" data-end="18:00">
                                Evening (2:00 PM - 6:00 PM)<br><small>Working Hours</small>
                            </button>
                            <button type="button" class="quick-slot-btn" data-start="09:00" data-end="20:00">
                                Full Day (9:00 AM - 8:00 PM)<br><small>Working Hours</small>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="section-label">Custom time:</div>
                        <div class="custom-time-row">
                            <div class="form-group">
                                <label>Start Time</label>
                                <input type="time" id="start_time" name="start_time" class="form-control"
                                    value="09:00" />
                            </div>
                            <div class="form-group">
                                <label>End Time</label>
                                <input type="time" id="end_time" name="end_time" class="form-control"
                                    value="17:00" />
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="day_id" name="day_id">

                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Close
                    </button>

                    <input type="hidden" id="_method" name="_method" value="POST">
                    <input type="hidden" id="flag" name="flag" value="A">
                    <input type="hidden" id="row_id" name="row_id">
                    <button type="submit" class="btn btn-primary" id="UpdateBtn">
                        <i class="bi bi-check-lg"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- / Content -->
@endsection

@push('script')
    <script>
        document.querySelectorAll('.day-off-switch').forEach(function(switchEl) {
            switchEl.addEventListener('change', function() {
                var dayId = this.value;
                var dayCard = this.closest('.day-card');
                if (!dayCard) return;

                let status = this.checked ? 1 : 0;
     

                updateDayOnOff(dayId, status); // ✅ should trigger for both check and uncheck

            });
        });



        // Hide all messages initially
        document.querySelectorAll('.day-off-message').forEach(function(msg) {
            msg.style.display = 'none';
        });

        function updateDayOnOff(dayId, status) {
            console.log("updateDayOnOff called =>", dayId, status);

            var data = {
                dayId: dayId,
                off: status
            };

            LoaderShow();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('admin.working-hour.full-day-off-staff') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    LoaderHide();
                    if (response.status === 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                }
            });
        }




        function DeleteWorkTime(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure to delete Work Time?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, confirm!',
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {

                    var data = {
                        'id': id
                    }

                    LoaderShow()

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/working-hour') }}/" + id,
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            LoaderHide()
                            if (response.status === 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 2000
                                });


                                location.reload();
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        }
                    });
                    return false;
                }
            });
        }

        function AddTime(id) {
            // Reset form
            $("#add-working-hours")[0].reset();
            $("#_method").val("POST");
            $("#flag").val("A");
            $("#row_id").val("");
            $("#UpdateBtn").html("Save");
            $('.quick-slot-btn').removeClass('active');
            
            // Set day_id
            $("#day_id").val(id);
            
            // Show modal
            $("#StaffTimeSlotModal").modal('show');
        }

        function EditWorkTime(id, start_time, end_time) {
            // Reset form
            $('.quick-slot-btn').removeClass('active');
            
            // Set values
            $("#start_time").val(start_time);
            $("#end_time").val(end_time);
            $("#_method").val("PUT");
            $("#flag").val("U");
            $("#row_id").val(id);
            $("#UpdateBtn").html("Update");

            // Show modal
            $("#StaffTimeSlotModal").modal('show');
        }

        $(document).on('click', '.quick-slot-btn', function() {
            // Remove active state from all buttons
            $('.quick-slot-btn').removeClass('active');

            // Add active to clicked one
            $(this).addClass('active');

            // Get data attributes
            let startTime = $(this).data('start');
            let endTime = $(this).data('end');

            // Set the time inputs
            $('input[name="start_time"]').val(startTime);
            $('input[name="end_time"]').val(endTime);
        });

        // 🔹 If user manually changes the time → remove active state
        $(document).on('input change', 'input[name="start_time"], input[name="end_time"]', function() {
            $('.quick-slot-btn').removeClass('active');
        });
    </script>
    <script>
        $(document).ready(function() {
            let validator = $("#add-working-hours").validate({
                rules: {
                    start_time: {
                        required: true
                    },
                    end_time: {
                        required: true
                    },
                },
                messages: {
                    start_time: {
                        required: "Please Select Start Time"
                    },
                    end_time: {
                        required: "Please Select End Time"
                    },
                },
                errorElement: "div",
                errorPlacement: function(error, element) {
                    error.addClass("invalid-feedback");
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                }
            });

            $("#add-working-hours").submit(function(e) {
                e.preventDefault();

                if (!$(this).valid()) {
                    return;
                }

                let form = $(this)[0];
                let formData = new FormData(form);

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });

                let flag = $("#flag").val(); // Should be "A" for add, else update mode
                let id = $("#row_id").val() || "";
                let url =
                    flag === "A" ?
                    "{{ route('admin.working-hour.store') }}" // Replace with your actual route
                    :
                    "{{ url('admin/working-hour') }}/" + id;

                if (typeof LoaderShow === "function") LoaderShow();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (typeof LoaderHide === "function") LoaderHide();
                        $("#StaffTimeSlotModal").modal("hide");
                        if (response.status === 1) {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000,
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "Warning",
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000,
                            });
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoaderHide === "function") LoaderHide();
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error occurred while processing.",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    },
                });
            });
        });
    </script>
@endpush
