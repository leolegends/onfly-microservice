<?php

use App\Http\Controllers\API\User\NotificationController;
use App\Http\Controllers\API\User\ProfileController;
use App\Http\Controllers\API\User\TravelRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register user API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "user" middleware group and "api" middleware group.
|
*/

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'user'], function () {
    
    // User profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/change-password', [ProfileController::class, 'changePassword']);
    });

    // Travel request routes for authenticated users
    Route::prefix('travel-requests')->group(function () {
        Route::get('/', [TravelRequestController::class, 'index']);
        Route::post('/', [TravelRequestController::class, 'store']);
        Route::get('/{travelRequest}', [TravelRequestController::class, 'show']);
        Route::put('/{travelRequest}', [TravelRequestController::class, 'update']);
        Route::patch('/{travelRequest}/cancel', [TravelRequestController::class, 'cancel']);
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/stats', [NotificationController::class, 'stats']);
    });
});
