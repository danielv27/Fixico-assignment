<?php

namespace App\Http\Controllers\Api;

use App\FeatureFlags\Evaluator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EvaluateFeatureFlagsRequest;
use Illuminate\Http\JsonResponse;

class FeatureFlagEvaluationController extends Controller
{
    public function evaluate(EvaluateFeatureFlagsRequest $request, Evaluator $evaluator): JsonResponse
    {
        return response()->json([
            'flags' => $evaluator->evaluateAll($request->context()),
        ]);
    }
}
