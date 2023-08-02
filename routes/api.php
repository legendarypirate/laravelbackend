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

    //order section
    Route::get('/order/{name}',[App\Http\Controllers\OrderController::class, 'order']);
    Route::get('/doneorder/{name}',[App\Http\Controllers\OrderController::class, 'doneorder']);
    Route::get('/orderdetail/{id}',[App\Http\Controllers\OrderController::class, 'orderdetail']);
    Route::post('/delivered',[App\Http\Controllers\OrderController::class, 'delivered']);
    Route::post('/writecomment',[App\Http\Controllers\OrderController::class, 'writecomment']);
    Route::post('/decline',[App\Http\Controllers\OrderController::class, 'decline']);
    Route::get('/phoneinfo/{name}',[App\Http\Controllers\OrderController::class, 'phoneinfo']);
    Route::get('/addressinfo/{name}',[App\Http\Controllers\OrderController::class, 'addressinfo']);
    Route::post('/createorder',[App\Http\Controllers\OrderController::class, 'createorder']);

    Route::get('/totake/{shop}',[App\Http\Controllers\DeliveryController::class, 'totake']);
    Route::get('/taken/{shop}',[App\Http\Controllers\DeliveryController::class, 'taken']);

    //devliery section
    Route::get('/delivery/{name}',[App\Http\Controllers\DeliveryController::class, 'delivery']);
    Route::get('/donedelivery/{name}',[App\Http\Controllers\DeliveryController::class, 'donedelivery']);
    Route::get('/deliverydetail/{id}',[App\Http\Controllers\DeliveryController::class, 'deliverydetail']);
    Route::get('/delivered_delivery/{id}',[App\Http\Controllers\DeliveryController::class, 'delivered_delivery']);
    Route::post('/write',[App\Http\Controllers\DeliveryController::class, 'write']);
    Route::post('/decline_delivery',[App\Http\Controllers\DeliveryController::class, 'decline_delivery']);
    Route::post('/createdelivery',[App\Http\Controllers\DeliveryController::class, 'createdelivery']);
    Route::post('/updateindex',[App\Http\Controllers\DeliveryController::class, 'updateindex']);


    //customer api
    Route::post('/logincust',[App\Http\Controllers\UserController::class, 'logincust']);
    Route::get('/deliveryshop/{name}',[App\Http\Controllers\DeliveryController::class, 'deliveryshop']);
    Route::get('/ordershop/{name}',[App\Http\Controllers\OrderController::class, 'ordershop']);
    Route::get('/gooddata/{name}',[App\Http\Controllers\GoodController::class, 'gooddata']);
    Route::get('/gooddetail/{id}',[App\Http\Controllers\GoodController::class, 'gooddetail']);

});