<?php

use App\Http\Controllers\Api\Authentication\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticationController::class)
    ->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('user', 'user')->middleware('auth:sanctum');
        Route::post('logout', 'logout')->middleware('auth:sanctum');
    });
