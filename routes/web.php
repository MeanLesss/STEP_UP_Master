<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login_app', [LoginController::class,'index'])->name('login_app');
Route::post('/login_submit', [LoginController::class,'web_login'])->name('login_submit');
Route::get('/', function () {
    return view('welcome');
});
Route::get('/index', [MasterController::class,'index'])->name('master');
