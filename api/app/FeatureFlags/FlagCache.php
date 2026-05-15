<?php

namespace App\FeatureFlags;

use App\Models\FeatureFlag;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class FlagCache
{
    private const KEY = 'flags:index';

    private const COLUMNS = [
        'name',
        'enabled',
        'attribute_rules',
        'rollout_percentage',
        'starts_at',
        'ends_at',
    ];

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
                ->get(self::COLUMNS)
                ->toArray(),
        );

        // Cache arrays instead of Eloquent models so Redis payloads rehydrate
        // cleanly across requests and deployments.
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
