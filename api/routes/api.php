<?php

use App\Http\Controllers\Api\FlagEvaluationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['name' => 'Fixico API', 'status' => 'ok']));

Route::post('/flags/evaluate', FlagEvaluationController::class);
