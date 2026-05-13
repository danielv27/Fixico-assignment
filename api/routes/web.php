<?php

use App\Http\Controllers\Admin\FlagWebController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/flags', [FlagWebController::class, 'index'])->name('flags.index');
    Route::get('/flags/create', [FlagWebController::class, 'create'])->name('flags.create');
    Route::post('/flags', [FlagWebController::class, 'store'])->name('flags.store');
    Route::get('/flags/{flag}/edit', [FlagWebController::class, 'edit'])->name('flags.edit');
    Route::patch('/flags/{flag}', [FlagWebController::class, 'update'])->name('flags.update');
    Route::delete('/flags/{flag}', [FlagWebController::class, 'destroy'])->name('flags.destroy');
});
