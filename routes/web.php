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
})->name('welcome');

Auth::routes();



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home/api/dashboard', [App\Http\Controllers\HomeController::class, 'getDashboardData'])->name('home.api.dashboard');
Route::get('/home/top-phones', [App\Http\Controllers\HomeController::class, 'getTopPhones'])->name('home.top-phones');
Route::get('/home/top-phones-by-price', [App\Http\Controllers\HomeController::class, 'getTopPhonesByPrice'])->name('home.top-phones-by-price');
Route::post('/user/register', [App\Http\Controllers\UserController::class, 'customerCreate'])->name('customerCreate');
Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
Route::post('/createMerchant', [App\Http\Controllers\HomeController::class, 'createMerchant'])->name('createMerchant');
Route::post('/editMerchant', [App\Http\Controllers\HomeController::class, 'editMerchant'])->name('editMerchant');
Route::get('/deleteMerchant/{id}', [App\Http\Controllers\HomeController::class, 'deleteMerchant'])->name('deleteMerchant');
Route::get('/admin/delivery-report', [\App\Http\Controllers\DeliveryController::class, 'dailyReport'])->name('delivery.report');
Route::get('/admin/report', [\App\Http\Controllers\DeliveryController::class, 'report'])->name('admin.report');
Route::post('/export-driver-excel', [\App\Http\Controllers\DriverController::class, 'exportDriverExcel'])->name('export.driver.excel');
Route::post('/export-items-excel', [\App\Http\Controllers\DeliveryController::class, 'exportItemExcel'])->name('export.items.excel');
Route::get('/get-driver-items', [\App\Http\Controllers\DeliveryController::class, 'getDriverItemsWeb'])->name('get-driver-items');

Route::get('/get-item-history', [\App\Http\Controllers\DeliveryController::class, 'getItemHistory'])->name('get-item-history');
Route::get('/item/drivers/{itemId}', [\App\Http\Controllers\ItemController::class, 'getItemDrivers'])->name('item.drivers');

Route::post('/item/add-quantity', [\App\Http\Controllers\ItemController::class, 'addQuantity'])->name('item.add-quantity');
Route::post('/item/decrease-quantity', [\App\Http\Controllers\ItemController::class, 'decreaseQuantity'])->name('item.decrease-quantity');
Route::get('/item/history/{id}', [\App\Http\Controllers\ItemController::class, 'getHistory'])->name('item.history');
// Add these routes for quantity validation
Route::get('/item/check-warehouse-quantity/{itemId}/{quantity}', [\App\Http\Controllers\ItemController::class, 'checkWarehouseQuantity']);
Route::get('/item/check-driver-quantity/{itemId}/{driverId}/{quantity}/{operation}', [\App\Http\Controllers\ItemController::class, 'checkDriverQuantity']);
// or whatever your route is
Route::prefix('order')->group(function () {;
    Route::get('/phone/{id}', [App\Http\Controllers\OrderController::class, 'phone']);
    Route::get('/address/{id}', [App\Http\Controllers\OrderController::class, 'address']);
    Route::get('/index', [App\Http\Controllers\OrderController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\OrderController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\OrderController::class, 'list']);
    Route::get('/datatable-order', [App\Http\Controllers\OrderController::class, 'loadOrderDataTable'])->name('datatable-order');
    Route::get('/driver', [App\Http\Controllers\OrderController::class, 'driver']);
    Route::get('/report', [App\Http\Controllers\OrderController::class, 'report']);

    Route::get('/finished', [App\Http\Controllers\OrderController::class, 'finished']);
    Route::get('/change_delete_on_order', [App\Http\Controllers\OrderController::class, 'change_delete_on_order'])->name('change_delete_on_order');
    Route::get('/edit/{id}', 'DeliveryController@edit');
    Route::post('/edit', 'DeliveryController@update');
    Route::post('/bulk', 'DeliveryController@bulk');
    Route::get('/change_driver_on_order', [App\Http\Controllers\OrderController::class, 'change_driver_on_order'])->name('change_driver_on_order');

    Route::get('/change_status_on_order', [App\Http\Controllers\OrderController::class, 'change_status_on_order'])->name('change_status_on_order');
    Route::get('/change_bus_on_order', [App\Http\Controllers\OrderController::class, 'change_bus_on_order'])->name('change_bus_on_order');

    Route::get('/excel-export-order', [App\Http\Controllers\OrderController::class, 'ExcelExport'])->name('excel-export-order');
    Route::get('/delete/{id}', [App\Http\Controllers\OrderController::class, 'delete']);
    Route::get('/print-data-delivery', [App\Http\Controllers\OrderController::class, 'PrintdeliveryData'])->name('print-data-delivery');
});

Route::get('/admin/report', [\App\Http\Controllers\DeliveryController::class, 'reportExport'])->name('admin.report');
Route::prefix('delivery')->group(function () {;
    Route::get('/search', [App\Http\Controllers\HomeController::class, 'searchType'])->name('search');
    Route::get('/index', [App\Http\Controllers\DeliveryController::class, 'index'])->name('deliveryIndex');
    Route::get('/phone/{id}', [App\Http\Controllers\DeliveryController::class, 'phone']);
    Route::get('detail/phone/{id}', [App\Http\Controllers\DeliveryController::class, 'phone']);
    Route::get('/new', [App\Http\Controllers\DeliveryController::class, 'new'])->name('deliveryNew');
    Route::get('/received', [App\Http\Controllers\DeliveryController::class, 'received']);
    Route::get('/report', [App\Http\Controllers\DeliveryController::class, 'report']);
    Route::get('/done', [App\Http\Controllers\DeliveryController::class, 'done']);
    Route::get('/all', [App\Http\Controllers\DeliveryController::class, 'allDelivery']);
    Route::get('/delivery_download', [App\Http\Controllers\DeliveryController::class, 'deliveryDownload']);
    Route::get('/delivery_download_data', [App\Http\Controllers\DeliveryController::class, 'getDeliveryDownload'])->name('delivery_download_data');
    Route::get('/deleted', [App\Http\Controllers\DeliveryController::class, 'deleted']);
    Route::get('/good/{shop}', [App\Http\Controllers\DeliveryController::class, 'good']);
    Route::get('/edit', [App\Http\Controllers\DeliveryController::class, 'edit']);
    Route::post('/edit', [App\Http\Controllers\DeliveryController::class, 'updatedel']);
    Route::post('/edit', [App\Http\Controllers\DeliveryController::class, 'updatedel']);
    Route::post('/bulkQRPrint/', [App\Http\Controllers\DeliveryController::class, 'showQrData'])->name('delivery.bulkQRPrint');
        Route::get('/print-data-delivery-invoice', [App\Http\Controllers\DeliveryController::class, 'PrintdeliveryInvoice'])->name('print-data-delivery_invoice');

    Route::get('/print-data-delivery_item', [App\Http\Controllers\DeliveryController::class, 'PrintdeliveryData'])->name('print-data-delivery_item');
    Route::get('/print-data-delivery-zarlaga', [App\Http\Controllers\DeliveryController::class, 'PrintdeliveryZarlaga'])->name('print-data-delivery_zarlaga');
    Route::get('/tootsooNiilvvleh', [App\Http\Controllers\DeliveryController::class, 'changeEstimateData'])->name('tootsooNiilvvlsenEseh');
    Route::post('/create', [App\Http\Controllers\DeliveryController::class, 'create'])->name('deliveryCreate');
    Route::get('/list', [App\Http\Controllers\DeliveryController::class, 'list']);
    Route::get('/datatable-delivery', [App\Http\Controllers\DeliveryController::class, 'loadDeliveryDataTable'])->name('datatable-delivery');
    Route::get('/edit_note_on_datatable', [App\Http\Controllers\DeliveryController::class, 'editNoteOnDataTable'])->name('edit_note_on_datatable');
    Route::get('/edit_comment_on_datatable', [App\Http\Controllers\DeliveryController::class, 'editCommentDataTable'])->name('editCommentDataTable');
    Route::get('/change_driver_on_delivery', [App\Http\Controllers\DeliveryController::class, 'change_driver_on_delivery'])->name('change_driver_on_delivery');
    Route::get('/change_status_on_delivery', [App\Http\Controllers\DeliveryController::class, 'change_status_on_delivery'])->name('change_status_on_delivery');
    Route::get('/change_delete_on_delivery', [App\Http\Controllers\DeliveryController::class, 'change_delete_on_delivery'])->name('change_delete_on_delivery');
    Route::get('/change_bus_on_delivery', [App\Http\Controllers\DeliveryController::class, 'change_bus_on_delivery'])->name('change_bus_on_delivery');
    Route::get('/change_verify_on_delivery', [App\Http\Controllers\DeliveryController::class, 'change_verify_on_delivery'])->name('change_verify_on_delivery');
    Route::get('/reorder-delivery', [App\Http\Controllers\DeliveryController::class, 'reorderDelivery'])->name('reorder-delivery');
    Route::get('/excel-export-delivery', [App\Http\Controllers\DeliveryController::class, 'ExcelExport'])->name('excel-export-delivery');
    Route::post('/excel_import_file', [App\Http\Controllers\DeliveryController::class, 'excelImport'])->name('excel_import_file');
    Route::get('/recover/{id}', [App\Http\Controllers\DeliveryController::class, 'recover'])->name('recover');
    Route::get('/detail/{id}', [App\Http\Controllers\DeliveryController::class, 'detail'])->name('detail');
});
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});
Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    return 'Config cache has been cleared';
});


// Clear view cache:
Route::get('/view-clear', function () {
    Artisan::call('route:optimized');
    return 'route has been cleared';
});

Route::prefix('region')->group(function () {;
    Route::get('/index', [App\Http\Controllers\RegionController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\RegionController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\RegionController::class, 'list']);
    Route::get('/delete/{id}', [App\Http\Controllers\RegionController::class, 'delete']);
});

Route::prefix('feedback')->group(function () {;
    Route::get('/index', [App\Http\Controllers\FeedbackController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\FeedbackController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\FeedbackController::class, 'list']);
    Route::post('/resolve', [App\Http\Controllers\FeedbackController::class, 'resolve']);
    Route::get('/delete/{id}', [App\Http\Controllers\FeedbackController::class, 'delete']);
});

Route::prefix('invoice')->group(function () {
    Route::get('/index', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoice.index');
    Route::post('/create', [App\Http\Controllers\InvoiceController::class, 'create'])->name('invoice.create');
    Route::get('/list', [App\Http\Controllers\InvoiceController::class, 'list'])->name('invoice.list');
    Route::post('/resolve', [App\Http\Controllers\InvoiceController::class, 'resolve'])->name('invoice.resolve');
    Route::get('/delete/{id}', [App\Http\Controllers\InvoiceController::class, 'delete'])->name('invoice.delete');
    Route::get('/profile', [App\Http\Controllers\InvoiceProfileController::class, 'index'])->name('invoice.profile.index');
    Route::post('/profile', [App\Http\Controllers\InvoiceProfileController::class, 'store'])->name('invoice.profile.store');
    Route::post('/profile/{profile}', [App\Http\Controllers\InvoiceProfileController::class, 'update'])->name('invoice.profile.update');
    Route::delete('/profile/{profile}', [App\Http\Controllers\InvoiceProfileController::class, 'destroy'])->name('invoice.profile.destroy');
    Route::post('/profile/{profile}/bank', [App\Http\Controllers\InvoiceProfileController::class, 'storeBank'])->name('invoice.profile.bank.store');
    Route::delete('/profile/{profile}/bank/{bank}', [App\Http\Controllers\InvoiceProfileController::class, 'destroyBank'])->name('invoice.profile.bank.destroy');
    
    // QR code generation route
    Route::get('/qr-code', [App\Http\Controllers\InvoiceController::class, 'generateQrCode'])->name('invoice.qr-code');
    
    // PDF view proxy route
    Route::get('/pdf/{uuid}', [App\Http\Controllers\InvoiceController::class, 'viewPdf'])->name('invoice.pdf');
    
    // Энэ route нэмэх (харах товчинд хэрэгтэй)
    Route::get('/{id}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoice.show');
});
Route::prefix('ware')->group(function () {;
    Route::get('/index', [App\Http\Controllers\WareController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\WareController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\WareController::class, 'list']);
});

Route::prefix('setting')->group(function () {;
    Route::get('/index', [App\Http\Controllers\SettingController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\SettingController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\SettingController::class, 'list']);
    Route::get('/edit/{id}', [App\Http\Controllers\SettingController::class, 'edit']);
    Route::post('/edit', [App\Http\Controllers\SettingController::class, 'update']);
});


Route::prefix('good')->group(function () {;
    Route::get('/index', [App\Http\Controllers\GoodController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\GoodController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\GoodController::class, 'list']);
    Route::get('/good/{name}', [App\Http\Controllers\GoodController::class, 'good']);
    Route::get('/income', [App\Http\Controllers\GoodController::class, 'income']);
    Route::post('/add', [App\Http\Controllers\GoodController::class, 'add']);
    Route::get('/delete/{id}', [App\Http\Controllers\GoodController::class, 'delete']);
});

Route::prefix('item')->group(function () {;
    Route::get('/index', [App\Http\Controllers\ItemController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\ItemController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\ItemController::class, 'list'])->name('item.list');
    Route::get('/item/{name}', [App\Http\Controllers\ItemController::class, 'good']);
    Route::get('/income', [App\Http\Controllers\ItemController::class, 'income']);
    Route::post('/add', [App\Http\Controllers\ItemController::class, 'add']);
    Route::get('/delete/{id}', [App\Http\Controllers\ItemController::class, 'delete']);
});

Route::prefix('user')->group(function () {;
    Route::get('/index', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\UserController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\UserController::class, 'list']);
    Route::get('/delete/{id}', [App\Http\Controllers\UserController::class, 'delete']);
    Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'edit']);
    Route::post('/edit', [App\Http\Controllers\UserController::class, 'update']);
    Route::post('/updateimage', [App\Http\Controllers\UserController::class, 'updateimage']);
    Route::post('/updateinfo', [App\Http\Controllers\UserController::class, 'updateinfo']);
    Route::post('/toggle-status/{id}', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('user.toggleStatus');

});

Route::prefix('notification')->group(function () {;
    Route::get('/index', [App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('/send', [App\Http\Controllers\NotificationController::class, 'send']);
});

Route::prefix('role')->group(function () {;
    Route::get('/index', [App\Http\Controllers\RoleController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\RoleController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\RoleController::class, 'list']);
});

Route::prefix('log')->group(function () {;
    Route::get('/index', [App\Http\Controllers\LogController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\LogController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\LogController::class, 'list']);
    Route::post('/income', [App\Http\Controllers\LogController::class, 'income']);
});

Route::prefix('phone')->group(function () {;
    Route::get('/index', [App\Http\Controllers\PhoneController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\PhoneController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\PhoneController::class, 'list']);
});

Route::prefix('address')->group(function () {;
    Route::get('/index', [App\Http\Controllers\AddressController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\AddressController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\AddressController::class, 'list']);
});


Route::prefix('report')->group(function () {;
    Route::get('/driver', [App\Http\Controllers\ReportController::class, 'driver']);
    Route::get('/driverdone', [App\Http\Controllers\ReportController::class, 'driverdone']);


    Route::get('/customer', [App\Http\Controllers\ReportController::class, 'customer']);
    Route::get('/customerdone', [App\Http\Controllers\ReportController::class, 'customerdone']);
    Route::get('/general', [App\Http\Controllers\ReportController::class, 'general']);
    Route::get('/datatable-general', [App\Http\Controllers\ReportController::class, 'loadGeneralDataTable'])->name('datatable-general');
    Route::get('/datatable-delivery-report', [App\Http\Controllers\ReportController::class, 'loadDeliveryDataTableForReport'])->name('datatable-delivery-report');

    Route::get('/report_compile', [App\Http\Controllers\ReportController::class, 'report_compile'])->name('report_compile');
    Route::get('/report_compile_customer', [App\Http\Controllers\ReportController::class, 'report_compile_customer'])->name('report_compile_customer');
});
Route::prefix('driver')->group(function () {;
    Route::get('/getDriverCounts', [App\Http\Controllers\DriverController::class, 'getDriverCounts'])->name('get-driver-counts');
    Route::get('/drivermonitoring', [App\Http\Controllers\DriverController::class, 'drivermonitoring']);
    Route::get('/location', [App\Http\Controllers\DriverController::class, 'driverLocation'])->name('driver-location');
    Route::get('/request', [App\Http\Controllers\DriverController::class, 'driverRequestShow']);
    Route::get('/requests', [App\Http\Controllers\DriverController::class, 'getDriverRequest'])->name('getDriverRequest');
    Route::get('/detail/{driver}', [App\Http\Controllers\DriverController::class, 'detail'])->name('detail');
    Route::get('/print-data-driver-item', [App\Http\Controllers\DriverController::class, 'printDriverData'])->name('print-data-driver-item');
    Route::get('/print-data-driver-request', [App\Http\Controllers\DriverController::class, 'printDriverRequest'])->name('print-data-driver-request');
    Route::get('/excel-export-driver', [App\Http\Controllers\DriverController::class, 'excelExportDriver'])->name('excel-export-driver');
    Route::get('/get-driver-delivery-detail', [App\Http\Controllers\DriverController::class, 'driverDetail'])->name('get-driver-delivery-info');
});

// New Driver Monitoring Routes
Route::prefix('admin')->group(function () {
    Route::get('/driver-monitoring', [App\Http\Controllers\DriverController::class, 'driverMonitoringNew'])->name('admin.driver-monitoring');
    Route::get('/driver-monitoring/data', [App\Http\Controllers\DriverController::class, 'getDriverMonitoringData'])->name('admin.driver-monitoring.data');
    Route::get('/driver-monitoring/drivers', [App\Http\Controllers\DriverController::class, 'getDriversForMonitoring'])->name('admin.driver-monitoring.drivers');
    Route::get('/driver-monitoring/deliveries', [App\Http\Controllers\DriverController::class, 'getDriverDeliveries'])->name('admin.driver-monitoring.deliveries');
    Route::get('/driver-monitoring/shops-breakdown', [App\Http\Controllers\DriverController::class, 'getDriverMonitoringShopsBreakdown'])->name('admin.driver-monitoring.shops-breakdown');
    Route::get('/driver-monitoring/items/{deliveryId}', [App\Http\Controllers\DriverController::class, 'getDeliveryItems'])->name('admin.driver-monitoring.items');
});


Route::prefix('role')->group(function () {
    Route::get('list', [App\Http\Controllers\RoleController::class, 'list']);
    Route::get('index', [App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
    Route::post('create', [App\Http\Controllers\RoleController::class, 'create'])->name('role.create');
    Route::get('edit/{id}', [App\Http\Controllers\RoleController::class, 'editRole'])->name('role.edit');
    Route::post('update/{id}', [App\Http\Controllers\RoleController::class, 'updateRole'])->name('role.update');
});

Route::prefix('banner')->group(function () {;
    Route::get('/index', [App\Http\Controllers\BannerController::class, 'index']);
    Route::post('/create', [App\Http\Controllers\BannerController::class, 'create']);
    Route::get('/list', [App\Http\Controllers\BannerController::class, 'list']);
    Route::get('/good/{name}', [App\Http\Controllers\BannerController::class, 'good']);
    Route::post('/add', [App\Http\Controllers\BannerController::class, 'add']);
    Route::get('/delete/{id}', [App\Http\Controllers\BannerController::class, 'delete']);
});


Route::post('add-phone-cart', [App\Http\Controllers\UserController::class, 'addphonecart']);
Route::get('/load-phone-details', [App\Http\Controllers\UserController::class, 'cartDetailsAjax']);
Route::post('add-address-cart', [App\Http\Controllers\UserController::class, 'addaddresscart']);
Route::get('/load-address-details', [App\Http\Controllers\UserController::class, 'cartDetailsAjaxAdd']);
Route::get('clear-cart', [App\Http\Controllers\UserController::class, 'clearcart']);
Route::post('add-to-cart', [App\Http\Controllers\DeliveryController::class, 'addtocart']);
Route::get('/load-cart-details', [App\Http\Controllers\DeliveryController::class, 'cartDetailsAjaxS']);
