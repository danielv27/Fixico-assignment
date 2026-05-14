<?php

use App\FeatureFlags\FlagCache;
use App\Models\FeatureFlag;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->cache = app(FlagCache::class);
});

it('returns every flag from the database on first call', function (): void {
    FeatureFlag::factory()->create(['name' => 'flag.a']);
    FeatureFlag::factory()->create(['name' => 'flag.b']);

    $flags = $this->cache->all();

    expect($flags)->toHaveCount(2)
        ->and($flags[0])->toBeInstanceOf(FeatureFlag::class);
});

it('includes all evaluator-relevant columns', function (): void {
    FeatureFlag::factory()->create([
        'name' => 'flag.a',
        'enabled' => true,
        'attribute_rules' => [['attribute' => 'role', 'values' => ['admin']]],
        'rollout_percentage' => 50,
        'starts_at' => '2026-01-01 00:00:00',
        'ends_at' => '2027-01-01 00:00:00',
    ]);

    $flag = $this->cache->all()[0];

    expect($flag->attribute_rules)->toBe([['attribute' => 'role', 'values' => ['admin']]])
        ->and($flag->rollout_percentage)->toBe(50)
        ->and($flag->starts_at)->not->toBeNull()
        ->and($flag->ends_at)->not->toBeNull();
});

it('reuses the cached payload on subsequent reads', function (): void {
    FeatureFlag::factory()->create(['name' => 'flag.a', 'enabled' => true]);

    $this->cache->all();

    DB::table('feature_flags')->where('name', 'flag.a')->update(['enabled' => false]);

    expect($this->cache->all()[0]->enabled)->toBeTrue();
});

it('flushes when a flag is saved via Eloquent', function (): void {
    $flag = FeatureFlag::factory()->create(['enabled' => true]);

    $this->cache->all();

    $flag->update(['enabled' => false]);

    expect($this->cache->all()[0]->enabled)->toBeFalse();
});

it('flushes when a flag is deleted', function (): void {
    $flag = FeatureFlag::factory()->create();

    $this->cache->all();

    $flag->delete();

    expect($this->cache->all())->toBeEmpty();
});

it('does not flush on query-builder mass update (bypass)', function (): void {
    FeatureFlag::factory()->create(['name' => 'flag.a', 'enabled' => true]);

    $this->cache->all();

    DB::table('feature_flags')->update(['enabled' => false]);

    expect($this->cache->all()[0]->enabled)->toBeTrue();
});
