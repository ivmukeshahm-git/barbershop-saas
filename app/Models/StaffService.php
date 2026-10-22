<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StaffService extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_staff_service';

    protected $fillable = [
        'shop_id',
        'staff_id',
        'service_id',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    /**
     * Replace all service assignments for a staff member.
     * Keeps the legacy users.service_id column in sync as a fallback.
     */
    public static function syncForStaff($staffId, array $serviceIds): void
    {
        $serviceIds = array_values(array_unique(array_filter(array_map('intval', $serviceIds))));

        DB::beginTransaction();
        try {
            foreach ($serviceIds as $serviceId) {
                DB::table('tbl_staff_service')->updateOrInsert(
                    ['staff_id' => $staffId, 'service_id' => $serviceId],
                    ['updated_at' => now()]
                );
            }

            DB::table('tbl_staff_service')
                ->where('staff_id', $staffId)
                ->when(!empty($serviceIds), function ($query) use ($serviceIds) {
                    $query->whereNotIn('service_id', $serviceIds);
                })
                ->delete();

            \App\Models\User::where('id', $staffId)->update([
                'service_id' => !empty($serviceIds) ? implode(',', $serviceIds) : null,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Repopulate tbl_staff_service from the legacy users.service_id column
     * when the pivot table is empty (e.g. after a database restore).
     */
    public static function backfillFromLegacy(): void
    {
        if (DB::table('tbl_staff_service')->count() > 0) {
            return;
        }

        $staffUsers = \App\Models\User::where('role', 'Staff')
            ->whereNotNull('service_id')
            ->where('service_id', '<>', '')
            ->get(['id', 'service_id']);

        $now = now();
        foreach ($staffUsers as $user) {
            $ids = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) $user->service_id)))));
            foreach ($ids as $serviceId) {
                if (!is_numeric($serviceId)) {
                    continue;
                }
                $exists = DB::table('tbl_staff_service')
                    ->where('staff_id', $user->id)
                    ->where('service_id', $serviceId)
                    ->exists();
                if (!$exists) {
                    DB::table('tbl_staff_service')->insert([
                        'staff_id'   => $user->id,
                        'service_id' => $serviceId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Get service ids for a staff member.
     * Falls back to the legacy users.service_id column if the pivot is empty.
     */
    public static function idsForStaff($staffId): array
    {
        $ids = DB::table('tbl_staff_service')
            ->where('staff_id', $staffId)
            ->pluck('service_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($ids)) {
            $legacy = \App\Models\User::where('id', $staffId)->value('service_id');
            $ids = array_values(array_filter(
                array_map('trim', explode(',', (string) $legacy)),
                fn ($id) => is_numeric($id) && $id !== ''
            ));
        }

        return $ids;
    }

    /**
     * Check whether a staff member offers a given service.
     */
    public static function offersService($staffId, $serviceId): bool
    {
        $serviceId = (int) $serviceId;

        if (DB::table('tbl_staff_service')->where('staff_id', $staffId)->where('service_id', $serviceId)->exists()) {
            return true;
        }

        return in_array($serviceId, array_map('intval', self::idsForStaff($staffId)), true);
    }
}