<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ConvertDateService
{
    public static function handle(?string $date): ?string
    {
        if (is_null($date)) {
            return null;
        }

        try {
            if (is_numeric($date)) {
                $dateTime = ExcelDate::excelToDateTimeObject($date);

                return Carbon::instance($dateTime)->format('Y-m-d');
            }

            $fecha = Carbon::parse($date);

            return $fecha->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return Carbon::now()->format('Y-m-d');
        }
    }
}
