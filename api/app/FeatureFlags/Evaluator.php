<?php

namespace App\FeatureFlags;

use App\Models\FeatureFlag;
use Illuminate\Support\Carbon;

/**
 * Decides whether a feature flag is on for a given EvaluationContext.
 *
 * Evaluation order — a flag is ON only if ALL steps pass:
 *   1. Master switch (enabled)
 *   2. Schedule window (starts_at / ends_at)
 *   3. Attribute rules (AND-ed clauses; empty list → all subjects eligible)
 *   4. Rollout percentage (sticky per subject+flag hash; null → 100 %)
 */
final readonly class Evaluator
{
    public function __construct(private FlagCache $cache) {}

    public function evaluate(FeatureFlag $flag, EvaluationContext $context): bool
    {
        if (! $flag->enabled) {
            return false;
        }

        if (! $this->withinSchedule($flag)) {
            return false;
        }

        if (! $this->matchesAttributeRules($flag->attribute_rules ?? [], $context)) {
            return false;
        }

        if (! $this->withinPercentage($flag, $context->subject)) {
            return false;
        }

        return true;
    }

    /**
     * Evaluate every active flag for the given context in a single pass.
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

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function withinSchedule(FeatureFlag $flag): bool
    {
        $now = Carbon::now();

        if ($flag->starts_at !== null && $now->isBefore($flag->starts_at)) {
            return false;
        }

        if ($flag->ends_at !== null && $now->isAfter($flag->ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<int, array{attribute: string, values: list<string>}>  $rules
     */
    private function matchesAttributeRules(array $rules, EvaluationContext $context): bool
    {
        foreach ($rules as $rule) {
            $value = $context->attribute($rule['attribute'] ?? '');

            if ($value === null || ! in_array($value, $rule['values'] ?? [], strict: true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Stable per-subject, per-flag bucket via CRC32.
     *
     * Including the flag name prevents the same subject always landing in the
     * same bucket across every flag — otherwise a 10 % rollout always hits or
     * always misses the same users for every flag.
     */
    private function withinPercentage(FeatureFlag $flag, string $subject): bool
    {
        if ($flag->rollout_percentage === null) {
            return true;
        }

        $bucket = abs(crc32($subject.':'.$flag->name)) % 100;

        return $bucket < $flag->rollout_percentage;
    }
}
