<?php

use App\Http\Controllers\Api\DamageReportController;
use App\Http\Controllers\Api\FlagEvaluationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['name' => 'Fixico API', 'status' => 'ok']));

Route::post('/flags/evaluate', FlagEvaluationController::class);

Route::apiResource('reports', DamageReportController::class)
    ->parameters(['reports' => 'report'])
    ->except(['destroy']);
