<?php

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $currency = 'IDR')
    {
        if ($currency === 'IDR') {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
        return number_format($amount, 2);
    }
}
