<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToShop;

    protected $table = 'tbl_setting';

    protected $fillable = [
        'shop_id',
        'service_reminder_day',
        'service_default_duration',
        'invoice_send_sts',
        'footer_about',
        'contact_address',
        'contact_phone',
        'contact_email',
        'support_email',
        'gstin',
        'sac_code',
        'booking_code_prefix',
        'whatsapp',
        'website_url',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'youtube_url',
        'twitter_url',
        'linkedin_url',
    ];

    /**
     * Settings row for the current shop (creates an empty one if missing).
     */
    public static function forCurrentShop(): ?self
    {
        $shopId = \App\Support\CurrentShop::id();
        if (!$shopId) {
            return static::withoutShopScope()->orderBy('id')->first();
        }

        return static::query()->first()
            ?? static::create(['shop_id' => $shopId]);
    }

    /**
     * Digits-only / tel: href value from display phone.
     */
    public function phoneTel(): string
    {
        $phone = (string) ($this->contact_phone ?? '');
        $digits = preg_replace('/\D+/', '', $phone);

        return $digits !== '' ? '+' . $digits : '';
    }

    /**
     * WhatsApp chat URL (wa.me).
     */
    public function whatsappUrl(): string
    {
        $raw = (string) ($this->whatsapp ?: $this->contact_phone ?: '');
        $digits = preg_replace('/\D+/', '', $raw);

        return $digits !== '' ? 'https://wa.me/' . $digits : '#';
    }

    /**
     * Google Maps search / embed query from address.
     */
    public function mapQuery(): string
    {
        return rawurlencode((string) ($this->contact_address ?? ''));
    }

    /**
     * Website host for display (no protocol).
     */
    public function websiteHost(): string
    {
        $url = (string) ($this->website_url ?? '');
        if ($url === '') {
            return '';
        }

        return preg_replace('#^https?://#i', '', rtrim($url, '/'));
    }
}
