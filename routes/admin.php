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
    
    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'List of users']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "User details for ID: {$id}"]);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Create new user']);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Update user ID: {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Delete user ID: {$id}"]);
        });
    });

    // Travel request management routes
    Route::prefix('travel-requests')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'List all travel requests']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "Travel request details for ID: {$id}"]);
        });
        Route::patch('/{id}/approve', function ($id) {
            return response()->json(['message' => "Approve travel request ID: {$id}"]);
        });
        Route::patch('/{id}/reject', function ($id) {
            return response()->json(['message' => "Reject travel request ID: {$id}"]);
        });
        Route::patch('/{id}/cancel', function ($id) {
            return response()->json(['message' => "Cancel travel request ID: {$id}"]);
        });
        Route::get('/statistics', function () {
            return response()->json(['message' => 'Travel request statistics']);
        });
    });

    // Department management routes
    Route::prefix('departments')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'List of departments']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Create new department']);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Update department ID: {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Delete department ID: {$id}"]);
        });
    });

    // System settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'System settings']);
        });
        Route::put('/', function () {
            return response()->json(['message' => 'Update system settings']);
        });
    });

    // Reports routes
    Route::prefix('reports')->group(function () {
        Route::get('/travel-requests', function () {
            return response()->json(['message' => 'Travel requests report']);
        });
        Route::get('/users', function () {
            return response()->json(['message' => 'Users report']);
        });
        Route::get('/departments', function () {
            return response()->json(['message' => 'Departments report']);
        });
    });
});
