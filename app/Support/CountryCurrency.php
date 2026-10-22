<?php

namespace App\Support;

class CountryCurrency
{
    /**
     * @return array<string, string> country name => default currency code
     */
    public static function map(): array
    {
        return config('countries', []);
    }

    /**
     * @return list<string>
     */
    public static function countries(): array
    {
        return array_keys(static::map());
    }

    public static function currencyForCountry(?string $country, string $fallback = 'INR'): string
    {
        $country = trim((string) $country);
        $map = static::map();

        return $map[$country] ?? $fallback;
    }

    /**
     * Unique currency codes with labels for select inputs.
     *
     * @return array<string, string> code => label
     */
    public static function currencies(): array
    {
        $labels = [
            'AED' => 'UAE Dirham (AED)',
            'AFN' => 'Afghan Afghani (AFN)',
            'ALL' => 'Albanian Lek (ALL)',
            'AMD' => 'Armenian Dram (AMD)',
            'AOA' => 'Angolan Kwanza (AOA)',
            'ARS' => 'Argentine Peso (ARS)',
            'AUD' => 'Australian Dollar (AUD)',
            'AZN' => 'Azerbaijani Manat (AZN)',
            'BAM' => 'Bosnia-Herzegovina Convertible Mark (BAM)',
            'BBD' => 'Barbadian Dollar (BBD)',
            'BDT' => 'Bangladeshi Taka (BDT)',
            'BGN' => 'Bulgarian Lev (BGN)',
            'BHD' => 'Bahraini Dinar (BHD)',
            'BIF' => 'Burundian Franc (BIF)',
            'BND' => 'Brunei Dollar (BND)',
            'BOB' => 'Bolivian Boliviano (BOB)',
            'BRL' => 'Brazilian Real (BRL)',
            'BSD' => 'Bahamian Dollar (BSD)',
            'BTN' => 'Bhutanese Ngultrum (BTN)',
            'BWP' => 'Botswana Pula (BWP)',
            'BYN' => 'Belarusian Ruble (BYN)',
            'BZD' => 'Belize Dollar (BZD)',
            'CAD' => 'Canadian Dollar (CAD)',
            'CDF' => 'Congolese Franc (CDF)',
            'CHF' => 'Swiss Franc (CHF)',
            'CLP' => 'Chilean Peso (CLP)',
            'CNY' => 'Chinese Yuan (CNY)',
            'COP' => 'Colombian Peso (COP)',
            'CRC' => 'Costa Rican Colón (CRC)',
            'CUP' => 'Cuban Peso (CUP)',
            'CVE' => 'Cape Verdean Escudo (CVE)',
            'CZK' => 'Czech Koruna (CZK)',
            'DJF' => 'Djiboutian Franc (DJF)',
            'DKK' => 'Danish Krone (DKK)',
            'DOP' => 'Dominican Peso (DOP)',
            'DZD' => 'Algerian Dinar (DZD)',
            'EGP' => 'Egyptian Pound (EGP)',
            'ERN' => 'Eritrean Nakfa (ERN)',
            'ETB' => 'Ethiopian Birr (ETB)',
            'EUR' => 'Euro (EUR)',
            'FJD' => 'Fijian Dollar (FJD)',
            'GBP' => 'British Pound (GBP)',
            'GEL' => 'Georgian Lari (GEL)',
            'GHS' => 'Ghanaian Cedi (GHS)',
            'GMD' => 'Gambian Dalasi (GMD)',
            'GNF' => 'Guinean Franc (GNF)',
            'GTQ' => 'Guatemalan Quetzal (GTQ)',
            'GYD' => 'Guyanese Dollar (GYD)',
            'HNL' => 'Honduran Lempira (HNL)',
            'HTG' => 'Haitian Gourde (HTG)',
            'HUF' => 'Hungarian Forint (HUF)',
            'IDR' => 'Indonesian Rupiah (IDR)',
            'ILS' => 'Israeli Shekel (ILS)',
            'INR' => 'Indian Rupee (INR)',
            'IQD' => 'Iraqi Dinar (IQD)',
            'IRR' => 'Iranian Rial (IRR)',
            'ISK' => 'Icelandic Króna (ISK)',
            'JMD' => 'Jamaican Dollar (JMD)',
            'JOD' => 'Jordanian Dinar (JOD)',
            'JPY' => 'Japanese Yen (JPY)',
            'KES' => 'Kenyan Shilling (KES)',
            'KGS' => 'Kyrgyzstani Som (KGS)',
            'KHR' => 'Cambodian Riel (KHR)',
            'KMF' => 'Comorian Franc (KMF)',
            'KPW' => 'North Korean Won (KPW)',
            'KRW' => 'South Korean Won (KRW)',
            'KWD' => 'Kuwaiti Dinar (KWD)',
            'KZT' => 'Kazakhstani Tenge (KZT)',
            'LAK' => 'Lao Kip (LAK)',
            'LBP' => 'Lebanese Pound (LBP)',
            'LKR' => 'Sri Lankan Rupee (LKR)',
            'LRD' => 'Liberian Dollar (LRD)',
            'LSL' => 'Lesotho Loti (LSL)',
            'LYD' => 'Libyan Dinar (LYD)',
            'MAD' => 'Moroccan Dirham (MAD)',
            'MDL' => 'Moldovan Leu (MDL)',
            'MGA' => 'Malagasy Ariary (MGA)',
            'MKD' => 'Macedonian Denar (MKD)',
            'MMK' => 'Myanmar Kyat (MMK)',
            'MNT' => 'Mongolian Tögrög (MNT)',
            'MRU' => 'Mauritanian Ouguiya (MRU)',
            'MUR' => 'Mauritian Rupee (MUR)',
            'MVR' => 'Maldivian Rufiyaa (MVR)',
            'MWK' => 'Malawian Kwacha (MWK)',
            'MXN' => 'Mexican Peso (MXN)',
            'MYR' => 'Malaysian Ringgit (MYR)',
            'MZN' => 'Mozambican Metical (MZN)',
            'NAD' => 'Namibian Dollar (NAD)',
            'NGN' => 'Nigerian Naira (NGN)',
            'NIO' => 'Nicaraguan Córdoba (NIO)',
            'NOK' => 'Norwegian Krone (NOK)',
            'NPR' => 'Nepalese Rupee (NPR)',
            'NZD' => 'New Zealand Dollar (NZD)',
            'OMR' => 'Omani Rial (OMR)',
            'PAB' => 'Panamanian Balboa (PAB)',
            'PEN' => 'Peruvian Sol (PEN)',
            'PGK' => 'Papua New Guinean Kina (PGK)',
            'PHP' => 'Philippine Peso (PHP)',
            'PKR' => 'Pakistani Rupee (PKR)',
            'PLN' => 'Polish Złoty (PLN)',
            'PYG' => 'Paraguayan Guaraní (PYG)',
            'QAR' => 'Qatari Riyal (QAR)',
            'RON' => 'Romanian Leu (RON)',
            'RSD' => 'Serbian Dinar (RSD)',
            'RUB' => 'Russian Ruble (RUB)',
            'RWF' => 'Rwandan Franc (RWF)',
            'SAR' => 'Saudi Riyal (SAR)',
            'SBD' => 'Solomon Islands Dollar (SBD)',
            'SCR' => 'Seychellois Rupee (SCR)',
            'SDG' => 'Sudanese Pound (SDG)',
            'SEK' => 'Swedish Krona (SEK)',
            'SGD' => 'Singapore Dollar (SGD)',
            'SLE' => 'Sierra Leonean Leone (SLE)',
            'SOS' => 'Somali Shilling (SOS)',
            'SRD' => 'Surinamese Dollar (SRD)',
            'SSP' => 'South Sudanese Pound (SSP)',
            'STN' => 'São Tomé and Príncipe Dobra (STN)',
            'SYP' => 'Syrian Pound (SYP)',
            'SZL' => 'Swazi Lilangeni (SZL)',
            'THB' => 'Thai Baht (THB)',
            'TJS' => 'Tajikistani Somoni (TJS)',
            'TMT' => 'Turkmenistani Manat (TMT)',
            'TND' => 'Tunisian Dinar (TND)',
            'TOP' => 'Tongan Paʻanga (TOP)',
            'TRY' => 'Turkish Lira (TRY)',
            'TTD' => 'Trinidad and Tobago Dollar (TTD)',
            'TWD' => 'New Taiwan Dollar (TWD)',
            'TZS' => 'Tanzanian Shilling (TZS)',
            'UAH' => 'Ukrainian Hryvnia (UAH)',
            'UGX' => 'Ugandan Shilling (UGX)',
            'USD' => 'US Dollar (USD)',
            'UYU' => 'Uruguayan Peso (UYU)',
            'UZS' => 'Uzbekistani Som (UZS)',
            'VES' => 'Venezuelan Bolívar (VES)',
            'VND' => 'Vietnamese Đồng (VND)',
            'VUV' => 'Vanuatu Vatu (VUV)',
            'WST' => 'Samoan Tala (WST)',
            'XAF' => 'Central African CFA Franc (XAF)',
            'XCD' => 'East Caribbean Dollar (XCD)',
            'XOF' => 'West African CFA Franc (XOF)',
            'YER' => 'Yemeni Rial (YER)',
            'ZAR' => 'South African Rand (ZAR)',
            'ZMW' => 'Zambian Kwacha (ZMW)',
            'ZWL' => 'Zimbabwean Dollar (ZWL)',
        ];

        $codes = array_unique(array_values(static::map()));
        sort($codes);

        $out = [];
        foreach ($codes as $code) {
            $out[$code] = $labels[$code] ?? $code;
        }

        return $out;
    }

    public static function isValidCountry(?string $country): bool
    {
        return $country !== null && array_key_exists($country, static::map());
    }

    public static function isValidCurrency(?string $code): bool
    {
        return $code !== null && array_key_exists(strtoupper($code), static::currencies());
    }

    public static function currentCode(string $fallback = 'INR'): string
    {
        $shop = CurrentShop::get();
        $code = $shop && !empty($shop->currency) ? strtoupper((string) $shop->currency) : $fallback;

        return $code !== '' ? $code : $fallback;
    }

    public static function symbol(?string $code = null): string
    {
        $code = strtoupper((string) ($code ?: static::currentCode()));

        $symbols = [
            'AED' => 'د.إ',
            'AUD' => 'A$',
            'BDT' => '৳',
            'BRL' => 'R$',
            'CAD' => 'C$',
            'CHF' => 'CHF ',
            'CNY' => '¥',
            'EUR' => '€',
            'GBP' => '£',
            'HKD' => 'HK$',
            'IDR' => 'Rp',
            'ILS' => '₪',
            'INR' => '₹',
            'JPY' => '¥',
            'KRW' => '₩',
            'MXN' => 'MX$',
            'MYR' => 'RM',
            'NGN' => '₦',
            'NOK' => 'kr',
            'NZD' => 'NZ$',
            'PHP' => '₱',
            'PKR' => '₨',
            'PLN' => 'zł',
            'RUB' => '₽',
            'SAR' => '﷼',
            'SEK' => 'kr',
            'SGD' => 'S$',
            'THB' => '฿',
            'TRY' => '₺',
            'TWD' => 'NT$',
            'USD' => '$',
            'VND' => '₫',
            'ZAR' => 'R',
        ];

        return $symbols[$code] ?? ($code . ' ');
    }

    public static function format($amount, ?string $code = null, int $decimals = 2): string
    {
        return static::symbol($code) . number_format((float) $amount, $decimals);
    }
}
