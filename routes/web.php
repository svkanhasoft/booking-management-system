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