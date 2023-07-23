<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login',[App\Http\Controllers\UserController::class, 'login']);
    Route::get('/userlist',[App\Http\Controllers\UserController::class, 'userlist']);
    Route::post('/addbonus',[App\Http\Controllers\UserController::class, 'addbonus']);
    Route::post('/subbonus',[App\Http\Controllers\UserController::class, 'subbonus']);
    Route::get('/totalbonus/{username}',[App\Http\Controllers\UserController::class, 'totalbonus']);
    Route::get('/txnlist/{username}',[App\Http\Controllers\UserController::class, 'txnlist']);

});