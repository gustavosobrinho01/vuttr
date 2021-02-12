<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ToolController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('auth/destroy', [AuthController::class, 'destroy'])->name('auth.destroy');

        Route::get('user', function (Request $request) {
            return $request->user();
        });

        Route::apiResource('tools', ToolController::class);
    });

