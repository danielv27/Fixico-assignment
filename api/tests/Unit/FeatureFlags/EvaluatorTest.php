<?php

use App\FeatureFlags\EvaluationContext;
use App\FeatureFlags\Evaluator;
use App\Models\FeatureFlag;
use Illuminate\Support\Carbon;

beforeEach(function (): void {
    $this->evaluator = app(Evaluator::class);
    $this->ctx = new EvaluationContext(userId: 'user-1');
});

it('returns true when the flag is enabled with no other rules', function (): void {
    $flag = new FeatureFlag(['name' => 'f', 'enabled' => true, 'attribute_rules' => []]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeTrue();
});

it('returns false when the flag is disabled regardless of other settings', function (): void {
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => false,
        'attribute_rules' => [], 'rollout_percentage' => 100,
    ]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeFalse();
});

it('returns false before starts_at', function (): void {
    Carbon::setTestNow('2026-01-01 10:00:00');
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true, 'attribute_rules' => [],
        'starts_at' => '2026-01-01 11:00:00',
    ]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeFalse();
});

it('returns true exactly at starts_at', function (): void {
    Carbon::setTestNow('2026-01-01 11:00:00');
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true, 'attribute_rules' => [],
        'starts_at' => '2026-01-01 11:00:00',
    ]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeTrue();
});

it('returns false after ends_at', function (): void {
    Carbon::setTestNow('2026-01-01 12:00:00');
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true, 'attribute_rules' => [],
        'ends_at' => '2026-01-01 11:00:00',
    ]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeFalse();
});

it('returns true inside the schedule window', function (): void {
    Carbon::setTestNow('2026-01-01 12:00:00');
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true, 'attribute_rules' => [],
        'starts_at' => '2026-01-01 11:00:00',
        'ends_at' => '2026-01-01 13:00:00',
    ]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeTrue();
});

it('returns true when no schedule window is set', function (): void {
    $flag = new FeatureFlag(['name' => 'f', 'enabled' => true, 'attribute_rules' => []]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeTrue();
});

it('returns true when attribute_rules is empty', function (): void {
    $flag = new FeatureFlag(['name' => 'f', 'enabled' => true, 'attribute_rules' => []]);
    $ctx = new EvaluationContext(userId: 's', attributes: []);

    expect($this->evaluator->evaluate($flag, $ctx))->toBeTrue();
});

it('returns true when the user matches a single rule', function (): void {
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true,
        'attribute_rules' => [['attribute' => 'role', 'values' => ['admin']]],
    ]);
    $ctx = new EvaluationContext(userId: 's', attributes: ['role' => 'admin']);

    expect($this->evaluator->evaluate($flag, $ctx))->toBeTrue();
});

it('returns false when the user does not match a rule', function (): void {
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true,
        'attribute_rules' => [['attribute' => 'role', 'values' => ['admin']]],
    ]);
    $ctx = new EvaluationContext(userId: 's', attributes: ['role' => 'customer']);

    expect($this->evaluator->evaluate($flag, $ctx))->toBeFalse();
});

it('returns false when a required attribute is missing from context', function (): void {
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true,
        'attribute_rules' => [['attribute' => 'country', 'values' => ['NL']]],
    ]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeFalse();
});

it('requires all rules to match (AND logic)', function (): void {
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true,
        'attribute_rules' => [
            ['attribute' => 'country', 'values' => ['NL']],
            ['attribute' => 'role', 'values' => ['admin']],
        ],
    ]);

    $passing = new EvaluationContext(userId: 's', attributes: ['country' => 'NL', 'role' => 'admin']);
    $failing = new EvaluationContext(userId: 's', attributes: ['country' => 'NL', 'role' => 'customer']);

    expect($this->evaluator->evaluate($flag, $passing))->toBeTrue()
        ->and($this->evaluator->evaluate($flag, $failing))->toBeFalse();
});

it('matches any value in the values list (OR within a rule)', function (): void {
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true,
        'attribute_rules' => [['attribute' => 'country', 'values' => ['NL', 'BE']]],
    ]);

    $nl = new EvaluationContext(userId: 's', attributes: ['country' => 'NL']);
    $be = new EvaluationContext(userId: 's', attributes: ['country' => 'BE']);
    $de = new EvaluationContext(userId: 's', attributes: ['country' => 'DE']);

    expect($this->evaluator->evaluate($flag, $nl))->toBeTrue()
        ->and($this->evaluator->evaluate($flag, $be))->toBeTrue()
        ->and($this->evaluator->evaluate($flag, $de))->toBeFalse();
});

it('uses strict string comparison — integer 1 does not match string "1"', function (): void {
    $flag = new FeatureFlag([
        'name' => 'f', 'enabled' => true,
        'attribute_rules' => [['attribute' => 'tier', 'values' => ['1']]],
    ]);
    $ctx = new EvaluationContext(userId: 's', attributes: ['tier' => '1']);

    expect($this->evaluator->evaluate($flag, $ctx))->toBeTrue();
});

it('returns true when rollout_percentage is null (100 %)', function (): void {
    $flag = new FeatureFlag(['name' => 'f', 'enabled' => true, 'attribute_rules' => [], 'rollout_percentage' => null]);

    expect($this->evaluator->evaluate($flag, $this->ctx))->toBeTrue();
});

it('returns false for every user when rollout_percentage is 0', function (): void {
    $flag = new FeatureFlag(['name' => 'f', 'enabled' => true, 'attribute_rules' => [], 'rollout_percentage' => 0]);

    foreach (['a', 'b', 'c', 'd', 'e'] as $user) {
        $ctx = new EvaluationContext(userId: $user);
        expect($this->evaluator->evaluate($flag, $ctx))->toBeFalse("user '$user' should be excluded at 0 %");
    }
});

it('returns true for every user when rollout_percentage is 100', function (): void {
    $flag = new FeatureFlag(['name' => 'f', 'enabled' => true, 'attribute_rules' => [], 'rollout_percentage' => 100]);

    foreach (['a', 'b', 'c', 'd', 'e'] as $user) {
        $ctx = new EvaluationContext(userId: $user);
        expect($this->evaluator->evaluate($flag, $ctx))->toBeTrue("user '$user' should be included at 100 %");
    }
});

it('is deterministic for the same user', function (): void {
    $flag = new FeatureFlag(['name' => 'reports.feature', 'enabled' => true, 'attribute_rules' => [], 'rollout_percentage' => 50]);
    $ctx = new EvaluationContext(userId: 'user-42');

    $first = $this->evaluator->evaluate($flag, $ctx);
    for ($i = 0; $i < 10; $i++) {
        expect($this->evaluator->evaluate($flag, $ctx))->toBe($first);
    }
});

it('gives the same user the same bucket across all flags (user-consistent bucketing)', function (): void {
    $ctx = new EvaluationContext(userId: 'user-999');
    $results = [];

    foreach (['flag.a', 'flag.b', 'flag.c', 'flag.d', 'flag.e'] as $name) {
        $flag = new FeatureFlag(['name' => $name, 'enabled' => true, 'attribute_rules' => [], 'rollout_percentage' => 50]);
        $results[] = $this->evaluator->evaluate($flag, $ctx);
    }

    expect(count(array_unique($results)))->toBe(1);
});

it('distributes roughly correctly over many users at 50 %', function (): void {
    $flag = new FeatureFlag(['name' => 'rollout.test', 'enabled' => true, 'attribute_rules' => [], 'rollout_percentage' => 50]);

    $included = 0;
    $total = 1000;

    for ($i = 0; $i < $total; $i++) {
        $ctx = new EvaluationContext(userId: "user-{$i}");
        if ($this->evaluator->evaluate($flag, $ctx)) {
            $included++;
        }
    }

    // Expect 40–60 % (within ±10 % of 50 %)
    expect($included)->toBeGreaterThanOrEqual(400)->toBeLessThanOrEqual(600);
});

it('evaluates every flag in one call and returns the map', function (): void {
    FeatureFlag::factory()->create(['name' => 'flag.a', 'enabled' => true, 'attribute_rules' => []]);
    FeatureFlag::factory()->create(['name' => 'flag.b', 'enabled' => false, 'attribute_rules' => []]);

    $decisions = $this->evaluator->evaluateAll($this->ctx);

    expect($decisions)->toBe(['flag.a' => true, 'flag.b' => false]);
});

it('returns an empty map when no flags exist', function (): void {
    expect($this->evaluator->evaluateAll($this->ctx))->toBe([]);
});

afterEach(function (): void {
    Carbon::setTestNow();
});
