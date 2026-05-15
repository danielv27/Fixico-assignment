<?php

namespace App\Http\Requests\Api;

use App\FeatureFlags\EvaluationContext;
use Illuminate\Foundation\Http\FormRequest;

class EvaluateFeatureFlagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'attributes' => ['array'],
            'attributes.*' => ['string', 'max:255'],
        ];
    }

    public function context(): EvaluationContext
    {
        return new EvaluationContext(
            subject: $this->string('subject')->toString(),
            attributes: $this->array('attributes'),
        );
    }
}
