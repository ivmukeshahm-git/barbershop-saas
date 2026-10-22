<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breaks;
use App\Models\Slot;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkinghoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index()
    {
        $staffId = Auth::id();

        $slots = DB::table('tbl_slots as s')
            ->where('s.staff_id', $staffId)
            ->select('s.id as slot_id', 's.day_name', 's.is_day_off', 's.open_time', 's.close_time');
        CurrentShop::constrain($slots, 's.shop_id');
        $slots = $slots->get();

        $daysWithBreaks = [];

        foreach ($slots as $slot) {
            // Get working hours (break_type = 1) for this slot
            $workingHours = DB::table('tbl_breaks')
                ->where('slot_id', $slot->slot_id)
                ->where('staff_id', $staffId)
                ->where('break_type', 1) // Working hours
                ->select('id', 'break_start', 'break_end')
                ->orderBy('break_start', 'asc');
            CurrentShop::constrain($workingHours, 'shop_id');
            $workingHours = $workingHours->get();

            $daysWithBreaks[] = [
                'is_day_off' => $slot->is_day_off,
                'slot_id' => $slot->slot_id,
                'day_name' => $slot->day_name,
                'open_time' => $slot->open_time,
                'close_time' => $slot->close_time,
                'breaks'   => $workingHours // These are actually working hours
            ];
        }

        return view('admin.working-hours.index', compact('slots', 'daysWithBreaks'));
    }
    
    
     public function updateFullDayOffStaff(Request $request)
    {
        $dayId = $request->input('dayId');
        $isOff = $request->input('off'); // boolean true/false from JS

        // Validation (optional)
        if (!in_array($isOff, ['0', '1', 0, 1, true, false], true)) {
            return response()->json(['status' => 0, 'message' => 'Invalid off value']);
        }


        // Update the slot row(s) for the given day
        $updated = DB::table('tbl_slots')
            ->where('id', $dayId);
        CurrentShop::constrain($updated, 'shop_id');
        $updated = $updated->update([
                'is_day_off' => $isOff,
                'updated_at' => now(),
            ]);

        if ($updated) {
            return response()->json(['status' => 1, 'message' => 'Day off status updated successfully']);
        } else {
            return response()->json(['status' => 0, 'message' => 'Failed to update day off status']);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'day_id'     => 'required|exists:tbl_slots,id',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        $staffId = Auth::id();
        $day_id     = $request->day_id;
        $start_time = $request->start_time;
        $end_time   = $request->end_time;

        // Check for overlapping time slots (working hours)
        $exists = Breaks::where('slot_id', $day_id)
            ->where('staff_id', $staffId)
            ->where('break_type', 1) // Only check working hours
            ->where(function ($q) use ($start_time, $end_time) {
                $q->where(function ($qq) use ($start_time, $end_time) {
                    $qq->where('break_start', '<', $end_time)
                        ->where('break_end',   '>', $start_time);
                });
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 0,
                'message' => 'This time slot overlaps with an existing working hour. Please choose another time.'
            ]);
        }

        // Create new working hour slot
        Breaks::create([
            'slot_id'     => $day_id,
            'staff_id'    => $staffId,
            'break_start' => $start_time,
            'break_end'   => $end_time,
            'break_type'  => 1, // 1 = Working Hours
        ]);

        // Update slot to mark as not day off
        Slot::where('id', $day_id)->update([
            'is_day_off' => 0,
        ]);

        return response()->json([
            'status'  => 1,
            'message' => 'Working Hours Added Successfully.'
        ]);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        $staffId = Auth::id();
        $break = Breaks::findOrFail($id);

        $start_time = $request->start_time;
        $end_time   = $request->end_time;

        // Check for overlapping time slots
        $exists = Breaks::where('slot_id', $break->slot_id)
            ->where('staff_id', $staffId)
            ->where('break_type', 1) // Only check working hours
            ->where('id', '!=', $id) // exclude current record
            ->where(function ($q) use ($start_time, $end_time) {
                $q->where(function ($qq) use ($start_time, $end_time) {
                    $qq->where('break_start', '<', $end_time)
                        ->where('break_end',   '>', $start_time);
                });
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 0,
                'message' => 'This time slot overlaps with an existing working hour. Please choose another time.'
            ]);
        }

        // Update the working hour
        $break->update([
            'break_start' => $start_time,
            'break_end'   => $end_time,
        ]);

        return response()->json([
            'status'  => 1,
            'message' => 'Working Hours Updated Successfully.'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $break = Breaks::findOrFail($id);
        $slotId = $break->slot_id;
        $staffId = $break->staff_id;
        
        $break->delete();

        // Check if there are any remaining working hours for this slot
        $hasWorkingHours = Breaks::where('slot_id', $slotId)
            ->where('staff_id', $staffId)
            ->where('break_type', 1)
            ->exists();

        // If no working hours left, mark as day off
        if (!$hasWorkingHours) {
            Slot::where('id', $slotId)->update([
                'is_day_off' => 1,
            ]);
        }

        return response()->json(['status' => 1, 'message' => 'Working hours deleted successfully.']);
    }
}
