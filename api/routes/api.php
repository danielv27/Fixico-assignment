<?php

use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Api\BulkDeleteReportsController;
use App\Http\Controllers\Api\DamageReportController;
use App\Http\Controllers\Api\FeatureFlagEvaluationController;
use App\Http\Controllers\Api\ReportPhotosController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['name' => 'Fixico API', 'status' => 'ok']));

// Public client-facing feature flag evaluation
Route::post('feature_flags/evaluate', [FeatureFlagEvaluationController::class, 'evaluate']);

// Damage report CRUD
Route::get('reports', [DamageReportController::class, 'index']);
Route::post('reports', [DamageReportController::class, 'store']);
Route::get('reports/{report}', [DamageReportController::class, 'show']);
Route::patch('reports/{report}', [DamageReportController::class, 'update']);

// Feature-gated mutations — middleware returns 410 when the feature flag is off.
Route::delete('reports/bulk', BulkDeleteReportsController::class)
    ->middleware('feature_flag:reports.bulk_actions');
Route::post('reports/{report}/photos', [ReportPhotosController::class, 'store'])
    ->middleware('feature_flag:reports.photo_attachments');

// Admin JSON API (Blade admin lives in routes/web.php at /admin/feature_flags)
Route::prefix('admin')->group(function (): void {
    Route::get('feature_flags', [FeatureFlagController::class, 'index']);
    Route::post('feature_flags', [FeatureFlagController::class, 'store']);
    Route::get('feature_flags/{flag}', [FeatureFlagController::class, 'show']);
    Route::patch('feature_flags/{flag}', [FeatureFlagController::class, 'update']);
    Route::delete('feature_flags/{flag}', [FeatureFlagController::class, 'destroy']);
});
