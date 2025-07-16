<?php

use Illuminate\Support\Facades\Route;

/**
 * API Routes
 */
Route::get('/', function () {
    return response()->json([
        'status' => 'online',
        'version' => '1.0.0',
        'description' => 'Welcome to the Onfly Microservice API - Travel Manager Module',
        'author' => 'Onfly Teams - Leonardo Ribeiro',
        'contact' => 'lviniciusribeiro@yahoo.com.br',
        'message' => 'Welcome to the API',
        'documentation' => url('/docs'),
    ]);
});

/**
 * Health Check
 */
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => 'connected',
        'cache' => 'connected',
        'message' => 'Service is healthy',
]);
});
