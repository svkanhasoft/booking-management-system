<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('admin')->group(function () {
    Route::post('/signin', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'signin'])->name('signin');
    Route::post('/forgot', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'forgot'])->name('forgot');
    Route::post('/otp-verify', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'otpVerify'])->name('otp-verify');
    Route::post('/change-password', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'changePassword'])->name('change-password');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/get-detail', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'details'])->name('get-details');
        Route::get('/logout', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'logout'])->name('logout');
    });
});
