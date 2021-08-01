<?php

use App\Http\Controllers\MpesaController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/kenyan/stkCallbackUrl',[MpesaController::class,'mpesaRes'])->name('stk-res');

Route::get('/', function () {
    return view('wake.dad');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/pay', function () {return view('pay-page');});
Route::get('/confirm', function () {return view('mpesa.confirm');})->name('confirm-page');

Route::post('/confirm',[MpesaController::class,'confirm'])->name('confirm_payment');
