<?php

namespace App\FeatureFlags;

use App\Models\FeatureFlag;
use Illuminate\Support\Carbon;

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

        if (! $this->withinPercentage($flag, $context->userId)) {
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

    private function withinPercentage(FeatureFlag $flag, string $userId): bool
    {
        if ($flag->rollout_percentage === null) {
            return true;
        }

        $bucket = abs(crc32($userId)) % 100;

        return $bucket < $flag->rollout_percentage;
    }
}
