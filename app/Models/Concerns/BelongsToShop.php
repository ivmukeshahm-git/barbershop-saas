<?php

namespace App\Models\Concerns;

use App\Support\CurrentShop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Scopes Eloquent queries to the current SaaS shop when CurrentShop is set.
 * Also auto-fills shop_id on create.
 */
trait BelongsToShop
{
    public static function bootBelongsToShop(): void
    {
        static::addGlobalScope('shop', function (Builder $builder) {
            $shopId = CurrentShop::id();
            if ($shopId === null) {
                return;
            }

            $model = $builder->getModel();
            $table = $model->getTable();
            if (!Schema::hasColumn($table, 'shop_id')) {
                return;
            }

            $builder->where($model->qualifyColumn('shop_id'), $shopId);
        });

        static::creating(function (Model $model) {
            if ($model->getAttribute('shop_id') !== null) {
                return;
            }

            $shopId = CurrentShop::id();
            if ($shopId === null) {
                return;
            }

            if (!Schema::hasColumn($model->getTable(), 'shop_id')) {
                return;
            }

            $model->setAttribute('shop_id', $shopId);
        });
    }

    public function shop()
    {
        return $this->belongsTo(\App\Models\SaasShop::class, 'shop_id');
    }

    public function scopeForShop(Builder $query, $shopId): Builder
    {
        return $query->withoutGlobalScope('shop')->where(
            $query->getModel()->qualifyColumn('shop_id'),
            $shopId
        );
    }

    public function scopeWithoutShopScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('shop');
    }
}
