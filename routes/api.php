<?php

use App\Http\Controllers\DollarController;
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

Route::get('/kenyan/password',[MpesaController::class,'lipaNaMpesaPassword']);
Route::post('/kenyan/token',[MpesaController::class,'newAccessToken']);
Route::post('/kenyan/stk/push',[MpesaController::class,'stkPush'])->name('lipa');
Route::post('/kenyan/stk/push/callbackUrl',[MpesaController::class,'mpesaRes'])->name('mpesa-res');

Route::get('/dollar/password',[MpesaController::class,'lipaNaMpesaPassword']);
Route::post('dollar/token', [DollarController::class,'generateAccessToken']);
Route::post('dollar/register/url', [DollarController::class,'mpesaRegisterUrls']);
Route::post('dollar/stk/push', [DollarController::class,'customerMpesaSTKPush']);
Route::post('dollar/validation', [DollarController::class,'mpesaValidation']);
Route::post('dollar/transaction/confirmation', [DollarController::class,'mpesaConfirmation']);

// Route::post('/stk/push/query',[MpesaController::class,'STKPushQuery']);
//Route::post('/transaction/confirmation',[MpesaController::class,'mpesaConfirmation']);
// Route::post('/transaction/confirmation',[MpesaController::class,'mpesaRes']);
// Route::post('/transaction/validation',[MpesaController::class,'mpesaValidation']);
// Route::post('/register/url',[MpesaController::class,'registerURLs']);
