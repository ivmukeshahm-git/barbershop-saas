<?php

use App\Support\CountryCurrency;

if (!function_exists('shop_currency_symbol')) {
    function shop_currency_symbol(?string $code = null): string
    {
        return CountryCurrency::symbol($code);
    }
}

if (!function_exists('shop_currency_code')) {
    function shop_currency_code(string $fallback = 'INR'): string
    {
        return CountryCurrency::currentCode($fallback);
    }
}

if (!function_exists('shop_money')) {
    /**
     * Format an amount with the current shop currency symbol.
     */
    function shop_money($amount, int $decimals = 2, ?string $code = null): string
    {
        return CountryCurrency::format($amount, $code, $decimals);
    }
}
