<?php

use App\Http\Controllers\Api\DamageReportController;
use App\Http\Controllers\Api\FlagEvaluationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['name' => 'Fixico API', 'status' => 'ok']));

Route::post('/flags/evaluate', [FlagEvaluationController::class, 'evaluate']);

Route::get('/reports', [DamageReportController::class, 'index']);
Route::post('/reports', [DamageReportController::class, 'store']);
Route::get('/reports/{report}', [DamageReportController::class, 'show']);
Route::patch('/reports/{report}', [DamageReportController::class, 'update']);
