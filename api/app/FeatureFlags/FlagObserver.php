<?php

namespace App\FeatureFlags;

use App\Models\FeatureFlag;

final readonly class FlagObserver
{
    public function __construct(private FlagCache $cache) {}

    public function saved(FeatureFlag $flag): void
    {
        $this->cache->flush();
    }

    public function deleted(FeatureFlag $flag): void
    {
        $this->cache->flush();
    }
}
