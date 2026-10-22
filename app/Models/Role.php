<?php

namespace App\Models;

use App\Support\CurrentShop;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Shop-scoped Spatie roles.
 *
 * IMPORTANT: Do NOT use BelongsToShop here. Spatie loads roles via this model;
 * a shop global scope would hide platform roles (shop_id null) and break @can menus.
 * Controllers must filter by shop_id explicitly when listing/editing.
 */
class Role extends SpatieRole
{
    protected $fillable = [
        'shop_id',
        'name',
        'guard_name',
    ];

    /**
     * Roles assignable inside the current shop admin (never platform Super-admin).
     */
    public function scopeAssignableForShop($query)
    {
        $shopId = CurrentShop::id();

        return $query
            ->when($shopId !== null, fn ($q) => $q->where('shop_id', $shopId))
            ->whereNotIn('name', ['Super-admin', 'Super Admin', 'Platform Admin']);
    }

    /**
     * Compatibility helper — Role is intentionally unscoped for Spatie.
     */
    public function scopeWithoutShopScope($query)
    {
        return $query;
    }

    /**
     * Resolve a global (platform) role by name.
     */
    public static function findGlobalByName(string $name): ?self
    {
        return static::query()
            ->whereNull('shop_id')
            ->where('name', $name)
            ->where('guard_name', 'web')
            ->first();
    }

    /**
     * Find or create a shop role, copying permissions from a template when available.
     */
    public static function ensureForShop(int $shopId, string $name, ?self $template = null): self
    {
        $role = static::query()
            ->where('shop_id', $shopId)
            ->where('name', $name)
            ->where('guard_name', 'web')
            ->first();

        if ($role) {
            if ($template && $role->permissions()->count() === 0) {
                $role->syncPermissions($template->permissions()->pluck('name')->all());
            }

            return $role;
        }

        $role = static::query()->create([
            'shop_id' => $shopId,
            'name' => $name,
            'guard_name' => 'web',
        ]);

        if ($template) {
            $role->syncPermissions($template->permissions()->pluck('name')->all());
        }

        return $role;
    }
}
