<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Here is where you can register authentication routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "api" middleware group.
|
*/

Route::prefix('auth')->group(function () {
    
    // Public authentication routes
    Route::post('login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    
    Route::post('register', function () {
        return response()->json(['message' => 'Register endpoint']);
    });
    
    Route::post('forgot-password', function () {
        return response()->json(['message' => 'Forgot password endpoint']);
    });
    
    Route::post('reset-password', function () {
        return response()->json(['message' => 'Reset password endpoint']);
    });
    
    Route::post('verify-email', function () {
        return response()->json(['message' => 'Verify email endpoint']);
    });
    
    Route::post('resend-verification', function () {
        return response()->json(['message' => 'Resend verification email endpoint']);
    });
    
    // Protected authentication routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
        
        Route::get('me', [App\Http\Controllers\AuthController::class, 'me']);
        
        Route::post('refresh', function () {
            return response()->json(['message' => 'Refresh token endpoint']);
        });
    });
});
