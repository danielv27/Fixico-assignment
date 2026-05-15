<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FeatureFlagWebController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::redirect('/feature_flags', '/admin')->name('feature_flags.index');
    Route::get('/feature_flags/create', [FeatureFlagWebController::class, 'create'])->name('feature_flags.create');
    Route::post('/feature_flags', [FeatureFlagWebController::class, 'store'])->name('feature_flags.store');
    Route::get('/feature_flags/{flag}/edit', [FeatureFlagWebController::class, 'edit'])->name('feature_flags.edit');
    Route::patch('/feature_flags/{flag}', [FeatureFlagWebController::class, 'update'])->name('feature_flags.update');
    Route::delete('/feature_flags/{flag}', [FeatureFlagWebController::class, 'destroy'])->name('feature_flags.destroy');
});
