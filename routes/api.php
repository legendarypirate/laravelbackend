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

Route::middleware('auth:sanctum', 'https')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [App\Http\Controllers\UserController::class, 'login'])->name('login');
    Route::get('/driver/medeelel', [App\Http\Controllers\DriverController::class, 'getDriverLocations'])->name('api.driver.locations');
    Route::get('/driver/locations', [App\Http\Controllers\DriverController::class, 'getDriverLocations'])->name('api.driver.locations.get');
    Route::get('/driver/all-active', [App\Http\Controllers\DriverController::class, 'getAllActiveDrivers'])->name('api.driver.all-active');

    //order sectiondeta
    Route::get('/order/{name}', [App\Http\Controllers\OrderController::class, 'order']);
    Route::get('/doneorder/{name}', [App\Http\Controllers\OrderController::class, 'doneorder']);
    Route::get('/orderdetail/{id}', [App\Http\Controllers\OrderController::class, 'orderdetail']);
    Route::post('/delivered', [App\Http\Controllers\OrderController::class, 'delivered']);
    Route::post('/writecomment', [App\Http\Controllers\OrderController::class, 'writecomment']);
    Route::post('/decline', [App\Http\Controllers\OrderController::class, 'decline']);
    Route::get('/phoneinfo/{name}', [App\Http\Controllers\OrderController::class, 'phoneinfo']);
    Route::get('/addressinfo/{name}', [App\Http\Controllers\OrderController::class, 'addressinfo']);
    Route::post('/createorder', [App\Http\Controllers\OrderController::class, 'createorder']);
             
 Route::get('/driver-items/{driverId}', [App\Http\Controllers\DeliveryController::class, 'getDriverItems']);
 Route::post('/driver-items/update-quantity', [App\Http\Controllers\DeliveryController::class, 'updateQuantity']);
 Route::post('/driver-items/add', [App\Http\Controllers\DeliveryController::class, 'addItem']);
 Route::delete('/driver-items/delete/{driverItemId}', [App\Http\Controllers\DeliveryController::class, 'deleteItem']);
 Route::post('/driver-items/submit', [App\Http\Controllers\DeliveryController::class, 'submitItems']);

    Route::post('/sign', [App\Http\Controllers\DeliveryController::class, 'sign']);

    Route::post('/items/deduct', [App\Http\Controllers\DeliveryController::class, 'deductQuantities']);

    Route::get('/totake/{shop}', [App\Http\Controllers\DeliveryController::class, 'totake']);
    Route::get('/taken/{shop}', [App\Http\Controllers\DeliveryController::class, 'taken']);
    Route::get('/totalfororder/{shop}', [App\Http\Controllers\OrderController::class, 'totalfororder']);
    //settings
    Route::get('/delivery/defaultprice', [App\Http\Controllers\DeliveryController::class, 'settings']);
    //delivery section
    Route::get('/delivery/merchant/{id}', [App\Http\Controllers\DeliveryController::class, 'merchant']);


    Route::get('/delivery/{name}', [App\Http\Controllers\DeliveryController::class, 'delivery']);
    Route::get('/delivery/type/{id}', [App\Http\Controllers\DeliveryController::class, 'typeSearch']);
    Route::get('/delivery/new/list', [App\Http\Controllers\DeliveryController::class, 'newDelivery']);
    Route::get('/delivery/new/status/{name}', [App\Http\Controllers\DeliveryController::class, 'newDeliveryStatus']);
    Route::post('/driver/request/{deliveryId}/{name}', [App\Http\Controllers\DeliveryController::class, 'driverRequest']);

    Route::get('/donedelivery/{name}', [App\Http\Controllers\DeliveryController::class, 'donedelivery']);
    Route::get('/deliverydetail/{id}', [App\Http\Controllers\DeliveryController::class, 'deliverydetail']);



    Route::get('/delivered_delivery/{id}', [App\Http\Controllers\DeliveryController::class, 'delivered_delivery']);
    Route::post('/write', [App\Http\Controllers\DeliveryController::class, 'write']);
    Route::post('/decline_delivery', [App\Http\Controllers\DeliveryController::class, 'decline_delivery']);
    Route::post('/createdelivery', [App\Http\Controllers\DeliveryController::class, 'createdelivery']);
    Route::post('/merchant/create', [App\Http\Controllers\DeliveryController::class, 'createMerchantApi']);
    Route::post('/paper/delivery/create', [App\Http\Controllers\DeliveryController::class, 'createDeliveryBank']);
    Route::post('/tsaas/delivery/create', [App\Http\Controllers\DeliveryController::class, 'createDeliveryTsaas']);
    Route::post('/updateindex', [App\Http\Controllers\DeliveryController::class, 'updateindex']);
    Route::get('/todeliver/{name}', [App\Http\Controllers\DeliveryController::class, 'todeliver']);
    Route::get('/donedeliver/{name}', [App\Http\Controllers\DeliveryController::class, 'donedeliver']);
    Route::get('/declinedeliver/{name}', [App\Http\Controllers\DeliveryController::class, 'declinedeliver']);
    Route::get('/totaldeliver/{name}', [App\Http\Controllers\DeliveryController::class, 'totaldeliver']);
    Route::get('/totalforcust/{name}', [App\Http\Controllers\DeliveryController::class, 'totalforcust']);
    Route::get('/active/{shop}', [App\Http\Controllers\DeliveryController::class, 'active']);
    Route::get('/success/{shop}', [App\Http\Controllers\DeliveryController::class, 'success']);
    Route::get('/delprice/{shop}', [App\Http\Controllers\DeliveryController::class, 'delprice']);
    Route::get('/declined/{shop}', [App\Http\Controllers\DeliveryController::class, 'declined']);
    Route::get('/successdelivery/{shop}', [App\Http\Controllers\DeliveryController::class, 'successdelivery']);
    Route::get('/declineddelivery/{shop}', [App\Http\Controllers\DeliveryController::class, 'declineddelivery']);
    Route::get('/getbanner', [App\Http\Controllers\BannerController::class, 'getbanner']);
    Route::post('/editing', [App\Http\Controllers\DeliveryController::class, 'editing']);
    Route::post('/declinefromshop', [App\Http\Controllers\DeliveryController::class, 'declinefromshop']);
    Route::get('/receive/{id}', [App\Http\Controllers\DeliveryController::class, 'receive']);

    //customer api
    Route::post('/logincust', [App\Http\Controllers\UserController::class, 'logincust']);
    Route::get('/deliveryshop/{name}', [App\Http\Controllers\DeliveryController::class, 'deliveryshop']);
    Route::get('/deliveryshop/count/{name}', [App\Http\Controllers\DeliveryController::class, 'newDeliveryShopCount']);
    Route::get('/deliveryshop/active/{name}', [App\Http\Controllers\DeliveryController::class, 'activeDeliveryShop']);
    Route::get('/deliveryshop/active/count/{name}', [App\Http\Controllers\DeliveryController::class, 'activeDeliveryShopCount']);
    Route::get('/ordershop/{name}', [App\Http\Controllers\OrderController::class, 'ordershop']);
    Route::get('/gooddata/{name}', [App\Http\Controllers\GoodController::class, 'gooddata']);
    Route::get('/gooddetail/{id}', [App\Http\Controllers\GoodController::class, 'gooddetail']);
    Route::get('/gd/{name}', [App\Http\Controllers\GoodController::class, 'gd']);
    Route::post('/goodpost', [App\Http\Controllers\GoodController::class, 'goodpost']);
    Route::post('/customer/create', [App\Http\Controllers\UserController::class, 'customerCreateApi']);
    //driver
    Route::get('/driver/{name}', [App\Http\Controllers\DriverController::class, 'getDriverOrlogo']);
    Route::get('/driver/rating/{name}', [App\Http\Controllers\DriverController::class, 'getDriverRating']);
    Route::put('/driver/income/{name}', [App\Http\Controllers\DriverController::class, 'getDriverIncome']);
    Route::post('/driver/create', [App\Http\Controllers\UserController::class, 'driverCreateApi']);
    Route::post('/driver/new/request', [App\Http\Controllers\DriverController::class, 'driverRequestApi']);
    Route::get('/city', [App\Http\Controllers\DriverController::class, 'cityApi']);
    // Driver location tracking
    Route::post('/driver/location/update', [App\Http\Controllers\DriverController::class, 'updateDriverLocation'])->name('api.driver.location.update');
    
    // Customer location tracking
    Route::post('/customer/location/update', [App\Http\Controllers\UserController::class, 'updateCustomerLocation'])->name('api.customer.location.update');
    Route::get('/customer/locations', [App\Http\Controllers\UserController::class, 'getCustomerLocations'])->name('api.customer.locations');
    
    // Dashboard API
    Route::middleware('auth:api')->get('/dashboard', [App\Http\Controllers\HomeController::class, 'getDashboardData'])->name('api.dashboard');
    
    // Delivery DataTable API for Flutter app (requires authentication)
    Route::middleware('auth:api')->get('/datatable-delivery', [App\Http\Controllers\DeliveryController::class, 'loadDeliveryDataTableApi'])->name('api.datatable-delivery');
    
    // Change driver on delivery API
    Route::post('/change_driver_on_delivery', [App\Http\Controllers\DeliveryController::class, 'changeDriverOnDeliveryApi'])->name('api.change_driver_on_delivery');
});
