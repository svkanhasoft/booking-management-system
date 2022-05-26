<?php

use App\Console\Commands\DeleteFilesCron;
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

Route::get('/status-cron', [App\Http\Controllers\API\ScriptController::class, 'statusCron']);
Route::get('/booking-notification-cron', [App\Http\Controllers\API\ScriptController::class, 'getBooking']);
Route::get('/document-cron', [App\Http\Controllers\API\ScriptController::class, 'documentCron']);
Route::get('/getData', [App\Http\Controllers\API\TestController::class, 'getData']);
Route::get('/getOrg', [App\Http\Controllers\API\TestController::class, 'getOrg']);
Route::get('/send-email', [App\Http\Controllers\API\TestController::class, 'sendEmail']);

// Route::resource('superadmin/plan', 'SuperAdmin\PlanController');