<?php
// app/Services/ConvertNumberToWords.php

namespace App\Helpers;

use NumberFormatter;

class ConvertNumberToWords
{
    /**
     * Convert a number to words in Spanish.
     *
     * @param  float|int  $number
     * @return string
     */
    public static function convert($number)
    {
        $formatter = new NumberFormatter('es_ES', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($number)); // Capitalize the first letter
    }
}
