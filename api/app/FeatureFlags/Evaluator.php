<?php

namespace App\FeatureFlags;

use App\Models\FeatureFlag;

/**
 * Decides whether a feature flag is on for a given EvaluationContext.
 *
 * Step order (later slices add steps 2-4 here without touching callers):
 *   1. master switch (enabled)
 *   2. schedule window  (Slice 6)
 *   3. attribute rules  (Slice 4)
 *   4. rollout percentage (Slice 5)
 */
final readonly class Evaluator
{
    public function __construct(private FlagCache $cache) {}

    public function evaluate(FeatureFlag $flag, EvaluationContext $context): bool
    {
        return $flag->enabled;
    }

    /**
     * Evaluate every active flag for the given context. Used by the public
     * batch endpoint so the client gets every decision in one round-trip.
     *
     * @return array<string, bool>
     */
    public function evaluateAll(EvaluationContext $context): array
    {
        $decisions = [];

        foreach ($this->cache->all() as $flag) {
            $decisions[$flag->name] = $this->evaluate($flag, $context);
        }

        return $decisions;
    }
}
