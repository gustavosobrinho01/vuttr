<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Profile\ProfileController;
use App\Http\Controllers\API\Tool\ToolController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');
    });

Route::prefix('profile')
    ->name('profile.')
    ->group(function () {
        Route::post('register', [ProfileController::class, 'register'])->name('register');
        Route::put('update', [ProfileController::class, 'update'])->name('update');
        Route::put('update-password', [ProfileController::class, 'updatePassword'])->name('updatePassword');
        Route::delete('destroy', [ProfileController::class, 'destroy'])->name('destroy');
    });

Route::delete('destroy-all', [ToolController::class, 'destroyAll'])->name('tools.destroyAll');
Route::resource('tools', ToolController::class);
