<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SellerController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/user', function (Request $request) {
    return "HELLO WORLD";
});


Route::get('order', [OrderController::class, 'index']);
Route::post('order', [OrderController::class, 'create']);
Route::put('order/{id}', [OrderController::class, 'update']);
Route::put('order/order_status/{id}', [OrderController::class, 'update_payment_status']);
Route::put('order/payment_status/{id}', [OrderController::class, 'update_order_status']);
Route::delete('order/{id}', [OrderController::class, 'delete']);
Route::get('company/search/{keyword}', [CompanyController::class, 'search']);
Route::get('client/search/{keyword}', [ClientController::class, 'search']);
Route::get('seller/search/{keyword}',  [SellerController::class, 'search']);