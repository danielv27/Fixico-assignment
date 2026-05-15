<?php

namespace App\FeatureFlags;

/**
 * Immutable evaluation context: which user is being evaluated, with what attributes.
 */
final readonly class EvaluationContext
{
    /**
     * @param  array<string, string>  $attributes
     */
    public function __construct(
        public string $userId,
        public array $attributes = [],
    ) {}

    public function attribute(string $key): ?string
    {
        return $this->attributes[$key] ?? null;
    }
}
