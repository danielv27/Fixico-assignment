<?php

namespace App\Http\Middleware;

use App\FeatureFlags\EvaluationContext;
use App\FeatureFlags\Evaluator;
use App\Models\FeatureFlag;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enforces that a named feature flag is currently enabled for the request
 * subject/attributes. Returns 410 Gone when the feature flag is off.
 *
 * Usage in routes: Route::middleware('feature_flag:reports.bulk_actions')
 *
 * Why 410 (Gone) rather than 403 (Forbidden): 403 implies the client lacks
 * permission and should not retry. 410 means the resource/feature existed
 * and is no longer available — which is exactly the stale-interaction case
 * where the user saw the feature, the flag was disabled, and they tried to
 * interact anyway. The client interprets 410 as "feature was removed" and
 * surfaces a toast rather than an access-denied error.
 */
class RequireFeatureFlag
{
    public function __construct(private Evaluator $evaluator) {}

    public function handle(Request $request, Closure $next, string $flagName): mixed
    {
        $flag = FeatureFlag::where('name', $flagName)->first();

        if ($flag === null || ! $this->evaluator->evaluate($flag, $this->contextFrom($request))) {
            return new JsonResponse(
                ['error' => 'feature_disabled', 'flag' => $flagName],
                410,
            );
        }

        return $next($request);
    }

    private function contextFrom(Request $request): EvaluationContext
    {
        return new EvaluationContext(
            subject: $request->input('user_id', 'anonymous'),
            attributes: $request->input('attributes', []),
        );
    }
}
