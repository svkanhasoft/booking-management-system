<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SuperAdmin\{ SuperAdminController,DesignationController };
use App\Http\Controllers\API\Organization\{OrganizationController,RoleController };
use App\Http\Controllers\API\Organization\SpecialitiesController;
use App\Http\Controllers\API\Organization\{ TrustsController,OrganizationShiftController,BookingController,ShiftTypeController,WardTypeController,UserController };
use App\Http\Controllers\API\Signees\{ UserController as SigneesUserController,SigneesController,AvailabilityController,SigneePreferencesController,HospitalController };
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\DashboardController;
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

Route::post('/forgot', [SuperAdminController::class, 'forgot']);
Route::post('/reset-password', [OrganizationController::class, 'resetPassword']);

Route::prefix('superadmin')->group(function () {
    Route::post('/signin', [SuperAdminController::class, 'signinV2']);
    Route::post('/otp-verify', [SuperAdminController::class, 'otpVerify']);
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/change-password', [SuperAdminController::class, 'changePassword']);
        Route::get('/get-detail', [SuperAdminController::class, 'details']);
        Route::post('/update-profile', [SuperAdminController::class, 'updates']);
        Route::get('/logout', [SuperAdminController::class, 'logout']);
        Route::post('/add-designation', [DesignationController::class, 'add']);
        Route::post('/edit-designation', [DesignationController::class, 'edit']);
        Route::get('/get-designation/{id?}', [DesignationController::class, 'show']);
        Route::delete('/delete-designation/{id?}', [DesignationController::class, 'destroy']);
        // Route::get('/get-designation-list', [DesignationController::class, 'list']);
        Route::get('/get-organization-detail/{id?}', [SuperAdminController::class, 'getOrgdetails']);
        Route::post('/update-org', [SuperAdminController::class, 'updateorg']);
        Route::post('/signup', [OrganizationController::class, 'signup']);
        Route::get('/organization-list/{search?}/{status?}', [OrganizationController::class, 'organizationlist']);
        Route::put('/change-org-activity-status', [SuperAdminController::class, 'ChangeOrgActivityStatus']);
    });
});


Route::prefix('organization')->group(function () {
    Route::post('/signin', [OrganizationController::class, 'signin']);
    // Route::post('/forgot', [OrganizationController::class, 'forgot']);
    Route::post('/otp-verify', [OrganizationController::class, 'otpVerify']);
    //Route::post('/reset-password', [App\Http\Controllers\API\Signees\OrganizationController::class, 'resetPassword']);
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/get-designation-list', [DesignationController::class, 'list']);
        Route::post('/update', [OrganizationController::class, 'update']);
        Route::post('/change-password', [OrganizationController::class, 'changePassword']);
        Route::get('/get-detail', [OrganizationController::class, 'details']);
        Route::get('/logout', [OrganizationController::class, 'logout']);
        Route::post('/add-role', [RoleController::class, 'create']);
        Route::post('/edit-role', [RoleController::class, 'edit']);
        Route::get('/get-role/{id?}', [RoleController::class, 'show']);
        Route::get('/get-all-role', [RoleController::class, 'showAll']);
        Route::delete('/delete-role/{id?}', [RoleController::class, 'destroy']);
        // Route::post('/change-status', [OrganizationController::class, 'changeStatus']);
        Route::post('/search', [OrganizationController::class, 'search']);
        Route::post('/add-speciality', [SpecialitiesController::class, 'create']);
        Route::post('/edit-speciality', [SpecialitiesController::class, 'update']);
        Route::delete('/delete-speciality/{id?}', [SpecialitiesController::class, 'destroy']);
        Route::get('/get-speciality/{id?}', [SpecialitiesController::class, 'show']);
        Route::get('/get-all-speciality/{search?}', [SpecialitiesController::class, 'showAll']);
        // Route::get('/all-speciality', [SpecialitiesController::class, 'AllSpeciality']);
        Route::post('/add-trust', [TrustsController::class, 'add']);
        Route::post('/update-trust', [TrustsController::class, 'update']);
        Route::get('/get-trust/{id?}', [TrustsController::class, 'getTrustDetail']);
        Route::delete('/delete-trust/{id}', [TrustsController::class, 'destroy']);
        Route::post('/add-shift', [OrganizationShiftController::class, 'create']);
        Route::post('/edit-shift', [OrganizationShiftController::class, 'edit']);
        Route::get('/get-shift/{id?}', [OrganizationShiftController::class, 'show']);
        Route::get('/get-shifts', [OrganizationShiftController::class, 'showAll']);
        Route::DELETE('/delete-shift/{id?}', [OrganizationShiftController::class, 'destroy']);
        Route::post('/add-booking', [BookingController::class, 'add']);
        Route::post('/edit-booking', [BookingController::class, 'edit']);
        Route::get('/get-booking-match/{id}', [BookingController::class, 'getMetchByBookingId']);
        Route::get('/update-match/{id}', [BookingController::class, 'updateMatchBySignee']);
        Route::get('/get-booking/{id}', [BookingController::class, 'show']);
        Route::get('/booking-by-status/{search?}/{status?}', [BookingController::class, 'bookingStatus']);
        // Route::post('/change-booking-status', [BookingController::class, 'changeBookingStatus']);
        Route::delete('/delete-booking/{id?}', [BookingController::class, 'destroy']);
        Route::get('/get-shift-type/{id?}', [ShiftTypeController::class, 'show']);
        Route::get('/get-all-shift-type', [ShiftTypeController::class, 'showAll']);
        Route::get('/get-ward-type/{id?}', [WardTypeController::class, 'show']);
        Route::get('/get-all-ward-type', [WardTypeController::class, 'showAll']);
        Route::get('/get-signee-detail', [BookingController::class, 'getSigneeByIdAndBookingId']);
        Route::get('/get-booking-user/{id?}', [BookingController::class, 'getBookingSignee']);
        Route::get('/get-hospitallist/{trustId?}', [BookingController::class, 'hospitallist']);
        Route::get('/get-ward-by-hospital', [BookingController::class, 'getWardByHospital']);
        Route::get('/get-gradelist', [BookingController::class, 'gradelist']);
        Route::get('/get-reference', [BookingController::class, 'reference']);
        Route::post('/add-signee', [UserController::class, 'addSignee']);
        Route::get('/get-signee/{search?}', [UserController::class, 'viewSignee']);
        Route::put('/edit-signee', [UserController::class, 'editSignee']);
        Route::delete('/delete-signee/{id}', [UserController::class, 'deleteSignee']);
        Route::get('/get-candidate', [UserController::class, 'getCandidate']);
        Route::get('/get-my-signee/{id}', [UserController::class, 'getMySigneeById']);    //get signee by id from org
        Route::post('/change-signee-compliance-status', [SigneesController::class, 'changeSigneeComplianceStatus']);
        Route::put('/change-signee-payment-status', [UserController::class, 'changeSigneePaymentStatus']);
        Route::post('/confirm-booking', [UserController::class, 'bookingStatus']);
        Route::get('/get-spec-shift-create', [SpecialitiesController::class, 'getSpecialtyWithoutPagination']);
        //Route::get('/get-completed-shift', [UserController::class, 'getCompletedShift']);
        /* ROUTE FOR STAFF USSER CREATE BY ORGANIZATION ADMIN  */

        Route::prefix('user')->group(function () {
            Route::post('/signup-user', [UserController::class, 'signup']);
            Route::post('/signin-user', [UserController::class, 'signin']);
            Route::post('/reset-password', [SigneesUserController::class, 'resetPassword']);
            Route::middleware(['auth:api'])->group(function () {
                Route::get('/get-user-details', [UserController::class, 'getDetails']);
                Route::get('/get-user-list/{search?}', [UserController::class, 'getuserlist']);
                Route::get('/get-user/{id}', [UserController::class, 'getuserById']);
                Route::put('/user-change-password', [UserController::class, 'changePassword']);
                Route::put('/edit-user', [UserController::class, 'update']);
                Route::delete('/delete-user/{id}', [UserController::class, 'destroy']);
                Route::post('/staff-profile-update', [UserController::class, 'profileUpdate']);
                Route::post('/change-shift-status', [UserController::class, 'changeShiftStatus']);
                Route::put('/change-signee-profile-status', [UserController::class, 'changeSigneeProfileStatus']);
                // Route::post('/confirm-booking', [UserController::class, 'confirmBooking']);
                Route::post('/pdf', [UserController::class, 'pdf']);
                Route::post('/change-document-status', [UserController::class, 'changeDocStatus']);
                Route::post('/send-invitation', [UserController::class, 'inviteSigneeForTheShift']);
                Route::post('/get-all-notification', [UserController::class, 'getAllNotifications']);
                Route::post('/update-notification', [UserController::class, 'updateNotifications']);
            });
        });
    });
});

Route::prefix('signee')->group(function () {
    Route::get('/add-signee-match/{id}', [SigneesController::class, 'addsigneeMatch']);
    Route::post('/signup', [SigneesController::class, 'signup']);
    Route::post('/signin', [SigneesController::class, 'signin']);
    Route::post('/forgot-signee', [SigneesController::class, 'forgot']);
    Route::post('/reset-passwordV2/{id}', [SigneesController::class, 'resetPassword']);
    Route::post('/reset-password', [SigneesController::class, 'resetPassword']);
    Route::get('/candidate-referred-from', [SigneesController::class, 'getCandidateReferredFrom']);
    Route::get('/get-organisation', [SigneesController::class, 'getOrganisation']);
    Route::get('/get-org-specialities/{id}', [SigneesController::class, 'getOrgSpecialities']);
    Route::get('/generate-candidateId', [SigneesController::class, 'getCandidateId']);
    Route::post('/get-email-organisation', [SigneesController::class, 'getEmailOrganisation']);
    // Route::get('/show-all-hospital', [App\Http\Controllers\API\Signees\HospitalController::class, 'showAllHospital']);
    // Route::get('/show-all-speciality', [App\Http\Controllers\API\Signees\HospitalController::class, 'showAllSpeciality']);
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/get-signee-details', [SigneesController::class, 'getDetails']);
        Route::post('/signee-change-password', [SigneesController::class, 'changePassword']);
        Route::post('/signee-profile-update', [SigneesController::class, 'profileUpdate']);
        Route::post('/signee-delete', [SigneesController::class, 'delete']);
        Route::post('/availability', [AvailabilityController::class, 'availability']);
        Route::get('/get-availability', [AvailabilityController::class, 'getAvailability']);
        Route::put('/add-preferences', [SigneePreferencesController::class, 'addPreferences']);
        Route::get('/get-preferences', [SigneePreferencesController::class, 'getPreferences']);
        // Route::post('/shift-list', [SigneesController::class, 'shiftList']);
        Route::match(['get', 'put'], '/shift-list', [SigneesController::class, 'shiftList']);
        Route::get('/my-shift', [SigneesController::class, 'myshift']);
        Route::get('/view-shift-details/{id}', [SigneesController::class, 'viewShiftDetails']);
        Route::get('/logout', [SigneesController::class, 'logout']);
        Route::put('/filter-shift', [SigneesController::class, 'filterBookings']);
        Route::get('/show-all-speciality', [HospitalController::class, 'showAllSpeciality']);
        Route::get('/show-all-hospital', [HospitalController::class, 'showAllHospital']);
        Route::post('/add-org', [SigneesController::class, 'addOrg']);
        Route::post('/upload-document', [SigneesController::class, 'documentUpload']);
        Route::put('/update-signee-speciality/{id}', [SigneesController::class, 'updateSpeciality']);
        Route::get('/get-signee-speciality', [SigneesController::class, 'getSigneeSpeciality']);
        Route::get('/get-signee-document/{key?}', [SigneesController::class, 'getSigneeDocument']);
        Route::delete('/delete-document/{id}', [SigneesController::class, 'deleteDocument']);
        //Route::get('/get-organisation', [SigneesController::class, 'getOrganisation']);
        Route::get('/get-organisation-add-org', [SigneesController::class, 'getOrganisationListAddOrg']);
        Route::post('/multi-org-login/{organization_id?}', [SigneesController::class, 'multiOrgLogin']);
        Route::post('/apply-shift', [SigneesController::class, 'applyShift']);
        Route::get('/get-applied-shift', [UserController::class, 'getAppliedShift']);
    });
});

Route::get('/test/{id}', [TestController::class, 'test']);
Route::get('/inactive', [TestController::class, 'inactive']);
Route::get('/send-notification', [TestController::class, 'notification']);
Route::get('/dashboard', [DashboardController::class, 'totalUser']);
// Route::get('/pdf/{signee_id?}', [TestController::class, 'pdf']);
