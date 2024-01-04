<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOrderController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function(){
    return view('login');
});
// Login API
Route::post('/login', [LoginController::class,'login'])->name('login');
// Route::get('/login/{email}/{password}', [LoginController::class,'login'])->name('signin');
Route::get('/logout', [LoginController::class,'logout'])->middleware('auth:sanctum')->name('logout');
// Login as Guest
Route::post('/signup', [LoginController::class,'store'])->name('signup');
Route::post('/user/update', [LoginController::class,'userUpdate'])->middleware('auth:sanctum')->name('userUpdate');
// Get user part
Route::get('/user', [LoginController::class,'show'])->middleware('auth:sanctum');

// Service part
Route::post('/service/create',[ServiceController::class,'store'])->middleware('auth:sanctum');
Route::post('/service/{id}/update',[ServiceController::class,'update'])->middleware('auth:sanctum');
Route::post('/service/data',[ServiceController::class,'getAllServices']);

Route::get('/test/{id}', function (int $id) {
    return response()->json(['message' => 'This is a public API endpoint. this is the id : '.$id]);
});

Route::get('/service/{id}/view',[ServiceController::class ,'show'])->middleware('auth:sanctum') ;

Route::get('/service/agreement',[ServiceOrderController::class,'showAgreement']);
Route::post('/service/confirm-agreement/',[ServiceOrderController::class,'confirmAgreement'])->middleware('auth:sanctum');
Route::post('/service/purchase',[ServiceOrderController::class,'store'])->middleware('auth:sanctum');
Route::get('/service/ordered/freelancer',[ServiceOrderController::class,'showOrdersForFreelancer'])->middleware('auth:sanctum');

Route::get('/my-service/view',[ServiceController::class ,'showAllMyService'])->middleware('auth:sanctum') ;
