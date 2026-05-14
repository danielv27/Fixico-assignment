<?php

use App\Http\Controllers\Admin\FlagController;
use App\Http\Controllers\Api\BulkDeleteReportsController;
use App\Http\Controllers\Api\DamageReportController;
use App\Http\Controllers\Api\FlagEvaluationController;
use App\Http\Controllers\Api\ReportPhotosController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['name' => 'Fixico API', 'status' => 'ok']));

Route::post('/flags/evaluate', [FlagEvaluationController::class, 'evaluate']);

// Admin JSON API — prefixed with /api to coexist with the Blade admin at /admin/flags.
Route::prefix('api')->group(function (): void {
    Route::get('/admin/flags', [FlagController::class, 'index']);
    Route::post('/admin/flags', [FlagController::class, 'store']);
    Route::get('/admin/flags/{flag}', [FlagController::class, 'show']);
    Route::patch('/admin/flags/{flag}', [FlagController::class, 'update']);
    Route::delete('/admin/flags/{flag}', [FlagController::class, 'destroy']);
});

Route::get('/reports', [DamageReportController::class, 'index']);
Route::post('/reports', [DamageReportController::class, 'store']);
Route::get('/reports/{report}', [DamageReportController::class, 'show']);
Route::patch('/reports/{report}', [DamageReportController::class, 'update']);

// Feature-gated mutations — return 410 when the backing flag is disabled.
Route::post('/reports/{report}/photos', [ReportPhotosController::class, 'store'])
    ->middleware('flag:reports.photo_attachments');
// This is the server-side enforcement of the stale-interaction contract:
// even if the client already rendered the UI, the server never executes
// a flagged mutation when the flag is off.
Route::delete('/reports/bulk', BulkDeleteReportsController::class)
    ->middleware('flag:reports.bulk_actions');
