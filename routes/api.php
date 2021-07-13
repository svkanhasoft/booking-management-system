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
    // Route::post('/signup-user', [App\Http\Controllers\API\Organization\UserController::class, 'create'])->name('signup-user');
    // Route::post('/signin-user', [App\Http\Controllers\API\Organization\UserController::class, 'signin'])->name('signin-user');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/get-detail', [App\Http\Controllers\API\Organization\OrganizationController::class, 'details'])->name('get-details');
        Route::get('/logout', [App\Http\Controllers\API\Organization\OrganizationController::class, 'logout'])->name('logout');
        Route::post('/add-role', [App\Http\Controllers\API\Organization\RoleController::class, 'create'])->name('add-role');
        Route::post('/edit-role', [App\Http\Controllers\API\Organization\RoleController::class, 'edit'])->name('edit-role');
        Route::get('/get-role/{id?}', [App\Http\Controllers\API\Organization\RoleController::class, 'show'])->name('get-role');
        Route::get('/get-all-role', [App\Http\Controllers\API\Organization\RoleController::class, 'showAll'])->name('get-all-role');
        Route::delete('/delete-role/{id?}', [App\Http\Controllers\API\Organization\RoleController::class, 'destroy'])->name('role-delete');
        Route::post('/change-status', [App\Http\Controllers\API\Organization\OrganizationController::class, 'changeStatus'])->name('change-status');
        Route::post('/search', [App\Http\Controllers\API\Organization\OrganizationController::class, 'search'])->name('search');
        Route::post('/add-speciality', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'create'])->name('add-speciality');
        Route::post('/edit-speciality', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'update'])->name('edit-speciality');
        Route::delete('/delete-speciality/{id?}', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'destroy'])->name('delete-speciality');
        Route::get('/get-speciality/{id?}', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'show'])->name('get-speciality');
        Route::get('/get-all-speciality', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'showAll'])->name('get-all-speciality');
        Route::post('/add-trust', [App\Http\Controllers\API\Organization\TrustsController::class, 'add'])->name('add-trust');
        Route::post('/update-trust', [App\Http\Controllers\API\Organization\TrustsController::class, 'update'])->name('update-trust');
        Route::get('/get-trust/{id}', [App\Http\Controllers\API\Organization\TrustsController::class, 'getTrustDetail'])->name('get-trust');
        Route::get('/get-all-trust', [App\Http\Controllers\API\Organization\TrustsController::class, 'getAllTrust'])->name('get-all-trust');
        Route::delete('/delete-trust/{id}', [App\Http\Controllers\API\Organization\TrustsController::class, 'destroy'])->name('delete-trust');
    });

    // Route::prefix('user')->group(function () {
    //     Route::post('/signup-user', [App\Http\Controllers\API\Organization\UserController::class, 'create'])->name('signup-user');
    //     Route::post('/signin-user', [App\Http\Controllers\API\Organization\UserController::class, 'signin'])->name('signin-user');
    //     Route::middleware(['auth:api'])->group(function () {
    //         Route::get('/get-user-details', [App\Http\Controllers\API\Organization\UserController::class, 'getDetails'])->name('get-user-details');
    //         Route::post('/user-change-password', [App\Http\Controllers\API\Organization\UserController::class, 'changePassword'])->name('user-change-password');
    //     });
    // });
});

Route::prefix('user')->group(function () {
    Route::post('/signup-user', [App\Http\Controllers\API\Organization\UserController::class, 'create'])->name('signup-user');
    Route::post('/signin-user', [App\Http\Controllers\API\Organization\UserController::class, 'signin'])->name('signin-user');
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/get-user-details', [App\Http\Controllers\API\Organization\UserController::class, 'getDetails'])->name('get-user-details');
        Route::post('/user-change-password', [App\Http\Controllers\API\Organization\UserController::class, 'changePassword'])->name('user-change-password');
    });
});

Route::prefix('signee')->group(function () {
    Route::post('/signup-signee', [App\Http\Controllers\API\Signees\SigneesDetailController::class, 'create'])->name('signup-signee');
    Route::post('/signin-signee', [App\Http\Controllers\API\Signees\SigneesDetailController::class, 'signin'])->name('signin-signee');
    Route::post('/signee-forgot', [App\Http\Controllers\API\Signees\SigneesDetailController::class, 'forgot'])->name('signee-forgot');
    Route::post('/reset-password/{id}', [App\Http\Controllers\API\Signees\SigneesDetailController::class, 'forgot'])->name('reset-password');
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/get-signee-details', [App\Http\Controllers\API\Signees\SigneesDetailController::class, 'getDetails'])->name('get-signee-details');
        Route::post('/signee-change-password', [App\Http\Controllers\API\Signees\SigneesDetailController::class, 'changePassword'])->name('signee-change-password');
        Route::post('/signee-profile-update', [App\Http\Controllers\API\Signees\SigneesDetailController::class, 'profileUpdate'])->name('signee-profile-update');
    });
});
