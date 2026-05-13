<?php

namespace App\Http\Requests\Api;

use App\DamageReports\ReportStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDamageReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_make' => ['sometimes', 'required', 'string', 'max:255'],
            'vehicle_model' => ['sometimes', 'required', 'string', 'max:255'],
            'license_plate' => ['sometimes', 'required', 'string', 'max:32'],
            'description' => ['sometimes', 'required', 'string', 'max:2000'],
            'status' => ['sometimes', Rule::enum(ReportStatus::class)],
        ];
    }
}
