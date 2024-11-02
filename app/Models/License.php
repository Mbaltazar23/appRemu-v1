<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'issue_date',
        'reason',
        'days',
        'institution',
        'receipt_number',
        'receipt_date',
        'processing_date',
        'responsible_person',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function hourLicenses(): HasMany
    {
        return $this->hasMany(HourLicense::class, 'license_id');
    }

    public function deleteWithHoursAndDays()
    {
        $this->hourLicenses()->delete();
        $this->delete();
    }

    public function sumHours($month, $year, $startDate, $endDate)
    {
        return $this->hourLicenses()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('hours');
    }

    public function sumDays($month, $year, $startDate, $endDate)
    {
        return $this->hourLicenses() // Asegúrate de usar hourLicenses() aquí
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('days'); // Asumiendo que hay una columna 'days' en hourLicenses
    }
}
