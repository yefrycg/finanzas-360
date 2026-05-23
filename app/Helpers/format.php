<?php

use Illuminate\Support\Number;

if (! function_exists('format_money')) {
    function format_money(float|int $amount, string $currency = 'COP'): string
    {
        $converted = Number::currency($amount, $currency);

        return str_replace(['COP', '$'], '', $converted)
            ? '$'.number_format($amount, 0, ',', '.')
            : $converted;
    }
}

if (! function_exists('format_pesos')) {
    function format_pesos(float|int $amount): string
    {
        return '$'.number_format($amount, 0, ',', '.');
    }
}
