<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\TrancsactionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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
// Route::get('/verify-email', [EmailController::class,'index'])->middleware('auth:sanctum');
// Email verification notice route
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth:sanctum')->name('verification.notice');


Route::get('/email/verify/{id}/{hash}', [EmailController::class,'verify'])->name('verification.verify');

// Resend email verification link route
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json([
        'verified'=>true,
        'status'=>'success',
        'msg'=>'Verify Email Sent Successfully! Please check you mail🚀!'
    ], 200);
})->middleware(['auth:sanctum','throttle:6,1'])->name('verification.send');



// Service part
Route::post('/service/create',[ServiceController::class,'store'])->middleware('auth:sanctum');
Route::post('/service/{id}/update',[ServiceController::class,'update'])->middleware('auth:sanctum');
Route::post('/service/data',[ServiceController::class,'getAllServices']);

Route::get('/test/{id}', function (int $id) {
    return response()->json(['message' => 'This is a public API endpoint. this is the id : '.$id]);
});

Route::get('/service/{id}/view',[ServiceController::class ,'show'])->middleware('auth:sanctum') ;


//Service Order
Route::get('/service/agreement',[ServiceOrderController::class,'showAgreement']);
Route::post('/service/purchase-summary/',[ServiceOrderController::class,'ShowSummary'])->middleware('auth:sanctum');
Route::post('/service/confirm-purchase/',[ServiceOrderController::class,'confirmPurchase'])->middleware('auth:sanctum');
Route::post('/service/purchase',[ServiceOrderController::class,'store'])->middleware('auth:sanctum');
Route::get('/service/ordered/freelancer',[ServiceOrderController::class,'showOrdersForFreelancer'])->middleware('auth:sanctum');

Route::get('/my-service/view',[ServiceController::class ,'showAllMyService'])->middleware('auth:sanctum') ;

Route::get('/order-service/{id}/view',[ServiceOrderController::class ,'show'])->middleware('auth:sanctum') ;
Route::post('/order-service/{id}/accept',[ServiceOrderController::class ,'accept'])->middleware('auth:sanctum') ;


//Transaction part
Route::post('/balance/top-up',[TrancsactionController::class ,'topUpBalance'])->middleware('auth:sanctum') ;

