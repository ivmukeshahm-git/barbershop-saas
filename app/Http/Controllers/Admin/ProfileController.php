<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffService;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $serviceNames = collect();

        $ids = StaffService::idsForStaff($user->id);
        if (!empty($ids)) {
            $serviceNames = DB::table('tbl_service as s')
                ->whereIn('s.id', $ids)
                ->whereNull('s.deleted_at')
                ->orderBy('s.service_name');
            CurrentShop::constrain($serviceNames, 's.shop_id');
            $serviceNames = $serviceNames->pluck('s.service_name');
        }

        $stats = null;
        if (strcasecmp((string) $user->role, 'Staff') === 0) {
            $stats = $this->staffStats($user->id);
        }

        return view('admin.profile.index', compact('user', 'serviceNames', 'stats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if ($request->hasFile('profile_image')) {
            if (!empty($user->profile_img) && Storage::disk('public')->exists($user->profile_img)) {
                Storage::disk('public')->delete($user->profile_img);
            }
            $user->profile_img = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->save();

        return redirect()->route('admin.profile.index')->with('success', 'Profile updated successfully.');
    }

    protected function staffStats(int $id): object
    {
        $totalAppointments = DB::table('tbl_appointment as a')
            ->join('tbl_appointment_service as b', 'a.appointment_id', '=', 'b.appointment_id')
            ->where('b.staff_id', $id)
            ->where('is_invoiced', 1);
        CurrentShop::constrain($totalAppointments, 'a.shop_id');
        $totalAppointments = $totalAppointments->count('b.appointment_id');

        $serviceSub = DB::table('tbl_appointment_service as s')
            ->select(
                's.appointment_id',
                DB::raw('SUM(s.total_amt) as total_service_amount'),
                DB::raw('SUM(s.service_commission) as total_service_commission'),
                DB::raw('SUM(s.service_tip) as total_service_tip')
            )
            ->where('s.staff_id', $id)
            ->groupBy('s.appointment_id');
        CurrentShop::constrain($serviceSub, 's.shop_id');

        $productSub = DB::table('tbl_appointment_product as p')
            ->select(
                'p.appointment_id',
                DB::raw('SUM(p.amount) as total_product_amount'),
                DB::raw('SUM(p.product_commision) as total_product_commission')
            )
            ->where('p.staff_id', $id)
            ->groupBy('p.appointment_id');
        CurrentShop::constrain($productSub, 'p.shop_id');

        $earning = DB::table('tbl_appointment as a')
            ->leftJoinSub($serviceSub, 's', fn ($join) => $join->on('a.appointment_id', '=', 's.appointment_id'))
            ->leftJoinSub($productSub, 'p', fn ($join) => $join->on('a.appointment_id', '=', 'p.appointment_id'))
            ->where('a.status', 1)
            ->selectRaw("
                COALESCE(SUM(s.total_service_tip),0) as total_service_tip,
                COALESCE(SUM(s.total_service_commission),0) as total_service_commission,
                COALESCE(SUM(p.total_product_commission),0) as total_product_commission,
                (
                    COALESCE(SUM(s.total_service_tip),0) +
                    COALESCE(SUM(s.total_service_commission),0) +
                    COALESCE(SUM(p.total_product_commission),0)
                ) as total_earning
            ");
        CurrentShop::constrain($earning, 'a.shop_id');
        $earning = $earning->first();

        $totalVisit = DB::table('tbl_appointment_service as s')
            ->join('tbl_appointment as a', 's.appointment_id', '=', 'a.appointment_id')
            ->where('s.staff_id', $id)
            ->where('a.is_invoiced', 1)
            ->distinct('a.appointment_id');
        CurrentShop::constrain($totalVisit, 'a.shop_id');
        $totalVisit = $totalVisit->count('a.customer_id');

        return (object) [
            'totalAppointments' => $totalAppointments,
            'totalVisit' => $totalVisit,
            'total_service_tip' => $earning->total_service_tip ?? 0,
            'total_service_commission' => $earning->total_service_commission ?? 0,
            'total_product_commission' => $earning->total_product_commission ?? 0,
            'total_earning' => $earning->total_earning ?? 0,
        ];
    }
}
