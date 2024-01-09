<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/pay', [PaymentController::class, 'redirectToGateway']);
Route::post('/register', [AuthController::class, 'Signup']);
Route::post('/login', [AuthController::class, 'Login']);
Route::post('/checkIfUserHasPaid', [AuthController::class, 'checkIfUserHasPaid']);

