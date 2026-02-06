<?php

use App\Http\Controllers\Api\Authentication\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticationController::class)
    ->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('register', 'register')->name('register');
        Route::post('user', 'user')->middleware('user.active')->name('user');
        Route::post('logout', 'logout')->middleware('user.active')->name('logout');
    });
