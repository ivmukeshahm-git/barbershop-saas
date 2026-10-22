<?php

namespace App\Support;

use App\Models\SaasShop;

class CurrentShop
{
    protected static ?SaasShop $shop = null;

    public static function set(?SaasShop $shop): void
    {
        static::$shop = $shop;
    }

    public static function get(): ?SaasShop
    {
        return static::$shop;
    }

    public static function id(): ?int
    {
        return static::$shop?->id;
    }

    public static function slug(): ?string
    {
        return static::$shop?->slug;
    }

    public static function require(): SaasShop
    {
        if (!static::$shop) {
            abort(404, 'Shop not found.');
        }

        return static::$shop;
    }

    /**
     * Constrain a query to the current shop when tenant context is set.
     *
     * @param  mixed  $query
     * @return mixed
     */
    public static function constrain($query, string $column = 'shop_id')
    {
        $shopId = static::id();
        if ($shopId !== null) {
            $query->where($column, $shopId);
        }

        return $query;
    }

    public static function columnValue(): ?int
    {
        return static::id();
    }
}
