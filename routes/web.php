<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Order;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



Route::prefix('order')->group(function(){;
    Route::get('/phone/{id}', [App\Http\Controllers\OrderController::class, 'phone']); 
    Route::get('/address/{id}', [App\Http\Controllers\OrderController::class, 'address']);
    Route::get('/index',[App\Http\Controllers\OrderController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\OrderController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\OrderController::class, 'list']);
    Route::get('/datatable-order', [App\Http\Controllers\OrderController::class, 'loadOrderDataTable'])->name('datatable-order'); 
    Route::get('/edit_note_on_datatable', [DeliveryController::class, 'editNoteOnDataTable'])->name('edit_note_on_datatable');
    Route::get('/driver',[App\Http\Controllers\OrderController::class, 'driver']);

    Route::get('/finished',[App\Http\Controllers\OrderController::class, 'finished']);
    Route::get('/change_delete_on_order',[App\Http\Controllers\OrderController::class, 'change_delete_on_order'])->name('change_delete_on_order');
    Route::get('/edit/{id}','DeliveryController@edit');
    Route::post('/edit','DeliveryController@update');
    Route::post('/bulk','DeliveryController@bulk');
    Route::get('/change_driver_on_order',[App\Http\Controllers\OrderController::class, 'change_driver_on_order'])->name('change_driver_on_order');

    Route::get('/change_status_on_order',[App\Http\Controllers\OrderController::class, 'change_status_on_order'])->name('change_status_on_order');
    Route::get('/change_bus_on_order',[App\Http\Controllers\OrderController::class, 'change_status_on_order'])->name('change_bus_on_order');
    Route::get('add-to-cart/{id}', [ProductController::class, 'addToCart'])->name('add.to.cart');
    Route::patch('update-cart', [ProductController::class, 'update'])->name('update.cart');
    Route::delete('remove-from-cart', [ProductController::class, 'remove'])->name('remove.from.cart');
    Route::get('/datatable-delivery', [DeliveryController::class, 'loadDeliveryDataTable'])->name('datatable-delivery'); 
    Route::get('/datatable-delivery100', [DeliveryController::class, 'loadDeliveryDataTable100'])->name('datatable-delivery100'); 

  

    Route::get('/excel-export-delivery', [App\Http\Controllers\OrderController::class, 'ExcelExport'])->name('excel-export-delivery'); 
    Route::get('/detail/{id}', 'DeliveryController@detail')->name( 'delivery.detail');
    Route::get('/delete/{id}','DeliveryController@delete');
    Route::get('/recover/{id}','DeliveryController@recover');
    Route::get('/print-data-delivery', [App\Http\Controllers\OrderController::class, 'PrintdeliveryData'])->name('print-data-delivery');
});


Route::prefix('delivery')->group(function(){;
    Route::get('/index',[App\Http\Controllers\DeliveryController::class, 'index']);
    Route::get('/new',[App\Http\Controllers\DeliveryController::class, 'new']);
    Route::get('/received',[App\Http\Controllers\DeliveryController::class, 'received']);
    Route::get('/done',[App\Http\Controllers\DeliveryController::class, 'done']);
    Route::get('/deleted',[App\Http\Controllers\DeliveryController::class, 'deleted']);
    Route::post('/create',[App\Http\Controllers\DeliveryController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\DeliveryController::class, 'list']);
    Route::get('/datatable-delivery', [App\Http\Controllers\DeliveryController::class, 'loadDeliveryDataTable'])->name('datatable-delivery'); 
    Route::get('/edit_note_on_datatable', [App\Http\Controllers\DeliveryController::class, 'editNoteOnDataTable'])->name('edit_note_on_datatable');
    Route::get('/change_driver_on_delivery',[App\Http\Controllers\DeliveryController::class, 'change_driver_on_delivery'])->name('change_driver_on_delivery');
    Route::get('/change_status_on_delivery',[App\Http\Controllers\DeliveryController::class, 'change_status_on_delivery'])->name('change_status_on_delivery');
    Route::get('/change_delete_on_delivery',[App\Http\Controllers\DeliveryController::class, 'change_delete_on_delivery'])->name('change_delete_on_delivery');
    Route::get('/change_bus_on_delivery',[App\Http\Controllers\DeliveryController::class, 'change_bus_on_delivery'])->name('change_bus_on_delivery');
    Route::get('/change_verify_on_delivery',[App\Http\Controllers\DeliveryController::class, 'change_verify_on_delivery'])->name('change_verify_on_delivery');

});


Route::prefix('region')->group(function(){;
    Route::get('/index',[App\Http\Controllers\RegionController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\RegionController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\RegionController::class, 'list']);
});

Route::prefix('ware')->group(function(){;
    Route::get('/index',[App\Http\Controllers\WareController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\WareController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\WareController::class, 'list']);
});
Route::prefix('good')->group(function(){;
    Route::get('/index',[App\Http\Controllers\GoodController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\GoodController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\GoodController::class, 'list']);
    Route::get('/good/{name}', [App\Http\Controllers\GoodController::class, 'good']); 
    Route::get('/income',[App\Http\Controllers\GoodController::class, 'income']);
    Route::post('/add',[App\Http\Controllers\GoodController::class, 'add']);

});

Route::prefix('user')->group(function(){;
    Route::get('/index',[App\Http\Controllers\UserController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\UserController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\UserController::class, 'list']);
    Route::get('/delete/{id}',[App\Http\Controllers\UserController::class, 'delete']);

});

Route::prefix('role')->group(function(){;
    Route::get('/index',[App\Http\Controllers\RoleController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\RoleController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\RoleController::class, 'list']);
});

Route::prefix('log')->group(function(){;
    Route::get('/index',[App\Http\Controllers\LogController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\LogController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\LogController::class, 'list']);
    Route::post('/income',[App\Http\Controllers\LogController::class, 'income']);
});

Route::prefix('phone')->group(function(){;
    Route::get('/index',[App\Http\Controllers\PhoneController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\PhoneController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\PhoneController::class, 'list']);
});

Route::prefix('address')->group(function(){;
    Route::get('/index',[App\Http\Controllers\AddressController::class, 'index']);
    Route::post('/create',[App\Http\Controllers\AddressController::class, 'create']);
    Route::get('/list',[App\Http\Controllers\AddressController::class, 'list']);
});


Route::prefix('report')->group(function(){;
    Route::get('/driver',[App\Http\Controllers\ReportController::class, 'driver']);
    Route::get('/driverdone',[App\Http\Controllers\ReportController::class, 'driverdone']);
    Route::get('/customer',[App\Http\Controllers\ReportController::class, 'customer']);
    Route::get('/customerdone',[App\Http\Controllers\ReportController::class, 'customerdone']);
    Route::get('/general',[App\Http\Controllers\ReportController::class, 'general']);
    Route::get('/datatable-general', [App\Http\Controllers\ReportController::class, 'loadGeneralDataTable'])->name('datatable-general');
    Route::get('/datatable-delivery-report', [App\Http\Controllers\ReportController::class, 'loadDeliveryDataTableForReport'])->name('datatable-delivery-report'); 
    Route::get('/edit_note_on_datatable', [App\Http\Controllers\DeliveryController::class, 'editNoteOnDataTable'])->name('edit_note_on_datatable');
  
    Route::get('/change_bus_on_delivery',[App\Http\Controllers\DeliveryController::class, 'change_bus_on_delivery'])->name('change_bus_on_delivery');
    Route::get('/report_compile',[App\Http\Controllers\ReportController::class, 'report_compile'])->name('report_compile');
    Route::get('/report_compile_customer',[App\Http\Controllers\ReportController::class, 'report_compile_customer'])->name('report_compile_customer');

});


Route::prefix('role')->group(function(){
    Route::get('list', [App\Http\Controllers\RoleController::class, 'list']);
    Route::get('index', [App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
    Route::post('create', [App\Http\Controllers\RoleController::class, 'create'])->name('role.create');
    Route::get('edit/{id}', [App\Http\Controllers\RoleController::class, 'editRole'])->name('role.edit');
    Route::post('update/{id}', [App\Http\Controllers\RoleController::class, 'updateRole'])->name('role.update');
});
