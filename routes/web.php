<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOrderController;

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
    return view('landing');
});
Route::get('/index1', [MasterController::class,'index'])->name('master');
// Route::get('/index', [MasterController::class,'index'])->name('home');
Route::get('/email-verified', function(){
    return view('email.VerifyEmail');
})->name('email-verified');

Route::get('/index', function(){return view('home');})->name('home');
// Route::get('/index2', [MasterController::class,'index'])->name('profile.edit');
Route::get('/index2', function(){return view('profile.edit');})->name('profile.edit');
Route::get('/index2update', function(){return view('profile.edit');})->name('profile.update');
Route::get('/index2password', function(){return view('profile.edit');})->name('profile.password');

// Route::get('/index3', [MasterController::class,'index'])->name('user.index');
Route::get('/index3',function(){return view('users.index');})->name('user.index');
Route::get('/page/{page}', [PageController::class,'index'])->name('page.index');

//Service management
Route::get('/service/management',function(){return view('services.manage');})->name('service.management');
Route::post('/service/management/pending', [ServiceController::class,'getAllServicesWeb'])->name('service.web');
Route::post('/service/management/approval', [ServiceController::class,'serviceApproval'])->name('service.approval');


//Service order management
Route::get('/service/order/management',function(){return view('service_order.manage');})->name('service.order.management');
Route::post('/service/order/data', [ServiceOrderController::class,'showOrdersForWeb'])->name('service.order.data');
// Route::get('/service/order/getAllComplain', [ServiceOrderController::class,'serviceApproval'])->name('service.order.complain');


