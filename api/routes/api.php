<?php

use App\Http\Controllers\Admin\FlagController;
use App\Http\Controllers\Api\BulkDeleteReportsController;
use App\Http\Controllers\Api\DamageReportController;
use App\Http\Controllers\Api\FlagEvaluationController;
use App\Http\Controllers\Api\ReportPhotosController;
use Illuminate\Support\Facades\Route;

// All routes here are auto-prefixed with /api (Laravel default).

Route::get('/', fn () => response()->json(['name' => 'Fixico API', 'status' => 'ok']));

// Public client-facing flag evaluation
Route::post('flags/evaluate', [FlagEvaluationController::class, 'evaluate']);

// Damage report CRUD
Route::get('reports', [DamageReportController::class, 'index']);
Route::post('reports', [DamageReportController::class, 'store']);
Route::get('reports/{report}', [DamageReportController::class, 'show']);
Route::patch('reports/{report}', [DamageReportController::class, 'update']);

// Feature-gated mutations — middleware returns 410 when the flag is off.
Route::delete('reports/bulk', BulkDeleteReportsController::class)
    ->middleware('flag:reports.bulk_actions');
Route::post('reports/{report}/photos', [ReportPhotosController::class, 'store'])
    ->middleware('flag:reports.photo_attachments');

// Admin JSON API (Blade admin lives in routes/web.php at /admin/flags)
Route::prefix('admin')->group(function (): void {
    Route::get('flags', [FlagController::class, 'index']);
    Route::post('flags', [FlagController::class, 'store']);
    Route::get('flags/{flag}', [FlagController::class, 'show']);
    Route::patch('flags/{flag}', [FlagController::class, 'update']);
    Route::delete('flags/{flag}', [FlagController::class, 'destroy']);
});
