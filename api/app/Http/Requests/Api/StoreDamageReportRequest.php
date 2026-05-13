<?php

namespace App\Http\Requests\Api;

use App\DamageReports\ReportStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDamageReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_make' => ['required', 'string', 'max:255'],
            'vehicle_model' => ['required', 'string', 'max:255'],
            'license_plate' => ['required', 'string', 'max:32'],
            'description' => ['required', 'string', 'max:2000'],
            'status' => ['sometimes', Rule::enum(ReportStatus::class)],
        ];
    }
}
