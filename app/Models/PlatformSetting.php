<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function values(): array
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('platform_settings')) {
            return [];
        }

        return static::query()->pluck('value', 'key')->all();
    }

    public static function setValues(array $values): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('platform_settings')) {
            throw new \RuntimeException('Platform settings storage is not installed. Run php artisan migrate.');
        }

        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
