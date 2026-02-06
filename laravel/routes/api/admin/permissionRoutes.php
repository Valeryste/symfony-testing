<?php


use App\Http\Controllers\Api\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

Route::controller(PermissionController::class)
    ->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/routes', 'getListRoute')->name('routes');
        Route::post('', 'store')->name('store');

        Route::prefix('{permission}')->where(['permission' => '[0-9]+'])->group(function () {
            Route::patch('', 'update')->name('update');
        });
    });

