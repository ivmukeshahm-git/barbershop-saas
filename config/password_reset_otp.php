<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Expiration (minutes)
    |--------------------------------------------------------------------------
    */
    'otp_expire_minutes' => (int) env('PASSWORD_RESET_OTP_EXPIRE', 10),

    /*
    |--------------------------------------------------------------------------
    | Max incorrect OTP verification attempts per OTP record
    |--------------------------------------------------------------------------
    */
    'max_attempts' => (int) env('PASSWORD_RESET_OTP_MAX_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Password reset token lifetime after OTP verification (minutes)
    |--------------------------------------------------------------------------
    */
    'reset_token_expire_minutes' => (int) env('PASSWORD_RESET_TOKEN_EXPIRE', 15),

    /*
    |--------------------------------------------------------------------------
    | Minimum password length (aligned with existing admin password rules)
    |--------------------------------------------------------------------------
    */
    'password_min_length' => (int) env('PASSWORD_RESET_MIN_LENGTH', 6),

];
