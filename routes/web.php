<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
