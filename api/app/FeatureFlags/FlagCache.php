<?php

namespace App\FeatureFlags;

use App\Models\FeatureFlag;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Single-key cache of every flag, refreshed on writes via FlagObserver.
 *
 * The cache stores plain associative arrays, not Eloquent models. Why arrays
 * specifically: PHP's unserialize() doesn't run the autoloader for unknown
 * classes, so caching custom objects across requests produces
 * __PHP_Incomplete_Class on the second hit. Arrays round-trip cleanly,
 * and we rehydrate to non-persisted FeatureFlag instances on read so the
 * evaluator works in the model's natural shape.
 */
final readonly class FlagCache
{
    private const KEY = 'flags:index:v1';

    public function __construct(private CacheRepository $cache) {}

    /**
     * @return array<int, FeatureFlag>
     */
    public function all(): array
    {
        $rows = $this->cache->remember(
            self::KEY,
            config('feature_flags.cache_ttl'),
            fn (): array => FeatureFlag::query()
                ->get(['name', 'enabled'])
                ->toArray(),
        );

        return array_map(
            fn (array $row): FeatureFlag => (new FeatureFlag)->forceFill($row),
            $rows,
        );
    }

    public function flush(): void
    {
        $this->cache->forget(self::KEY);
    }
}
