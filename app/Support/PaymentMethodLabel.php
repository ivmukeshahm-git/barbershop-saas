<?php

namespace App\Support;

class PaymentMethodLabel
{
    public static function normalize(?string $method): string
    {
        $method = trim((string) $method);
        $key = strtolower($method);

        if (in_array($key, ['upi', 'gpay', 'google pay', 'phonepe', 'paytm'], true)) {
            return 'UPI';
        }

        if (in_array($key, ['card', 'credit card', 'debit card'], true)) {
            return 'Card';
        }

        if (in_array($key, ['cash', 'cash payment'], true)) {
            return 'Cash';
        }

        return $method === '' ? '' : ucfirst($key);
    }
}
