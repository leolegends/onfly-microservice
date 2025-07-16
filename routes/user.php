<?php

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
        Route::get('/', function () {
            return response()->json(['message' => 'User profile']);
        });
        Route::put('/', function () {
            return response()->json(['message' => 'Update user profile']);
        });
        Route::post('/change-password', function () {
            return response()->json(['message' => 'Change password']);
        });
    });

    // Travel request routes for authenticated users
    Route::prefix('travel-requests')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'List my travel requests']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "My travel request details for ID: {$id}"]);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Create new travel request']);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Update my travel request ID: {$id}"]);
        });
        Route::patch('/{id}/cancel', function ($id) {
            return response()->json(['message' => "Cancel my travel request ID: {$id}"]);
        });
        Route::get('/{id}/history', function ($id) {
            return response()->json(['message' => "Travel request history for ID: {$id}"]);
        });
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
        Route::get('/', function () {
            return response()->json(['message' => 'List notifications']);
        });
        Route::patch('/{id}/read', function ($id) {
            return response()->json(['message' => "Mark notification as read ID: {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Delete notification ID: {$id}"]);
        });
    });
});
