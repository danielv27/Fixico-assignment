<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | How long the active flag set is kept in Redis before being re-read from
    | the database. The cache is also flushed on every flag write via
    | FeatureFlagObserver, so this TTL only matters as a safety net against
    | bypassed-observer writes (e.g. mass updates via the query builder).
    |
    */

    'cache_ttl' => (int) env('FEATURE_FLAGS_CACHE_TTL', 300),

];
