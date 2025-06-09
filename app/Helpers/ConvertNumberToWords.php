<?php
// app/Services/ConvertNumberToWords.php

namespace App\Helpers;

class ConvertNumberToWords
{
    private static $units = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'
    ];
    
    private static $tens = [
        '', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 
        'sesenta', 'setenta', 'ochenta', 'noventa'
    ];
    
    private static $teens = [
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 
        'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'
    ];
    
    private static $hundreds = [
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 
        'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];

    public static function convert($number)
    {
        if ($number == 0) {
            return 'cero';
        }
        
        return ucfirst(trim(self::convertNumberToWords($number)));
    }
    
    private static function convertNumberToWords($number)
    {
        if ($number < 10) {
            return self::$units[$number];
        } elseif ($number < 20) {
            return self::$teens[$number - 10];
        } elseif ($number < 100) {
            $ten = floor($number / 10);
            $unit = $number % 10;
            return self::$tens[$ten] . ($unit > 0 ? ' y ' . self::$units[$unit] : '');
        } elseif ($number < 1000) {
            $hundred = floor($number / 100);
            $remainder = $number % 100;
            $word = self::$hundreds[$hundred];
            if ($remainder > 0) {
                $word .= ' ' . self::convertNumberToWords($remainder);
            }
            return $word;
        } else {
            return 'Número demasiado grande';
        }
    }
}