<?php

use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Api\FeatureFlagEvaluationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['name' => 'Fixico API', 'status' => 'ok']));

Route::post('feature_flags/evaluate', [FeatureFlagEvaluationController::class, 'evaluate']);

Route::prefix('admin')->group(function (): void {
    Route::get('feature_flags', [FeatureFlagController::class, 'index']);
    Route::post('feature_flags', [FeatureFlagController::class, 'store']);
    Route::get('feature_flags/{flag}', [FeatureFlagController::class, 'show']);
    Route::patch('feature_flags/{flag}', [FeatureFlagController::class, 'update']);
    Route::delete('feature_flags/{flag}', [FeatureFlagController::class, 'destroy']);
});
