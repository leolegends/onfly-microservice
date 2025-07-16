<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group and "api" middleware group.
|
*/

Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'admin']], function () {
    
    // Dashboard routes
    Route::get('dashboard', [App\Http\Controllers\API\Admin\DashboardController::class, 'index']);
    Route::get('health', [App\Http\Controllers\API\Admin\DashboardController::class, 'health']);
    
    // User management routes
    Route::apiResource('users', App\Http\Controllers\API\Admin\UserController::class);
    
    // Travel request management routes
    Route::prefix('travel-requests')->group(function () {
        Route::get('/', [App\Http\Controllers\API\Admin\TravelRequestController::class, 'index']);
        Route::get('statistics', [App\Http\Controllers\API\Admin\TravelRequestController::class, 'statistics']);
        Route::get('{travelRequest}', [App\Http\Controllers\API\Admin\TravelRequestController::class, 'show']);
        Route::patch('{travelRequest}/approve', [App\Http\Controllers\API\Admin\TravelRequestController::class, 'approve']);
        Route::patch('{travelRequest}/reject', [App\Http\Controllers\API\Admin\TravelRequestController::class, 'reject']);
        Route::patch('{travelRequest}/cancel', [App\Http\Controllers\API\Admin\TravelRequestController::class, 'cancel']);
    });

    // System settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', function () {
            return response()->json([
                'settings' => [
                    'app_name' => config('app.name'),
                    'timezone' => config('app.timezone'),
                    'locale' => config('app.locale'),
                    'environment' => config('app.env'),
                ]
            ]);
        });
    });

});
