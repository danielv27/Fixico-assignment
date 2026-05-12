<?php

namespace App\Http\Controllers\Api;

use App\FeatureFlags\Evaluator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EvaluateFlagsRequest;
use Illuminate\Http\JsonResponse;

class FlagEvaluationController extends Controller
{
    public function __invoke(EvaluateFlagsRequest $request, Evaluator $evaluator): JsonResponse
    {
        return response()->json([
            'flags' => $evaluator->evaluateAll($request->context()),
        ]);
    }
}
