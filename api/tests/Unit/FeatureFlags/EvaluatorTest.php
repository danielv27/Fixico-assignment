<?php

use App\FeatureFlags\EvaluationContext;
use App\FeatureFlags\Evaluator;
use App\Models\FeatureFlag;

beforeEach(function (): void {
    $this->evaluator = app(Evaluator::class);
    $this->context = new EvaluationContext(subject: 'subject-1');
});

it('returns true when the flag is enabled', function (): void {
    $flag = new FeatureFlag(['name' => 'flag.a', 'enabled' => true]);

    expect($this->evaluator->evaluate($flag, $this->context))->toBeTrue();
});

it('returns false when the flag is disabled', function (): void {
    $flag = new FeatureFlag(['name' => 'flag.a', 'enabled' => false]);

    expect($this->evaluator->evaluate($flag, $this->context))->toBeFalse();
});

it('evaluates every active flag in one call', function (): void {
    FeatureFlag::factory()->create(['name' => 'flag.a', 'enabled' => true]);
    FeatureFlag::factory()->create(['name' => 'flag.b', 'enabled' => false]);

    $decisions = $this->evaluator->evaluateAll($this->context);

    expect($decisions)->toBe(['flag.a' => true, 'flag.b' => false]);
});

it('returns an empty map when no flags exist', function (): void {
    expect($this->evaluator->evaluateAll($this->context))->toBe([]);
});
