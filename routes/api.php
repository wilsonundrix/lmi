<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/mpesa/password',[MpesaController::class,'lipaNaMpesaPassword']);
Route::post('/mpesa/new/access/token',[MpesaController::class,'newAccessToken']);
Route::post('/mpesa/stk/push',[MpesaController::class,'stkPush'])->name('lipa');
Route::post('/stk/push/callback/url',[MpesaController::class,'mpesaRes'])->name('mpesa-res');



Route::post('/stk/push/query',[MpesaController::class,'STKPushQuery']);
//Route::post('/transaction/confirmation',[MpesaController::class,'mpesaConfirmation']);
Route::post('/transaction/confirmation',[MpesaController::class,'mpesaRes']);
Route::post('/transaction/validation',[MpesaController::class,'mpesaValidation']);
Route::post('/register/url',[MpesaController::class,'registerURLs']);
