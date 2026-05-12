<?php

namespace App\FeatureFlags;

/**
 * Immutable evaluation context: who is being evaluated, with what attributes.
 *
 * Slice 1 only uses the subject. Attributes ride along for later slices so
 * the public API contract doesn't change as targeting capabilities grow.
 */
final readonly class EvaluationContext
{
    /**
     * @param  array<string, string>  $attributes
     */
    public function __construct(
        public string $subject,
        public array $attributes = [],
    ) {}

    public function attribute(string $key): ?string
    {
        return $this->attributes[$key] ?? null;
    }
}
