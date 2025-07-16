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

    // Manager routes (approve/reject requests)
    Route::group(['prefix' => 'manager', 'middleware' => ['manager']], function () {
        Route::prefix('travel-requests')->group(function () {
            Route::get('/pending', function () {
                return response()->json(['message' => 'List pending travel requests']);
            });
            Route::patch('/{id}/approve', function ($id) {
                return response()->json(['message' => "Approve travel request ID: {$id}"]);
            });
            Route::patch('/{id}/reject', function ($id) {
                return response()->json(['message' => "Reject travel request ID: {$id}"]);
            });
        });
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/stats', [NotificationController::class, 'stats']);
    });
});
