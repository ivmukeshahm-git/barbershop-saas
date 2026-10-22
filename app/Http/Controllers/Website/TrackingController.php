<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TrackingController extends Controller
{
    public function index()
    {
        $bookingCodePrefix = Schema::hasColumn('tbl_setting', 'booking_code_prefix')
            ? (Setting::forCurrentShop()?->booking_code_prefix ?? 'MHS')
            : 'MHS';

        return view('website.track-booking', compact('bookingCodePrefix'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'booking_code' => ['required', 'string', 'max:40'],
        ], [
            'booking_code.required' => 'Please enter your Booking Code.',
        ]);

        $code = strtoupper(trim($request->input('booking_code')));
        $code = preg_replace('/\s+/', '', $code);

        $appointment = Appointment::with(['services.service', 'services.staff', 'customer'])
            ->where('booking_code', $code)
            ->first();

        if (!$appointment) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Booking not found. Please check your booking code.',
                ]);
            }

            return redirect()
                ->route('track-booking')
                ->withInput()
                ->with('track_error', 'Booking not found. Please check your booking code.');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 1,
                'redirect' => route('track-booking.show', ['code' => $appointment->booking_code]),
            ]);
        }

        return redirect()->route('track-booking.show', ['code' => $appointment->booking_code]);
    }

    public function show(string $code)
    {
        $code = strtoupper(trim($code));
        $code = preg_replace('/\s+/', '', $code);

        $appointment = Appointment::with(['services.service', 'services.staff', 'customer'])
            ->where('booking_code', $code)
            ->first();

        if (!$appointment) {
            return redirect()
                ->route('track-booking')
                ->with('track_error', 'Booking not found. Please check your booking code.');
        }

        $payload = $this->buildTrackingPayload($appointment);

        return view('website.track-booking-details', $payload);
    }

    protected function buildTrackingPayload(Appointment $appointment): array
    {
        $services = $appointment->services ?? collect();
        $serviceNames = $services->map(fn ($row) => optional($row->service)->service_name)->filter()->unique()->values();
        $servicePrices = $services->sum(fn ($row) => (float) (optional($row->service)->price ?? 0));
        $durationMins = $services->sum(fn ($row) => (int) (optional($row->service)->duration ?? 0));
        $staffNames = $services->map(fn ($row) => optional($row->staff)->name)->filter()->unique()->values();

        $status = (int) $appointment->status;
        $timeline = $this->buildTimeline($appointment, $status, $staffNames->isNotEmpty());

        $start = $appointment->slot_start ? Carbon::parse($appointment->slot_start)->format('h:i A') : '—';
        $end = $appointment->slot_end ? Carbon::parse($appointment->slot_end)->format('h:i A') : null;
        $timeLabel = $end ? ($start . ' – ' . $end) : $start;

        $total = $appointment->total_amt !== null && $appointment->total_amt !== ''
            ? (float) $appointment->total_amt
            : $servicePrices;

        $paymentStatus = ((int) $appointment->is_invoiced === 1 || $status === 1)
            ? 'Paid'
            : 'Unpaid';

        $trackUrl = route('track-booking.show', ['code' => $appointment->booking_code]);

        return [
            'appointment' => $appointment,
            'bookingCode' => $appointment->booking_code,
            'status' => $status,
            'statusLabel' => Appointment::statusLabel($status),
            'statusBadge' => Appointment::statusBadgeClass($status),
            'customerName' => $appointment->name ?: optional($appointment->customer)->customer_name ?: '—',
            'phone' => $appointment->phone ?: optional($appointment->customer)->customer_phone ?: '—',
            'email' => $appointment->email ?: optional($appointment->customer)->customer_email ?: '—',
            'serviceNames' => $serviceNames->implode(', ') ?: '—',
            'servicePrice' => $servicePrices,
            'durationMins' => $durationMins,
            'durationLabel' => $durationMins > 0 ? ($durationMins . ' min') : '—',
            'barberNames' => $staffNames->implode(', ') ?: '—',
            'appointmentDate' => $appointment->date ? Carbon::parse($appointment->date)->format('d M Y') : '—',
            'appointmentTime' => $timeLabel,
            'notes' => $appointment->notes ?: '—',
            'total' => $total,
            'paymentStatus' => $paymentStatus,
            'createdAt' => $appointment->created_at ? $appointment->created_at->format('d M Y, h:i A') : '—',
            'timeline' => $timeline,
            'trackUrl' => $trackUrl,
            'qrUrl' => 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' . urlencode($trackUrl),
        ];
    }

    protected function buildTimeline(Appointment $appointment, int $status, bool $hasStaff): array
    {
        $created = $appointment->created_at ? $appointment->created_at->format('d M Y, h:i A') : null;
        $updated = $appointment->updated_at ? $appointment->updated_at->format('d M Y, h:i A') : null;
        $isCancelled = $status === 2;
        $isCompleted = $status === 1;
        $isConfirmed = in_array($status, [1, 3, 4], true);

        $steps = [
            [
                'label' => 'Booking Received',
                'desc' => 'Your appointment request was received.',
                'state' => 'done',
                'time' => $created,
            ],
        ];

        if ($isCancelled) {
            $steps[] = [
                'label' => 'Cancelled',
                'desc' => 'This appointment has been cancelled.',
                'state' => 'cancelled',
                'time' => $updated,
            ];
            return $steps;
        }

        $steps[] = [
            'label' => 'Confirmed',
            'desc' => 'Your booking has been confirmed by the shop.',
            'state' => $isConfirmed ? 'done' : ($status === 0 ? 'current' : 'pending'),
            'time' => $isConfirmed ? $updated : null,
        ];

        $staffDone = $hasStaff && ($isConfirmed || $status === 0);
        $steps[] = [
            'label' => 'Staff Assigned',
            'desc' => $hasStaff ? 'A barber has been assigned to your service.' : 'Barber will be assigned soon.',
            'state' => $hasStaff ? ($isConfirmed ? 'done' : 'current') : 'pending',
            'time' => $hasStaff ? $created : null,
        ];

        $readyDone = $isConfirmed || $isCompleted;
        $steps[] = [
            'label' => 'Ready for Appointment',
            'desc' => 'Everything is set for your visit.',
            'state' => $isCompleted ? 'done' : ($readyDone ? 'current' : 'pending'),
            'time' => $readyDone ? $updated : null,
        ];

        $steps[] = [
            'label' => 'Completed',
            'desc' => 'Service completed successfully.',
            'state' => $isCompleted ? 'done' : 'pending',
            'time' => $isCompleted ? $updated : null,
        ];

        // Ensure only one "current" mark for visual clarity
        $hasCurrent = collect($steps)->contains(fn ($s) => $s['state'] === 'current');
        if (!$hasCurrent && !$isCompleted) {
            foreach ($steps as $i => $step) {
                if ($step['state'] === 'pending') {
                    $steps[$i]['state'] = 'current';
                    break;
                }
            }
        }

        return $steps;
    }
}
