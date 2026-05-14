<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeatureFlagRequest extends FormRequest
{
    /** @var list<string> */
    private const ALLOWED_ATTRIBUTES = ['country', 'role'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:500'],
            'enabled' => ['required', 'boolean'],

            'attribute_rules' => ['array'],
            'attribute_rules.*.attribute' => ['required', 'string', Rule::in(self::ALLOWED_ATTRIBUTES)],
            'attribute_rules.*.values' => ['required', 'array', 'min:1'],
            'attribute_rules.*.values.*' => ['required', 'string'],

            'rollout_percentage' => ['nullable', 'integer', 'between:0,100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'attribute_rules.*.attribute.in' => 'Allowed attributes: '.implode(', ', self::ALLOWED_ATTRIBUTES).'.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('enabled')) {
            $this->merge(['enabled' => filter_var($this->input('enabled'), FILTER_VALIDATE_BOOLEAN)]);
        }

        if ($this->has('attribute_rules') && is_string($this->input('attribute_rules'))) {
            $decoded = json_decode($this->input('attribute_rules'), true);
            $this->merge(['attribute_rules' => is_array($decoded) ? $decoded : []]);
        }
    }
}
