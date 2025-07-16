<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check route
Route::get('test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Load authentication routes
require __DIR__ . '/auth.php';

// Load user routes
require __DIR__ . '/user.php';

// Load admin routes
require __DIR__ . '/admin.php';

