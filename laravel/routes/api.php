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
Route::prefix('')->name('app.')->group(function () {
    Route::prefix('auth')->group(base_path('routes/api/authRoutes.php'));
    Route::prefix('admin')->middleware('auth:sanctum')->group(function() {
        Route::prefix('users')->group(base_path('routes/api/admin/userRoutes.php'));
        Route::prefix('permissions')->group(base_path('routes/api/admin/permissionRoutes.php'));
    });
});
