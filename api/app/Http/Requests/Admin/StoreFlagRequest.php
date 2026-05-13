<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'regex:/^[a-z0-9._-]+$/', 'unique:feature_flags,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'enabled' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain lowercase letters, numbers, dots, underscores, and hyphens.',
        ];
    }
}
