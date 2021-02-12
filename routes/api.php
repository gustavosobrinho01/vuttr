<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ToolController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::prefix('auth')
            ->name('auth.')
            ->group(function () {
                Route::put('update', [AuthController::class, 'update'])->name('update');
                Route::put('update-password', [AuthController::class, 'updatePassword'])->name('updatePassword');
                Route::post('logout', [AuthController::class, 'logout'])->name('logout');
                Route::delete('destroy', [AuthController::class, 'destroy'])->name('destroy');
            });

        Route::get('user', function (Request $request) {
            return $request->user();
        });

        Route::apiResource('tools', ToolController::class);
    });

