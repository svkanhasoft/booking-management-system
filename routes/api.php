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

Route::prefix('superadmin')->group(function () {
    Route::post('/signin', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'signin'])->name('signin');
    Route::post('/forgot', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'forgot'])->name('forgot');
    Route::post('/otp-verify', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'otpVerify'])->name('otp-verify');
    Route::post('/change-password', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'changePassword'])->name('change-password');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/get-detail', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'details'])->name('get-details');
        Route::get('/logout', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'logout'])->name('logout');
        Route::post('/add-designation', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'add'])->name('add-designation');
        Route::post('/edit-designation', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'edit'])->name('edit-designation');
        Route::get('/get-designation/{id?}', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'show'])->name('get-designation');
        Route::delete('/delete-designation/{id?}', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'destroy'])->name('delete-designation');
        Route::get('/get-designation-list', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'list'])->name('get-designation-list');
    });
});

Route::prefix('organization')->group(function () {
    Route::post('/signup', [App\Http\Controllers\API\Organization\OrganizationController::class, 'signup'])->name('signup');
    Route::post('/signin', [App\Http\Controllers\API\Organization\OrganizationController::class, 'signin'])->name('signin');
    Route::post('/forgot', [App\Http\Controllers\API\Organization\OrganizationController::class, 'forgot'])->name('forgot');
    Route::post('/otp-verify', [App\Http\Controllers\API\Organization\OrganizationController::class, 'otpVerify'])->name('otp-verify');
    Route::post('/change-password', [App\Http\Controllers\API\Organization\OrganizationController::class, 'changePassword'])->name('change-password');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/get-detail', [App\Http\Controllers\API\Organization\OrganizationController::class, 'details'])->name('get-details');
        Route::get('/logout', [App\Http\Controllers\API\Organization\OrganizationController::class, 'logout'])->name('logout');
        Route::post('/add-role', [App\Http\Controllers\API\Organization\RoleController::class, 'create'])->name('add-role');
    });
});
