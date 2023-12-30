<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ServiceController;

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
Route::get('/index1', [MasterController::class,'index'])->name('master');
// Route::get('/index', [MasterController::class,'index'])->name('home');
Route::get('/index', function(){return view('home');})->name('home');
// Route::get('/index2', [MasterController::class,'index'])->name('profile.edit');
Route::get('/index2', function(){return view('profile.edit');})->name('profile.edit');
Route::get('/index2update', function(){return view('profile.edit');})->name('profile.update');
Route::get('/index2password', function(){return view('profile.edit');})->name('profile.password');

// Route::get('/index3', [MasterController::class,'index'])->name('user.index');
Route::get('/index3',function(){return view('users.index');})->name('user.index');
Route::get('/page/{page}', [PageController::class,'index'])->name('page.index');


