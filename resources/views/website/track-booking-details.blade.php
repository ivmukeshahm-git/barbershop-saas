@extends('layouts.master')



@section('title', 'Booking ' . $bookingCode . ' | ' . ($shopName ?? 'Misha Hair Studio Mumbai'))


@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<link href="{{ asset('frontend/css/track-booking.css') }}?v=2" rel="stylesheet">



<div class="tb-page" id="tbPrintRoot">

    <section class="tb-hero" style="padding-bottom:2rem;">

        <div class="container">

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">

                <div>
                    <p class="text-uppercase fw-semibold mb-2" style="letter-spacing:.08em;color:#136207;font-size:.8rem;">
                        Booking Status
                    </p>

                    <h1 class="mb-2" style="font-size:clamp(1.6rem,3vw,2.2rem);">Your Appointment</h1>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="tb-code-chip" id="tbBookingCodeChip">
                            <span id="tbBookingCodeText">{{ $bookingCode }}</span>
                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" id="tbCopyBtn"
                                title="Copy Booking Code">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </span>

                        <span class="tb-status {{ $statusBadge }}">
                            <i class="bi bi-circle-fill" style="font-size:.55rem;"></i> {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div class="tb-actions">

                    <a href="{{ route('track-booking') }}" class="tb-btn-outline">

                        <i class="bi bi-arrow-left me-1"></i> Track Another

                    </a>

                </div>

            </div>

        </div>

    </section>



    <section class="tb-section">

        <div class="container">

            <div class="row g-4">

                <div class="col-lg-8">

                    <div class="tb-card p-4 p-md-4 mb-4">

                        <h5 class="fw-bold mb-3"><i class="bi bi-signpost-split me-2 text-primary"></i>Booking Timeline</h5>

                        <ul class="tb-timeline">

                            @foreach ($timeline as $step)

                                <li class="tb-timeline-item {{ $step['state'] }}">

                                    <div class="tb-timeline-dot">
                                        @if ($step['state'] === 'done')
                                            <i class="bi bi-check-lg"></i>
                                        @elseif ($step['state'] === 'cancelled')
                                            <i class="bi bi-x-lg"></i>
                                        @elseif ($step['state'] === 'current')
                                            <i class="bi bi-record-circle"></i>
                                        @else
                                            <i class="bi bi-circle"></i>
                                        @endif
                                    </div>

                                    <h6>{{ $step['label'] }}</h6>

                                    <p>{{ $step['desc'] }}</p>

                                    @if (!empty($step['time']))

                                        <span class="time">{{ $step['time'] }}</span>

                                    @endif

                                </li>

                            @endforeach

                        </ul>

                    </div>



                    <div class="tb-card p-4">

                        <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist me-2 text-primary"></i>Appointment Details</h5>

                        <div class="row tb-detail-grid">

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Booking Code</span>

                                    <span class="val">{{ $bookingCode }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Booking Status</span>

                                    <span class="val"><span class="tb-status {{ $statusBadge }}">{{ $statusLabel }}</span></span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Customer Name</span>

                                    <span class="val">{{ $customerName }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Phone</span>

                                    <span class="val">{{ $phone }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Email</span>

                                    <span class="val">{{ $email }}</span>

                                </div>

                            </div>

                            

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Service Name</span>

                                    <span class="val">{{ $serviceNames }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Service Price</span>

                                    <span class="val">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($servicePrice, 2) }}</span>
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Duration</span>

                                    <span class="val">{{ $durationLabel }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Appointment Date</span>

                                    <span class="val">{{ $appointmentDate }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Appointment Time</span>

                                    <span class="val">{{ $appointmentTime }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Payment Status</span>

                                    <span class="val">{{ $paymentStatus }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Created At</span>

                                    <span class="val">{{ $createdAt }}</span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="tb-meta">

                                    <span class="lbl">Notes</span>

                                    <span class="val">{{ $notes }}</span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <div class="col-lg-4">

                    <div class="tb-card p-4 tb-summary mb-4 sticky-lg-top" style="top:100px;">

                        <h5 class="fw-bold mb-3">Booking Summary</h5>

                        <div class="row-line"><span>Booking Code</span><strong>{{ $bookingCode }}</strong></div>

                        <div class="row-line"><span>Service</span><strong>{{ $serviceNames }}</strong></div>

                        <div class="row-line"><span>Barber</span><strong>{{ $barberNames }}</strong></div>

                        <div class="row-line"><span>Date</span><strong>{{ $appointmentDate }}</strong></div>

                        <div class="row-line"><span>Time</span><strong>{{ $appointmentTime }}</strong></div>

                        <div class="row-line"><span>Duration</span><strong>{{ $durationLabel }}</strong></div>

                        <div class="row-line total-line"><span>Total</span><strong>{{ $shopCurrencySymbol ?? '₹' }}{{ number_format($total, 2) }}</strong></div>


                        <div class="text-center mt-4">

                            <img class="tb-qr" src="{{ $qrUrl }}" alt="Booking QR Code">

                            <p class="small text-muted mt-2 mb-0">Scan to open this booking</p>

                        </div>



                        <div class="d-grid gap-2 mt-4">

                            <a href="{{ route('book') }}" class="tb-btn-primary text-center text-decoration-none d-flex align-items-center justify-content-center">

                                Book Another Appointment

                            </a>

                            <a href="{{ route('home') }}" class="tb-btn-outline text-center text-decoration-none d-flex align-items-center justify-content-center">

                                Back to Home

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>



<div class="tb-toast" id="tbToast">Booking Code Copied</div>



<script>

(function () {

    function toast(msg) {

        var el = document.getElementById('tbToast');

        if (!el) return;

        el.textContent = msg || 'Booking Code Copied';

        el.classList.add('show');

        setTimeout(function () { el.classList.remove('show'); }, 2200);

    }



    var copyBtn = document.getElementById('tbCopyBtn');

    if (copyBtn) {

        copyBtn.addEventListener('click', function () {

            var code = document.getElementById('tbBookingCodeText').textContent.trim();

            if (navigator.clipboard && navigator.clipboard.writeText) {

                navigator.clipboard.writeText(code).then(function () {

                    toast('Booking Code Copied');

                });

            } else {

                var ta = document.createElement('textarea');

                ta.value = code;

                document.body.appendChild(ta);

                ta.select();

                document.execCommand('copy');

                document.body.removeChild(ta);

                toast('Booking Code Copied');

            }

        });

    }

})();

</script>

@endsection

