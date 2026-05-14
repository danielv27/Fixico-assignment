<?php

namespace App\Models;

use App\DamageReports\ReportStatus;
use Database\Factories\DamageReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageReport extends Model
{
    /** @use HasFactory<DamageReportFactory> */
    use HasFactory;

    protected $fillable = [
        'vehicle_make',
        'vehicle_model',
        'license_plate',
        'description',
        'status',
        'photos',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'photos' => 'array',
        ];
    }
}
