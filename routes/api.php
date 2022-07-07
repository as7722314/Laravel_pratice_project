<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [AuthController::class, 'authenticate']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logutService']);
Route::middleware('auth:sanctum')->get('/order/{id}', [OrderController::class, 'getByIdOrder']);

Route::middleware('auth:sanctum')->get('/checkout', [OrderController::class, 'checkout']);
Route::post('/checkout/callback', [OrderController::class, 'checkoutCallback']);
