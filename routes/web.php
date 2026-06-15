<?php

use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\ClaimsController;
use App\Http\Controllers\Web\ItemController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\Routing\RoutingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RoutingController::class, 'home'])->name('home');
Route::get('/dashboard', [RoutingController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard/category/{id}', [RoutingController::class, 'category'])->name('categories.show');
Route::get('/dashboard/location/{id}', [RoutingController::class, 'location'])->name('locations.show');

Route::middleware(['guest'])->group(function () {
    Route::get('/auth/login', [RoutingController::class, 'login'])->name('auth.login');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login.post');

    Route::get('/auth/register', [RoutingController::class, 'register'])->name('auth.register');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register.post');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');

    Route::get('/profile/edit', [RoutingController::class, 'profileEdit'])->name('profile.edit');
    Route::patch('/profile/edit', [AuthController::class, 'modify'])->name('profile.edit.post');

    Route::get('/profile/reclamations', [RoutingController::class, 'profileReclamations'])->name('profile.reclamations');
    Route::get('/profile/declarations', [RoutingController::class, 'profileDeclarations'])->name('profile.declarations');

    Route::middleware(['user.only'])->group(function () {
        Route::post('/claim/store/{item}', [ClaimsController::class, 'store'])->name('claims.store');

        Route::get('/declare/store', [RoutingController::class, 'declaration'])->name('item.declare');
        Route::post('/declare/store', [ItemController::class, 'declaration'])->name('item.declare.post');

        Route::delete('/profile/declarations/{item}', [ItemController::class, 'delete'])->name('items.destroy');
        Route::delete('/profile/reclamations/{claim}', [ClaimsController::class, 'delete'])->name('claims.destroy');

        Route::get('/profile/declarations/edit/{item}', [RoutingController::class, 'declarationModify'])->name('declaration.modify');
        Route::get('/profile/reclamations/edit/{claim}', [RoutingController::class, 'claimModify'])->name('claim.modify');

        Route::post('/profile/declarations/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::patch('/profile/reclamations/{claim}', [ClaimsController::class, 'update'])->name('claims.update');
    });
});
