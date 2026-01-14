<?php


use App\Http\Controllers\Api\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

Route::controller(PermissionController::class)
    ->group(function () {
        Route::get('', 'index')->name('permission.index');
        Route::get('/routes', 'getListRoute')->name('permission.routes');
    });

