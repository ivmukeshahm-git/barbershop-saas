<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetOtp extends Model
{
    protected $table = 'password_reset_otps';

    protected $fillable = [
        'user_id',
        'email',
        'otp',
        'expires_at',
        'verified_at',
        'attempts',
        'reset_token',
        'reset_token_expires_at',
        'reset_token_used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'reset_token_expires_at' => 'datetime',
        'reset_token_used_at' => 'datetime',
        'attempts' => 'integer',
    ];

    protected $hidden = [
        'otp',
        'reset_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at === null || $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function hasExceededAttempts(int $maxAttempts): bool
    {
        return $this->attempts >= $maxAttempts;
    }

    public function isResetTokenValid(?string $token): bool
    {
        if (!$token || !$this->reset_token || $this->reset_token_used_at) {
            return false;
        }

        if ($this->reset_token_expires_at === null || $this->reset_token_expires_at->isPast()) {
            return false;
        }

        return hash_equals($this->reset_token, $token);
    }
}
