<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('organization/organization', 'Organization\OrganizationController');
Route::resource('organization/designation', 'Organization\DesignationController');
Route::resource('organization/role', 'Organization\RoleController');
Route::resource('organization/organization_user_detail', 'Organization\organization_user_detailController');
Route::resource('organization/organization-user-detail', 'Organization\OrganizationUserDetailController');
Route::resource('organization/specialities', 'Organization\SpecialitiesController');
Route::resource('organization/trusts', 'Organization\TrustsController');
Route::resource('organization/ward', 'Organization\WardController');
Route::resource('organization/traning', 'Organization\TraningController');
Route::resource('signees/signees-detail', 'Signees\SigneesDetailController');
Route::resource('signee/signee_organization', 'Signee\signee_organizationController');
Route::resource('signee/signee-organization', 'Signee\SigneeOrganizationController');
Route::resource('organization/organization-shift', 'Organization\OrganizationShiftController');
Route::resource('signees/signee-specialitie', 'Signees\SigneeSpecialitieController');
Route::resource('organization/booking', 'Organization\BookingController');
Route::resource('organization/booking-speciality', 'Organization\BookingSpecialityController');