<?php

use App\Http\Controllers\Admin\ClaimController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('claims')->name('claims.')->group(function () {
    Route::get('/', [ClaimController::class, 'index'])->name('index');
    Route::get('/{claim}/review', [ClaimController::class, 'review'])->name('review');
    Route::post('/{claim}/approve', [ClaimController::class, 'approve'])->name('approve');
    Route::post('/{claim}/reject', [ClaimController::class, 'reject'])->name('reject');
});
