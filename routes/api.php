<?php

use App\Http\Controllers\Api\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{location}', [LocationController::class, 'show']);

    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{item}', [ItemController::class, 'show']);

    Route::middleware('user.only')->group(function () {
        Route::post('/items', [ItemController::class, 'store']);
        Route::match(['put', 'patch'], '/items/{item}', [ItemController::class, 'update']);
        Route::delete('/items/{item}', [ItemController::class, 'destroy']);

        Route::get('/claims', [ClaimController::class, 'index']);
        Route::get('/claims/{claim}', [ClaimController::class, 'show']);
        Route::patch('/claims/{claim}', [ClaimController::class, 'update']);
        Route::delete('/claims/{claim}', [ClaimController::class, 'destroy']);
        Route::post('/items/{item}/claims', [ClaimController::class, 'store']);
    });

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

    Route::get('/profile/declarations', [ProfileController::class, 'declarations']);
    Route::get('/profile/reclamations', [ProfileController::class, 'reclamations']);
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/claims', [AdminClaimController::class, 'index']);
        Route::get('/claims/{claim}', [AdminClaimController::class, 'show']);
        Route::post('/claims/{claim}/approve', [AdminClaimController::class, 'approve']);
        Route::post('/claims/{claim}/reject', [AdminClaimController::class, 'reject']);
    });
});
