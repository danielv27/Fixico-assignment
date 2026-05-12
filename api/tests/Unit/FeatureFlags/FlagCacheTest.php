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

it('reuses the cached payload on subsequent reads', function (): void {
    FeatureFlag::factory()->create(['name' => 'flag.a', 'enabled' => true]);

    $this->cache->all();

    // Bypass the observer so the cache is not flushed.
    DB::table('feature_flags')->where('name', 'flag.a')->update(['enabled' => false]);

    expect($this->cache->all()[0]->enabled)->toBeTrue();
});

it('flushes when a flag is saved', function (): void {
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
