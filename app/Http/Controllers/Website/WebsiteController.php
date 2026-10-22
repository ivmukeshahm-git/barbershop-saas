<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Customer;
use App\Models\Gst;
use App\Models\Service;
use App\Models\Setting;
use App\Models\StaffService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Mail\WelcomeMail;


class WebsiteController extends Controller
{
    public function sendContactMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'max:0'],
        ]);

        $recipient = Setting::forCurrentShop()?->contact_email ?: config('mail.from.address');
        if (!$recipient) {
            abort(503, 'Contact email is not configured.');
        }

        Mail::raw(
            "Name: {$validated['name']}\nEmail: {$validated['email']}\nPhone: " . ($validated['phone'] ?? 'Not provided') .
            "\n\n{$validated['message']}",
            function ($mail) use ($recipient, $validated) {
                $mail->to($recipient)->subject($validated['subject']);
                $mail->replyTo($validated['email'], $validated['name']);
            }
        );

        return response()->json(['message' => 'Your message has been sent.']);
    }

    public function index()
    {
        StaffService::backfillFromLegacy();
        $bookingCodePrefix = Schema::hasColumn('tbl_setting', 'booking_code_prefix')
            ? (Setting::forCurrentShop()?->booking_code_prefix ?? 'MHS')
            : 'MHS';

        $servicesByCategoryRaw = DB::table('tbl_service_category as sc')
            ->join('tbl_service as s', 'sc.id', '=', 's.category_id')
            ->select(
                'sc.id as category_id',
                'sc.category_name',
                's.id as service_id',
                's.service_name',
                's.price',
                's.duration',
                's.status',
                's.tip_status',
                's.service_img',
                's.description'
            )
            ->whereNull('s.deleted_at')  // service delete नहीं हुई
            ->whereNull('sc.deleted_at') // category delete नहीं हुई
            ->orderBy('sc.id')
            ->orderBy('s.id')
            ->get();

        $servicesByCategory = $servicesByCategoryRaw->groupBy('category_id');

        $Staff = User::where('role', 'Staff')
            ->where('is_barber', 1)
            ->where('active_status', 1)
            ->whereNull('deleted_at')
            ->get();

        $flexibleStaffIds = $Staff->filter(function ($row) {
            return strcasecmp(trim((string) $row->name), 'First available') === 0
                || stripos((string) $row->name, 'First available') === 0;
        })->pluck('id')->values();

        // Shop weekly schedule (staff_id = 0) for calendar day-off checks
        $shopSchedule = DB::table('tbl_slots')
            ->where('staff_id', 0)
            ->get(['day_name', 'is_day_off', 'open_time', 'close_time'])
            ->keyBy('day_name');

        // Full-day shop closures (Y-m-d)
        $shopFullClosures = DB::table('tbl_shop_closure')
            ->where('closure_status', 1)
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->values();

        // Staff day-off map: { staffId: ['Sunday', ...] }
        $staffDayOffMap = [];
        $staffSlotRows = DB::table('tbl_slots')
            ->where('staff_id', '>', 0)
            ->where('is_day_off', 1)
            ->get(['staff_id', 'day_name']);
        foreach ($staffSlotRows as $row) {
            $staffDayOffMap[$row->staff_id][] = $row->day_name;
        }

        return view('website.book', compact(
            'servicesByCategory',
            'Staff',
            'shopSchedule',
            'shopFullClosures',
            'staffDayOffMap',
            'flexibleStaffIds',
            'bookingCodePrefix'
        ));
    }


    public function get_staff_service(Request $request)
    {

        $staff_service_id = $request->input('staff_service_id'); // string like "1,2" or "3"
        $select_type = $request->input('select_type'); // 'service' or 'staff'

        if ($select_type === 'Service') {
            // Convert comma-separated string into array
            $serviceIds = array_values(array_filter(array_map('trim', explode(',', $staff_service_id)), fn ($id) => $id !== ''));

            // Fetch staff who provide any of these services.
            // Uses the dedicated staff-service assignment table with a legacy fallback.
            $staffs = User::where('role', 'Staff')
                ->where('is_barber', 1)
                ->where('active_status', 1)
                ->whereNull('deleted_at')
                ->get()
                ->filter(function ($staff) use ($serviceIds) {
                    if ($this->isFlexibleStaffId($staff->id)) {
                        return true;
                    }
                    foreach ($serviceIds as $sid) {
                        if (StaffService::offersService($staff->id, $sid)) {
                            return true;
                        }
                    }
                    return false;
                })
                ->values();
//            $staffs = User::whereHas('services', function($q) use ($serviceIds) {
//                $q->whereIn('service_id', $serviceIds);
//            })->get();

            return response()->json([
                'status' => 1,
                'staff' => $staffs,
                'staff_day_offs' => DB::table('tbl_slots')
                    ->whereIn('staff_id', $staffs->pluck('id'))
                    ->where('is_day_off', 1)
                    ->get(['staff_id', 'day_name'])
                    ->groupBy('staff_id')
                    ->map(fn ($rows) => $rows->pluck('day_name')->values())
            ]);
        } elseif ($select_type === 'Staff') {
            // $staff_service_id contains single staff id
            $staff = User::where('id', $staff_service_id)
                ->where('role', 'Staff')
                ->where('is_barber', 1)
                ->whereNull('deleted_at')
                ->first();

            if (!$staff) {
                return response()->json(['error' => 'Staff not found'], 404);
            }

            $service_ids = StaffService::idsForStaff($staff->id);

            $services = collect();
            if (!empty($service_ids)) {
                $services = DB::table('tbl_service as s')
                    ->join('tbl_service_category as sc', 's.category_id', '=', 'sc.id')
                    ->whereIn('s.id', $service_ids)
                    ->whereNull('s.deleted_at')
                    ->whereNull('sc.deleted_at')
                    ->select(
                        's.id as service_id',
                        's.service_name',
                        's.price',
                        's.duration',
                        'sc.id as category_id',
                        'sc.category_name'
                    )
                    ->orderBy('sc.id')
                    ->orderBy('s.id')
                    ->get();
            }

            // category-wise group
            $grouped = [];
            foreach ($services as $service) {
                $grouped[$service->category_id]['category_id'] = $service->category_id;
                $grouped[$service->category_id]['category_name'] = $service->category_name;
                $grouped[$service->category_id]['services'][] = [
                    'service_id'   => $service->service_id,
                    'service_name' => $service->service_name,
                    'price'        => $service->price,
                    'duration'     => $service->duration,
                ];
            }

            $staffDayOffs = DB::table('tbl_slots')
                ->where('staff_id', $staff->id)
                ->where('is_day_off', 1)
                ->pluck('day_name')
                ->values();

            // response
            return response()->json([
                'status' => 1,
                'data'   => array_values($grouped),
                'staff_day_offs' => $staffDayOffs,
            ]);
        } else {
            return response()->json(['error' => 'Invalid select_type'], 400);
        }
    }

     public function get_slot_by_staff(Request $request)
    {
        $year        = (int) $request->input('selectedYear');
        $month       = (int) $request->input('selectedMonth');
        $day         = (int) $request->input('selectedDay');
        $staff_id    = $request->input('staff_id');
        $service_ids = $request->input('service_id', '');
        $day_name    = $request->input('day_name');

        $date = Carbon::createFromDate($year, $month + 1, $day)->toDateString();
        $plan = $this->buildStaffBookingPlan($this->parseIdList($staff_id), $this->parseIdList($service_ids));

        if (!$plan) {
            return response()->json(['status' => 0, 'message' => 'Please select a barber.']);
        }

        $resolved = $this->resolveStaffSlotGroups($date, $day_name, $plan);
        if (($resolved['status'] ?? 0) !== 1) {
            return response()->json($resolved);
        }

        $groups = $resolved['groups'] ?? [];
        $hasGroupSlots = collect($groups)->contains(fn ($g) => !empty($g['slots']));

        return response()->json([
            'status'   => 1,
            'date'     => $date,
            'slots'    => $resolved['slots'],
            'groups'   => $groups,
            'duration' => $resolved['duration'],
            'message'  => !$hasGroupSlots && empty($resolved['slots'])
                ? 'No overlapping time for the selected barbers. Try different staff or another date.'
                : 'OK',
        ]);
    }

    private function parseIdList($value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $value)), function ($id) {
            return $id !== '';
        }));
    }

    /**
     * Pair barbers to services. "First available" stays as open jobs, not a dummy calendar.
     */
    private function mapStaffToServices(array $staffIds, array $serviceIds): array
    {
        $plan = $this->buildStaffBookingPlan($staffIds, $serviceIds);
        return $plan['named'] ?? [];
    }

    /**
     * Booking start-time grid. Use the shop interval (15/30), not the GCD of
     * service lengths — a 90-min GCD with a 270-min visit leaves almost no starts
     * once lunch splits the day into ~4-hour blocks.
     */
    private function slotStepMinutes(array $serviceIds): int
    {
        $default = (int) (Setting::query()->value('service_default_duration') ?: 15);
        if ($default < 5) {
            $default = 15;
        }
        if ($default > 60) {
            $default = 60;
        }

        return $default;
    }

    private function isFlexibleStaffId($staffId): bool
    {
        $id = strtolower(trim((string) $staffId));
        if ($id === '' || $id === '0' || $id === 'any' || $id === 'first') {
            return true;
        }

        static $flexibleIds = null;
        if ($flexibleIds === null) {
            $flexibleIds = User::query()
                ->where('role', 'Staff')
                ->whereNull('deleted_at')
                ->where(function ($q) {
                    $q->whereRaw('LOWER(name) = ?', ['first available'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['first available%']);
                })
                ->pluck('id')
                ->map(fn ($v) => (string) $v)
                ->all();
        }

        return in_array((string) $staffId, $flexibleIds, true);
    }

    private function staffOffersService($staff, $serviceId): bool
    {
        // "First available" flexible staff accept any service
        if ($this->isFlexibleStaffId($staff->id)) {
            return true;
        }
        if ($staff->id === null) {
            return false;
        }

        return StaffService::offersService($staff->id, $serviceId);
    }

    private function durationLookup(array $serviceIds)
    {
        $unique = array_values(array_unique($serviceIds));
        if (empty($unique)) {
            return collect();
        }

        return Service::whereIn('id', $unique)->pluck('duration', 'id');
    }

    private function durationOf($map, $id): int
    {
        return (int) ($map[$id] ?? $map[(string) $id] ?? $map[(int) $id] ?? 0);
    }

    private function sumServiceDurations($map, array $serviceIds): int
    {
        $total = 0;
        foreach ($serviceIds as $id) {
            $total += $this->durationOf($map, $id);
        }
        return $total > 0 ? $total : 30;
    }

    /**
     * @return array{named: array, any_jobs: array, any_sequential: array, service_ids: array}|null
     */
    private function buildStaffBookingPlan(array $staffIds, array $serviceIds): ?array
    {
        if (empty($staffIds) || empty($serviceIds)) {
            return null;
        }

        $named = [];
        $anyJobs = [];
        $anySequential = [];
        $pairs = [];

        if (count($staffIds) === 1) {
            $flexible = $this->isFlexibleStaffId($staffIds[0]);
            if ($flexible) {
                $anySequential = $serviceIds;
            } else {
                $named[$staffIds[0]] = $serviceIds;
            }
            foreach ($serviceIds as $svcId) {
                $pairs[] = [
                    'staff_id' => $staffIds[0],
                    'service_id' => $svcId,
                    'flexible' => $flexible,
                ];
            }
        } else {
            $staffCount = count($staffIds);
            foreach ($serviceIds as $i => $svcId) {
                $sid = $staffIds[$i] ?? $staffIds[$i % $staffCount];
                $flexible = $this->isFlexibleStaffId($sid);
                $pairs[] = [
                    'staff_id' => $sid,
                    'service_id' => $svcId,
                    'flexible' => $flexible,
                ];
                if ($flexible) {
                    // Same "First available" chair: sequential (25×2 = 50 min), not parallel jobs
                    $anySequential[] = $svcId;
                } else {
                    $named[$sid][] = $svcId;
                }
            }
        }

        return [
            'named' => $named,
            'any_jobs' => $anyJobs,
            'any_sequential' => $anySequential,
            'service_ids' => $serviceIds,
            'pairs' => $pairs,
        ];
    }

    private function realBarbers()
    {
        return User::query()
            ->where('role', 'Staff')
            ->where('is_barber', 1)
            ->where('active_status', 1)
            ->whereNull('deleted_at')
            ->get()
            ->filter(fn ($staff) => !$this->isFlexibleStaffId($staff->id))
            ->values();
    }

    private function timeFitsStaff(array $ctx, Carbon $start, Carbon $end): bool
    {
        if ($end->lte($start)) {
            return false;
        }

        $insideHours = collect($ctx['workingHours'])->contains(function ($block) use ($start, $end) {
            return $start->gte($block['start']) && $end->lte($block['end']);
        });
        if (!$insideHours) {
            return false;
        }

        $blockedByClosure = collect($ctx['partialClosures'])->contains(function ($closure) use ($start, $end) {
            return $start->lt($closure['end']) && $end->gt($closure['start']);
        });
        if ($blockedByClosure) {
            return false;
        }

        return !collect($ctx['busyRanges'])->contains(function ($busy) use ($start, $end) {
            return $start->lt($busy['end']) && $end->gt($busy['start']);
        });
    }

    private function staffOffersAll($staff, array $serviceIds): bool
    {
        foreach ($serviceIds as $serviceId) {
            if (!$this->staffOffersService($staff, $serviceId)) {
                return false;
            }
        }
        return true;
    }

    private function firstFit(array $ctx, Carbon $from, int $duration, int $step = 5): ?Carbon
    {
        $step = max(5, $step);
        $blocks = collect($ctx['workingHours'] ?? [])->sortBy(fn ($block) => $block['start']->timestamp)->values();

        foreach ($blocks as $block) {
            if ($from->gte($block['end'])) {
                continue;
            }
            $cursor = $from->copy()->max($block['start']);
            while (true) {
                $end = $cursor->copy()->addMinutes($duration);
                if ($end->gt($block['end'])) {
                    break;
                }
                if ($this->timeFitsStaff($ctx, $cursor, $end)) {
                    return $cursor;
                }
                $cursor->addMinutes($step);
            }
        }
        return null;
    }

    /**
     * Place services on one barber. The first must start at $start; later
     * services may resume after lunch/breaks so a 270-min visit can span the day.
     */
    private function placeSequenceOnStaff(array $ctx, Carbon $start, array $serviceIds, $durationMap, int $step): ?Carbon
    {
        $cursor = $start->copy();
        foreach (array_values($serviceIds) as $i => $svcId) {
            $dur = $this->durationOf($durationMap, $svcId) ?: 30;
            if ($i === 0) {
                $end = $cursor->copy()->addMinutes($dur);
                if (!$this->timeFitsStaff($ctx, $cursor, $end)) {
                    return null;
                }
                $cursor = $end;
                continue;
            }

            $fit = $this->firstFit($ctx, $cursor, $dur, $step);
            if (!$fit) {
                return null;
            }
            $cursor = $fit->copy()->addMinutes($dur);
        }

        return $cursor;
    }

    private function planChairDuration(array $plan, $durationMap): int
    {
        $loads = [];
        foreach ($plan['named'] as $svcIds) {
            $loads[] = $this->sumServiceDurations($durationMap, $svcIds);
        }
        if (!empty($plan['any_sequential'])) {
            $loads[] = $this->sumServiceDurations($durationMap, $plan['any_sequential']);
        }
        foreach ($plan['any_jobs'] as $svcId) {
            $loads[] = $this->durationOf($durationMap, $svcId) ?: 30;
        }
        if (empty($loads)) {
            return $this->sumServiceDurations($durationMap, $plan['service_ids']);
        }

        return max($loads);
    }

    private function serviceNameLookup(array $serviceIds)
    {
        $unique = array_values(array_unique($serviceIds));
        if (empty($unique)) {
            return collect();
        }

        return Service::whereIn('id', $unique)->pluck('service_name', 'id');
    }

    private function describeServices($durationMap, $nameMap, array $serviceIds): array
    {
        $rows = [];
        foreach ($serviceIds as $id) {
            $key = (string) $id;
            if (!isset($rows[$key])) {
                $rows[$key] = [
                    'id' => $id,
                    'name' => (string) ($nameMap[$id] ?? $nameMap[(string) $id] ?? $nameMap[(int) $id] ?? 'Service'),
                    'duration' => $this->durationOf($durationMap, $id) ?: 30,
                    'qty' => 0,
                ];
            }
            $rows[$key]['qty']++;
        }

        return array_values($rows);
    }

    private function generateSequenceSlots(
        array $ctx,
        array $serviceIds,
        $durationMap,
        int $step,
        Carbon $shopOpen,
        Carbon $shopClose,
        ?Carbon $earliest
    ): array {
        $duration = $this->sumServiceDurations($durationMap, $serviceIds);
        $slots = [];
        $cursor = $shopOpen->copy();

        while ($cursor->lt($shopClose)) {
            if ($earliest && $cursor->lt($earliest)) {
                $cursor->addMinutes($step);
                continue;
            }
            if ($duration > 0 && $cursor->copy()->addMinutes($duration)->gt($shopClose)) {
                $cursor->addMinutes($step);
                continue;
            }

            $end = $this->placeSequenceOnStaff($ctx, $cursor, $serviceIds, $durationMap, $step);
            if ($end && !$end->gt($shopClose)) {
                $slots[] = [
                    'start' => $cursor->format('H:i'),
                    'end'   => $end->format('H:i'),
                ];
            }
            $cursor->addMinutes($step);
        }

        return [$slots, $duration];
    }

    private function mergeSlotLists(array $lists): array
    {
        $byStart = [];
        foreach ($lists as $slots) {
            foreach ($slots as $slot) {
                $key = $slot['start'];
                if (!isset($byStart[$key]) || $slot['end'] < $byStart[$key]['end']) {
                    $byStart[$key] = $slot;
                }
            }
        }
        ksort($byStart);

        return array_values($byStart);
    }

    /**
     * One slot section per barber, using that barber's assigned minutes
     * (First available 25×2 → 50 min slots).
     */
    private function resolveStaffSlotGroups(string $date, string $day_name, array $plan): array
    {
        $durationMap = $this->durationLookup($plan['service_ids']);
        $nameMap = $this->serviceNameLookup($plan['service_ids']);
        // IMPORTANT:
        // In staff-wise mode, users expect "slot spacing" to match the full
        // service duration for that section (ex: 25×2 => 50min slots).
        // So we compute step per group, not globally.

        $shopCtx = $this->getShopDayContext($date, $day_name);
        if (($shopCtx['status'] ?? 0) !== 1) {
            return $shopCtx;
        }

        $now = Carbon::now();
        $isToday = $date === Carbon::today()->toDateString();
        $earliest = $isToday ? $now->copy()->addMinutes(10) : null;

        $barbers = $this->realBarbers();
        $barberCtx = [];
        foreach ($barbers as $barber) {
            $ctx = $this->getStaffWorkContext($date, $day_name, $barber->id);
            if (($ctx['status'] ?? 0) === 1) {
                $barberCtx[$barber->id] = $ctx;
            }
        }

        $namedNames = collect();
        if (!empty($plan['named'])) {
            $namedNames = User::whereIn('id', array_keys($plan['named']))->pluck('name', 'id');
        }
        $groups = [];
        $flatSlots = [];
        $durations = [];

        if (!empty($plan['any_sequential'])) {
            $slotLists = [];
            $duration = $this->sumServiceDurations($durationMap, $plan['any_sequential']);
            $groupStep = max(5, (int) $duration);
            foreach ($barbers as $barber) {
                if (empty($barberCtx[$barber->id]) || !$this->staffOffersAll($barber, $plan['any_sequential'])) {
                    continue;
                }
                [$slots] = $this->generateSequenceSlots(
                    $barberCtx[$barber->id],
                    $plan['any_sequential'],
                    $durationMap,
                    $groupStep,
                    $shopCtx['open'],
                    $shopCtx['close'],
                    $earliest
                );
                $slotLists[] = $slots;
            }
            $slots = $this->mergeSlotLists($slotLists);
            $groups[] = [
                'key' => 'any',
                'staff_id' => 'any',
                'staff_name' => 'First available',
                'flexible' => true,
                'duration' => $duration,
                'services' => $this->describeServices($durationMap, $nameMap, $plan['any_sequential']),
                'slots' => $slots,
            ];
            $durations[] = $duration;
            $flatSlots = array_merge($flatSlots, $slots);
        }

        foreach ($plan['named'] as $staffId => $svcIds) {
            $ctx = $this->getStaffWorkContext($date, $day_name, $staffId);
            if (($ctx['status'] ?? 0) !== 1) {
                $groups[] = [
                    'key' => (string) $staffId,
                    'staff_id' => (string) $staffId,
                    'staff_name' => (string) ($namedNames[$staffId] ?? 'Barber'),
                    'flexible' => false,
                    'duration' => $this->sumServiceDurations($durationMap, $svcIds),
                    'services' => $this->describeServices($durationMap, $nameMap, $svcIds),
                    'slots' => [],
                ];
                continue;
            }
            $groupDuration = $this->sumServiceDurations($durationMap, $svcIds);
            $groupStep = max(5, (int) $groupDuration);
            [$slots, $duration] = $this->generateSequenceSlots(
                $ctx,
                $svcIds,
                $durationMap,
                $groupStep,
                $shopCtx['open'],
                $shopCtx['close'],
                $earliest
            );
            $groups[] = [
                'key' => (string) $staffId,
                'staff_id' => (string) $staffId,
                'staff_name' => (string) ($namedNames[$staffId] ?? 'Barber'),
                'flexible' => false,
                'duration' => $duration,
                'services' => $this->describeServices($durationMap, $nameMap, $svcIds),
                'slots' => $slots,
            ];
            $durations[] = $duration;
            $flatSlots = array_merge($flatSlots, $slots);
        }

        $flatSlots = $this->mergeSlotLists([$flatSlots]);

        return [
            'status' => 1,
            'groups' => $groups,
            'slots' => $flatSlots,
            'duration' => !empty($durations) ? max($durations) : $this->planChairDuration($plan, $durationMap),
        ];
    }

    private function resolveFlexibleBarberAt(
        Carbon $start,
        array $serviceIds,
        $durationMap,
        array $barberCtx,
        $barbers,
        int $step
    ) {
        foreach ($barbers as $barber) {
            if (empty($barberCtx[$barber->id]) || !$this->staffOffersAll($barber, $serviceIds)) {
                continue;
            }
            $end = $this->placeSequenceOnStaff(
                $barberCtx[$barber->id],
                $start,
                $serviceIds,
                $durationMap,
                $step
            );
            if ($end) {
                return ['staff_id' => $barber->id, 'end' => $end];
            }
        }

        return null;
    }

    private function validateAndResolveGroupPicks(string $date, string $day_name, array $plan, array $picks): ?array
    {
        $byKey = [];
        foreach ($picks as $pick) {
            if (!empty($pick['key']) && !empty($pick['start'])) {
                $byKey[(string) $pick['key']] = $pick;
            }
        }

        $durationMap = $this->durationLookup($plan['service_ids']);
        $step = $this->slotStepMinutes($plan['service_ids']);
        $barbers = $this->realBarbers();
        $barberCtx = [];
        foreach ($barbers as $barber) {
            $ctx = $this->getStaffWorkContext($date, $day_name, $barber->id);
            if (($ctx['status'] ?? 0) === 1) {
                $barberCtx[$barber->id] = $ctx;
            }
        }

        $overallStart = null;
        $overallEnd = null;
        $faStaffId = null;

        if (!empty($plan['any_sequential'])) {
            $pick = $byKey['any'] ?? null;
            if (!$pick) {
                return null;
            }
            $start = Carbon::parse($date . ' ' . $pick['start']);
            $found = $this->resolveFlexibleBarberAt(
                $start,
                $plan['any_sequential'],
                $durationMap,
                $barberCtx,
                $barbers,
                $step
            );
            if (!$found) {
                return null;
            }
            $faStaffId = $found['staff_id'];
            $overallStart = $start->copy();
            $overallEnd = $found['end']->copy();
        }

        foreach ($plan['named'] as $staffId => $svcIds) {
            $pick = $byKey[(string) $staffId] ?? null;
            if (!$pick) {
                return null;
            }
            $ctx = $this->getStaffWorkContext($date, $day_name, $staffId);
            if (($ctx['status'] ?? 0) !== 1) {
                return null;
            }
            $start = Carbon::parse($date . ' ' . $pick['start']);
            $end = $this->placeSequenceOnStaff($ctx, $start, $svcIds, $durationMap, $step);
            if (!$end) {
                return null;
            }
            if (!$overallStart || $start->lt($overallStart)) {
                $overallStart = $start->copy();
            }
            if (!$overallEnd || $end->gt($overallEnd)) {
                $overallEnd = $end->copy();
            }
        }

        if (!$overallStart || !$overallEnd) {
            return null;
        }

        $resolved = [];
        foreach ($plan['pairs'] as $pair) {
            if (!empty($pair['flexible'])) {
                $resolved[] = ['staff_id' => $faStaffId, 'service_id' => $pair['service_id']];
            } else {
                $resolved[] = ['staff_id' => $pair['staff_id'], 'service_id' => $pair['service_id']];
            }
        }

        return [
            'start' => $overallStart->format('H:i'),
            'end' => $overallEnd->format('H:i'),
            'resolved' => $resolved,
        ];
    }

    /**
     * Pack named barbers from appointment start T, then queue First available
     * onto the soonest free real barber (no extra concurrent chair required).
     */
    private function tryPackAt(
        Carbon $start,
        array $plan,
        $durationMap,
        array $namedContexts,
        array $barberCtx,
        $barbers,
        int $step = 15
    ): ?array {
        $freeAt = [];
        $latestEnd = $start->copy();
        $pickedAnySeq = null;
        $pickedAnyJobs = [];
        $step = max(5, $step);

        foreach ($plan['named'] as $staffId => $svcIds) {
            $ctx = $namedContexts[$staffId] ?? null;
            if (!$ctx) {
                return null;
            }
            $cursor = $this->placeSequenceOnStaff($ctx, $start, $svcIds, $durationMap, $step);
            if (!$cursor) {
                return null;
            }
            $freeAt[(string) $staffId] = $cursor;
            if ($cursor->gt($latestEnd)) {
                $latestEnd = $cursor->copy();
            }
        }

        if (!empty($plan['any_sequential'])) {
            foreach ($barbers as $barber) {
                if (empty($barberCtx[$barber->id]) || !$this->staffOffersAll($barber, $plan['any_sequential'])) {
                    continue;
                }
                $cursor = $this->placeSequenceOnStaff(
                    $barberCtx[$barber->id],
                    $start,
                    $plan['any_sequential'],
                    $durationMap,
                    $step
                );
                if (!$cursor) {
                    continue;
                }
                $pickedAnySeq = $barber->id;
                $freeAt[(string) $barber->id] = $cursor;
                if ($cursor->gt($latestEnd)) {
                    $latestEnd = $cursor->copy();
                }
                break;
            }
            if (!$pickedAnySeq) {
                return null;
            }
        }

        foreach ($plan['any_jobs'] as $svcId) {
            $dur = $this->durationOf($durationMap, $svcId) ?: 30;
            $bestId = null;
            $bestEnd = null;
            foreach ($barbers as $barber) {
                if (empty($barberCtx[$barber->id]) || !$this->staffOffersService($barber, $svcId)) {
                    continue;
                }
                $sid = (string) $barber->id;
                $from = $freeAt[$sid] ?? $start->copy();
                if ($from->lt($start)) {
                    $from = $start->copy();
                }
                $fit = $this->firstFit($barberCtx[$barber->id], $from, $dur, $step);
                if (!$fit) {
                    continue;
                }
                $end = $fit->copy()->addMinutes($dur);
                if ($bestEnd === null || $end->lt($bestEnd)) {
                    $bestId = $barber->id;
                    $bestEnd = $end;
                }
            }
            if (!$bestId) {
                return null;
            }
            $freeAt[(string) $bestId] = $bestEnd;
            $pickedAnyJobs[] = $bestId;
            if ($bestEnd->gt($latestEnd)) {
                $latestEnd = $bestEnd->copy();
            }
        }

        $resolved = [];
        $anyJobIndex = 0;
        foreach ($plan['pairs'] as $pair) {
            if (!empty($pair['flexible']) && !empty($plan['any_sequential'])) {
                $resolved[] = ['staff_id' => $pickedAnySeq, 'service_id' => $pair['service_id']];
            } elseif (!empty($pair['flexible'])) {
                $resolved[] = ['staff_id' => $pickedAnyJobs[$anyJobIndex++] ?? $pair['staff_id'], 'service_id' => $pair['service_id']];
            } else {
                $resolved[] = ['staff_id' => $pair['staff_id'], 'service_id' => $pair['service_id']];
            }
        }

        return [
            'end' => $latestEnd,
            'duration' => (int) round($start->diffInMinutes($latestEnd)),
            'resolved' => $resolved,
        ];
    }

    /**
     * Staff-wise slots: named barbers use their own calendar; First available
     * is queued onto the next free real barber.
     */
    private function resolveStaffWiseSlots(string $date, string $day_name, array $plan, ?string $onlyStart = null): array
    {
        $durationMap = $this->durationLookup($plan['service_ids']);
        $step = $this->slotStepMinutes($plan['service_ids']);

        $namedContexts = [];
        foreach ($plan['named'] as $staffId => $svcIds) {
            $ctx = $this->getStaffWorkContext($date, $day_name, $staffId);
            if (($ctx['status'] ?? 0) !== 1) {
                return $ctx;
            }
            $namedContexts[$staffId] = $ctx;
        }

        $barbers = $this->realBarbers();
        $barberCtx = [];
        foreach ($barbers as $barber) {
            $ctx = $this->getStaffWorkContext($date, $day_name, $barber->id);
            if (($ctx['status'] ?? 0) === 1) {
                $barberCtx[$barber->id] = $ctx;
            }
        }

        $shopCtx = $this->getShopDayContext($date, $day_name);
        if (($shopCtx['status'] ?? 0) !== 1) {
            return $shopCtx;
        }

        $now = Carbon::now();
        $isToday = $date === Carbon::today()->toDateString();
        $earliest = $isToday ? $now->copy()->addMinutes(10) : null;

        $slots = [];
        $assignmentsByStart = [];
        $chairDuration = $this->planChairDuration($plan, $durationMap);
        $cursor = $shopCtx['open']->copy();

        if ($onlyStart) {
            $cursor = Carbon::parse($date . ' ' . $onlyStart);
        }

        while ($cursor->lt($shopCtx['close'])) {
            if ($earliest && $cursor->lt($earliest)) {
                $cursor->addMinutes($step);
                continue;
            }

            if ($chairDuration > 0 && $cursor->copy()->addMinutes($chairDuration)->gt($shopCtx['close'])) {
                if ($onlyStart) {
                    break;
                }
                $cursor->addMinutes($step);
                continue;
            }

            $packed = $this->tryPackAt($cursor, $plan, $durationMap, $namedContexts, $barberCtx, $barbers, $step);
            if ($packed && $packed['duration'] > 0 && !$packed['end']->gt($shopCtx['close'])) {
                $startKey = $cursor->format('H:i');
                $slots[] = [
                    'start' => $startKey,
                    'end'   => $packed['end']->format('H:i'),
                ];
                $assignmentsByStart[$startKey] = $packed['resolved'];
            }

            if ($onlyStart) {
                break;
            }
            $cursor->addMinutes($step);
        }

        return [
            'status' => 1,
            'slots' => $slots,
            'duration' => $chairDuration,
            'assignments' => $assignmentsByStart,
        ];
    }

    private function getShopDayContext(string $date, string $day_name): array
    {
        $slotShopDay = DB::table('tbl_slots')
            ->where('staff_id', 0)
            ->where('day_name', $day_name)
            ->first();

        if (!$slotShopDay) {
            return ['status' => 0, 'message' => 'Shop hours not configured for this day.'];
        }
        if ((int) $slotShopDay->is_day_off === 1) {
            return ['status' => 0, 'message' => 'Shop is Closed', 'day_name' => $day_name];
        }
        if (!$slotShopDay->open_time || !$slotShopDay->close_time) {
            return ['status' => 0, 'message' => 'Shop hours not configured for this day.'];
        }

        $fullClosure = DB::table('tbl_shop_closure')
            ->whereDate('date', $date)
            ->where('closure_status', 1)
            ->first();
        if ($fullClosure) {
            return ['status' => 0, 'message' => 'Shop is Closed'];
        }

        return [
            'status' => 1,
            'open'   => Carbon::parse($date . ' ' . $slotShopDay->open_time),
            'close'  => Carbon::parse($date . ' ' . $slotShopDay->close_time),
            'row'    => $slotShopDay,
        ];
    }

    private function getStaffWorkContext(string $date, string $day_name, $staff_id): array
    {
        $shop = $this->getShopDayContext($date, $day_name);
        if (($shop['status'] ?? 0) !== 1) {
            return $shop;
        }

        $shopOpen = $shop['open'];
        $shopClose = $shop['close'];
        $slotShopDay = $shop['row'];

        $slotStaffDay = DB::table('tbl_slots')
            ->where('staff_id', $staff_id)
            ->where('day_name', $day_name)
            ->first();

        if (!$slotStaffDay) {
            $workingSource = collect([(object) [
                'break_start' => $slotShopDay->open_time,
                'break_end'   => $slotShopDay->close_time,
            ]]);
            $staffBreakRows = collect();
            $slotStaffDay = (object) [
                'id' => 0,
                'staff_id' => $staff_id,
                'open_time' => $slotShopDay->open_time,
                'close_time' => $slotShopDay->close_time,
            ];
        } elseif ((int) $slotStaffDay->is_day_off === 1) {
            return [
                'status'   => 0,
                'message'  => 'No available slots for this date.',
                'day_name' => $day_name,
            ];
        } else {
            $type1Rows = DB::table('tbl_breaks')
                ->where('slot_id', $slotStaffDay->id)
                ->where('staff_id', $slotStaffDay->staff_id)
                ->where('break_type', 1)
                ->orderBy('break_start')
                ->get();

            $type2Rows = DB::table('tbl_breaks')
                ->where('slot_id', $slotStaffDay->id)
                ->where('staff_id', $slotStaffDay->staff_id)
                ->where(function ($q) {
                    $q->where('break_type', 2)->orWhereNull('break_type');
                })
                ->orderBy('break_start')
                ->get();

            $staffBreakRows = collect();
            if ($type1Rows->isNotEmpty()) {
                $workingSource = $type1Rows;
            } elseif ($type2Rows->isNotEmpty()) {
                $workingSource = $type2Rows;
            } elseif ($slotStaffDay->open_time && $slotStaffDay->close_time) {
                $workingSource = collect([(object) [
                    'break_start' => $slotStaffDay->open_time,
                    'break_end'   => $slotStaffDay->close_time,
                ]]);
            } else {
                $workingSource = collect([(object) [
                    'break_start' => $slotShopDay->open_time,
                    'break_end'   => $slotShopDay->close_time,
                ]]);
            }
        }

        $workingHours = $workingSource->map(function ($b) use ($date) {
            return [
                'start' => Carbon::parse($date . ' ' . $b->break_start),
                'end'   => Carbon::parse($date . ' ' . $b->break_end),
            ];
        })->filter(fn ($w) => $w['start']->lt($w['end']))->values();

        $workingHours = $workingHours->map(function ($w) use ($shopOpen, $shopClose) {
            $start = $w['start']->copy()->max($shopOpen);
            $end   = $w['end']->copy()->min($shopClose);
            return ['start' => $start, 'end' => $end];
        })->filter(fn ($w) => $w['start']->lt($w['end']))
            ->sortBy(fn ($w) => $w['start']->timestamp)
            ->values();

        if ($workingHours->isEmpty()) {
            return ['status' => 0, 'message' => 'No available slots for this date.'];
        }

        $partialClosures = DB::table('tbl_shop_closure')
            ->whereDate('date', $date)
            ->where('closure_status', 0)
            ->get()
            ->map(function ($c) use ($date) {
                return $this->parseClosureWindow($date, $c->start_time, $c->end_time);
            })
            ->filter()
            ->values();

        $personalBreaks = $staffBreakRows->map(function ($b) use ($date) {
            return $this->parseClosureWindow($date, $b->break_start, $b->break_end);
        })->filter()->values();

        $additionalBreaks = DB::table('tbl_breaks')
            ->where('staff_id', $slotStaffDay->staff_id)
            ->where('slot_id', 0)
            ->where(function ($query) use ($date) {
                $query->whereDate('break_date', $date)
                    ->orWhere(function ($legacy) use ($date) {
                        $legacy->whereNull('break_date')->whereDate('created_at', $date);
                    });
            })
            ->get()
            ->map(function ($b) use ($date) {
                return $this->parseClosureWindow($date, $b->break_start, $b->break_end);
            })->filter()->values();

        $bookedSlots = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
            ->where('a.date', $date)
            ->where('s.staff_id', $staff_id)
            ->whereIn('a.status', [0, 1, 3, 4])
            ->select('a.slot_start', 'a.slot_end')
            ->distinct()
            ->get()
            ->map(function ($b) use ($date) {
                return $this->parseClosureWindow($date, $b->slot_start, $b->slot_end);
            })->filter()->values();

        return [
            'status' => 1,
            'workingHours' => $workingHours->all(),
            'partialClosures' => $partialClosures,
            'busyRanges' => $personalBreaks->merge($additionalBreaks)->merge($bookedSlots)->values(),
        ];
    }

    /**
     * Build bookable slots using:
     * Shop hours → Staff hours → Closures → Bookings → Service duration
     */
    private function buildAvailableSlots(string $date, string $day_name, $staff_id, $service_ids, bool $expandDuration = false, ?int $slotStep = null): array
    {
        // 1) Shop working hours
        $slotShopDay = DB::table('tbl_slots')
            ->where('staff_id', 0)
            ->where('day_name', $day_name)
            ->first();

        if (!$slotShopDay) {
            return ['status' => 0, 'message' => 'Shop hours not configured for this day.'];
        }

        if ((int) $slotShopDay->is_day_off === 1) {
            return ['status' => 0, 'message' => 'Shop is Closed', 'day_name' => $day_name];
        }

        if (!$slotShopDay->open_time || !$slotShopDay->close_time) {
            return ['status' => 0, 'message' => 'Shop hours not configured for this day.'];
        }

        $shopOpen  = Carbon::parse($date . ' ' . $slotShopDay->open_time);
        $shopClose = Carbon::parse($date . ' ' . $slotShopDay->close_time);

        // Full-day shop closure
        $fullClosure = DB::table('tbl_shop_closure')
            ->whereDate('date', $date)
            ->where('closure_status', 1)
            ->first();

        if ($fullClosure) {
            return ['status' => 0, 'message' => 'Shop is Closed'];
        }

        // 2) Staff schedule
        $slotStaffDay = DB::table('tbl_slots')
            ->where('staff_id', $staff_id)
            ->where('day_name', $day_name)
            ->first();

        if (!$slotStaffDay) {
            return ['status' => 0, 'message' => 'No available slots for this date.'];
        }

        if ((int) $slotStaffDay->is_day_off === 1) {
            return [
                'status'   => 0,
                'message'  => 'No available slots for this date.',
                'day_name' => $day_name,
            ];
        }

        // Prefer break_type = 1 (admin working hours).
        // Legacy staff seed saved hours as break_type = 2 — treat those as hours when type 1 is empty.
        $type1Rows = DB::table('tbl_breaks')
            ->where('slot_id', $slotStaffDay->id)
            ->where('staff_id', $slotStaffDay->staff_id)
            ->where('break_type', 1)
            ->orderBy('break_start')
            ->get();

        $type2Rows = DB::table('tbl_breaks')
            ->where('slot_id', $slotStaffDay->id)
            ->where('staff_id', $slotStaffDay->staff_id)
            ->where(function ($q) {
                $q->where('break_type', 2)->orWhereNull('break_type');
            })
            ->orderBy('break_start')
            ->get();

        $usedLegacyType2AsHours = false;

        if ($type1Rows->isNotEmpty()) {
            // Admin Working Hours uses break_type = 1.
            // Ignore same-slot type-2 rows: those are often leftover seed data
            // (09-13 / 14-18) that incorrectly blanked mornings when treated as breaks.
            $workingSource = $type1Rows;
            $staffBreakRows = collect();
        } elseif ($type2Rows->isNotEmpty()) {
            // Legacy staff creation saved schedule blocks as break_type = 2
            $workingSource = $type2Rows;
            $usedLegacyType2AsHours = true;
            $staffBreakRows = collect();
        } elseif ($slotStaffDay->open_time && $slotStaffDay->close_time) {
            $workingSource = collect([(object) [
                'break_start' => $slotStaffDay->open_time,
                'break_end'   => $slotStaffDay->close_time,
            ]]);
            $staffBreakRows = collect();
        } else {
            // Fall back to shop window only
            $workingSource = collect([(object) [
                'break_start' => $slotShopDay->open_time,
                'break_end'   => $slotShopDay->close_time,
            ]]);
            $staffBreakRows = collect();
        }

        $workingHours = $workingSource->map(function ($b) use ($date) {
            return [
                'start' => Carbon::parse($date . ' ' . $b->break_start),
                'end'   => Carbon::parse($date . ' ' . $b->break_end),
            ];
        })->filter(fn ($w) => $w['start']->lt($w['end']))->values();

        // Intersect each staff block with shop open/close
        $workingHours = $workingHours->map(function ($w) use ($shopOpen, $shopClose) {
            $start = $w['start']->copy()->max($shopOpen);
            $end   = $w['end']->copy()->min($shopClose);
            return ['start' => $start, 'end' => $end];
        })->filter(fn ($w) => $w['start']->lt($w['end']))
            ->sortBy(fn ($w) => $w['start']->timestamp)
            ->values();

        if ($workingHours->isEmpty()) {
            return ['status' => 0, 'message' => 'No available slots for this date.'];
        }

        // 3) Partial shop closures + staff breaks + ad-hoc breaks
        $partialClosures = DB::table('tbl_shop_closure')
            ->whereDate('date', $date)
            ->where('closure_status', 0)
            ->get()
            ->map(function ($c) use ($date) {
                return $this->parseClosureWindow($date, $c->start_time, $c->end_time);
            })
            ->filter()
            ->values();

        $personalBreaks = $staffBreakRows->map(function ($b) use ($date) {
            return $this->parseClosureWindow($date, $b->break_start, $b->break_end);
        })->filter()->values();

        $additionalBreaks = DB::table('tbl_breaks')
            ->where('staff_id', $slotStaffDay->staff_id)
            ->where('slot_id', 0)
            ->where(function ($query) use ($date) {
                $query->whereDate('break_date', $date)
                    ->orWhere(function ($legacy) use ($date) {
                        $legacy->whereNull('break_date')->whereDate('created_at', $date);
                    });
            })
            ->get()
            ->map(function ($b) use ($date) {
                return $this->parseClosureWindow($date, $b->break_start, $b->break_end);
            })->filter()->values();

        // 4) Existing appointments for this staff/date
        $bookedRaw = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as s', 'a.appointment_id', '=', 's.appointment_id')
            ->where('a.date', $date)
            ->where('s.staff_id', $staff_id)
            ->whereIn('a.status', [0, 1, 3, 4])
            ->select('a.slot_start', 'a.slot_end')
            ->distinct()
            ->get();

        $bookedSlots = $bookedRaw->map(function ($b) use ($date) {
            return $this->parseClosureWindow($date, $b->slot_start, $b->slot_end);
        })->filter()->values();

        // Busy ranges use interval overlap (bookings / breaks)
        $busyRanges = $personalBreaks
            ->merge($additionalBreaks)
            ->merge($bookedSlots)
            ->values();

        // 5) Service duration
        $serviceIdsArray = is_array($service_ids)
            ? array_values(array_filter($service_ids, fn ($id) => $id !== '' && $id !== null))
            : $this->parseIdList($service_ids);

        $uniqueIds = array_values(array_unique($serviceIdsArray));
        $durationMap = !empty($uniqueIds)
            ? Service::whereIn('id', $uniqueIds)->pluck('duration', 'id')
            : collect();

        $totalDuration = 0;
        if ($expandDuration) {
            foreach ($serviceIdsArray as $id) {
                $totalDuration += (int) ($durationMap[$id] ?? 0);
            }
        } else {
            $totalDuration = (int) $durationMap->sum();
        }

        if ($totalDuration <= 0) {
            $totalDuration = 30;
        }

        $step = (int) ($slotStep ?? 0);
        if ($step < 5) {
            $step = $this->slotStepMinutes($serviceIdsArray);
        }
        if ($step < 5) {
            $step = 20;
        }

        $now = Carbon::now();
        $isToday = $date === Carbon::today()->toDateString();
        $earliestBookable = $isToday ? $now->copy()->addMinutes(10) : null;

        $slots = [];

        foreach ($workingHours as $workingBlock) {
            $blockStart = $workingBlock['start']->copy();
            $blockEnd   = $workingBlock['end']->copy();

            // Start times follow the service duration grid (e.g. :00/:20/:40).
            // Occupied length is the selected service total for this barber.
            $cursor = $blockStart->copy();

            while (true) {
                $slotEnd = $cursor->copy()->addMinutes($totalDuration);
                if ($slotEnd->gt($blockEnd)) {
                    break;
                }

                if ($earliestBookable && $cursor->lt($earliestBookable)) {
                    $cursor->addMinutes($step);
                    continue;
                }

                $blockedByClosure = $partialClosures->contains(function ($closure) use ($cursor) {
                    return $cursor->gte($closure['start']) && $cursor->lt($closure['end']);
                });

                $blockedByBusy = $busyRanges->contains(function ($b) use ($cursor, $slotEnd) {
                    return $cursor->lt($b['end']) && $slotEnd->gt($b['start']);
                });

                if (!$blockedByClosure && !$blockedByBusy) {
                    $slots[] = [
                        'start' => $cursor->format('H:i'),
                        'end'   => $slotEnd->format('H:i'),
                    ];
                }

                $cursor->addMinutes($step);
            }
        }

        $slots = collect($slots)
            ->unique(fn ($s) => $s['start'] . '-' . $s['end'])
            ->sortBy('start')
            ->values()
            ->all();

        $allBlockedWindows = $partialClosures->merge($busyRanges)->values();

        return [
            'status'   => 1,
            'slots'    => $slots,
            'duration' => $totalDuration,
            'booked'   => $bookedRaw,
            'workingHours' => collect($workingHours)->map(fn ($wh) => [
                'start' => $wh['start']->format('H:i'),
                'end'   => $wh['end']->format('H:i'),
            ])->all(),
            'breaks' => $allBlockedWindows->map(fn ($b) => [
                'start' => $b['start']->format('H:i'),
                'end'   => $b['end']->format('H:i'),
            ])->all(),
            'shopHours' => [
                'start' => $shopOpen->format('H:i'),
                'end'   => $shopClose->format('H:i'),
            ],
            'closures' => $partialClosures->map(fn ($b) => [
                'start' => $b['start']->format('H:i'),
                'end'   => $b['end']->format('H:i'),
            ])->all(),
            'legacy_type2_as_hours' => $usedLegacyType2AsHours,
        ];
    }

    /**
     * Parse a same-day time window into Carbon start/end.
     * Never compares times as strings.
     *
     * Handles inverted / 12h-clock bugs (e.g. end stored as 00:55 instead of 12:55)
     * by preferring end+12h when that lands later the same day.
     */
    private function parseClosureWindow(string $date, $startTime, $endTime): ?array
    {
        $start = $this->parseTimeOnDate($date, $startTime);
        $end   = $this->parseTimeOnDate($date, $endTime);

        if (!$start || !$end) {
            return null;
        }

        if (!$end->gt($start)) {
            $plus12 = $end->copy()->addHours(12);
            if ($plus12->gt($start) && $plus12->isSameDay($start)) {
                $end = $plus12;
            } else {
                // Overnight window (e.g. 22:00 → 02:00)
                $end = $end->copy()->addDay();
            }
        }

        if (!$end->gt($start)) {
            return null;
        }

        return [
            'start' => $start,
            'end'   => $end,
        ];
    }

    /**
     * Build a Carbon datetime from Y-m-d + any time-ish string from DB/UI.
     */
    private function parseTimeOnDate(string $date, $time): ?Carbon
    {
        if ($time === null) {
            return null;
        }

        $time = trim((string) $time);
        if ($time === '') {
            return null;
        }

        try {
            return Carbon::parse($date . ' ' . $time);
        } catch (\Throwable $e) {
            try {
                return Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . substr($time, 0, 5));
            } catch (\Throwable $e2) {
                return null;
            }
        }
    }







    public function get_appointment_details(Request $request)
    {
        $staff_id = $request->input('staff_id');
        $service_id = $request->input('service_id');
        $date = $request->input('date');
        $groupPicks = $request->input('group_picks');

        $serviceIdsArray = $this->parseIdList($service_id);
        $originalStaffIds = $this->parseIdList($staff_id);
        $staffIds = $originalStaffIds;

        // If we're previewing in staff-wise mode, resolve the actual barbers
        // using the selected slot picks (no appointment is created here).
        if (!empty($date) && !empty($groupPicks)) {
            if (is_string($groupPicks)) {
                $decoded = json_decode($groupPicks, true);
                $groupPicks = is_array($decoded) ? $decoded : [];
            }

            if (is_array($groupPicks) && !empty($groupPicks)) {
                $plan = $this->buildStaffBookingPlan($staffIds, $serviceIdsArray);
                if ($plan) {
                    $dayName = Carbon::parse($date)->format('l');
                    $checked = $this->validateAndResolveGroupPicks($date, $dayName, $plan, $groupPicks);
                    if ($checked && !empty($checked['resolved'])) {
                        $resolvedRows = $checked['resolved'];
                        $staffIds = array_values(array_unique(array_map(fn ($row) => $row['staff_id'], $resolvedRows)));
                    }
                }
            }
        }

        $qtyMap = array_count_values($serviceIdsArray);
        $uniqueServiceIds = array_keys($qtyMap);
        $serviceRows = Service::whereIn('id', $uniqueServiceIds)->get()->keyBy('id');

        $services = [];
        foreach ($qtyMap as $sid => $qty) {
            $row = $serviceRows->get($sid);
            if (!$row) {
                continue;
            }
            $services[] = [
                'id' => $row->id,
                'service_name' => $row->service_name,
                'duration' => $row->duration,
                'price' => $row->price,
                'qty' => $qty,
            ];
        }

        $staffList = User::whereIn('id', $staffIds)->get()->keyBy('id');
        $staffOrdered = [];
        foreach ($staffIds as $sid) {
            if ($staffList->has($sid)) {
                $staffOrdered[] = $staffList->get($sid);
            }
        }

        $defaultGst = Gst::where('default_status', 1)->first();

        return response()->json([
            'status' => 1,
            'services' => $services,
            'staff' => $staffOrdered[0] ?? null,
            'staff_list' => $staffOrdered,
            'staff_display' => $this->buildStaffDisplayList($originalStaffIds),
            'gst' => $defaultGst ? [
                'name' => $defaultGst->name,
                'rate' => (float) $defaultGst->rate,
                'tax_type' => $defaultGst->tax_type,
            ] : null,
        ]);
    }

    /**
     * Confirm screen: keep "First available" labels from the user's selection.
     */
    private function buildStaffDisplayList(array $staffIds): array
    {
        if (empty($staffIds)) {
            return [];
        }

        $realIds = [];
        foreach ($staffIds as $sid) {
            if (!$this->isFlexibleStaffId($sid)) {
                $realIds[] = $sid;
            }
        }

        $users = !empty($realIds)
            ? User::whereIn('id', array_values(array_unique($realIds)))->get()->keyBy('id')
            : collect();

        $display = [];
        $sawFlexible = false;
        $seenNamed = [];
        foreach ($staffIds as $sid) {
            if ($this->isFlexibleStaffId($sid)) {
                if ($sawFlexible) {
                    continue;
                }
                $sawFlexible = true;
                $display[] = [
                    'name' => 'First available',
                    'flexible' => true,
                    'profile_img' => null,
                    'profile_image_url' => asset('assets/img/user.svg'),
                ];
                continue;
            }

            $sidKey = (string) $sid;
            if (isset($seenNamed[$sidKey])) {
                continue;
            }
            $seenNamed[$sidKey] = true;

            $user = $users->get($sid) ?? $users->get((int) $sid) ?? $users->get((string) $sid);
            $display[] = [
                'name' => $user->name ?? 'Barber',
                'flexible' => false,
                'profile_img' => $user->profile_img ?? null,
                'profile_image_url' => $user->profile_image_url,
            ];
        }

        return $display;
    }

    /**
     * Map each booked service line to its assigned barber for database storage.
     */
    private function buildServiceLineAssignments(
        array $plan,
        array $serviceIdsArray,
        array $resolvedRows,
        string $date,
        string $dayName,
        ?string $slotStart
    ): array {
        $pairs = $plan['pairs'] ?? [];
        $flexStaffId = $this->getFlexibleStaffUserId();
        $assignments = [];

        foreach ($serviceIdsArray as $index => $serviceId) {
            $pair = $pairs[$index] ?? null;
            $requestedRaw = $pair['staff_id'] ?? null;
            $isFirstAvailable = !empty($pair['flexible']) || $this->isFlexibleStaffId($requestedRaw);

            $assignedStaffId = null;
            if (isset($resolvedRows[$index]['staff_id']) && is_numeric($resolvedRows[$index]['staff_id'])) {
                $assignedStaffId = (int) $resolvedRows[$index]['staff_id'];
            } elseif (!$isFirstAvailable && is_numeric($requestedRaw)) {
                $assignedStaffId = (int) $requestedRaw;
            } elseif ($isFirstAvailable && $slotStart) {
                $assignedStaffId = $this->resolveFlexibleStaffForService(
                    $date,
                    $dayName,
                    $slotStart,
                    (string) $serviceId,
                    $plan
                );
            }

            if (!$assignedStaffId) {
                return [];
            }

            $requestedStaffId = null;
            if ($isFirstAvailable) {
                $requestedStaffId = is_numeric($requestedRaw) ? (int) $requestedRaw : $flexStaffId;
            } elseif (is_numeric($requestedRaw)) {
                $requestedStaffId = (int) $requestedRaw;
            }

            $assignments[] = [
                'service_id' => $serviceId,
                'staff_id' => $assignedStaffId,
                'requested_staff_id' => $requestedStaffId,
                'is_first_available' => $isFirstAvailable,
            ];
        }

        return $assignments;
    }

    private function resolveFlexibleStaffForService(
        string $date,
        string $dayName,
        string $slotStart,
        string $serviceId,
        array $plan
    ): ?int {
        $durationMap = $this->durationLookup($plan['service_ids'] ?? []);
        $step = $this->slotStepMinutes($plan['service_ids'] ?? []);
        $barbers = $this->realBarbers();
        $barberCtx = [];
        foreach ($barbers as $barber) {
            $ctx = $this->getStaffWorkContext($date, $dayName, $barber->id);
            if (($ctx['status'] ?? 0) === 1) {
                $barberCtx[$barber->id] = $ctx;
            }
        }

        $start = Carbon::parse($date . ' ' . $slotStart);

        if (!empty($plan['any_sequential'])) {
            $found = $this->resolveFlexibleBarberAt(
                $start,
                $plan['any_sequential'],
                $durationMap,
                $barberCtx,
                $barbers,
                $step
            );
            if ($found && !empty($found['staff_id'])) {
                return (int) $found['staff_id'];
            }
        }

        foreach ($barbers as $barber) {
            if (empty($barberCtx[$barber->id]) || !$this->staffOffersService($barber, $serviceId)) {
                continue;
            }
            $dur = $this->durationOf($durationMap, $serviceId) ?: 30;
            $end = $start->copy()->addMinutes($dur);
            if ($this->timeFitsStaff($barberCtx[$barber->id], $start, $end)) {
                return (int) $barber->id;
            }
        }

        return null;
    }

    private function getFlexibleStaffUserId(): ?int
    {
        static $flexId = false;
        if ($flexId !== false) {
            return $flexId;
        }

        $id = User::query()
            ->where('role', 'Staff')
            ->where(function ($q) {
                $q->whereRaw('LOWER(name) = ?', ['first available'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['first available%']);
            })
            ->value('id');

        $flexId = $id ? (int) $id : null;
        return $flexId;
    }


    public function submit_appointment_details(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'string', 'max:255'],
            'service_id' => ['required', 'string', 'max:255'],
            'slot_start' => ['required', 'date_format:H:i,H:i:s'],
            'slot_end' => ['required', 'date_format:H:i,H:i:s'],
            'phone_number' => ['required', 'string', 'max:25', 'regex:/^\+?[0-9().\-\s]+$/'],
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:150'],
            'note' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'group_picks' => ['nullable'],
        ]);
        if (!\App\Support\PhoneNumber::isValid($validated['phone_number'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'phone_number' => ['Enter a valid phone number with 7 to 15 digits.'],
            ]);
        }

        $staff_id      = $request->input('staff_id');
        $service_id    = $request->input('service_id'); // comma separated
        $slot_start    = $request->input('slot_start');
        $slot_end      = $request->input('slot_end');
        $phone_number  = $request->input('phone_number');
        $full_name     = $request->input('full_name');
        $email         = $request->input('email');
        $note          = $request->input('note');
        $date          = $request->input('date'); // should come from frontend
        $total_amt          = $request->input('total_amt'); // should come from frontend

        $staffIds = $this->parseIdList($staff_id);
        $serviceIdsArray = $this->parseIdList($service_id);
        $plan = $this->buildStaffBookingPlan($staffIds, $serviceIdsArray);

        if (!$plan) {
            return response()->json([
                'status' => 0,
                'message' => 'Please select a service and barber.',
            ]);
        }

        $dayName = Carbon::parse($date)->format('l');
        $groupPicks = $request->input('group_picks');
        if (is_string($groupPicks)) {
            $decoded = json_decode($groupPicks, true);
            $groupPicks = is_array($decoded) ? $decoded : [];
        }

        $resolvedRows = [];
        if (is_array($groupPicks) && !empty($groupPicks)) {
            $checked = $this->validateAndResolveGroupPicks($date, $dayName, $plan, $groupPicks);
            if (!$checked) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Selected slot is no longer available for all selected barbers. Please choose another time.',
                ]);
            }
            $slot_start = $checked['start'];
            $slot_end = $checked['end'];
            $resolvedRows = $checked['resolved'] ?? [];
        } else {
            $reqStart = Carbon::parse($date . ' ' . $slot_start)->format('H:i');
            $resolved = $this->resolveStaffWiseSlots($date, $dayName, $plan, $reqStart);

            if (($resolved['status'] ?? 0) !== 1 || empty($resolved['slots'])) {
                return response()->json([
                    'status'  => 0,
                    'message' => $resolved['message'] ?? 'Selected slot is no longer available for all selected barbers. Please choose another time.',
                ]);
            }

            $slotStillOpen = collect($resolved['slots'])->contains(fn ($slot) => $slot['start'] === $reqStart);
            if (!$slotStillOpen) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Selected slot is no longer available for all selected barbers. Please choose another time.',
                ]);
            }

            $resolvedRows = $resolved['assignments'][$reqStart] ?? [];
        }

        $lineAssignments = $this->buildServiceLineAssignments(
            $plan,
            $serviceIdsArray,
            $resolvedRows,
            $date,
            $dayName,
            $slot_start
        );

        if (count($lineAssignments) !== count($serviceIdsArray)) {
            return response()->json([
                'status' => 0,
                'message' => 'Could not assign all services to barbers. Please choose another time.',
            ]);
        }

        DB::beginTransaction();
        try {

            $normalizedPhone = \App\Support\PhoneNumber::digits($phone_number);
            $Customer = Customer::query()->get(['customer_id', 'customer_name', 'customer_phone', 'customer_email'])
                ->first(fn (Customer $customer) => \App\Support\PhoneNumber::digits((string) $customer->customer_phone) === $normalizedPhone);

            if (!$Customer) {
                // If not exists, create new customer
                $Customer = Customer::create([
                    'customer_name'   => $full_name,
                    'customer_phone'  => $phone_number,
                    'customer_email'  => $email,
                    'customer_status' => 1,
                ]);
            }

            // 1. Create appointment
            $appointment = Appointment::create([
                "customer_id" => $Customer->customer_id,
                "name" => $full_name,
                "email" => $email,
                "phone" => $phone_number,
                "total_amt" => $total_amt,
                "date"       => $date, // e.g. 2025-09-30
                "slot_start" => $slot_start,
                "slot_end"   => $slot_end,
                "notes"      => $note,
                "status"     => 0, // Pending by default
            ]);

 $serviceIdsNameArray = [];

            // 2. Insert appointment services (one row per service line with its barber)
            foreach ($serviceIdsArray as $index => $sid) {
                $line = $lineAssignments[$index];
                $assignedStaff = $line['staff_id'];
                $requestedStaff = $line['requested_staff_id'];
                $isFirstAvailable = !empty($line['is_first_available']);

                $serviceRow = [
                    "appointment_id"     => $appointment->appointment_id,
                    "service_id"         => $sid,
                    "staff_id"           => $assignedStaff,
                    "product_commission" => 0,
                    "service_commission" => 0,
                    "service_tip"        => 0,
                    "total_amt"          => 0,
                    "status"             => 0,
                ];

                if (Schema::hasColumn('tbl_appointment_service', 'requested_staff_id')) {
                    $serviceRow['requested_staff_id'] = $requestedStaff;
                }
                if (Schema::hasColumn('tbl_appointment_service', 'is_first_available')) {
                    $serviceRow['is_first_available'] = $isFirstAvailable ? 1 : 0;
                }

                AppointmentService::create($serviceRow);

                  $serviceName = Service::where('id', $sid)->value('service_name');
                if ($serviceName) {
                    $serviceIdsNameArray[] = $serviceName;
                }
            }

            DB::commit();

            $assignedStaffIds = array_values(array_unique(array_column($lineAssignments, 'staff_id')));
            $StaffName = User::whereIn('id', $assignedStaffIds)->pluck('name')->implode(', ');
            $serviceNamesString = implode(',', $serviceIdsNameArray);
            $appointment->refresh();

            $emailQueued = false;
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailData = [
                    'customer_name' => $full_name,
                    'service' => $serviceNamesString,
                    'staff' => $StaffName,
                    'date'       => Carbon::parse($date)->format('d M Y'),
                    'slot_start' => Carbon::parse($slot_start)->format('h:i A'),
                    'slot_end'   => Carbon::parse($slot_end)->format('h:i A'),
                    'email' => $email,
                    'subject' => 'Appointment Confirmation - Rosenthal Barbershop',
                    'booking_code' => $appointment->booking_code,
                    'track_url' => route('track-booking.show', ['code' => $appointment->booking_code]),
                ];

                try {
                    // Queue only — never send SMTP inside the HTTP request (keeps loader fast)
                    Mail::to($email)->queue(new WelcomeMail($emailData));
                    $emailQueued = true;
                } catch (\Throwable $mailEx) {
                    Log::error('Failed to queue booking confirmation email: ' . $mailEx->getMessage(), [
                        'appointment_id' => $appointment->appointment_id,
                        'email' => $email,
                    ]);
                }
            }

            return response()->json([
                "status" => 1,
                "message" => "Appointment submitted successfully",
                "appointment_id" => $appointment->appointment_id,
                "booking_code" => $appointment->booking_code,
                "email_sent" => $emailQueued,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                "status" => 0,
                "message" => "Error submitting appointment: " . $e->getMessage(),
                "error"   => $e->getMessage()
            ], 500);
        }
    }
}
