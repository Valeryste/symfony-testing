<?php

use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)
    ->group(function () {
        Route::get('', 'index')->name('index');
        Route::prefix('{user}')->where(['user' => '[0-9]+'])->group(function () {
            Route::get('', 'show')->name('show');
            Route::patch('', 'update')->name('update');
            Route::delete('', 'delete')->name('delete');
        });
    });
