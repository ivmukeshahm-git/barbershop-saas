<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breaks;
use App\Models\Slot;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slots = Slot::where('staff_id', 0)->with('breaks')->get()->keyBy('day_name');

        return view('admin.slot.index', compact('slots'));
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
        $days = $request->input('days', []); // default empty array if not present


        foreach ($days as $dayName => $dayData) {
            // Get existing or new slot
            $slot = Slot::firstOrNew([
                'day_name' => $dayName,
                'staff_id' => 0,
            ]);

            // Normalize is_day_off (handle "1" or "on")
            $slot->is_day_off = (!empty($dayData['is_day_off']) && ($dayData['is_day_off'] === '1' || $dayData['is_day_off'] === 'on')) ? 1 : 0;

            // Update open & close time (if provided in request)
            $slot->open_time  = $dayData['open_time'] ?? null;
            $slot->close_time = $dayData['close_time'] ?? null;
            $slot->save();

            // Delete old breaks
            $slot->breaks()->delete();

            // Handle breaks array safely
            if (!empty($dayData['breaks']) && is_array($dayData['breaks'])) {
                foreach ($dayData['breaks'] as $break) {
                    $breakStart = $break['break_start'] ?? null;
                    $breakEnd   = $break['break_end'] ?? null;

                    // Flatten if array
                    if (is_array($breakStart)) $breakStart = reset($breakStart);
                    if (is_array($breakEnd)) $breakEnd = reset($breakEnd);

                    if (!empty($breakStart) && !empty($breakEnd)) {
                        $slot->breaks()->create([
                            'break_start' => $breakStart,
                            'break_end'   => $breakEnd,
                            'break_type'   => 1,
                        ]);
                    }
                }
            }
        }


        return response()->json([
            'status' => 1,
            'message' => 'Slot configuration stored successfully.'
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
        $days = $request->input('days', []); // default empty array if not present

        foreach ($days as $dayName => $dayData) {
            // Update or create slot for the day
            $slot = Slot::updateOrCreate(
                ['day_name' => $dayName],
                ['is_day_off' => isset($dayData['is_day_off']) && $dayData['is_day_off'] ? true : false]
            );

            // Delete existing breaks for the day to replace fresh
            $slot->breaks()->delete();

            // Check for breaks and insert
            if (!empty($dayData['breaks']) && is_array($dayData['breaks'])) {
                foreach ($dayData['breaks'] as $break) {
                    if (
                        isset($break['break_start'], $break['break_end'])
                        && $break['break_start'] !== '' && $break['break_end'] !== ''
                    ) {

                        $slot->breaks()->create([
                            'break_start' => $break['break_start'],
                            'break_end' => $break['break_end'],
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Slot configuration updated successfully.'
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
        //
    }
}
