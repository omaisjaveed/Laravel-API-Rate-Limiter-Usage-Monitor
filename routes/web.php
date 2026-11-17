<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\ApiClientController;
use App\Http\Controllers\Dashboard\ApiKeyController;
use App\Http\Controllers\Dashboard\ApiRequestLogController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\RateLimitController;
use App\Http\Controllers\Dashboard\ViolationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard.overview')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'overview'])->name('overview');

    Route::get('/clients', [ApiClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ApiClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ApiClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ApiClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ApiClientController::class, 'update'])->name('clients.update');
    Route::patch('/clients/{client}/toggle', [ApiClientController::class, 'toggle'])->name('clients.toggle');

    Route::get('/keys', [ApiKeyController::class, 'index'])->name('keys.index');
    Route::get('/keys/create', [ApiKeyController::class, 'create'])->name('keys.create');
    Route::post('/keys', [ApiKeyController::class, 'store'])->name('keys.store');
    Route::patch('/keys/{key}/revoke', [ApiKeyController::class, 'revoke'])->name('keys.revoke');
    Route::post('/keys/{key}/regenerate', [ApiKeyController::class, 'regenerate'])->name('keys.regenerate');

    Route::get('/rate-limits', [RateLimitController::class, 'index'])->name('rate-limits.index');
    Route::put('/rate-limits/{client}', [RateLimitController::class, 'update'])->name('rate-limits.update');

    Route::get('/logs', [ApiRequestLogController::class, 'index'])->name('logs.index');
    Route::get('/violations', [ViolationController::class, 'index'])->name('violations.index');
});
