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
Route::get('/getData', [App\Http\Controllers\API\TestController::class, 'getData']);
