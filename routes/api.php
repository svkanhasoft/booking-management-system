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

Route::post('/forgot', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'forgot']);
Route::post('/reset-password', [App\Http\Controllers\API\Organization\OrganizationController::class, 'resetPassword']);

Route::prefix('superadmin')->group(function () {
    Route::post('/signin', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'signinV2']);
    Route::post('/otp-verify', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'otpVerify']);
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/change-password', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'changePassword']);
        Route::get('/get-detail', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'details']);
        Route::post('/update-profile', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'updates']);
        Route::get('/logout', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'logout']);
        Route::post('/add-designation', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'add']);
        Route::post('/edit-designation', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'edit']);
        Route::get('/get-designation/{id?}', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'show']);
        Route::delete('/delete-designation/{id?}', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'destroy']);
        // Route::get('/get-designation-list', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'list']);
        Route::get('/get-organization-detail/{id?}', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'getOrgdetails']);
        Route::post('/update-org', [App\Http\Controllers\API\SuperAdmin\SuperAdminController::class, 'updateorg']);
        Route::post('/signup', [App\Http\Controllers\API\Organization\OrganizationController::class, 'signup']);
        Route::get('/organization-list/{search?}/{status?}', [App\Http\Controllers\API\Organization\OrganizationController::class, 'organizationlist']);
    });
});


Route::prefix('organization')->group(function () {
    Route::post('/signin', [App\Http\Controllers\API\Organization\OrganizationController::class, 'signin']);
    // Route::post('/forgot', [App\Http\Controllers\API\Organization\OrganizationController::class, 'forgot']);
    Route::post('/otp-verify', [App\Http\Controllers\API\Organization\OrganizationController::class, 'otpVerify']);
    //Route::post('/reset-password', [App\Http\Controllers\API\Signees\OrganizationController::class, 'resetPassword']);
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/get-designation-list', [App\Http\Controllers\API\SuperAdmin\DesignationController::class, 'list']);
        Route::post('/update', [App\Http\Controllers\API\Organization\OrganizationController::class, 'update']);
        Route::post('/change-password', [App\Http\Controllers\API\Organization\OrganizationController::class, 'changePassword']);
        Route::get('/get-detail', [App\Http\Controllers\API\Organization\OrganizationController::class, 'details']);
        Route::get('/logout', [App\Http\Controllers\API\Organization\OrganizationController::class, 'logout']);
        Route::post('/add-role', [App\Http\Controllers\API\Organization\RoleController::class, 'create']);
        Route::post('/edit-role', [App\Http\Controllers\API\Organization\RoleController::class, 'edit']);
        Route::get('/get-role/{id?}', [App\Http\Controllers\API\Organization\RoleController::class, 'show']);
        Route::get('/get-all-role', [App\Http\Controllers\API\Organization\RoleController::class, 'showAll']);
        Route::delete('/delete-role/{id?}', [App\Http\Controllers\API\Organization\RoleController::class, 'destroy']);
        Route::post('/change-status', [App\Http\Controllers\API\Organization\OrganizationController::class, 'changeStatus']);
        Route::post('/search', [App\Http\Controllers\API\Organization\OrganizationController::class, 'search']);
        Route::post('/add-speciality', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'create']);
        Route::post('/edit-speciality', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'update']);
        Route::delete('/delete-speciality/{id?}', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'destroy']);
        Route::get('/get-speciality/{id?}', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'show']);
        Route::get('/get-all-speciality/{search?}', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'showAll']);
        Route::get('/all-speciality', [App\Http\Controllers\API\Organization\SpecialitiesController::class, 'AllSpeciality']);
        Route::post('/add-trust', [App\Http\Controllers\API\Organization\TrustsController::class, 'add']);
        Route::post('/update-trust', [App\Http\Controllers\API\Organization\TrustsController::class, 'update']);
        Route::get('/get-trust/{id?}', [App\Http\Controllers\API\Organization\TrustsController::class, 'getTrustDetail']);
        Route::delete('/delete-trust/{id}', [App\Http\Controllers\API\Organization\TrustsController::class, 'destroy']);
        Route::post('/add-shift', [App\Http\Controllers\API\Organization\OrganizationShiftController::class, 'create']);
        Route::post('/edit-shift', [App\Http\Controllers\API\Organization\OrganizationShiftController::class, 'edit']);
        Route::get('/get-shift/{id?}', [App\Http\Controllers\API\Organization\OrganizationShiftController::class, 'show']);
        Route::get('/get-shifts', [App\Http\Controllers\API\Organization\OrganizationShiftController::class, 'showAll']);
        Route::DELETE('/delete-shift/{id?}', [App\Http\Controllers\API\Organization\OrganizationShiftController::class, 'destroy']);
        Route::post('/add-booking', [App\Http\Controllers\API\Organization\BookingController::class, 'add']);
        Route::post('/edit-booking', [App\Http\Controllers\API\Organization\BookingController::class, 'edit']);
        Route::get('/add-match/{id}', [App\Http\Controllers\API\Organization\BookingController::class, 'getMetchByBookingId']);
        Route::get('/update-match/{id}', [App\Http\Controllers\API\Organization\BookingController::class, 'updateMatchBySignee']);
        Route::get('/get-booking/{id}', [App\Http\Controllers\API\Organization\BookingController::class, 'show']);
        Route::get('/booking-by-status/{search?}/{status?}', [App\Http\Controllers\API\Organization\BookingController::class, 'bookingStatus']);
        Route::post('/change-booking-status', [App\Http\Controllers\API\Organization\BookingController::class, 'changeBookingStatus']);
        Route::delete('/delete-booking/{id?}', [App\Http\Controllers\API\Organization\BookingController::class, 'destroy']);
        Route::get('/get-shift-type/{id?}', [App\Http\Controllers\API\Organization\ShiftTypeController::class, 'show']);
        Route::get('/get-all-shift-type', [App\Http\Controllers\API\Organization\ShiftTypeController::class, 'showAll']);
        Route::get('/get-ward-type/{id?}', [App\Http\Controllers\API\Organization\WardTypeController::class, 'show']);
        Route::get('/get-all-ward-type', [App\Http\Controllers\API\Organization\WardTypeController::class, 'showAll']);
        Route::get('/get-signee-detail', [App\Http\Controllers\API\Organization\BookingController::class, 'getSigneeByIdAndBookingId']);
        Route::get('/get-booking-user/{id?}', [App\Http\Controllers\API\Organization\BookingController::class, 'getBookingSignee']);
        Route::get('/get-hospitallist/{trustId?}', [App\Http\Controllers\API\Organization\BookingController::class, 'hospitallist']);
        Route::get('/get-ward-by-hospital', [App\Http\Controllers\API\Organization\BookingController::class, 'getWardByHospitalAndTrust']);
        Route::get('/get-gradelist', [App\Http\Controllers\API\Organization\BookingController::class, 'gradelist']);
        Route::get('/get-reference', [App\Http\Controllers\API\Organization\BookingController::class, 'reference']);
        Route::post('/add-signee', [App\Http\Controllers\API\Organization\UserController::class, 'addSignee']);
        Route::get('/get-signee', [App\Http\Controllers\API\Organization\UserController::class, 'viewSignee']);
        Route::put('/edit-signee', [App\Http\Controllers\API\Organization\UserController::class, 'editSignee']);
        Route::delete('/delete-signee/{id}', [App\Http\Controllers\API\Organization\UserController::class, 'deleteSignee']);
        Route::get('/get-candidate', [App\Http\Controllers\API\Organization\UserController::class, 'getCandidate']);
        Route::get('/get-signee/{id}', [App\Http\Controllers\API\Organization\UserController::class, 'getSignee']);
        

        /* ROUTE FOR STAFF USSER CREATE BY ORGANIZATION ADMIN  */

        Route::prefix('user')->group(function () {
            Route::post('/signup-user', [App\Http\Controllers\API\Organization\UserController::class, 'signup']);
            Route::post('/signin-user', [App\Http\Controllers\API\Organization\UserController::class, 'signin']);
            Route::post('/reset-password', [App\Http\Controllers\API\Signees\UserController::class, 'resetPassword']);
            Route::middleware(['auth:api'])->group(function () {
                Route::get('/get-user-details', [App\Http\Controllers\API\Organization\UserController::class, 'getDetails']);
                Route::get('/get-user-list/{search?}', [App\Http\Controllers\API\Organization\UserController::class, 'getuserlist']);
                Route::get('/get-user/{id}', [App\Http\Controllers\API\Organization\UserController::class, 'getuserById']);
                Route::put('/user-change-password', [App\Http\Controllers\API\Organization\UserController::class, 'changePassword']);
                Route::put('/edit-user', [App\Http\Controllers\API\Organization\UserController::class, 'update']);
                Route::delete('/delete-user/{id}', [App\Http\Controllers\API\Organization\UserController::class, 'destroy']);
                Route::post('/staff-profile-update', [App\Http\Controllers\API\Organization\UserController::class, 'profileUpdate']);
            });
        });
    });
});

// Route::prefix('staff')->middleware(['auth:api'])->group(function () {
//     Route::get('/staff-booking-list/{search?}/{status?}', [App\Http\Controllers\API\Staff\BookingController::class, 'staffBooking']);
//     Route::post('/add-staff-booking', [App\Http\Controllers\API\Staff\BookingController::class, 'add']);
//     Route::put('/edit-staff-booking', [App\Http\Controllers\API\Staff\BookingController::class, 'edit']);
//     Route::delete('/delete-staff-booking/{id}', [App\Http\Controllers\API\Staff\BookingController::class, 'destroy']);
// });

Route::prefix('signee')->group(function () {
    Route::post('/signup-signee', [App\Http\Controllers\API\Signees\SigneesController::class, 'signup']);
    Route::post('/signin', [App\Http\Controllers\API\Signees\SigneesController::class, 'signin']);
    Route::post('/forgot-signee', [App\Http\Controllers\API\Signees\SigneesController::class, 'forgot']);
    Route::post('/reset-passwordV2/{id}', [App\Http\Controllers\API\Signees\SigneesController::class, 'resetPassword']);
    Route::post('/reset-password', [App\Http\Controllers\API\Signees\SigneesController::class, 'resetPassword']);
    Route::get('/candidate-referred-from', [App\Http\Controllers\API\Signees\SigneesController::class, 'getCandidateReferredFrom']);
    Route::get('/get-organisation', [App\Http\Controllers\API\Signees\SigneesController::class, 'getOrganisation']);
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/get-signee-details', [App\Http\Controllers\API\Signees\SigneesController::class, 'getDetails']);
        Route::post('/signee-change-password', [App\Http\Controllers\API\Signees\SigneesController::class, 'changePassword']);
        Route::post('/signee-profile-update', [App\Http\Controllers\API\Signees\SigneesController::class, 'profileUpdate']);
        Route::post('/signee-delete', [App\Http\Controllers\API\Signees\SigneesController::class, 'delete']);
        Route::post('/availability', [App\Http\Controllers\API\Signees\AvailabilityController::class, 'availability']);
        Route::get('/get-availability', [App\Http\Controllers\API\Signees\AvailabilityController::class, 'getAvailability']);
        Route::post('/add-preferences', [App\Http\Controllers\API\Signees\SigneePreferencesController::class, 'addPreferences']);
    });
});

Route::get('/test/{id}', [App\Http\Controllers\API\TestController::class, 'test']);
