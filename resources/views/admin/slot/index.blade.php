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

        .slot-box {
            background: #eee;
            border: 1px solid #bfbfbf;
        }

        .open-close-box {
            background: #fff;
            padding: 10px;
            border-radius: 10px;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Slot";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Slot Configuration</h4>
                <p>Configure business hours and appointment slots</p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">
            <div class="card-body table-responsive p-4">
                <form id="slot-form" onsubmit="return false;">
                    <div class="row">

                        <!-- Monday -->
                        <div class="col-lg-6">
                            <div class="card mb-4 day-section slot-box" id="sectionMonday">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fw-bold">Monday</span>
                                        {{-- <span class="badge bg-light text-dark ms-2 break-count">0 breaks</span> --}}
                                    </div>
                                    <div class="form-check form-switch">
                                        <!-- Hidden input to send 0 if unchecked -->
                                        <input type="hidden" name="days[Monday][is_day_off]" value="0" />
                                        <input class="form-check-input day-off-switch" type="checkbox" id="dayOffMonday"
                                            name="days[Monday][is_day_off]" value="1" data-day="Monday" />
                                        <label class="form-check-label me-2" for="dayOffMonday">Day Off</label>
                                    </div>

                                </div>

                                <div class="card-body">

                                    <div class="breaks-section" id="breaksSectionMonday">
                                        <div class="open-close-box mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>Shop Open Time</label>
                                                    <input class="form-control" type="time" id="openTimeMonday"
                                                        name="days[Monday][open_time]" />
                                                </div>

                                                <div class="col-lg-6">
                                                    <label>Shop Close Time</label>
                                                    <input class="form-control" type="time" id="closeTimeMonday"
                                                        name="days[Monday][close_time]" />
                                                </div>
                                            </div>
                                        </div>


                                        {{-- <div id="breaksMonday"></div> --}}
                                        {{-- <button type="button" class="btn btn-sm btn-primary mt-2" id="addBreakMonday">+ Add
                                            Break</button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Tuesday -->
                        <div class="col-lg-6">
                            <div class="card mb-4 day-section slot-box" id="sectionTuesday">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fw-bold">Tuesday</span>
                                        {{-- <span class="badge bg-light text-dark ms-2 break-count">0 breaks</span> --}}
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input day-off-switch" type="checkbox" id="dayOffTuesday"
                                            name="days[Tuesday][is_day_off]" data-day="Tuesday" />
                                        <label class="form-check-label me-2" for="dayOffTuesday">Day Off</label>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="breaks-section" id="breaksSectionTuesday">
                                        <div class="open-close-box mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>Shop Open Time</label>
                                                    <input class="form-control" type="time" id="openTimeTuesday"
                                                        name="days[Tuesday][open_time]" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Shop Close Time</label>
                                                    <input class="form-control" type="time" id="closeTimeTuesday"
                                                        name="days[Tuesday][close_time]" />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div id="breaksTuesday"></div>
                                        <button type="button" class="btn btn-sm btn-primary mt-2" id="addBreakTuesday">+
                                            Add Break</button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wednesday -->
                        <div class="col-lg-6">
                            <div class="card mb-4 day-section slot-box" id="sectionWednesday">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fw-bold">Wednesday</span>
                                        {{-- <span class="badge bg-light text-dark ms-2 break-count">0 breaks</span> --}}
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input day-off-switch" type="checkbox" id="dayOffWednesday"
                                            name="days[Wednesday][is_day_off]" data-day="Wednesday" />
                                        <label class="form-check-label me-2" for="dayOffWednesday">Day Off</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="breaks-section" id="breaksSectionWednesday">
                                        <div class="open-close-box mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>Shop Open Time</label>
                                                    <input class="form-control" type="time" id="openTimeWednesday"
                                                        name="days[Wednesday][open_time]" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Shop Close Time</label>
                                                    <input class="form-control" type="time" id="closeTimeWednesday"
                                                        name="days[Wednesday][close_time]" />
                                                </div>
                                            </div>
                                        </div>


                                        {{-- <div id="breaksWednesday"></div>
                                        <button type="button" class="btn btn-sm btn-primary mt-2"
                                            id="addBreakWednesday">+
                                            Add Break</button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thursday -->
                        <div class="col-lg-6">
                            <div class="card mb-4 day-section slot-box" id="sectionThursday">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fw-bold">Thursday</span>
                                        {{-- <span class="badge bg-light text-dark ms-2 break-count">0 breaks</span> --}}
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input day-off-switch" type="checkbox"
                                            id="dayOffThursday" name="days[Thursday][is_day_off]" data-day="Thursday" />
                                        <label class="form-check-label me-2" for="dayOffThursday">Day Off</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="breaks-section" id="breaksSectionThursday">
                                        <div class="open-close-box mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>Shop Open Time</label>
                                                    <input class="form-control" type="time" id="openTimeThursday"
                                                        name="days[Thursday][open_time]" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Shop Close Time</label>
                                                    <input class="form-control" type="time" id="closeTimeThursday"
                                                        name="days[Thursday][close_time]" />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 
                                        <div id="breaksThursday"></div>
                                        <button type="button" class="btn btn-sm btn-primary mt-2"
                                            id="addBreakThursday">+
                                            Add Break</button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Friday -->
                        <div class="col-lg-6">
                            <div class="card mb-4 day-section slot-box" id="sectionFriday">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fw-bold">Friday</span>
                                        {{-- <span class="badge bg-light text-dark ms-2 break-count">0 breaks</span> --}}
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input day-off-switch" type="checkbox" id="dayOffFriday"
                                            name="days[Friday][is_day_off]" data-day="Friday" />
                                        <label class="form-check-label me-2" for="dayOffFriday">Day Off</label>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="breaks-section" id="breaksSectionFriday">
                                        <div class="open-close-box mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>Shop Open Time</label>
                                                    <input class="form-control" type="time" id="openTimeFriday"
                                                        name="days[Friday][open_time]" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Shop Close Time</label>
                                                    <input class="form-control" type="time" id="closeTimeFriday"
                                                        name="days[Friday][close_time]" />
                                                </div>
                                            </div>
                                        </div>


                                        {{-- <div id="breaksFriday"></div>
                                        <button type="button" class="btn btn-sm btn-primary mt-2" id="addBreakFriday">+
                                            Add Break</button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Saturday -->
                        <div class="col-lg-6">
                            <div class="card mb-4 day-section slot-box" id="sectionSaturday">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fw-bold">Saturday</span>
                                        {{-- <span class="badge bg-light text-dark ms-2 break-count">0 breaks</span> --}}
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input day-off-switch" type="checkbox"
                                            id="dayOffSaturday" name="days[Saturday][is_day_off]" data-day="Saturday" />
                                        <label class="form-check-label me-2" for="dayOffSaturday">Day Off</label>
                                    </div>
                                </div>
                                <div class="card-body">


                                    <div class="breaks-section" id="breaksSectionSaturday">
                                        <div class="open-close-box mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>Shop Open Time</label>
                                                    <input class="form-control" type="time" id="openTimeSaturday"
                                                        name="days[Saturday][open_time]" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Shop Close Time</label>
                                                    <input class="form-control" type="time" id="closeTimeSaturday"
                                                        name="days[Saturday][close_time]" />
                                                </div>
                                            </div>
                                        </div>


                                        {{-- <div id="breaksSaturday"></div>
                                        <button type="button" class="btn btn-sm btn-primary mt-2"
                                            id="addBreakSaturday">+ Add Break</button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sunday full width -->
                        <div class="col-lg-12">
                            <div class="card mb-4 day-section slot-box" id="sectionSunday">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fw-bold">Sunday</span>
                                        {{-- <span class="badge bg-light text-dark ms-2 break-count">0 breaks</span> --}}
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input day-off-switch" type="checkbox" id="dayOffSunday"
                                            name="days[Sunday][is_day_off]" data-day="Sunday" />
                                        <label class="form-check-label me-2" for="dayOffSunday">Day Off</label>
                                    </div>
                                </div>
                                <div class="card-body">


                                    <div class="breaks-section" id="breaksSectionSunday">
                                        <div class="open-close-box mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>Shop Open Time</label>
                                                    <input class="form-control" type="time" id="openTimeSunday"
                                                        name="days[Sunday][open_time]" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Shop Close Time</label>
                                                    <input class="form-control" type="time" id="closeTimeSunday"
                                                        name="days[Sunday][close_time]" />
                                                </div>
                                            </div>
                                        </div>


                                        {{-- <div id="breaksSunday"></div>
                                        <button type="button" class="btn btn-sm btn-primary mt-2" id="addBreakSunday">+
                                            Add Break</button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs for backend processing -->
                        <input type="hidden" id="_method" name="_method" value="POST" />
                        <input type="hidden" id="flag" name="flag" value="A" />
                        <input type="hidden" id="row_id" name="row_id" value="" />

                        @can('Slot-Update')
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        @endcan
                        <!-- Submit button -->

                    </div>
                </form>



            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection

@push('script')
    <script>
        // Make $slots data available for JS, converting collection to plain JSON
        var slotsData = @json($slots);

        $(document).ready(function() {
            // Loop over each day key in slotsData

            Object.keys(slotsData).forEach(function(day) {
                var slot = slotsData[day];

                // ✅ Set Shop Open/Close Time
                $('#openTime' + day).val(slot.open_time ?? '');
                $('#closeTime' + day).val(slot.close_time ?? '');

                // Set Day Off checkbox value
                var dayOffCheckbox = $('#dayOff' + day);
                if (slot.is_day_off) {
                    dayOffCheckbox.prop('checked', true).trigger('change');
                }


                const breaksSection = document.getElementById(`breaksSection${day}`);

                if (slot.is_day_off) {
                    breaksSection.style.display = 'none';
                } else {
                    breaksSection.style.display = 'block';
                }


            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const daysOfWeek = [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
            ];

            daysOfWeek.forEach(day => {
                const section = document.getElementById(`section${day}`);
                const breaksSection = document.getElementById(`breaksSection${day}`);
                const dayOffSwitch = document.getElementById(`dayOff${day}`);

                dayOffSwitch.addEventListener('change', function() {
                    breaksSection.style.display = this.checked ? 'none' : '';
                });


                breaksSection.style.display = dayOffSwitch.checked ? 'none' : '';
                // }
            });
        });


        $(document).ready(function() {
            let validator = $("#slot-form").validate({
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
                },
            });

            $("#slot-form").submit(function(e) {
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
                    "{{ route('admin.slot.store') }}" // Replace with your actual route
                    :
                    "{{ url('admin/slot') }}/" + id;

                if (typeof LoaderShow === "function") LoaderShow();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (typeof LoaderHide === "function") LoaderHide();
                        $("#SlotModal").modal("hide");
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
