<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SaasShop extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'saas_shops';

    protected $fillable = [
        'owner_user_id',
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'currency',
        'pincode',
        'logo',
        'theme_color',
        'status',
        'activated_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
    ];

    public const DEFAULT_THEME_COLOR = '#2D5BFF';

    public const THEME_PRESETS = [
        '#2D5BFF' => 'Infotech Blue',
        '#0EA5E9' => 'Sky',
        '#136207' => 'Forest',
        '#7C3AED' => 'Violet',
        '#DC2626' => 'Red',
        '#EA580C' => 'Orange',
        '#0F172A' => 'Midnight',
        '#0891B2' => 'Teal',
    ];

    public function themeColor(): string
    {
        $color = trim((string) ($this->theme_color ?: self::DEFAULT_THEME_COLOR));
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            return self::DEFAULT_THEME_COLOR;
        }

        return strtoupper($color);
    }

    public function themeRgb(): array
    {
        $hex = ltrim($this->themeColor(), '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    public function themeRgbString(): string
    {
        return implode(', ', $this->themeRgb());
    }

    /**
     * Darken theme color for gradients / hover states.
     */
    public function themeDark(float $factor = 0.65): string
    {
        [$r, $g, $b] = $this->themeRgb();
        $r = max(0, min(255, (int) round($r * $factor)));
        $g = max(0, min(255, (int) round($g * $factor)));
        $b = max(0, min(255, (int) round($b * $factor)));

        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }

    public function logoUrl(): string
    {
        if ($this->logo) {
            return asset('storage/' . ltrim($this->logo, '/'));
        }

        return asset('assets/img/logo.webp');
    }

    /**
     * Inline CSS variables applied across the whole admin panel.
     */
    public function themeStyleAttributes(): string
    {
        $color = $this->themeColor();
        $rgb = $this->themeRgbString();
        $dark = $this->themeDark(0.55);

        return implode('; ', [
            "--shop-theme: {$color}",
            "--shop-theme-rgb: {$rgb}",
            "--shop-theme-dark: {$dark}",
            "--shop-theme-soft: rgba({$rgb}, 0.12)",
            "--shop-theme-soft-strong: rgba({$rgb}, 0.22)",
            "--bc-primary: {$color}",
            "--bc-primary-dark: {$dark}",
            "--bc-primary-soft: rgba({$rgb}, 0.12)",
            "--bc-success: {$color}",
            "--bc-success-soft: rgba({$rgb}, 0.12)",
            "--bc-grad-primary: linear-gradient(135deg, {$color} 0%, {$dark} 100%)",
            "--bc-grad-success: linear-gradient(135deg, {$color} 0%, {$dark} 100%)",
            "--bc-grad-info: linear-gradient(135deg, {$dark} 0%, {$color} 100%)",
            "--bc-grad-purple: linear-gradient(135deg, {$dark} 0%, {$color} 100%)",
            "--bs-primary: {$color}",
            "--bs-primary-rgb: {$rgb}",
            "--bs-link-color: {$color}",
            "--bs-link-hover-color: {$dark}",
            "--al-blue: {$color}",
            "--al-navy: {$dark}",
            "--al-soft: rgba({$rgb}, 0.12)",
            "--al-grad: linear-gradient(135deg, {$color} 0%, {$dark} 100%)",
        ]);
    }

    public const RESERVED_SLUGS = [
        'admin', 'platform', 'api', 'home', 'about', 'services', 'barbers',
        'gallery', 'contact', 'book', 'pricing', 'signup', 'get-started',
        'login', 'logout', 'register', 'storage', 'storage-link', 'cache-clear',
        'mail', 'track-booking', 'www', 'app', 'saas', 'billing', 'checkout',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(SaasSubscription::class, 'shop_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(SaasSubscription::class, 'shop_id')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latestOfMany();
    }

    public function latestSubscription()
    {
        return $this->hasOne(SaasSubscription::class, 'shop_id')->latestOfMany();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Mark active rows as expired once ends_at has passed.
     */
    public function syncExpiredSubscriptions(): void
    {
        $this->subscriptions()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->update(['status' => 'expired']);
    }

    /**
     * Free can be purchased once per shop. Any past free subscription counts.
     */
    public function hasUsedFreePlan(): bool
    {
        return $this->subscriptions()
            ->whereHas('plan', function ($query) {
                $query->where('slug', 'free')
                    ->orWhere(function ($inner) {
                        $inner->where('price_monthly', '<=', 0)
                            ->where('price_yearly', '<=', 0);
                    });
            })
            ->exists();
    }

    public function adminLoginUrl(): string
    {
        return url('/' . $this->slug . '/admin/login');
    }

    public function adminHomeUrl(): string
    {
        return url('/' . $this->slug . '/admin/dashboard');
    }

    public static function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'shop';
        }

        if (in_array($base, self::RESERVED_SLUGS, true)) {
            $base .= '-shop';
        }

        $slug = $base;
        $i = 1;

        while (
            self::withTrashed()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
            || in_array($slug, self::RESERVED_SLUGS, true)
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
