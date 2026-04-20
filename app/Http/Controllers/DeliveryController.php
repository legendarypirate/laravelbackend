<?php
namespace App\Http\Controllers;

use App\Exports\DeliveryExport;
use App\Exports\DriverItemExport;
use App\Exports\DeliveryReportExport;
use App\Exports\DeliveryDetailedReportExport;

use App\Imports\RequestImportExcel;
use App\Models\Delivery;
use App\Models\DeliveryDownload;
use App\Models\Good;
use App\Models\Log;
use App\Models\Merchant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Redirect;
use Yajra\DataTables\DataTables;
use Firebase\JWT\JWT;

class DeliveryController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.delivery.index');
    }
    public function phone($id)
    {
        echo json_encode(DB::table('merchant')->where('id', $id)->get());
    }

    public function address($id)
    {
        echo json_encode(DB::table('merchant')->where('id', $id)->get());
    }

    public function exportItemExcel(Request $request)
{
    $driverName = $request->input('driver');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $itemIds = $request->input('item_ids', []);

    // Get the actual user ID from the driver name
    $driver = DB::table('users')->where('name', $driverName)->first();
    
    if (!$driver) {
        return redirect()->back()->with('error', 'Driver not found');
    }

    $driverId = $driver->id;

    $fileName = 'driver_items_' . $driverName . '_' . date('Y-m-d') . '.xlsx';

    return Excel::download(new DriverItemExport($driverId, $startDate, $endDate, $itemIds), $fileName);
}
    public function createMerchantApi(Request $request)
    {
        $merchant = new Merchant;
        $rules    = [
            'merchantName'    => 'required',
            'merchantAddress' => 'required',
            'merchantPhone1'  => 'required|numeric|digits:8',
            'merchantPhone2'  => 'numeric|digits:8',
        ];

        $merchant->user_id            = $request->id;
        $merchant->merchantName       = $request->merchantName;
        $merchant->merchantAddress    = $request->merchantAddress;
        $merchant->merchantPhone1     = $request->merchantPhone1;
        $merchant->merchantPhone2     = $request->merchantPhone2;
        $merchant->merchantWhat3Words = $request->merchantWhat3Words;
        $merchant->save();
        return response()->json(['data' => $merchant, 'success' => true]);
    }

    public function detail($id)
    {
        $list          = Delivery::find($id);
        $merchant_info = Merchant::find($list['merchant_id']);
        //dd($merchant);
        return view('admin.delivery.detail', compact('list', 'merchant_info'));
    }

    public function delivery($name)
    {
        $list = Delivery::with(['merchant', 'user'])
            ->join('users', 'deliveries.shop', '=', 'users.name')
            ->where('driver', '=', $name)
            ->where(function ($query) {
                $query->where('status', "=", "2")
                    ->orWhere('status', "=", "10");
            })
            ->orderByRaw('CASE WHEN deliveries.ordering IS NOT NULL THEN deliveries.ordering END ASC')
            ->orderBy('deliveries.id', 'DESC') // If order is NULL, sort by id in DESC
            ->select('users.name as shop', 'users.image as customer_image', 'deliveries.*')
            ->get();

        return response()->json(['data' => $list, 'success' => true]);
    }

    //merchant list haruulah
    public function merchant($id)
    {
        $list          = User::find($id);
        $merchant_info = Merchant::where('user_id', '=', $list['id'])->get();

        return response()->json(['data' => $merchant_info, 'success' => true]);
    }

    //Driver Request
    public function driverRequest($deliveryId, $name)
    {
        $delivery = Delivery::find($deliveryId);
        if (! $delivery->driver_request) {
            $delivery->driver_request = $name;
        } else {
            $delivery->driver_request = trim($delivery->driver_request . ',' . $name, ',');
        }
        $delivery->save();
        return response()->json(['data' => $delivery, 'success' => true]);
    }

    public function newDelivery(Request $request)
    {
        $list = Delivery::with('merchant')
            ->where('driver', '=', $request->name)
            ->where('status', "=", "1")
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(['data' => $list, 'success' => true]);
    }
    public function newDeliveryStatus($name)
    {
        $list = Delivery::with('merchant')
            ->where('status', "=", "1")
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($list as $delivery) {
            if ($delivery->driver_request) {
                $nameArray               = explode(',', $delivery->driver_request);
                $nameExists              = in_array($name, $nameArray);
                $delivery->requestStatus = $nameExists ? 1 : 0;
            } else {
                $delivery->requestStatus = 0;
            }
        }
        return response()->json(['data' => $list, 'success' => true]);
    }

public function deductQuantities(Request $request)
{
    \Log::info('🚀 API Called - deductQuantities', $request->all());

    $validator = Validator::make($request->all(), [
        'deductions' => 'required|array|min:1',
        'deductions.*.item_id' => 'required|integer',
        'deductions.*.driver_id' => 'required|integer', // This is the user ID
        'deductions.*.deduction_quantity' => 'required|integer|min:1',
        'deductions.*.delivery_id' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $deductions = $request->input('deductions');
    
    // Additional validation: Ensure deductions array is not empty and has at least one valid deduction
    if (empty($deductions) || count($deductions) === 0) {
        return response()->json([
            'success' => false,
            'message' => 'Хасалт хийхэд ядаж нэг барааны тоо хэмжээ оруулах шаардлагатай!',
            'errors' => ['deductions' => ['At least one deduction is required']]
        ], 422);
    }
    
    // Validate that at least one deduction has quantity > 0
    $hasValidDeduction = false;
    foreach ($deductions as $deduction) {
        if (isset($deduction['deduction_quantity']) && $deduction['deduction_quantity'] > 0) {
            $hasValidDeduction = true;
            break;
        }
    }
    
    if (!$hasValidDeduction) {
        return response()->json([
            'success' => false,
            'message' => 'Хасалт хийхэд ядаж нэг барааны тоо хэмжээ оруулах шаардлагатай!',
            'errors' => ['deductions' => ['At least one deduction with quantity greater than 0 is required']]
        ], 422);
    }
    $successfulUpdates = [];
    $failedUpdates = [];

    DB::beginTransaction();
    try {
        foreach ($deductions as $deduction) {
            $itemId = $deduction['item_id'];
            $driverId = $deduction['driver_id'];
            $deductionQuantity = $deduction['deduction_quantity'];
            $deliveryId = isset($deduction['delivery_id']) && $deduction['delivery_id'] ? (int) $deduction['delivery_id'] : null;

            // Check if driver item exists
            $driverItem = DB::table('driver_items')
                ->where('item_id', $itemId)
                ->where('driver_id', $driverId)
                ->first();

            if (!$driverItem) {
                $failedUpdates[] = ['item_id' => $itemId, 'error' => 'Driver item not found'];
                continue;
            }

            // Check quantity
            if ($driverItem->quantity < $deductionQuantity) {
                $failedUpdates[] = ['item_id' => $itemId, 'error' => 'Not enough quantity'];
                continue;
            }

            // Get item info for history
            $item = DB::table('items')->where('id', $itemId)->first();
            $goodname = $item ? $item->name : 'Unknown Item';
            
            // Get driver info for history
            $driver = DB::table('users')->where('id', $driverId)->first();
            $driverName = $driver ? $driver->name : 'Unknown Driver';

            // Update driver item quantity
            $newQuantity = $driverItem->quantity - $deductionQuantity;
            
            $updated = DB::table('driver_items')
                ->where('item_id', $itemId)
                ->where('driver_id', $driverId)
                ->update([
                    'quantity' => $newQuantity,
                    'updated_at' => now()
                ]);

            if ($updated) {
                // Create history record (include delivery_id when driver deducts from app)
                $historyInsert = [
                    'item_id' => $itemId,
                    'user_id' => $driverId, // Store driver ID as user_id
                    'goodname' => $goodname,
                    'comment' => $deductionQuantity . ' ширхэг хасагдлаа. Улдсан: ' . $newQuantity . ' ширхэг',
                    'quantity' => $deductionQuantity,
                    'type' => 'out',
                    'driver_id' => $driverId,
                    'operation_by' => $driverName,
                    'image' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if ($deliveryId !== null) {
                    $historyInsert['delivery_id'] = $deliveryId;
                }
                DB::table('histories')->insert($historyInsert);

                $successfulUpdates[] = [
                    'item_id' => $itemId,
                    'item_name' => $goodname,
                    'driver_id' => $driverId,
                    'driver_name' => $driverName,
                    'old_quantity' => $driverItem->quantity,
                    'new_quantity' => $newQuantity,
                    'deduction' => $deductionQuantity,
                ];
            } else {
                $failedUpdates[] = ['item_id' => $itemId, 'error' => 'Update failed'];
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Deduction successful',
            'data' => [
                'successful_updates' => $successfulUpdates,
                'failed_updates' => $failedUpdates,
                'timestamp' => now()->toDateTimeString()
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('❌ Deduction failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Deduction failed: ' . $e->getMessage()
        ], 500);
    }
}
    public function typeSearch($id)
    {
        $list = Delivery::with('merchant')
            ->where('type', '=', $id)
            ->orderBy('id', 'DESC')
            ->get();
        return response()->json(['data' => $list, 'success' => true]);
    }
    //create delivary bank api
    public function createDeliveryBank(Request $request)
    {
        $user   = User::where('name', 'EZPAY')->first();
        $userid = $user->id;

        $order         = new Delivery();
        $request->type = 1;
        if ($user->role == 'customer') {
            $order->shop = 'EZPAY';
            if ($request->type == 1) {
                if (!empty(Auth::user()->engiin)) {

                    $order->deliveryprice = Auth::user()->engiin;
                } else {
                    $defaultPrice         = Setting::where('type', 1)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 2) {
                if (isset(Auth::user()->tsagtai)) {
                    $order->deliveryprice = Auth::user()->tsagtai;
                } else {
                    $defaultPrice         = Setting::where('type', 2)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 3) {
                if (isset(Auth::user()->yaraltai)) {
                    $order->deliveryprice = Auth::user()->yaraltai;
                } else {
                    $defaultPrice         = Setting::where('type', 3)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 4) {
                if (isset(Auth::user()->onts_yaraltai)) {
                    $order->deliveryprice = Auth::user()->onts_yaraltai;
                } else {
                    $defaultPrice         = Setting::where('type', 4)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            }
        } else {
            $order->shop = 'EZPAY';
        }

        $order->phone         = $request->phone;
        $order->phone2        = $request->phone2;
        $order->address       = $request->address;
        $order->comment       = $request->comment;
        $order->price         = $request->number*640;
        $order->received      = $request->received;
        $order->type          = 1;
        $order->merchant_id   = 47;
        $order->parcel_info   = "57";
        $order->order_code    = $request->order_code;
        $order->download_time = $request->download_time;
        $order->district      = $request->district;
        $order->receivername  = $request->receivername;
        $order->size          = $request->size;
        $order->number        = $request->number; //get
        $order->region        = $request->region;
        $order->goodtype      = $request->goodtype;
        $order->verified      = 0;
        $order->track         = 'CH' . rand(100000, 999999) . $userid;
        $order->status        = 1;
        $order->save();


        $string = str_replace('\n', '', $request->data);

        $strings = rtrim($string, ',');
        $convst  = str_replace('\\', '+', $strings);
        $qq      = json_decode($convst, true);

        if ($request->data) {
            foreach ($qq as $dd) {
                $idk                     = Good::where('goodname', str_replace('+', '\\', $dd['name']))->where('shop', $dd['cname'])->first();
                $od                      = $idk->id;
                $updatedgood             = Good::find($od);
                $updatedgood->count      = Good::where('id', '=', $od)->first()->count - $dd['count'];
                $updatedgood->indelivery = Good::where('id', '=', $od)->first()->indelivery + $dd['count'];
                $updatedgood->save();
            }
        }

        // Send FCM notification to admin users
        $this->sendFCMNotification($order);

        return response()->json(['success' => true]);
    }

    //Tsaas.mn захиалга
    public function createDeliveryTsaas(Request $request)
    {
        $user   = User::where('name', 'TSAAS.MN')->first();
        $userid = $user->id;

        $order         = new Delivery();
        $request->type = 1;
        if ($user->role == 'customer') {
            $order->shop = 'TSAAS.MN';
            if ($request->type == 1) {
                if (isset(Auth::user()->engiin)) {
                    $order->deliveryprice = Auth::user()->engiin;
                } else {
                    $defaultPrice         = Setting::where('type', 1)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 2) {
                if (isset(Auth::user()->tsagtai)) {
                    $order->deliveryprice = Auth::user()->tsagtai;
                } else {
                    $defaultPrice         = Setting::where('type', 2)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 3) {
                if (isset(Auth::user()->yaraltai)) {
                    $order->deliveryprice = Auth::user()->yaraltai;
                } else {
                    $defaultPrice         = Setting::where('type', 3)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 4) {
                if (isset(Auth::user()->onts_yaraltai)) {
                    $order->deliveryprice = Auth::user()->onts_yaraltai;
                } else {
                    $defaultPrice         = Setting::where('type', 4)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            }
        } else {
            $order->shop = 'TSAAS.MN';
        }

        $order->phone         = $request->phone;
        $order->phone2        = $request->phone2;
        $order->address       = $request->address;
        $order->comment       = $request->comment;
        $order->price         = $request->price;
        $order->received      = $request->received;
        $order->type          = 1;
        $order->merchant_id   = 398;
        $order->parcel_info   = $request->parcel_info;
        $order->order_code    = $request->order_code;
        $order->download_time = $request->download_time;
        $order->district      = $request->district;
        $order->receivername  = $request->receivername;
        $order->size          = $request->size;
        $order->number        = $request->number; //get
        $order->region        = $request->region;
        $order->goodtype      = $request->goodtype;
        $order->verified      = 0;
        $order->track         = '1000' . $request->track;
        $order->status        = 1;
        
        // Invoice fields
        $order->invoice_number = $request->invoice_number ?? null;
        $order->invoice_date   = $request->invoice_date ?? null;
        $order->customer_register = $request->customer_register ?? null;
        $order->customer_email = $request->customer_email ?? null;
        
        $order->save();

        $log        = new Log();
        $log->phone = $request->phone;
        $log->staff = 'TSAAS.MN';
        $log->value = 'TSAAS.MN' . ' ' . $order->track . ' дугаартай хүргэлт үүсгэлээ';
        $log->save();

        $string = str_replace('\n', '', $request->data);

        $strings = rtrim($string, ',');
        $convst  = str_replace('\\', '+', $strings);
        $qq      = json_decode($convst, true);

        if ($request->data) {
            foreach ($qq as $dd) {
                $idk                     = Good::where('goodname', str_replace('+', '\\', $dd['name']))->where('shop', $dd['cname'])->first();
                $od                      = $idk->id;
                $updatedgood             = Good::find($od);
                $updatedgood->count      = Good::where('id', '=', $od)->first()->count - $dd['count'];
                $updatedgood->indelivery = Good::where('id', '=', $od)->first()->indelivery + $dd['count'];
                $updatedgood->save();
            }
        }

        // Send FCM notification to admin users
        $this->sendFCMNotification($order);

        return response()->json(['success' => true]);
    }

    //Create delivery api
    public function createdelivery(Request $request)
    {
        //   dd($request->name);
        $user   = User::where('name', $request->name)->first();
        $userid = $user->id;

        $order = new Delivery();

        if ($user->role == 'customer') {
            $order->shop = $request->name;
            if ($request->type == 1) {
                if (isset(Auth::user()->engiin)) {
                    $order->deliveryprice = Auth::user()->engiin;
                } else {
                    $defaultPrice         = Setting::where('type', 1)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 2) {
                if (isset(Auth::user()->tsagtai)) {
                    $order->deliveryprice = Auth::user()->tsagtai;
                } else {
                    $defaultPrice         = Setting::where('type', 2)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 3) {
                if (isset(Auth::user()->yaraltai)) {
                    $order->deliveryprice = Auth::user()->yaraltai;
                } else {
                    $defaultPrice         = Setting::where('type', 3)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 4) {
                if (isset(Auth::user()->onts_yaraltai)) {
                    $order->deliveryprice = Auth::user()->onts_yaraltai;
                } else {
                    $defaultPrice         = Setting::where('type', 4)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            }
        } else {
            $order->shop = $request->shop;
        }

        $order->phone         = $request->phone;
        $order->phone2        = $request->phone2;
        $order->address       = $request->detailadd;
        $order->comment       = $request->comment;
        $order->price         = $request->price;
        $order->received      = $request->received;
        $order->type          = $request->type;
        $order->merchant_id   = $request->merchant_id;
        $order->parcel_info   = $request->parcel_info;
        $order->order_code    = $request->order_code;
        $order->download_time = $request->download_time;
        $order->district      = $request->district;
        $order->receivername  = $request->receivername;
        $order->size          = $request->size;
        $order->number        = $request->number;
        $order->region        = $request->region;
        $order->goodtype      = $request->goodtype;
        $order->verified      = 0;
        $order->track         = 'CH' . rand(100000, 999999) . $userid;
        $order->status        = 1;

        if ($request->hasFile('image')) {
            $files = "";
            foreach ($request->file('image') as $image_url) {
                $fileExt         = $image_url->getClientOriginalExtension();
                $randomString    = substr(uniqid(rand()), 0, 15);
                $fileNameToStore = $randomString . '_' . time() . '.' . $fileExt;

                $picUrl = $image_url->store('baraa', 'public');

                $files .= $picUrl . "|";
            }

            $files        = rtrim($files, "|");
            $order->image = $files;
        }

        $order->save();

        $log        = new Log();
        $log->phone = $request->phone;
        $log->staff = $request->name;
        $log->value = $request->name . ' ' . $order->track . ' дугаартай хүргэлт үүсгэлээ';
        $log->save();

        $string = str_replace('\n', '', $request->data);

        $strings = rtrim($string, ',');
        $convst  = str_replace('\\', '+', $strings);
        $qq      = json_decode($convst, true);

        if ($request->data) {
            foreach ($qq as $dd) {
                $idk                     = Good::where('goodname', str_replace('+', '\\', $dd['name']))->where('shop', $dd['cname'])->first();
                $od                      = $idk->id;
                $updatedgood             = Good::find($od);
                $updatedgood->count      = Good::where('id', '=', $od)->first()->count - $dd['count'];
                $updatedgood->indelivery = Good::where('id', '=', $od)->first()->indelivery + $dd['count'];
                $updatedgood->save();
            }
        }

        // Send FCM notification to admin users
        $this->sendFCMNotification($order);

        return response()->json(['data' => $order, 'success' => true]);
    }

    /**
     * Send FCM notification to admin users when a new delivery is created
     */
    private function sendFCMNotification($delivery, $type = 'new')
    {
        try {
            // Check if fcm_token column exists in users table
            $hasFcmTokenColumn = Schema::hasColumn('users', 'fcm_token');
            
            if (!$hasFcmTokenColumn) {
                \Log::info('FCM token column does not exist in users table. Please add fcm_token column to users table.');
                return;
            }

            // Get all admin users with FCM tokens
            $adminUsers = User::where('role', 'admin')
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->get();

            if ($adminUsers->isEmpty()) {
                \Log::info('No admin users with FCM tokens found');
                return;
            }

            // Prepare notification message based on type
            $messages = [
                'new' => [
                    'title' => 'Шинэ хүргэлт үүслээ',
                    'body' => $delivery->track . ' дугаартай хүргэлт үүсгэгдлээ. ' . ($delivery->shop ?? '') . ' - ' . ($delivery->phone ?? '')
                ],
                'driver_changed' => [
                    'title' => 'Жолооч солигдлоо',
                    'body' => $delivery->track . ' дугаартай хүргэлтийн жолооч ' . ($delivery->driver ?? '') . ' болж солигдлоо'
                ],
                'delivered' => [
                    'title' => 'Хүргэлт дууслаа',
                    'body' => $delivery->track . ' дугаартай хүргэлт амжилттай хүргэгдлээ. ' . ($delivery->shop ?? '')
                ]
            ];

            $message = $messages[$type] ?? $messages['new'];
            $title = $message['title'];
            $body = $message['body'];

            // Send notification to each admin user
            foreach ($adminUsers as $adminUser) {
                $this->sendFCMToDevice($adminUser->fcm_token, $title, $body, $delivery);
            }
        } catch (\Exception $e) {
            \Log::error('FCM Notification Error: ' . $e->getMessage());
        }
    }

    /**
     * Send FCM notification to a specific device token
     */
    private function sendFCMToDevice($token, $title, $body, $delivery)
    {
        try {
            // Get Firebase configuration from environment
            $projectId = env('FIREBASE_PROJECT_ID');
            $accessToken = env('FIREBASE_ACCESS_TOKEN');
            $serviceAccountPath = env('FIREBASE_SERVICE_ACCOUNT_PATH');

            // Convert relative path to absolute path if needed
            if ($serviceAccountPath) {
                // If path starts with storage/app/, convert to absolute path
                if (strpos($serviceAccountPath, 'storage/app/') === 0) {
                    $serviceAccountPath = storage_path('app/' . substr($serviceAccountPath, strlen('storage/app/')));
                } elseif (strpos($serviceAccountPath, 'storage/') === 0) {
                    $serviceAccountPath = storage_path(substr($serviceAccountPath, strlen('storage/')));
                } elseif (strpos($serviceAccountPath, '/') !== 0) {
                    // If it's a relative path (doesn't start with /), try base_path
                    $serviceAccountPath = base_path($serviceAccountPath);
                }
            }

            // Try to get access token from service account JSON file if available
            if (!$accessToken && $serviceAccountPath && file_exists($serviceAccountPath)) {
                $accessToken = $this->getAccessTokenFromServiceAccount($serviceAccountPath);
                $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
                if (!$projectId && isset($serviceAccount['project_id'])) {
                    $projectId = $serviceAccount['project_id'];
                }
            }

            // If using FCM v1 API with access token
            if ($projectId && $accessToken) {
                $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
                
                $payload = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'type' => $type === 'new' ? 'new_delivery' : ($type === 'driver_changed' ? 'driver_changed' : 'delivered'),
                            'delivery_id' => (string)$delivery->id,
                            'track' => $delivery->track ?? '',
                            'status' => (string)$delivery->status,
                        ],
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'channel_id' => 'delivery_channel',
                                'sound' => 'default',
                                'visibility' => 'public',
                            ],
                        ],
                    ],
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->post($url, $payload);

                if ($response->successful()) {
                    $responseBody = $response->json();
                    \Log::info('FCM notification sent successfully', [
                        'token' => substr($token, 0, 20) . '...',
                        'message_id' => $responseBody['name'] ?? 'unknown'
                    ]);
                } else {
                    $errorBody = $response->body();
                    \Log::warning('FCM notification failed', [
                        'status' => $response->status(),
                        'response' => $errorBody,
                        'token_preview' => substr($token, 0, 20) . '...',
                    ]);
                }
            } else {
                \Log::warning('FCM configuration missing. Please set FIREBASE_PROJECT_ID and either FIREBASE_ACCESS_TOKEN or FIREBASE_SERVICE_ACCOUNT_PATH in .env file');
            }
        } catch (\Exception $e) {
            \Log::error('FCM Send Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate access token from service account JSON file
     */
    private function getAccessTokenFromServiceAccount($serviceAccountPath)
    {
        try {
            if (!file_exists($serviceAccountPath)) {
                \Log::error('Service account file not found: ' . $serviceAccountPath);
                return null;
            }

            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
            
            if (!isset($serviceAccount['client_email']) || !isset($serviceAccount['private_key'])) {
                \Log::error('Invalid service account file format');
                return null;
            }

            // Use Firebase JWT library (already installed via laravel/passport)
            $now = time();
            $jwt = [
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600, // Token expires in 1 hour
                'iat' => $now,
            ];

            // Generate JWT
            $jwtToken = JWT::encode($jwt, $serviceAccount['private_key'], 'RS256');

            // Exchange JWT for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            } else {
                \Log::error('Failed to get access token: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('Error generating access token: ' . $e->getMessage());
            return null;
        }
    }

    public function donedelivery($name)
    {
        $delivery = Delivery::where('driver', '=', $name)
            ->where(function ($query) {
                $query->where('status', "=", "3");
                $query->orWhere('status', "=", "4");
                $query->orWhere('status', "=", "5");
                $query->orWhere('status', "=", "10");
            })->orderBy('deliveries.id', 'DESC')->get();
        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data'    => $delivery,
        ], 200);
    }

    public function editNoteOnDataTable(Request $request)
    {
        if ($request->ajax()) {

            $id   = $request->get('id', 0);
            $note = $request->get('note');
            Delivery::where('id', $id)->update(['note' => $note]);
        }
    }
    public function editCommentDataTable(Request $request)
    {
        if ($request->ajax()) {
            $id      = $request->get('id', 0);
            $comment = $request->get('comment');
            Delivery::where('id', $id)->update(['comment' => $comment]);
            // Alert::success('Comment', 'Амжилттай шинэчлэгдлээ');
        }
    }

    //driver statistic begins

    public function todeliver($name)
    {
        $list = Delivery::where('driver', $name)->where('status', 2)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function totalforcust($name)
    {
        $list = Delivery::where('shop', $name)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function donedeliver($name)
    {
        $list = Delivery::where('driver', $name)->where('status', 3)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function declinedeliver($name)
    {
        $list = Delivery::where('driver', $name)->where('status', 4)->orWhere('status', 6)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function totaldeliver($name)
    {
        $list = Delivery::where('driver', $name)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }
    //driver stat ends

    public function write(Request $request)
    {
        $order       = Delivery::find($request->id);
        $order->note = $request->comm;
        $order->save();
        return response()->json(['data' => $order, 'success' => true]);
    }

public function sign(Request $request)
{
    $user = Delivery::where('id', $request->phone)->first();

    if ($picInfo = $request->file('image')) {
        $picUrl = $request->file('image')->store('signImage', 'public');
        $user->sign_image = $picUrl;
    }

    $user->rating = $request->rating;
    $user->status = 3;
    $user->delivered_at = now();
    
    // Save latitude and longitude if provided
    if ($request->has('latitude') && $request->latitude) {
        $user->latitude = $request->latitude;
    }
    if ($request->has('longitude') && $request->longitude) {
        $user->longitude = $request->longitude;
    }
    
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Гарын үсэг хүлээн авлаа',
    ]);
}


    public function receive($id)
    {
        $user         = Delivery::where('id', $id)->first();
        $user->status = 10;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Утасны дугаарыг хүлээн авлаа',
            'data'    => $user,
        ]);
    }

    public function declinefromshop(Request $request)
    {
        $order                = Delivery::find($request->id);
        $order->status        = 4;
        $order->deliveryprice = 0;
        $order->save();
        return response()->json(['data' => $order, 'success' => true]);
    }

    public function editing(Request $request)
    {
        $order               = Delivery::find($request->id);
        $order->note         = $request->note;
        $order->phone        = $request->phone;
        $order->address      = $request->address;
        $order->received     = $request->received;
        $order->receivername = $request->receivername;
        $order->save();
        return response()->json(['data' => $order, 'success' => true]);
    }

    public function decline_delivery(Request $request)
    {

        $delivery         = Delivery::find($request->id);
        $delivery->status = $request->status;
        if ($request->status == "Цуцалсан") {
            $delivery->status   = 4;
            $delivery->note     = $request->comm;
            $delivery->received = 0;
            $delivery->save();

            $log        = new Log();
            $log->phone = $delivery->phone;
            $log->staff = $delivery->shop;
            $log->value = $delivery->name . ' ' . $delivery->track . ' дугаартай хүргэлт цуцалсан төлөвт орууллаа';
            $log->save();

            // $ware=Ware::where('deliverid',$black->tracking)->first();
            // $wareg=Ware::where('deliverid',$black->tracking)->get();
            //     if($ware){
            //         $ware->delete();
            //         foreach($wareg as $wares){
            //             $good=Good::where('id',$wares->goodid)->first();
            //             $good->inprogress=$good->inprogress-$wares->count;
            //             $good->count=$good->count+$wares->count;
            //             $good->save();
            //         }
            //     }

        } elseif ($request->status == "Буцаасан") {
            $delivery->status   = 5;
            $delivery->note     = $request->comm;
            $delivery->received = 0;
            $delivery->save();

            $log        = new Log();
            $log->phone = $delivery->phone;
            $log->staff = $delivery->shop;
            $log->value = $delivery->name . ' ' . $delivery->track . ' дугаартай хүргэлт Буцаасан төлөвт орууллаа';
            $log->save();
            // if($ware){
            //     $ware->delete();
            //     foreach($wareg as $wares){
            //     $good=Good::where('id',$wares->goodid)->first();
            //     $good->inprogress=$good->inprogress-$wares->count;
            //     $good->count=$good->count+$wares->count;
            //     $good->save();
            //             }
            // }

        } else {
            $delivery->status = 6;
            $delivery->note   = $request->comm;

            if ($request->status == 'Утсаа аваагүй') {
                $delivery->note = $request->status;
                $delivery->save();
            } elseif ($request->status == 'Хэрэглэгч хойшлуулсан') {
                $delivery->note = $request->status;
                $delivery->save();
            } elseif ($request->status == 'Хаяг солигдсон') {
                $delivery->note = $request->status;
                $delivery->save();
            } elseif ($request->status == 'Хаасан байсан') {
                $delivery->note = $request->status;
                $delivery->save();
            } elseif ($request->status == 'Жолоочийн машинд асуудал гарсан') {
                $delivery->note = $request->status;
                $delivery->save();
            } else {
                $delivery->note = $request->status;
                $delivery->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data'    => $delivery,
        ], 200);
    }
    public function settings()
    {
        $defaultPrice = Setting::get();
        return response()->json(['data' => $defaultPrice, 'success' => true]);
    }

    // Хүргэлт дэлгэрэнгүй харах
    public function deliverydetail($id)
    {
        $list = Delivery::with('merchant')->where('id', $id)->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function create(Request $request)
{
    $cookie_data = stripslashes(Cookie::get('shopping_cart'));
    $cart_data   = json_decode($cookie_data, true, 512, JSON_UNESCAPED_UNICODE);
    $order       = new Delivery();

    $rules = [
        'address'      => 'required',
        'phone'        => 'required|numeric|digits:8',
        'receivername' => 'required',
        'number'       => 'required|numeric',
        'price'        => 'numeric',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Баталгаажуулалтын алдаа',
                'errors' => $validator->errors()
            ], 422);
        }
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        DB::beginTransaction();

        // Merchant creation logic
        if (isset($request->merchant_id)) {
            $order->merchant_id = $request->merchant_id;
        } else {
            // Validate merchant data
            $merchantRules = [
                'merchantName'    => 'required',
                'merchantAddress' => 'required',
                'merchantPhone1'  => 'required',
            ];

            $merchantValidator = Validator::make($request->all(), $merchantRules);
            if ($merchantValidator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Мерчантын мэдээлэл дутуу',
                        'errors' => $merchantValidator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($merchantValidator)
                    ->withInput();
            }

            $merchant = new Merchant;
            $merchant->user_id            = Auth::user()->id;
            $merchant->merchantName       = $request->merchantName;
            $merchant->merchantAddress    = $request->merchantAddress;
            $merchant->merchantPhone1     = $request->merchantPhone1;
            $merchant->merchantPhone2     = $request->merchantPhone2;
            $merchant->merchantWhat3Words = $request->merchantWhat3Words;
            
            if (!$merchant->save()) {
                throw new \Exception('Мерчант хадгалахад алдаа гарлаа');
            }
            $order->merchant_id = $merchant->id;
        }

        // Delivery pricing logic
        if (Auth::user()->role == 'customer') {
            $order->shop = Auth::user()->name;
            $type = $request->type;
            
            $priceFieldMap = [
                1 => 'engiin',
                2 => 'tsagtai',
                3 => 'yaraltai',
                4 => 'onts_yaraltai'
            ];

            if (isset($priceFieldMap[$type]) && Auth::user()->{$priceFieldMap[$type]}) {
                $order->deliveryprice = Auth::user()->{$priceFieldMap[$type]};
            } else {
                $defaultPrice = Setting::where('type', $type)->first();
                if (!$defaultPrice) {
                    throw new \Exception('Тохиргооны үнэ олдсонгүй');
                }
                $order->deliveryprice = $defaultPrice->price;
            }
        } else {
            $order->shop = $request->shop;
        }

        // Set order properties
        $order->phone         = $request->phone;
        $order->phone2        = $request->phone2;
        $order->type          = $request->type;
        $order->parcel_info   = $request->parcel_info;
        $order->order_code    = $request->order_code;
        $order->download_time = $request->download_time;
        $order->district      = $request->district;
        $order->address       = $request->address;
        $order->comment       = $request->comment;
        $order->receivername  = $request->receivername;
        $order->size          = $request->size;
        $order->number        = $request->number;
        $order->price         = $request->price ?? 0;
        
        // Merchant-specific pricing logic
        $merchantName = Auth::user()->name;
        if ($merchantName === 'EZPAY' && isset($request->number)) {
            $order->price = $request->number * 640;
        } elseif ($merchantName === 'Golomt' && isset($request->number)) {
            $order->price = $request->number * 715;
        }
        
        $order->region        = $request->region;
        $order->goodtype      = $request->goodtype;
        $order->verified      = 0;
        $order->track         = 'CH' . rand(100000, 999999) . Auth::user()->id;
        $order->status        = 1;

        // Handle image upload
        if ($picInfo = $request->file('image')) {
            $picUrl       = $request->file('image')->store('baraa', 'public');
            $order->image = $picUrl;
        }

        // Save the order
        if (!$order->save()) {
            throw new \Exception('Хүргэлт хадгалахад алдаа гарлаа');
        }

        // Handle cart data
        $psum = 0;
        if ($cart_data) {
            foreach ($cart_data as $cdata) {
                $updatedgood = Good::find($cdata['item_id']);
                if (!$updatedgood) {
                    throw new \Exception('Бараа олдсонгүй: ' . $cdata['item_id']);
                }

                $updatedgood->count      = $updatedgood->count - $cdata['item_quantity'];
                $updatedgood->indelivery = $updatedgood->indelivery + $cdata['item_quantity'];
                
                if (!$updatedgood->save()) {
                    throw new \Exception('Бараа шинэчлэхэд алдаа гарлаа');
                }

                $psum += $cdata['item_price'] * $cdata['item_quantity'];
            }
            
            // Update order price if cart has items
            if ($psum > 0) {
                $order->price    = $psum;
                $order->received = $psum;
                if (!$order->save()) {
                    throw new \Exception('Захиалгын үнийг шинэчлэхэд алдаа гарлаа');
                }
            }
        }

        // Create log
        $log = new Log();
        $log->phone = $request->phone;
        $log->staff = Auth::user()->name;
        $log->value = Auth::user()->name . ' ' . $order->track . ' дугаартай захиалга үүсгэлээ';
        
        if (!$log->save()) {
            throw new \Exception('Лог хадгалахад алдаа гарлаа');
        }

        DB::commit();

        // Send FCM notification to admin users
        $this->sendFCMNotification($order);

        // Clear cookies
        Cookie::queue(Cookie::forget('shopping_cart'));
        Cookie::queue(Cookie::forget('phone_cart'));
        Cookie::queue(Cookie::forget('address_cart'));

        // Return appropriate response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Амжилттай хадгалагдлаа',
                'delivery_id' => $order->id,
                'track_number' => $order->track
            ]);
        }

        Alert::success('Хүргэлт', 'Амжилттай хадгалагдлаа');
        return redirect('/delivery/new');

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Delivery creation failed: ' . $e->getMessage());
        \Log::error('Request data: ', $request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }

        Alert::error('Алдаа', 'Хүргэлт үүсгэхэд алдаа гарлаа: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}
    public function good($shop)
    {
        echo json_encode(DB::table('goods')->where('shop', $shop)->get());
    }

    public function addtocart(Request $request)
    {
        $prod_id = $request->input('product_id');

        $quantity     = $request->input('quantity');
        $product_name = $request->input('product_name');

        if (Cookie::get('shopping_cart')) {
            $cookie_data = stripslashes(Cookie::get('shopping_cart'));
            $cart_data   = json_decode($cookie_data, true);
        } else {
            $cart_data = [];
        }

        $item_id_list     = array_column($cart_data, 'item_id');
        $prod_id_is_there = $prod_id;

        if (in_array($prod_id_is_there, $item_id_list)) {
            foreach ($cart_data as $keys => $values) {
                if ($cart_data[$keys]["item_id"] == $prod_id) {
                    $cart_data[$keys]["item_quantity"] = $request->input('quantity');
                    $cart_data[$keys]["item_name"]     = urldecode($request->input('product_name'));
                    $item_data                         = json_encode($cart_data);
                    $minutes                           = 60;
                    // Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));
                    return response()->json(['status' => '"' . $cart_data[$keys]["item_name"] . '" Уг бүтээгдэхүүн сагсанд байна']);
                }
            }
        } else {
            $products  = Good::find($prod_id);
            $prod_name = $products['goodname'];
            $priceval  = $products['price'];
            $quantity  = $request->input('quantity');
            if ($products) {
                $item_array = [
                    'item_id'       => $prod_id,
                    'item_name'     => urlencode($prod_name),
                    'item_quantity' => $quantity,
                    'item_price'    => $priceval,

                ];
                $cart_data[] = $item_array;

                $item_data = json_encode($cart_data);
                $minutes   = 60;
                Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));
                return response()->json(['status' => '"' . $prod_name . '" сагсанд нэмэгдлээ']);
            }
        }
    }

    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart                           = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }

    public function recover($id)
    {
        $delivery         = Delivery::find($id);
        $delivery->status = 1;
        $delivery->driver = '';
        $delivery->save();
        return Redirect::back();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }

    public function list()
    {
        return view('admin.delivery.list');
    }

    public function new ()
    {
        return view('admin.delivery.new');
    }

    public function deliveryshop($name)
    {
        $list = Delivery::with('merchant')->where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "1");
        })->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function newDeliveryShopCount($name)
    {
        $list = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "1");
        })->count();
        return response()->json(['total' => $list, 'success' => true]);
    }

    public function activeDeliveryShop($name)
    {
        $list = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "2");
        })->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function activeDeliveryShopCount($name)
    {
        $list = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "2");
        })->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function declineddelivery($name)
    {
        $list = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "4");
            $query->orWhere('deliveries.status', "=", "5");
        })->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function totake($name)
    {
        $list = Delivery::where('shop', $name)->sum('price');
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function taken($name)
    {
        $list = Delivery::where('shop', $name)->sum('received');
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function successdelivery($name)
    {
        $list = Delivery::where('shop', $name)->where('status', 3)->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function done()
    {
        return view('admin.delivery.done');
    }

    public function allDelivery()
    {
        return view('admin.delivery.all');
    }
    //Жолоочийн хүргэлт татан авалт
    public function deliveryDownload()
    {
        return view('admin.delivery.delivery_download');
    }
    //Жолоочийн хүргэлт татан авалтын мэдээлэл татах
    public function getDeliveryDownload()
    {
        //$deliveryDownloaded = DB::table('deliveries_download')->orderBy('id', 'DESC') ->get();
        // $deliveryDownloaded = DeliveryDownload::with('delivery')->orderBy('id', 'DESC')
        // ->get();
        $deliveryDownloaded = DeliveryDownload::join('deliveries', 'deliveries.id', '=', 'deliveries_download.deliveries_id')
            ->select(
                'deliveries_download.id',
                'deliveries_download.driver_id',
                'deliveries_download.deliveries_id',
                'deliveries_download.download_price',
                'deliveries_download.created_at',
                'deliveries_download.updated_at',
                'deliveries.shop' // Include the 'shop' column from the associated 'Delivery' model
            )
            ->latest('deliveries_download.id')
            ->get();

        // dd($deliveryDownloaded);
        $table = Datatables::of($deliveryDownloaded)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" style="width:20px;height:20px;" class="checkbox" onclick="updateCount()" name="foo" data-id="' . $row->id . '" value="' . $row->id . '">';
            })
            ->addColumn('driver_id', function ($row) {
                return $row->driver_id;
            })
            ->addColumn('shop', function ($row) {
                $deliveryInfo = $row->delivery;
                return $deliveryInfo ? ($deliveryInfo->shop ?? '-') : '-';
            })
            ->addColumn('deliveries_id', function ($row) {
                return $row->deliveries_id;
            })
            ->addColumn('download_price', function ($row) {
                return $row->download_price;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at;
            })

            ->rawColumns(['checkbox', 'driver_id', 'deliveries_id', 'download_price', 'shop', 'created_at'])
        // ->setTotalRecords($dataCount)
            ->skipPaging()
            ->make(true);
        //  dd($table);
        return $table;
    }

    public function received()
    {
        return view('admin.delivery.received');
    }

    public function delprice($name)
    {
        $delprice = Delivery::where('shop', $name)->sum('deliveryprice');
        return response()->json(['data' => $delprice, 'success' => true]);
    }

    public function declined($name)
    {
        $active = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "4");
            $query->orWhere('deliveries.status', "=", "5");
        })->count();

        return response()->json(['data' => $active, 'success' => true]);
    }

    public function active($name)
    {
        $active = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "1");
            $query->orWhere('deliveries.status', "=", "2");
        })->count();

        return response()->json(['data' => $active, 'success' => true]);
    }

    public function success($name)
    {
        $active = Delivery::where('shop', $name)->where('status', 3)->count();

        return response()->json(['data' => $active, 'success' => true]);
    }

    public function updatedel(Request $request)
    {
        $del                = Delivery::find($request->delId);
        $del->merchant_id   = $request->merchant_id;
        $del->parcel_info   = $request->parcel_info;
        $del->order_code    = $request->order_code;
        $del->download_time = $request->download_time;
        $del->phone         = $request->phone;
        $del->address       = $request->address;
        $del->comment       = $request->comment;
        $del->price         = $request->price;
        $del->number        = $request->number;
        $del->region        = $request->region;

        $del->type = $request->type;

        if ($request->driver_request) {
            $del->driver = $request->driver_request;
            $del->status = 2;
        }

        $userName = User::find($request->shop);
        if ($request->type == 1) {
            if (isset($userName->engiin)) {
                $del->deliveryprice = $userName->engiin;
            } else {
                $defaultPrice       = Setting::where('type', 1)->first();
                $del->deliveryprice = $defaultPrice->price;
            }
        } else if ($request->type == 2) {
            if (isset($userName->tsagtai)) {
                $del->deliveryprice = $userName->tsagtai;
            } else {
                $defaultPrice       = Setting::where('type', 2)->first();
                $del->deliveryprice = $defaultPrice->price;
            }
        } else if ($request->type == 3) {
            if (isset($userName->yaraltai)) {
                $del->deliveryprice = $userName->yaraltai;
            } else {
                $defaultPrice       = Setting::where('type', 3)->first();
                $del->deliveryprice = $defaultPrice->price;
            }
        } else if ($request->type == 4) {
            if (isset($userName->onts_yaraltai)) {
                $del->deliveryprice = $userName->onts_yaraltai;
            } else {
                $defaultPrice       = Setting::where('type', 4)->first();
                $del->deliveryprice = $defaultPrice->price;
            }
        }

        $del->size = $request->size;
        if ($picInfo = $request->file('image')) {
            $picUrl     = $request->file('image')->store('baraa', 'public');
            $del->image = $picUrl;
        }

        $del->save();
        Alert::success('Хүргэлт', 'Амжилттай шинэчлэгдлээ');
        return redirect()->route('deliveryNew');
    }

    public function deleted()
    {
        return view('admin.delivery.deleted');
    }

    public function delivered_delivery($id)
    {
        $delivery         = Delivery::find($id);
        $delivery->status = 3;
        $delivery->save();

        $log        = new Log();
        $log->phone = $delivery->phone;
        $log->staff = $delivery->shop;
        $log->value = $delivery->shop . ' ' . $delivery->track . ' дугаартай захиалаг үүсгэлээ';
        $log->save();

        // Send FCM notification to admin users
        $this->sendFCMNotification($delivery, 'delivered');

        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data'    => $delivery,
        ], 200);
    }

    public function updateindex(Request $request)
    {
        $string  = str_replace('\n', '', $request->data);
        $strings = rtrim($string, ',');
        $convst  = str_replace('\\', '+', $strings);
        $qq      = json_decode($convst, true);

        // Reset ordering for all deliveries to ensure consistent sorting
        Delivery::whereNotNull('id')->update(['ordering' => null]);

        foreach ($qq as $dds) {
            $delivery = Delivery::where('id', $dds['id'])->first();
            if ($delivery) {
                $delivery->ordering = $dds['ordering'];
                $delivery->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
        ], 200);
    }

    public function change_status_on_delivery(Request $request)
    {

        $data           = [];
        $data['status'] = 0;

        if ($request->ids && $request->status) {
            $ids   = explode(',', $request->ids);
            $dddd  = Delivery::whereIn('id', $ids)->where('verified', '0')->count();
            $dddds = Delivery::whereIn('id', $ids)->where('driver', null)->count();

            if ($request->status == 10) {
                // $data=Order::where('reqid','=',$ids)->get();
                $data['status']  = 1;
                $data['message'] = "Success";

                if ($request->status == 3) {
                    foreach ($data as $datas) {
                        $good             = Good::where('goodname', $datas->good)->first();
                        $good->inprogress = $good->inprogress - $datas->count;
                        $good->delivered  = $good->delivered + $datas->count;
                        $good->save();
                    }
                }

                $array_ids = array_filter(explode(',', $request->ids));
                $ids       = implode(',', $array_ids);
                $idss      = explode(',', $request->ids);
                Delivery::whereIn('id', $idss)->update(['status' => '10']);

                foreach ($idss as $id) {
                    $delivery = Delivery::find($id)->first();
                    // dd($delivery->driver);
                    $delivery_download                = new DeliveryDownload();
                    $delivery_download->deliveries_id = $id;
                    $delivery_download->driver_id     = $delivery->driver;

                    $userName = User::find($delivery->shop);

                    if (isset($userName->tatan_avalt)) {
                        $delivery_download->download_price = $userName->tatan_avalt;
                    } else {
                        $defaultPrice                      = Setting::where('type', 5)->first();
                        $delivery_download->download_price = $defaultPrice->price;
                    }

                    // $delivery_download->download_price =  '4500';
                    $delivery_download->created_at = now();
                    $delivery_download->updated_at = now();
                    $delivery_download->save();
                }
                for ($i = 0; $i < count($array_ids); $i++) {
                    $dddd       = Delivery::where('id', '=', $array_ids[$i])->first();
                    $log        = new Log();
                    $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг хүлээн авсан төлөвт орууллаа.';
                    $log->phone = $dddd['phone'];
                    $log->staff = Auth::user()->name;
                    $log->save();
                }

                // if($request->status==3)
                // {
                //     $ido = explode(',',$request->ids);
                //     Req::whereIn('id',$ido)->update(['deliveryprice'=>5000]);
                // } else {
                //     $ido = explode(',',$request->ids);

                //     Req::whereIn('id',$ido)->update(['deliveryprice'=>0]);
                // }

                Alert::success('Хүргэлт', 'Төлөв солигдлоо');
            } elseif ($request->status == 4 || $request->status == 5) {
                $array_ids = array_filter(explode(',', $request->ids));
                $ids       = implode(',', $array_ids);
                $idss      = explode(',', $request->ids);
                Delivery::whereIn('id', $idss)->update(['status' => $request->status]);
            } elseif ($dddd > 0 || $dddds > 0) {
                // dd('www');
                Alert::error('Хүргэлт', 'Баталгаажаагүй эсвэл жолоочгүй хүргэлт байна');
            } else {
                // dd('qqq');
                $idss      = explode(',', $request->ids);
                $array_ids = array_filter(explode(',', $request->ids));
                // $data=Order::where('reqid','=',$ids)->get();
                $data['status']  = 1;
                $data['message'] = "Success";
                // $data=Order::where('reqid','=',$ids)->get();
                if ($request->status == 3) {
                    $ids       = implode(',', $array_ids);
                    $array_ids = array_filter(explode(',', $request->ids));

                    Delivery::whereIn('id', $idss)->update(['status' => '3']);

                    $cc = Delivery::whereIn('id', $array_ids)->get();
                    // $data=Order::where('reqid','=',$ids)->get();
                    $data1        = Delivery::whereIn('id', $array_ids)->get();
                    $arr_ware     = [];
                    $arr_tracking = [];
                    for ($i = 0; $i < count($array_ids); $i++) {
                        $dddd           = Delivery::where('id', '=', $array_ids[$i])->first();
                        $arr_tracking[] = $dddd['track'];
                        $phone          = $dddd['phone'];

                        $response = Http::post('https://tsaas.mn/echuchuApi/order_update.php', [
                            'zdugaar' => $dddd->order_code,
                        ]);

                        $log        = new Log();
                        $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг хүргэсэн төлөвт орууллаа.';
                        $log->phone = $dddd['phone'];
                        $log->staff = Auth::user()->name;
                        $log->save();
                    }
                    //change status tsaas.mn tolovs = 4
                    // foreach ($idss as $id) {
                    //     $delivery = Delivery::find($id)->first();

                    // }
                    // $qqq=Ware::where('deliverid',$arr_tracking)->get();
                    // $wareg= Ware::whereIn('deliverid',$arr_tracking)->get();
                    // $arr_goodid = array();
                    // for($i=0; $i<count($arr_tracking);$i++){
                    //     $good=Ware::where('deliverid',$arr_tracking[$i])->first();
                    //     if($good){
                    //     $arr_goodid[]=$good['goodid'];
                    //     }
                    // }
                    // if(!empty($wareg)){
                    //     foreach($wareg as $wares){
                    //         $good=Good::where('id',$wares['goodid'])->first();
                    //         $good->inprogress=$good->inprogress-$wares['count'];
                    //         $good->delivered=$good->delivered+$wares['count'];
                    //         $good->save();
                    //     }
                    // }
                }
                $idss = explode(',', $request->ids);
                if ($request->status == 3) {
                    Delivery::whereIn('id', $idss)->update(['deliveryprice' => 5000]);
                } else {
                    Delivery::whereIn('id', $idss)->update(['deliveryprice' => 0]);
                }
                if ($request->status == 6) {
                    $array_ids = array_filter(explode(',', $request->ids));
                    $ids       = implode(',', $array_ids);
                    $idss      = explode(',', $request->ids);
                    Delivery::whereIn('id', $idss)->update(['status' => '6']);
                    $cc = Delivery::whereIn('id', $array_ids)->get();
                    // $data=Order::where('reqid','=',$ids)->get();
                    $data1    = Delivery::whereIn('id', $array_ids)->get();
                    $arr_ware = [];

                    $arr_tracking = [];
                    for ($i = 0; $i < count($array_ids); $i++) {
                        // Req::where('id','=',$array_ids[$i])->delete();
                        $dddd           = Delivery::where('id', '=', $array_ids[$i])->first();
                        $arr_tracking[] = $dddd['track'];
                        $phone          = $dddd['phone'];

                        $log        = new Log();
                        $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг хүлээгдэж буй төлөвт орууллаа.';
                        $log->phone = $dddd['phone'];
                        $log->staff = Auth::user()->name;
                        $log->save();
                    }
                }
                if ($request->status == 2) {
                    $array_ids = array_filter(explode(',', $request->ids));
                    $ids       = implode(',', $array_ids);
                    $idss      = explode(',', $request->ids);
                    Delivery::whereIn('id', $idss)->update(['status' => '2']);
                    for ($i = 0; $i < count($array_ids); $i++) {
                        $dddd = Delivery::where('id', '=', $array_ids[$i])->first();

                        $log        = new Log();
                        $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг жолоочид хуваарилсан төлөвт орууллаа.';
                        $log->phone = $dddd['phone'];
                        $log->staff = Auth::user()->name;
                        $log->save();
                    }
                }
                if ($request->status == 4 || $request->status == 5 || $request->status == 6) {

                    $array_ids = array_filter(explode(',', $request->ids));
                    $ids       = implode(',', $array_ids);
                    // Req::whereIn('id',$ids)->update(['status'=>'8']);
                    Delivery::where('id', $ids)->orWhere('status', '4')->orWhere('status', '6')->update(['deliveryprice' => '0']);
                    $cc = Delivery::whereIn('id', $array_ids)->get();
                    Delivery::where('id', $ids)->orWhere('status', '4')->orWhere('status', '5')->orWhere('status', '6')->update(['received' => '0']);
                    // $data=Order::where('reqid','=',$ids)->get();
                    $data1        = Delivery::whereIn('id', $array_ids)->get();
                    $arr_ware     = [];
                    $arr_tracking = [];
                    for ($i = 0; $i < count($array_ids); $i++) {
                        // Req::where('id','=',$array_ids[$i])->delete();
                        $dddd           = Delivery::where('id', '=', $array_ids[$i])->first();
                        $arr_tracking[] = $dddd['track'];
                        if ($request->status == 4) {
                            $idss = explode(',', $request->ids);
                            Delivery::whereIn('id', $idss)->update(['status' => '4']);
                            $cust       = $dddd['custname'];
                            $phone      = $dddd['phone'];
                            $log        = new Log();
                            $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг цуцалсан төлөвт орууллаа.';
                            $log->phone = $dddd['phone'];
                            $log->staff = Auth::user()->name;
                            $log->save();
                        }
                        if ($request->status == 5) {
                            $idss = explode(',', $request->ids);
                            Delivery::whereIn('id', $idss)->update(['status' => '5']);
                            $cust  = $dddd['custname'];
                            $phone = $dddd['phone'];

                            $log        = new Log();
                            $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг буцаасан төлөвт орууллаа.';
                            $log->phone = $dddd['phone'];
                            $log->staff = Auth::user()->name;
                            $log->save();
                        }
                    }
                    // $qqq=Ware::where('deliverid',$arr_tracking)->get();
                    // $wareg= Ware::whereIn('deliverid',$arr_tracking)->get();
                    $arr_goodid = [];
                    for ($i = 0; $i < count($arr_tracking); $i++) {
                        // $good=Ware::where('deliverid',$arr_tracking[$i])->first();
                        // if($good){
                        //     $arr_goodid[]=$good['goodid'];
                        // }

                    }
                    // if(!empty($wareg)){
                    //     foreach($wareg as $wares){
                    //         $good=Good::where('id',$wares['goodid'])->first();
                    //         $good->inprogress=$good->inprogress-$wares['count'];
                    //         $good->count=$good->count+$wares['count'];
                    //         $good->save();
                    //     }
                    // }
                    // Ware::whereIn('deliverid',$arr_tracking)->delete();
                }
                Alert::success('Хүргэлт', 'Төлөв солигдлоо');
            }
        }

        return json_encode($data);
    }

    public function change_bus_on_delivery(Request $request)
    {

        $data           = [];
        $data['status'] = 0;
        if ($request->ids && $request->region) {
            $ids = explode(',', $request->ids);
            Delivery::whereIn('id', $ids)->update(['region' => $request->region]);
            $data['region']  = 1;
            $data['message'] = "Success";
        }
        Alert::success('Хүргэлт', 'Бүс солигдлоо');
        return json_encode($data);
    }

    public function cartDetailsAjaxS()
    {

        if (Cookie::get('shopping_cart')) {
            $cookie_data = stripslashes(Cookie::get('shopping_cart'));
            $cart_data   = json_decode($cookie_data, true);
            $total       = 0;
            $html        = '<div class="col-md-7 ms-auto">
            <div class="cart-page-header"><h6 class="cart-page-header-title">Order list</h6></div>
            <div class="d-flex flex-column gap-3">';

            foreach ($cart_data as $data) {
                $html .= '<label class="order-card col-12 cartpage" data-cart-item-id="123" data-product-id="12">
                        <!-- <input class="order-card__input" type="checkbox" checked /> -->
                        <div class="order-card__body">

                            <input type="hidden" class="product_id" value="' . $data['item_id'] . '" >
                            <div class="product-row">

                                <div class="product-row__content">
                                    <h6 class="product-row__content-title"><div style="width:200px;">' . urldecode($data['item_name']) . '</div>  Тоо:' . number_format($data['item_quantity']) . ' <div style="display:inline;margin-left:50px;">Үнэ:' . number_format($data['item_price']) . '</div></h6>
                                    <div class="product-row__content-author">
                                    </div>
                                </div>
                                <div class="product-row__tally" style="display:inline;">
                                    <div class="product-row__tally--price">

                                    </div>

                                </div>
                            </div>
                        </div>
                    </label>
                    <script>

                    $(".delete_cart_data_bask").click(function (e) {
    e.preventDefault();

    var product_id = $(this).closest(".cartpage").find(".product_id").val();

    var data = {
        "_token": $("input[name=_token]").val(),
        "product_id": product_id,
    };

    // $(this).closest(".cartpage").remove();

    $.ajax({
        url: "/delete-from",
        type: "DELETE",

        data: data,
        success: function (response) {
            window.location.reload();
        }
    });
    });

                </script>
                    ';
            }
            $html .= '</div>
        </div>


        <div class="col-md-3 me-auto">

        <div class="cart-page__purchase">
            <div class="cart-page__purchase-lists">';
            foreach ($cart_data as $data):
                $html .= '<div class="cart-page__purchase-lists-item">

                                            </div>';
                $total = $total + ($data["item_price"] * $data["item_quantity"]);
            endforeach;

            $html .= '</div>
            <div class="cart-page__purchase-total">
                <div class="cart-page__purchase-total-item">
                    <div class="span">Total sum:</div>
                    <div class="total-price">₮ ' . number_format($total, 2) . '</div>
                </div>
            </div>

        </div>
        </div>';
        } else {
            $html .= '<div class="row">
            <div class="col-md-12 mycard py-5 text-center">
                <div class="mycards">
                    <h4>Таны сагс одоогоор хоосон байна.</h4>

                </div>
            </div>
        </div>';
        }

        return $html;
    }

public function change_driver_on_delivery(Request $request)
{
    $data = ['status' => 0, 'message' => ''];

    try {
        // Validate inputs
        if (!$request->ids || !$request->driverselected) {
            throw new \Exception('IDs болон Жолооч хоосон байна');
        }

        $ids = array_filter(explode(',', $request->ids));
        
        if (empty($ids)) {
            throw new \Exception('Хүргэлтийн ID олдсонгүй');
        }

        // Update database
        Delivery::whereIn('id', $ids)->update([
            'driver' => $request->driverselected,
            'status' => '2'
        ]);

        // Call TSaaS API (with error suppression for timeouts) and send notifications
        foreach ($ids as $deliveryId) {
            try {
                $delivery = Delivery::find($deliveryId);
                
                if ($delivery && $delivery->order_code) {
                    // Quick timeout to prevent long delays
                    Http::timeout(5)
                        ->post('https://tsaas.mn/echuchuApi/order_update_driver.php', [
                            'zdugaar' => $delivery->order_code,
                        ]);
                }
                
                // Send FCM notification for driver change
                if ($delivery) {
                    $this->sendFCMNotification($delivery, 'driver_changed');
                }
            } catch (\Exception $e) {
                // Silently fail for API calls - database is already updated
                continue;
            }
        }

        // Success response
        $data['status'] = 1;
        $data['driverselected'] = $request->driverselected;
        $data['message'] = "Success";
        $data['updated_count'] = count($ids);

        Alert::success('Амжилттай', count($ids) . ' хүргэлтийн жолооч солигдлоо');

    } catch (\Exception $e) {
        $data['message'] = $e->getMessage();
        Alert::error('Алдаа', $e->getMessage());
    }

    return response()->json($data);
}

    /**
     * API endpoint for changing driver on delivery (for mobile app)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeDriverOnDeliveryApi(Request $request)
    {
        $data = ['success' => false, 'status' => 0, 'message' => ''];

        try {
            // Validate inputs
            if (!$request->ids || !$request->driverselected) {
                return response()->json([
                    'success' => false,
                    'status' => 0,
                    'message' => 'IDs болон Жолооч хоосон байна'
                ], 400);
            }

            $ids = array_filter(explode(',', $request->ids));
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'status' => 0,
                    'message' => 'Хүргэлтийн ID олдсонгүй'
                ], 400);
            }

            // Update database
            $updatedCount = Delivery::whereIn('id', $ids)->update([
                'driver' => $request->driverselected,
                'status' => '2'
            ]);

            // Call TSaaS API (with error suppression for timeouts)
            $tsaasSuccessCount = 0;
            $tsaasFailCount = 0;
            
            foreach ($ids as $deliveryId) {
                try {
                    $delivery = Delivery::find($deliveryId);
                    
                    if ($delivery && $delivery->order_code) {
                        // Quick timeout to prevent long delays
                        Http::timeout(5)
                            ->post('https://tsaas.mn/echuchuApi/order_update_driver.php', [
                                'zdugaar' => $delivery->order_code,
                            ]);
                        $tsaasSuccessCount++;
                    }
                } catch (\Exception $e) {
                    // Silently fail for API calls - database is already updated
                    $tsaasFailCount++;
                    continue;
                }
            }

            // Success response
            $data['success'] = true;
            $data['status'] = 1;
            $data['driverselected'] = $request->driverselected;
            $data['message'] = count($ids) . ' хүргэлтийн жолооч амжилттай солигдлоо';
            $data['updated_count'] = count($ids);
            $data['tsaas_success'] = $tsaasSuccessCount;
            $data['tsaas_failed'] = $tsaasFailCount;

            return response()->json($data, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 0,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function change_verify_on_delivery(Request $request)
    {

        $data           = [];
        $data['status'] = 0;

        if ($request->ids) {
            $ids       = explode(',', $request->ids);
            $array_ids = array_filter(explode(',', $request->ids));
            if ($request->verified == 1) {
                for ($i = 0; $i < count($array_ids); $i++) {
                    // $delivery=Delivery::where('id','=',$array_ids[$i])->first();

                    // $log = new Log();
                    // $log -> desc = Auth::user()->name.', нь '.$dddd["tracking"].' ID-тай хүргэлтийг баталгаажууллаа.';
                    // $log -> phone = $dddd['phone'];
                    // $log -> value = $dddd['tracking'];
                    // $log->staff=Auth::user()->name;
                    // $log -> save();
                }
            } else {
                for ($i = 0; $i < count($array_ids); $i++) {
                    // $delivery=Delivery::where('id','=',$array_ids[$i])->first();

                    // $log = new Log();
                    // $log -> desc = Auth::user()->name.', нь '.$dddd["tracking"].' ID-тай хүргэлтийг баталсныг цуцаллаа.';
                    // $log -> phone = $dddd['phone'];
                    // $log -> value = $dddd['tracking'];
                    // $log->staff=Auth::user()->name;
                    // $log -> save();
                }
            }

            Delivery::whereIn('id', $ids)->update(['verified' => $request->verified]);

            // $req=Delivery::where('id',$ids)->get();
            // $good = Ware::where('deliverid',$req[0]['tracking'])->get();
            // foreach($good as $goods){
            //     $goods->verify=1;
            //     $goods->save();
            // }
            $data['status']  = 1;
            $data['message'] = "Success";
        }
        Alert::success('Хүргэлт', 'Баталгаажлаа');

        return json_encode($data);
    }

    public function reorderDelivery(Request $request)
    {
        try {
            if (!$request->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Хүргэлтийн ID олдсонгүй'
                ], 400);
            }

            $delivery = Delivery::find($request->id);
            
            if (!$delivery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Хүргэлт олдсонгүй'
                ], 404);
            }

            $userid = Auth::user()->id;

            // Create a new delivery with the same data
            $newDelivery = new Delivery();
            
            // Copy all relevant fields
            $newDelivery->merchant_id = $delivery->merchant_id;
            $newDelivery->shop = $delivery->shop;
            $newDelivery->phone = $delivery->phone;
            $newDelivery->phone2 = $delivery->phone2;
            $newDelivery->address = $delivery->address;
            $newDelivery->comment = $delivery->comment;
            $newDelivery->receivername = $delivery->receivername;
            $newDelivery->size = $delivery->size;
            // Use the number from request if provided, otherwise use original
            $newDelivery->number = $request->number ?? $delivery->number;
            $newDelivery->price = $delivery->price;
            $newDelivery->received = $delivery->received;
            $newDelivery->region = $delivery->region;
            $newDelivery->goodtype = $delivery->goodtype;
            $newDelivery->parcel_info = $delivery->parcel_info;
            $newDelivery->order_code = $delivery->order_code;
            $newDelivery->download_time = $delivery->download_time;
            $newDelivery->district = $delivery->district;
            $newDelivery->type = $delivery->type;
            $newDelivery->deliveryprice = $delivery->deliveryprice;
            $newDelivery->image = $delivery->image;
            
            // Copy latitude and longitude if they exist
            if (isset($delivery->latitude)) {
                $newDelivery->latitude = $delivery->latitude;
            }
            if (isset($delivery->longitude)) {
                $newDelivery->longitude = $delivery->longitude;
            }
            
            // Reset fields for new delivery
            $newDelivery->track = 'CH' . rand(100000, 999999) . $userid;
            $newDelivery->status = 1; // Бүртгэгдсэн
            $newDelivery->verified = 0;
            $newDelivery->driver = null;
            $newDelivery->note = null;
            $newDelivery->delivered_at = null;
            $newDelivery->rating = null;
            
            $newDelivery->save();

            // Create log entry
            $log = new Log();
            $log->phone = $delivery->phone;
            $log->staff = Auth::user()->name;
            $log->value = Auth::user()->name . ' ' . $delivery->track . ' дугаартай хүргэлтийг дахин захиалж ' . $newDelivery->track . ' дугаартай шинэ хүргэлт үүсгэлээ';
            $log->save();

            return response()->json([
                'success' => true,
                'message' => 'Амжилттай',
                'count' => 1
            ]);

        } catch (\Exception $e) {
            \Log::error('Re-order delivery failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }
    }

    public function change_delete_on_delivery(Request $request)
    {

        $data           = [];
        $data['status'] = 0;
        $ids            = explode(',', $request->ids);
        $dddd           = Delivery::whereIn('id', $ids)->where('verified', '1')->count();
        $st             = Delivery::whereIn('id', $ids)->where('status', '10')->count();
        if ($dddd > 0 || $st > 0) {
            Alert::error('Хүргэлт', 'Баталгаажсан хүргэлт устгах боломжгүй');
        } else {
            if ($request->ids) {
                $array_ids = array_filter(explode(',', $request->ids));
                $ids       = implode(',', $array_ids);
                // Req::whereIn('id',$ids)->update(['status'=>'8']);
                Delivery::where('id', $ids)->orWhere('status', '4')->orWhere('status', '5')->update(['deliveryprice' => '0']);
                $cc              = Delivery::whereIn('id', $array_ids)->get();
                $data['status']  = 1;
                $data['message'] = "Success";
                //  $data=Order::where('reqid','=',$ids)->get();
                $data1        = Delivery::whereIn('id', $array_ids)->get();
                $arr_ware     = [];
                $arr_tracking = [];
                for ($i = 0; $i < count($array_ids); $i++) {
                    // Req::where('id','=',$array_ids[$i])->delete();
                    $dddd         = Delivery::where('id', '=', $array_ids[$i])->first();
                    $dddd->status = 100;
                    $dddd->save();
                    //  $arr_tracking[]=$dddd['tracking'];
                    $log        = new Log();
                    $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг устгалаа.';
                    $log->phone = $dddd['phone'];
                    $log->staff = Auth::user()->name;
                    $log->save();
                }
                // $qqq=Ware::where('deliverid',$arr_tracking)->get();
                //  $wareg= Ware::whereIn('deliverid',$arr_tracking)->get();
                //  $arr_goodid = array();
                //  for($i=0; $i<count($arr_tracking);$i++){
                //      $good=Ware::where('deliverid',$arr_tracking[$i])->first();
                //      if($good){
                //          $arr_goodid[]=$good['goodid'];
                //      }
                //  }
                //      if(!empty($wareg)){
                //          foreach($wareg as $wares){
                //              $good=Good::where('id',$wares['goodid'])->first();
                //              $good->inprogress=$good->inprogress-$wares['count'];
                //              $good->count=$good->count+$wares['count'];
                //              $good->save();
                //          }
                //      }
                //  Ware::whereIn('deliverid',$arr_tracking)->delete();
            }
            Alert::success('Хүргэлт', 'Амжилттай устгагдлаа');
        }
        return json_encode($data);
    }

    public function excelImport()
    {
        $file = request()->file('file');
        if ($file) {
            Excel::import(new RequestImportExcel, $file);
            return back();
        } else {
            return back()->with('error', 'Please Select File');
        }
    }

    public function showQRData(Request $request)
    {
        $arr_ids = explode(",", $request->post('ids'));
        $ids     = implode(",", array_filter($arr_ids));
        $user_id = Auth::user()->id;
        $role    = Auth::user()->role;
        $offset  = $request->get('start', 0);
        $limit   = $request->get('length', 10);
        if ($limit < 1 or $limit > 100) {
            $limit = 100;
        }
        $Params = [
            'ids'     => $ids,
            'user_id' => $user_id,
            'role'    => $role,
            'limit'   => $limit,
            'offset'  => $offset,
        ];
        $dataQR = Delivery::GetQRData($Params);
        return view('admin.delivery.bulkQRPrint', compact('dataQR'));
    }

        public function loadDeliveryDataTable(Request $request)
        {

            if ($request->ajax()) {

                $user_id    = Auth::user()->id;
                $role       = Auth::user()->role;
                $ids        = $request->get('ids', []);
                $status     = $request->get('status', 0);
                $district   = $request->get('district', 0);
                $region     = $request->get('region', 0);
                $phone      = $request->get('phone', 0);
                $address    = $request->get('address', 0);
                $note       = $request->get('note', 0);
                $tuluv      = $request->get('tuluv', 0);
                $start_date = $request->get('start_date', 0);
                $merchant   = $request->get('merchant', 0);
                $type       = $request->get('type', 0);
                $estimated  = $request->get('estimated', 0);
                //   dd( $estimated);
                $late           = $request->get('late', 0);
                $customer       = $request->get('customer', 0);
                $status_100     = $request->get('status_100', 0);
                $end_date       = $request->get('end_date', 0);
                $driverselected = $request->get('driver', 0);
                $except_status  = $request->get('except_status', 0);
                $except_stat    = $request->get('except_stat', 0);
                $status_10      = $request->get('status_10', 0);
                $status_1       = $request->get('status_1', 0);
                $status_2       = $request->get('status_2', 0);
                $status_3       = $request->get('status_3', 0);
                $status_6       = $request->get('status_6', 0);
                $status_4       = $request->get('status_4', 0);
                $status_5       = $request->get('status_5', 0);
                $not_3          = $request->get('not_3', 0);
                $not_4          = $request->get('not_4', 0);
                $not_2          = $request->get('not_2', 0);
                $not_6          = $request->get('not_6', 0);
                $not_5          = $request->get('not_5', 0);
                $not_1          = $request->get('not_1', 0);
                $not_10         = $request->get('not_10', 0);
                $not_100        = $request->get('not_100', 0);
                $offset         = $request->get('start', 0);
                $limit          = $request->get('length', 10);
                if ($limit < 1 or $limit > 3500) {
                    $limit = 3500;
                }

                $search = isset($request->get('search')['value'])
                ? $request->get('search')['value']
                : null;
                //dd($search);
                $orderColumnList = [
                    'id',
                    'organization',
                    'phone',
                    'address',
                    'created_at',
                    'status',
                    'region',
                    'note',
                    'driverselected',
                    'actions',
                ];

                $orderColumnIndex = isset($request->get('order')[0]['column'])
                ? $request->get('order')[0]['column']
                : 0;

                $orderColumnDir = isset($request->get('order')[0]['dir'])
                ? $request->get('order')[0]['dir']
                : 'asc';

                $orderColumn = isset($orderColumnList[$orderColumnIndex])
                ? $orderColumnList[$orderColumnIndex]
                : 'product_name';

                $Params = [
                    'user_id'        => $user_id,
                    'role'           => $role,
                    'search'         => $search,
                    'limit'          => $limit,
                    'offset'         => $offset,
                    'order_column'   => $orderColumn,
                    'order_dir'      => $orderColumnDir,
                    'ids'            => $ids,
                    'status'         => $status,
                    'status_10'      => $status_10,
                    'status_100'     => $status_100,
                    'status_1'       => $status_1,
                    'status_6'       => $status_6,
                    'status_2'       => $status_2,
                    'status_5'       => $status_5,
                    'status_4'       => $status_4,
                    'status_3'       => $status_3,
                    'not_5'          => $not_5,
                    'not_4'          => $not_4,
                    'not_3'          => $not_3,
                    'not_1'          => $not_1,
                    'not_2'          => $not_2,
                    'not_6'          => $not_6,
                    'not_10'         => $not_10,
                    'not_100'        => $not_100,
                    'tuluv'          => $tuluv,
                    'start_date'     => $start_date,
                    'end_date'       => $end_date,
                    'region'         => $region,
                    'district'       => $district,
                    'note'           => $note,
                    'late'           => $late,
                    'customer'       => $customer,
                    'phone'          => $phone,
                    'address'        => $address,
                    'merchant_id'    => $merchant,
                    'type'           => $type,
                    'estimated'      => $estimated,
                    'driverselected' => $driverselected,

                ];
                // dd($Params);
                $data      = Delivery::GetExcelData($Params);
                $dataCount = Delivery::GetExcelDataCount($Params);

                $table = Datatables::of($data)
                    ->addColumn('checkbox', function ($row) {
                        return '<input type="checkbox" style="width:20px;height:20px;" class="checkbox" onclick="updateCount()" name="foo" data-id="' . $row->id . '" value="' . $row->id . '">';
                    })
                    ->addColumn('id', function ($row) {
                        return $row->id;
                    })

                    ->addColumn('track', function ($row) {
                        if ($row->driver_request) {
                            return '<div><a  class="btn btn-danger" href="' . url('/delivery/detail/' . $row->id) . '" style="color:white;">' . $row->track . '</a></div>';
                            //  return '<div style="color:white;background-color:red">' . $row->track . '</div>';
                        } else {
                            return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 100% !important; height:100% !important" class="text-center whitespace-nowrap table-info">' . $row->track . '</div>';
                        }
                    })
                    ->addColumn('region', function ($row) {
                        if (Auth::user()->role != 'Customer') {
                            if ($row->driver_request) {
                                return '<div style="color:white;background-color:red">' . $row->region . '</div>';
                            } else {
                                return $row->region;
                            }
                        } else {
                            return '';
                        }
                    })
                    ->addColumn('type', function ($row) {
                        if ($row->type == '1') {
                            return '<div style="color:green;">Энгийн</div>';
                        } elseif ($row->type == '2') {
                            return '<div style="color:orange;">Цагтай</div>';
                        } elseif ($row->type == '3') {
                            return '<div style="color:pink;">Яаралтай</div>';
                        } elseif ($row->type == '4') {
                            return '<div style="color:red;">Онц яаралтай</div>';
                        }
                    })
                    ->addColumn('address', function ($row) {
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">' . $row->address . '</div>';
                    })
                    ->addColumn('receivername', function ($row) {
                        $mergedInfo = '';
                        if (isset($row->receivername) && ! empty($row->receivername)) {
                            $mergedInfo .= $row->receivername;
                        }
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 170px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                    })
                    ->addColumn('order_code', function ($row) {
                        $mergedInfo = '';
                        if (isset($row->order_code) && ! empty($row->order_code)) {
                            $mergedInfo .= $row->order_code;
                        }
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 100px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                    })
                    ->addColumn('invoice_number', function ($row) {
                        $invoiceNumber = isset($row->invoice_number) && !empty($row->invoice_number) ? $row->invoice_number : '-';
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 120px !important;" class="text-center whitespace-nowrap">' . $invoiceNumber . '</div>';
                    })
                    ->addColumn('invoice_date', function ($row) {
                        $invoiceDate = isset($row->invoice_date) && !empty($row->invoice_date) ? date('Y-m-d', strtotime($row->invoice_date)) : '-';
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 120px !important;" class="text-center whitespace-nowrap">' . $invoiceDate . '</div>';
                    })
                    ->addColumn('customer_register', function ($row) {
                        $customerRegister = isset($row->customer_register) && !empty($row->customer_register) ? $row->customer_register : '-';
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 120px !important;" class="text-center whitespace-nowrap">' . $customerRegister . '</div>';
                    })
                    ->addColumn('customer_email', function ($row) {
                        $customerEmail = isset($row->customer_email) && !empty($row->customer_email) ? $row->customer_email : '-';
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 150px !important;" class="text-center whitespace-nowrap">' . $customerEmail . '</div>';
                    })
                    ->addColumn('shop', function ($row) {
                        $mergedInfo = '';

                        $user = User::where('name', $row->shop)->first();
                        if (isset($row->shop) && ! empty($row->shop)) {
                            $mergedInfo .= $row->shop;
                        }
                        if ($user) {
                            if (isset($user->image) && ! empty($user->image)) {

                                $mergedInfo .= (! empty($mergedInfo) ? ', ' : '') . '<img src="' . asset('storage/') . '/' . $user->image . '" width="30"  style="float:right">';
                            }
                        } else {
                            $mergedInfo .= "";
                        }

                        if (empty($mergedInfo)) {
                            $mergedInfo = "Мэдээлэл байхгүй";
                        }
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 170px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                    })
                    ->addColumn('mergedMerchantParcel', function ($row) {
    $parts = [];
    
    // Add goodtype if exists
    if (!empty($row->goodtype)) {
        $parts[] = $row->goodtype;
    }
    
    // Handle parcel_info - REMOVE ALL HTML TAGS
    if (!empty($row->parcel_info)) {
        // Remove all HTML tags and clean up
        $cleanText = strip_tags($row->parcel_info);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        $cleanText = trim($cleanText);
        
        if (!empty($cleanText)) {
            $parts[] = $cleanText;
        }
    }

    // Add image if exists
    if (!empty($row->image)) {
        $imagesArray = explode('|', $row->image);
        $firstImagePath = $imagesArray[0];
        $parts[] = '<img src="' . asset('storage/' . $firstImagePath) . '" width="30" style="vertical-align: middle;" class="thumbnail zoom">';
    }

    if (empty($parts)) {
        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">Мэдээлэл байхгүй</div>';
    }

    $output = implode(', ', $parts);
    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">' . $output . '</div>';
})
                    ->addColumn('comment', function ($row) {
                        return '
                                <input class="font-medium whitespace-nowrap input" id="note_' . $row->id . '"  style="width:150px;"  value="' . $row->comment . '" name="comment"/>
                                <input type="hidden" value="' . $row->id . '" name="realid">
                                <button data-id="' . $row->id . '" class="btn btn-primary button_edit_comment" >  Засах </button>

                                <a class="font-medium whitespace-nowrap"></a>
                        ';
                    })
                    ->addColumn('note', function ($row) {
                        return '
                                <input class="font-medium whitespace-nowrap input" id="not_' . $row->id . '"  style="width:150px;"  value="' . $row->note . '" name="note"/>
                                <input type="hidden" value="' . $row->id . '" name="realid">
                                <button data-id="' . $row->id . '" class="btn btn-primary button_edit_note" >  Засах </button>

                                <a class="font-medium whitespace-nowrap"></a>
                        ';
                    })
                    ->addColumn('rating', function ($row) {
                        return '<div class="star-rating" data-rating="0">
                                    <span class="star">&#9733;</span>
                                    <span class="star">&#9733;</span>
                                    <span class="star">&#9733;</span>
                                    <span class="star">&#9733;</span>
                                    <span class="star">&#9733;</span>
                                    </div>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return substr($row->created_at, 5, -3);
                    })
                    ->addColumn('verified', function ($row) {
                        if ($row->verified == 1) {
                            return 'Тийм';
                        } else {
                            return 'Үгүй';
                        }
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            return '<div class="status1">Бүртгэгдсэн</div>';
                        } elseif ($row->status == 2) {
                            return '<div class="status2">Хуваарилсан</div>';
                        } elseif ($row->status == 6) {
                            return '<div class="status6">Хүлээгдэж буй</div>';
                        } elseif ($row->status == 3) {
                            return '<div class="status3">Хүргэгдсэн</div>';
                        } elseif ($row->status == 4) {
                            return 'Цуцалсан';
                        } elseif ($row->status == 5) {
                            return 'Буцаасан';
                        } elseif ($row->status == 10) {
                            return '<div class="status10">Хүлээн авсан</div>';
                        } elseif ($row->status == 4) {
                            return 'Дууссан';
                        }
                    })

                    ->addColumn('merchantName', function ($row) {
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 150px !important;" class="text-center whitespace-nowrap">' . $row->merchantName . '</div>';
                    })

                    ->addColumn('merchantPhone1', function ($row) {
                        $mergedInfo = '';
                        if (isset($row->merchantPhone1) && ! empty($row->merchantPhone1)) {
                            $mergedInfo .= $row->merchantPhone1;
                        }
                        if (isset($row->merchantPhone2) && ! empty($row->merchantPhone2)) {
                            $mergedInfo .= (! empty($mergedInfo) ? ', ' : '') . $row->merchantPhone2;
                        }

                        if (empty($mergedInfo)) {
                            $mergedInfo = "Мэдээлэл байхгүй";
                        }

                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 200px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                    })
                    ->addColumn('phone', function ($row) {
                        $mergedInfo = '';
                        if (isset($row->phone) && ! empty($row->phone)) {
                            $mergedInfo .= $row->phone;
                        }
                        if (isset($row->phone2) && ! empty($row->phone)) {
                            $mergedInfo .= (! empty($mergedInfo) ? ', ' : '') . $row->phone2;
                        }

                        if (empty($mergedInfo)) {
                            $mergedInfo = "Мэдээлэл байхгүй";
                        }

                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                    })
                    ->addColumn('merchantPhone2', function ($row) {
                        return $row->merchantPhone2;
                    })
                    ->addColumn('merchantAddress', function ($row) {
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">' . $row->merchantAddress . '</div>';
                    })
                    ->addColumn('driver', function ($row) {
                        if (Auth::user()->role == 'Customer') {
                            return '';
                        } else {
                            return $row->driver;
                        }
                    })
                    ->addColumn('actions', function ($row) {
                        $actions = '
                                                        <button type="submit" class="btn btn-info"><a href="' . url('/delivery/detail/' . $row->id) . '" style="color:white;">Дэлгэрэнгүй</a></button>
                                                        <button type="button" class="btn btn-warning btn-reorder-row" data-id="' . $row->id . '" data-number="' . ($row->number ?? '') . '" style="margin-top:5px;"><i class="fa fa-redo" style="margin-right:5px"></i>Дахин захиалах</button>';

                        return $actions;
                    })
                    ->addColumn('recover', function ($row) {

                        return '<button type="submit" class="btn btn-info" style="margin-bottom:2px;"><a href="' . url('/delivery/recover/' . $row->id) . '"style="color:white;">Сэргээх</a></button><br>
                                        <button type="submit" class="btn btn-info"><a href="' . url('/delivery/detail/' . $row->id) . '" style="color:white;">Дэлгэрэнгүй</a></button>
                            ';
                    })
                    ->rawColumns(['checkbox', 'track', 'merchantAddress', 'region', 'mergedMerchantParcel', 'actions', 'note', 'comment', 'address', 'status', 'recover', 'shop', 'rating', 'type', 'receivername', 'order_code', 'invoice_number', 'invoice_date', 'customer_register', 'customer_email', 'merchantName', 'merchantPhone1', 'phone', 'number'])

                    ->setTotalRecords($dataCount)
                    ->skipPaging()
                    ->make(true);
                return $table;
            }
        }

        /**
         * API endpoint for Flutter app to get delivery list
         * Supports Bearer token authentication (Passport)
         * Middleware auth:api ensures user is authenticated
         */
        public function loadDeliveryDataTableApi(Request $request)
        {
            try {
                // User is authenticated by auth:api middleware
                $user = Auth::user();
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized - Please provide a valid Bearer token',
                        'data' => [],
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0
                    ], 401);
                }

                $user_id = $user->id;
                $role = $user->role;
                $ids = $request->get('ids', []);
                $status = $request->get('status', 0);
                $district = $request->get('district', 0);
                $region = $request->get('region', 0);
                $phone = $request->get('phone', 0);
                $address = $request->get('address', 0);
                $note = $request->get('note', 0);
                $tuluv = $request->get('tuluv', 0);
                $start_date = $request->get('start_date', 0);
                $merchant = $request->get('merchant', 0);
                $type = $request->get('type', 0);
                $estimated = $request->get('estimated', 0);
                $late = $request->get('late', 0);
                $customer = $request->get('customer', 0);
                $status_100 = $request->get('status_100', 0);
                $end_date = $request->get('end_date', 0);
                $driverselected = $request->get('driver', 0);
                $except_status = $request->get('except_status', 0);
                $except_stat = $request->get('except_stat', 0);
                $status_10 = $request->get('status_10', 0);
                $status_1 = $request->get('status_1', 0);
                $status_2 = $request->get('status_2', 0);
                $status_3 = $request->get('status_3', 0);
                $status_6 = $request->get('status_6', 0);
                $status_4 = $request->get('status_4', 0);
                $status_5 = $request->get('status_5', 0);
                $not_3 = $request->get('not_3', 0);
                $not_4 = $request->get('not_4', 0);
                $not_2 = $request->get('not_2', 0);
                $not_6 = $request->get('not_6', 0);
                $not_5 = $request->get('not_5', 0);
                $not_1 = $request->get('not_1', 0);
                $not_10 = $request->get('not_10', 0);
                $not_100 = $request->get('not_100', 0);
                $offset = $request->get('start', 0);
                $limit = $request->get('length', 10);
                
                if ($limit < 1 || $limit > 3500) {
                    $limit = 3500;
                }

                $search = isset($request->get('search')['value'])
                    ? $request->get('search')['value']
                    : null;

                $orderColumnList = [
                    'id',
                    'organization',
                    'phone',
                    'address',
                    'created_at',
                    'status',
                    'region',
                    'note',
                    'driverselected',
                    'actions',
                ];

                $orderColumnIndex = isset($request->get('order')[0]['column'])
                    ? $request->get('order')[0]['column']
                    : 0;

                $orderColumnDir = isset($request->get('order')[0]['dir'])
                    ? $request->get('order')[0]['dir']
                    : 'asc';

                $orderColumn = isset($orderColumnList[$orderColumnIndex])
                    ? $orderColumnList[$orderColumnIndex]
                    : 'id';

                $Params = [
                    'user_id' => $user_id,
                    'role' => $role,
                    'search' => $search,
                    'limit' => $limit,
                    'offset' => $offset,
                    'order_column' => $orderColumn,
                    'order_dir' => $orderColumnDir,
                    'ids' => $ids,
                    'status' => $status,
                    'status_10' => $status_10,
                    'status_100' => $status_100,
                    'status_1' => $status_1,
                    'status_6' => $status_6,
                    'status_2' => $status_2,
                    'status_5' => $status_5,
                    'status_4' => $status_4,
                    'status_3' => $status_3,
                    'not_5' => $not_5,
                    'not_4' => $not_4,
                    'not_3' => $not_3,
                    'not_1' => $not_1,
                    'not_2' => $not_2,
                    'not_6' => $not_6,
                    'not_10' => $not_10,
                    'not_100' => $not_100,
                    'tuluv' => $tuluv,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'region' => $region,
                    'district' => $district,
                    'note' => $note,
                    'late' => $late,
                    'customer' => $customer,
                    'phone' => $phone,
                    'address' => $address,
                    'merchant_id' => $merchant,
                    'type' => $type,
                    'estimated' => $estimated,
                    'driverselected' => $driverselected,
                ];

                $data = Delivery::GetExcelData($Params);
                $dataCount = Delivery::GetExcelDataCount($Params);

                // Format data for API response (without HTML)
                $formattedData = [];
                foreach ($data as $row) {
                    $formattedData[] = [
                        'id' => $row->id,
                        'track' => $row->track ?? '',
                        'region' => $row->region ?? '',
                        'type' => $row->type ?? '1',
                        'address' => $row->address ?? '',
                        'receivername' => $row->receivername ?? '',
                        'order_code' => $row->order_code ?? '',
                        'shop' => $row->shop ?? '',
                        'mergedMerchantParcel' => $this->formatMergedMerchantParcel($row),
                        'phone' => $row->phone ?? '',
                        'number' => $row->number ?? '',
                        'status' => $this->formatStatus($row->status ?? 0),
                        'driver' => $row->driver ?? '',
                        'comment' => strip_tags($row->comment ?? ''),
                        'note' => strip_tags($row->note ?? ''),
                        'price' => $row->price ?? '',
                    ];
                }

                return response()->json([
                    'data' => $formattedData,
                    'recordsTotal' => $dataCount,
                    'recordsFiltered' => $dataCount,
                    'draw' => $request->get('draw', 1),
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'data' => [],
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ], 500);
            }
        }

        /**
         * Helper method to format merged merchant parcel info
         */
        private function formatMergedMerchantParcel($row)
        {
            $parts = [];
            
            if (!empty($row->goodtype)) {
                $parts[] = $row->goodtype;
            }
            
            if (!empty($row->parcel_info)) {
                $cleanText = strip_tags($row->parcel_info);
                $cleanText = preg_replace('/\s+/', ' ', $cleanText);
                $cleanText = trim($cleanText);
                if (!empty($cleanText)) {
                    $parts[] = $cleanText;
                }
            }

            return implode(', ', $parts);
        }

        /**
         * Helper method to format status
         */
        private function formatStatus($status)
        {
            $statusMap = [
                1 => 'Бүртгэгдсэн',
                2 => 'Хуваарилсан',
                3 => 'Хүргэгдсэн',
                4 => 'Цуцалсан',
                5 => 'Буцаасан',
                6 => 'Хүлээгдэж буй',
                10 => 'Хүлээн авсан',
            ];
            
            return $statusMap[$status] ?? $status;
        }

        public function getDriverItemsWeb(Request $request)
{
    $driver = $request->driver;
    
    // Get driver info
    $driverInfo = DB::table('users')->where('name', $driver)->first();
    
    if (!$driverInfo) {
        return response()->json(['data' => []]);
    }
    
    // Get driver items with item names
    $items = DB::table('driver_items')
        ->join('items', 'driver_items.item_id', '=', 'items.id')
        ->where('driver_items.driver_id', $driverInfo->id)
        ->select('driver_items.*', 'items.name as item_name')
        ->get();
    
    return response()->json(['data' => $items]);
}
// In your controller
public function getItemHistory(Request $request)
{
    $itemId = $request->item_id;
    $driver = $request->driver;

    // Get driver info
    $driverInfo = DB::table('users')->where('name', $driver)->first();
    
    if (!$driverInfo) {
        return response()->json(['data' => []]);
    }
    
    // Get item history
    $history = DB::table('histories')
        ->where('item_id', $itemId)
        ->where('driver_id', $driverInfo->id)
        ->orderBy('created_at', 'desc')
        ->get();
    dd($history);
    return response()->json(['data' => $history]);
}
public function ExcelExport(Request $request)
{
    if (isset($request->excel)) {
        // DEBUG: Log everything
        \Log::info('=== EXCEL EXPORT DEBUG ===');
        \Log::info('All request parameters:', $request->all());
        \Log::info('Selected IDs:', ['ids' => $request->get('ids')]);
        
        $ids = $request->get('ids', '');
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No records selected');
        }

        \Log::info('Rows selected count:', ['count' => count(explode(',', $ids))]);

        // Use the direct SQL approach to avoid any model filtering issues
        $dataExcel = $this->getDeliveryDataByIds($ids);
        
        \Log::info('Final Excel Data Count:', ['count' => count($dataExcel)]);

        $excel_data = [];
        if (is_array($dataExcel) && count($dataExcel) > 0) {
            foreach ($dataExcel as $key => $row) {
                $excel_data[$key]['track'] = $row->track ?? '';
                $excel_data[$key]['created_at'] = $row->created_at ?? '';
                $excel_data[$key]['delivered_at'] = $row->delivered_at ?? '';
                $excel_data[$key]['note'] = $row->note ?? '';

                // Type conversion
                if (($row->type ?? '') == '1') {
                    $excel_data[$key]['type'] = 'Энгийн';
                } else if (($row->type ?? '') == '2') {
                    $excel_data[$key]['type'] = 'Цагтай';
                } else if (($row->type ?? '') == '3') {
                    $excel_data[$key]['type'] = 'Яаралтай';
                } else if (($row->type ?? '') == '4') {
                    $excel_data[$key]['type'] = 'Онц яаралтай';
                } else {
                    $excel_data[$key]['type'] = '';
                }

                $excel_data[$key]['shop'] = $row->shop ?? '';
                $excel_data[$key]['order_code'] = $row->order_code ?? '';
                $excel_data[$key]['merchantName'] = $row->merchantName ?? '';

                // Merchant phones
                $merchantPhone1 = $row->merchantPhone1 ?? null;
                $merchantPhone2 = $row->merchantPhone2 ?? null;
                if ($merchantPhone1 && $merchantPhone2) {
                    $mergedPhones = $merchantPhone1 . ',' . $merchantPhone2;
                } else if (!$merchantPhone1 && $merchantPhone2) {
                    $mergedPhones = $merchantPhone2;
                } else if ($merchantPhone1 && !$merchantPhone2) {
                    $mergedPhones = $merchantPhone1;
                } else {
                    $mergedPhones = "";
                }
                $excel_data[$key]['merchantPhone'] = $mergedPhones;
                $excel_data[$key]['merchantAddress'] = $row->merchantAddress ?? '';

                // Good type and parcel info
                $goodtype = $row->goodtype ?? null;
                $parcel_info = $row->parcel_info ?? null;
                if ($goodtype && $parcel_info) {
                    $mergedInfo = $goodtype . ',' . $parcel_info;
                } else if (!$goodtype && $parcel_info) {
                    $mergedInfo = $parcel_info;
                } else if ($goodtype && !$parcel_info) {
                    $mergedInfo = $goodtype;
                } else {
                    $mergedInfo = "мэдээлэл алга";
                }
                $excel_data[$key]['goodtype'] = $mergedInfo;

                $excel_data[$key]['number'] = $row->number ?? '';
                $excel_data[$key]['receivername'] = $row->receivername ?? '';

                // Receiver phones
                $phone = $row->phone ?? null;
                $phone2 = $row->phone2 ?? null;
                if ($phone && $phone2) {
                    $mergedPhone = $phone . ',' . $phone2;
                } else if (!$phone && $phone2) {
                    $mergedPhone = $phone2;
                } else if ($phone && !$phone2) {
                    $mergedPhone = $phone;
                } else {
                    $mergedPhone = "";
                }
                $excel_data[$key]['phone'] = $mergedPhone;
                $excel_data[$key]['address'] = $row->address ?? '';
                $excel_data[$key]['comment'] = $row->comment ?? '';
                $excel_data[$key]['price'] = $row->price ?? '';
                
                // Verified status
                if (($row->verified ?? 0) == 1) {
                    $ver = 'Тийм';
                } else {
                    $ver = 'Үгүй';
                }
                $excel_data[$key]['verified'] = $ver;
                $excel_data[$key]['driver'] = $row->driver ?? '';
                
                // Delivery status
                $status = $row->status ?? 0;
                if ($status == 1) {
                    $excel_data[$key]['status'] = 'Бүртгэгдсэн';
                } elseif ($status == 2) {
                    $excel_data[$key]['status'] = 'Жолоочид хуваарилсан';
                } elseif ($status == 3) {
                    $excel_data[$key]['status'] = 'Хүргэгдсэн';
                } elseif ($status == 4) {
                    $excel_data[$key]['status'] = 'Цуцалсан';
                } elseif ($status == 5) {
                    $excel_data[$key]['status'] = 'Буцаасан';
                } elseif ($status == 6) {
                    $excel_data[$key]['status'] = 'Хүлээгдэж буй';
                } elseif ($status == 10) {
                    $excel_data[$key]['status'] = 'Хүлээн авсан';
                } else {
                    $excel_data[$key]['status'] = '';
                }
            }
        } else {
            \Log::warning('No data found for the selected IDs');
            return redirect()->back()->with('error', 'No data found for the selected records');
        }

        \Log::info('Excel data prepared successfully', ['record_count' => count($excel_data)]);
        
        $export = new DeliveryExport($excel_data);
        $filename = 'delivery_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download($export, $filename);
    } else {
        return redirect()->back()->with('error', 'Invalid request');
    }
}

private function getDeliveryDataByIds($ids)
{
    if (empty($ids)) {
        return [];
    }
    
    $query = "SELECT deliveries.*,
                     merchant.merchantName AS merchantName,
                     merchant.merchantPhone1 AS merchantPhone1,
                     merchant.merchantPhone2 AS merchantPhone2,
                     merchant.merchantAddress AS merchantAddress
              FROM deliveries
              LEFT JOIN merchant ON merchant.id = deliveries.merchant_id
              WHERE deliveries.id IN ($ids)
              ORDER BY deliveries.id DESC";

    \Log::info('Direct SQL Query:', ['query' => $query]);
    
    try {
        $results = DB::select(DB::raw($query));
        \Log::info('Query executed successfully', ['result_count' => count($results)]);
        return $results;
    } catch (\Exception $e) {
        \Log::error('Database query failed:', ['error' => $e->getMessage()]);
        return [];
    }
}
    public function changeEstimateData(Request $request)
    {
        if ($request->ajax()) {
            // Check if $request->ids is set and is a non-empty string
            if (is_string($request->ids) && ! empty($request->ids)) {
                // Convert the comma-separated string to an array
                $ids = [];
                $ids = explode(',', $request->ids);
                // $ids = explode(',', $request->ids);

                $updateData = [
                    'estimated' => 2,
                ];

                foreach ($ids as $id) {
                    // Trim each ID to remove potential whitespace
                    $id = trim($id);
                    // Perform your update logic here using $id and $updateData
                    Delivery::where('id', $id)->update($updateData);
                }

                return "Updated successfully";
            } else {
                return "Invalid data format for IDs";
            }
        }

        return "Invalid request";
    }

    public function PrintdeliveryData(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->print)) {
                $user_id        = Auth::user()->id;
                $role           = Auth::user()->role;
                $arr_ids        = explode(",", $request->post('ids'));
                $ids            = implode(",", array_filter($arr_ids));
                $print          = $request->get('print', 0);
                $excel          = $request->get('excel', 0);
                $status         = $request->get('status', 0);
                $district       = $request->get('district', 0);
                $region         = $request->get('region', 0);
                $phone          = $request->get('phone', 0);
                $address        = $request->get('address', 0);
                $note           = $request->get('note', 0);
                $tuluv          = $request->get('tuluv', 0);
                $start_date     = $request->get('start_date', 0);
                $merchant       = $request->get('merchant', 0);
                $type           = $request->get('type', 0);
                $late           = $request->get('late', 0);
                $customer       = $request->get('customer', 0);
                $status_100     = $request->get('status_100', 0);
                $end_date       = $request->get('end_date', 0);
                $driverselected = $request->get('driver', 0);
                $except_status  = $request->get('except_status', 0);
                $except_stat    = $request->get('except_stat', 0);
                $status_10      = $request->get('status_10', 0);
                $status_1       = $request->get('status_1', 0);
                $status_6       = $request->get('status_6', 0);
                $status_2       = $request->get('status_2', 0);
                $status_3       = $request->get('status_3', 0);
                $status_4       = $request->get('status_4', 0);
                $status_5       = $request->get('status_5', 0);
                $not_3          = $request->get('not_3', 0);
                $not_4          = $request->get('not_4', 0);
                $not_2          = $request->get('not_2', 0);
                $not_6          = $request->get('not_6', 0);
                $not_5          = $request->get('not_5', 0);
                $not_1          = $request->get('not_1', 0);
                $not_100        = $request->get('not_100', 0);
                $offset         = $request->get('start', 0);
                $limit          = $request->get('length', 0);

                $Params = [
                    'ids'            => $ids,
                    'user_id'        => $user_id,
                    'role'           => $role,

                    'ids'            => $ids,
                    'status'         => $status,
                    'status_10'      => $status_10,
                    'status_100'     => $status_100,
                    'status_1'       => $status_1,
                    'status_6'       => $status_6,
                    'status_2'       => $status_2,
                    'status_5'       => $status_5,
                    'status_4'       => $status_4,
                    'status_3'       => $status_3,
                    'not_5'          => $not_5,
                    'not_4'          => $not_4,
                    'not_3'          => $not_3,
                    'not_1'          => $not_1,
                    'not_2'          => $not_2,
                    'not_6'          => $not_6,
                    'not_100'        => $not_100,
                    'tuluv'          => $tuluv,
                    'start_date'     => $start_date,
                    'end_date'       => $end_date,
                    'region'         => $region,
                    'district'       => $district,
                    'note'           => $note,
                    'late'           => $late,
                    'customer'       => $customer,
                    'phone'          => $phone,
                    'address'        => $address,
                    'merchant_id'    => $merchant,
                    'type'           => $type,
                    'driverselected' => $driverselected,
                ];
                $i          = 0;
                $print_data = [];
                $dataExcel  = Delivery::GetExcelData($Params);

                $table = '<div class="text-center" style="font-weight:bold;"><h1>' . 'ИЛГЭЭМЖ ХҮЛЭЭЛЦЭХ АКТ №' . '</h1></div><br><br>';
                $table .= '<div class="row"><div class="col-md-6"><div>' . 'LOGO Байгууллагын нэр:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '<div>' . 'Регистрийн дугаар: .........' . '</div>';
                $table .= '<div>' . 'Утас:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '<div>' . 'Хаяг:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '<div>' . 'Огноо:' . '</div>';
                $table .= '<div>' . date('Y-m-d H:i:s') . '</div></div>';
                $table .= '<div class="col-md-6"><div>' . 'Хүлээн авагчийн нэр:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '<div>' . 'Регистрийн дугаар: .........' . '</div>';
                $table .= '<div>' . 'Утас:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '</div>';
                $table .= '<table class="table table-striped  table-bordered" style="border-width: 1px;border-style: solid;border-color: black;">
                <thead>
                    <tr>
                    <th class="text-center whitespace-nowrap">#</th>
                    <th class="text-center whitespace-nowrap">Track ID</th>
                    <th class="whitespace-nowrap">Мерчант нэр</th>
                    <th class="whitespace-nowrap">Мерчант хаяг</th>
                    <th class="text-center whitespace-nowrap">Z-код</th>
                    <th class="whitespace-nowrap">Илгээмж</th>
                    <th class="whitespace-nowrap">Тоо ширхэг</th>
                    <th class="text-center whitespace-nowrap">Хүлээн авагч</th>
                    <th class="text-center whitespace-nowrap">Хаягийн мэдээлэл</th>
                    <th class="text-center whitespace-nowrap">Жолооч</th>
                    <th class="text-center whitespace-nowrap">Гарын үсэг</th>
                    </tr>
                </thead>
                <tbody>';
                foreach ($dataExcel as $key => $row) {
                    $table .= "<tr>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>" . ++$i . "</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->track}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->merchantName} {$row->merchantPhone1},{$row->merchantPhone2}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->merchantAddress}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->order_code},{$row->shop}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->parcel_info}, {$row->goodtype}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->number}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->receivername},{$row->phone},{$row->phone2}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black; overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;'>{$row->district} , {$row->address}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->driver}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'><img src='" . asset('storage/') . "/{$row->sign_image}' width='100'/></td>
    </tr>";
                }

                $table .= '</tbody></table><br>' . '' . '</div><br>';
                $table .= '<div style="margin-left:350px;">' . 'Хүлээлгэж өгсөн эд хариуцагч................................/................./' . '</div><br>';
                $table .= '<div style="margin-left:350px;">' . 'Хүлээн авагч................................/................./' . '</div><br>';
                $table .= '<div style="margin-left:350px;">' . 'Шалгасан нягтлан бодогч................................/................./' . '</div><br><br>';
                $table .= '<div style="margin-left:350px;">' . 'Тамга/ Тэмдэг' . '</div><br>';

                echo $table;
            }
        }
    }

     public function PrintdeliveryInvoice(Request $request)
{
    if ($request->ajax()) {
        if (isset($request->print)) {
            $user_id = Auth::user()->id;
            $role    = Auth::user()->role;
            $arr_ids = explode(",", $request->post('ids'));
            $ids     = implode(",", array_filter($arr_ids));
            $print   = $request->get('print', 0);
            $excel   = $request->get('excel', 0);
            $status  = $request->get('status', 0);
            $district = $request->get('district', 0);
            $region  = $request->get('region', 0);
            $phone   = $request->get('phone', 0);
            $address = $request->get('address', 0);
            $note    = $request->get('note', 0);
            $tuluv   = $request->get('tuluv', 0);
            $start_date = $request->get('start_date', 0);
            $merchant = $request->get('merchant', 0);
            $type    = $request->get('type', 0);
            $late    = $request->get('late', 0);
            $customer = $request->get('customer', 0);
            $status_100 = $request->get('status_100', 0);
            $end_date = $request->get('end_date', 0);
            $driverselected = $request->get('driver', 0);
            $except_status = $request->get('except_status', 0);
            $except_stat = $request->get('except_stat', 0);
            $status_10 = $request->get('status_10', 0);
            $status_1 = $request->get('status_1', 0);
            $status_6 = $request->get('status_6', 0);
            $status_2 = $request->get('status_2', 0);
            $status_3 = $request->get('status_3', 0);
            $status_4 = $request->get('status_4', 0);
            $status_5 = $request->get('status_5', 0);
            $not_3   = $request->get('not_3', 0);
            $not_4   = $request->get('not_4', 0);
            $not_2   = $request->get('not_2', 0);
            $not_6   = $request->get('not_6', 0);
            $not_5   = $request->get('not_5', 0);
            $not_1   = $request->get('not_1', 0);
            $not_100 = $request->get('not_100', 0);
            $offset  = $request->get('start', 0);
            $limit   = $request->get('length', 0);

            // Get phone numbers from phones table
            $phones = DB::table('phones')->where('userid', $user_id)->get();
            $phoneNumbers = [];
            foreach ($phones as $phone) {
                $phoneNumbers[] = $phone->phone;
            }
            $phoneNumbersString = implode(', ', $phoneNumbers);

            // Get addresses
            $add = DB::table('addresses')->where('userid', $user_id)->get();
            $addressesMore = [];

            if (!$add->isEmpty()) {
                foreach ($add as $addressUser) {
                    $addressesMore[] = $addressUser->address;
                }
                $addressesString = implode(', ', $addressesMore);
            } else {
                $addressesString = "";
            }

            $Params = [
                'ids'            => $ids,
                'user_id'        => $user_id,
                'role'           => $role,
                'status'         => $status,
                'status_10'      => $status_10,
                'status_100'     => $status_100,
                'status_1'       => $status_1,
                'status_6'       => $status_6,
                'status_2'       => $status_2,
                'status_5'       => $status_5,
                'status_4'       => $status_4,
                'status_3'       => $status_3,
                'not_5'          => $not_5,
                'not_4'          => $not_4,
                'not_3'          => $not_3,
                'not_1'          => $not_1,
                'not_2'          => $not_2,
                'not_6'          => $not_6,
                'not_100'        => $not_100,
                'tuluv'          => $tuluv,
                'start_date'     => $start_date,
                'end_date'       => $end_date,
                'region'         => $region,
                'district'       => $district,
                'note'           => $note,
                'late'           => $late,
                'customer'       => $customer,
                'phone'          => $phone,
                'address'        => $address,
                'merchant_id'    => $merchant,
                'type'           => $type,
                'driverselected' => $driverselected,
            ];

            // Get the data first
            $dataExcel = Delivery::GetExcelData($Params);

            // Now generate the table with landscape and duplicates
            $table = '<div style="width: 100%; page-break-after: always;">';
            $table .= '<style>
                @media print {
                    @page {
                        size: portrait;
                        margin: 10mm;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                }
            </style>';

            // Generate the content
            $zarlagaContent = $this->generateInvoiceContent($dataExcel, $addressesString, $phoneNumbersString, $user_id);

            // Display two copies side by side
            $table .= '<div style="display: flex; justify-content: space-between; gap: 20px;">';
            $table .= '<div style="width: 100%;">' . $zarlagaContent . '</div>';
            $table .= '</div>';
            $table .= '</div>';

            echo $table;
        }
    }
}

private function generateInvoiceContent($dataExcel, $addressesString, $phoneNumbersString, $user_id)
{
    $content = '';
    
    // Title Section
    $content .= '<div style="text-align: center; font-weight: bold; width: 100%; margin-top: 20px;">';
    $content .= '<h3>Нэхэмжлэхийн №</h3>';
    $content .= '</div><br><br>';

    // Two Column Layout - Company Info and Receiver Info side by side
    $content .= '<div style="display: flex; justify-content: space-between; gap: 15px; margin-bottom: 15px;">';
    
        // First Column - Company Info (Left side)
        $content .= '<div style="width: 100%; box-sizing: border-box;">';
        $content .= '<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">';
        // Add user image inline with name
        if (!empty(Auth::user()->image)) {
            $content .= '<img src="' . asset('storage/' . Auth::user()->image) . '" style="width: 40px; height: 40px; border-radius: 5px; object-fit: cover;">';
        }
        $content .= '<h5 style="text-decoration: underline; text-transform: uppercase; margin: 0; font-size: 14px;">' . Auth::user()->name . '</h5>';
        $content .= '</div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Хаяг: ' . $addressesString . '</div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Утас, Факс: <i style="text-decoration: underline;">' . $phoneNumbersString . '</i></div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Э-шуудан: <i style="text-decoration: underline;">' . Auth::user()->email . '</i></div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Банкны нэр: <i style="text-decoration: underline;">' . Auth::user()->bank . '</i></div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Банкны дансны дугаар: <i style="text-decoration: underline;">' . Auth::user()->account . '</i></div>';
        $content .= '</div>';

        // Second Column - Receiver Info (Right side)
        if ($dataExcel && count($dataExcel) > 0) {
            $firstItem = $dataExcel[0];
            
            // Prepare receiver phone numbers
            $receiverPhones = [];
            if (!empty($firstItem->phone)) {
                $receiverPhones[] = $firstItem->phone;
            }
            if (!empty($firstItem->phone2)) {
                $receiverPhones[] = $firstItem->phone2;
            }
            $receiverPhoneString = implode(', ', $receiverPhones);

            $content .= '<div style="width: 48%; box-sizing: border-box;">';
            $content .= '<div style="padding: 3px; font-size: 12px;"><b>Хүлээн авагч: </b> ' . $firstItem->receivername . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Байгууллагын нэр: ' . $firstItem->receivername . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Хаяг:' . $firstItem->address . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Утас: ' . $receiverPhoneString . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Гэрээний №: ........................</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Баримтын огноо: ' . date('Y-m-d') . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Төлбөр хийх хугацаа:</div>';
            $content .= '</div>';
        } else {
            // Empty receiver column if no data
            $content .= '<div style="width: 100%; box-sizing: border-box;">';
            $content .= '<div style="padding: 3px; font-size: 12px; color: #999;">Хүлээн авагчийн мэдээлэл байхгүй</div>';
            $content .= '</div>';
        }

    $content .= '</div>'; // End of two-column layout

    // Table
        // Table
    $i = 0;
    $totalSum = 0;
    
    $content .= '<table class="table table-striped table-bordered" style="border-width: 1px; border-style: solid; border-color: black; margin-top: 20px; width: 100%; font-size: 10px;">';
    $content .= '<thead>
        <tr>
            <th class="text-center whitespace-nowrap">№</th>
            <th class="text-center whitespace-nowrap">Барааны нэр</th>
            <th class="whitespace-nowrap">Тоо ширхэг</th>
            <th class="text-center whitespace-nowrap">Нэгжийн үнэ</th>
            <th class="text-center whitespace-nowrap">Нийт үнэ</th>
        </tr>
    </thead>
    <tbody>';
    
    if ($dataExcel) {
        foreach ($dataExcel as $key => $row) {
            // Handle price safely - remove commas for numeric check
            $priceValue = $row->price;
            
            // Remove commas and convert to numeric
            $cleanPrice = str_replace(',', '', $priceValue);
            $numericPrice = floatval($cleanPrice);
            $isValidPrice = is_numeric($cleanPrice) && $numericPrice > 0;
            
            if ($isValidPrice) {
                $cost = number_format($numericPrice, 0, ',', ',');
                $totalSum += $numericPrice;
            } else {
                $cost = " ";
            }
            
            $content .= "<tr class='text-center'>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>" . ++$i . "</td>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->parcel_info}</td>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->number}</td>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>{$cost}</td>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>{$cost}</td>
            </tr>";
        }
    }

    $content .= '</tbody></table><br>';

    // Add Total Sum row below the table
    $content .= '<div style="display: flex; justify-content: flex-end; margin-top: 10px;">';
    $content .= '<div style="font-weight: bold; font-size: 12px; padding: 8px; border: 1px solid black; background-color: #f8f9fa;">';
    $content .= 'Нийт дүн: ' . number_format($totalSum, 0, ',', ',') . '₮';
    $content .= '</div>';
    $content .= '</div><br>';

    // Signatures with stamp
    $content .= '<div style="display: flex; align-items: flex-start; margin-top: 20px;">';
    if (!empty($user->stamp)) {
        // Use Storage::url() for reliable path generation, with fallback
        try {
            if (Storage::disk('public')->exists($user->stamp)) {
                $stampPath = Storage::url($user->stamp);
            } else {
                $stampPath = asset('storage/' . $user->stamp);
            }
        } catch (\Exception $e) {
            $stampPath = asset('storage/' . $user->stamp);
        }
        $content .= '<img src="' . $stampPath . '" style="width: 60px; height: 60px; margin-right: 15px; object-fit: contain;" onerror="this.style.display=\'none\';">';
    }
    $content .= '<div style="font-size: 11px;">';
    $content .= '<div>Хүлээлгэн өгсөн................................/................./</div><br>';
    $content .= '<div>Хүлээн авсан................................/................./</div>';
    $content .= '</div>';
    $content .= '</div>';

    // Footer note
    $content .= '<div style="margin-top: 15px; font-size: 10px; font-family: \'Arial\', sans-serif; font-weight: bold; font-style: italic;">';
    $content .= 'Та төлбөр шилжүүлэхдээ гүйлгээний утга дээр<br> захиалга өгсөн утасны дугаараа заавал бичнэ үү.<br>Баярлалаа';
    $content .= '</div>';

    return $content;
}
    //zarlagiin barimt hewleh
   public function PrintdeliveryZarlaga(Request $request)
{
    if ($request->ajax()) {
        if (isset($request->print)) {
            $user_id = Auth::user()->id;
            $role    = Auth::user()->role;
            $arr_ids = explode(",", $request->post('ids'));
            $ids     = implode(",", array_filter($arr_ids));
            $print   = $request->get('print', 0);
            $excel   = $request->get('excel', 0);
            $status  = $request->get('status', 0);
            $district = $request->get('district', 0);
            $region  = $request->get('region', 0);
            $phone   = $request->get('phone', 0);
            $address = $request->get('address', 0);
            $note    = $request->get('note', 0);
            $tuluv   = $request->get('tuluv', 0);
            $start_date = $request->get('start_date', 0);
            $merchant = $request->get('merchant', 0);
            $type    = $request->get('type', 0);
            $late    = $request->get('late', 0);
            $customer = $request->get('customer', 0);
            $status_100 = $request->get('status_100', 0);
            $end_date = $request->get('end_date', 0);
            $driverselected = $request->get('driver', 0);
            $except_status = $request->get('except_status', 0);
            $except_stat = $request->get('except_stat', 0);
            $status_10 = $request->get('status_10', 0);
            $status_1 = $request->get('status_1', 0);
            $status_6 = $request->get('status_6', 0);
            $status_2 = $request->get('status_2', 0);
            $status_3 = $request->get('status_3', 0);
            $status_4 = $request->get('status_4', 0);
            $status_5 = $request->get('status_5', 0);
            $not_3   = $request->get('not_3', 0);
            $not_4   = $request->get('not_4', 0);
            $not_2   = $request->get('not_2', 0);
            $not_6   = $request->get('not_6', 0);
            $not_5   = $request->get('not_5', 0);
            $not_1   = $request->get('not_1', 0);
            $not_100 = $request->get('not_100', 0);
            $offset  = $request->get('start', 0);
            $limit   = $request->get('length', 0);

            // Get phone numbers from phones table
            $phones = DB::table('phones')->where('userid', $user_id)->get();
            $phoneNumbers = [];
            foreach ($phones as $phone) {
                $phoneNumbers[] = $phone->phone;
            }
            $phoneNumbersString = implode(', ', $phoneNumbers);

            // Get addresses
            $add = DB::table('addresses')->where('userid', $user_id)->get();
            $addressesMore = [];

            if (!$add->isEmpty()) {
                foreach ($add as $addressUser) {
                    $addressesMore[] = $addressUser->address;
                }
                $addressesString = implode(', ', $addressesMore);
            } else {
                $addressesString = "";
            }

            $Params = [
                'ids'            => $ids,
                'user_id'        => $user_id,
                'role'           => $role,
                'status'         => $status,
                'status_10'      => $status_10,
                'status_100'     => $status_100,
                'status_1'       => $status_1,
                'status_6'       => $status_6,
                'status_2'       => $status_2,
                'status_5'       => $status_5,
                'status_4'       => $status_4,
                'status_3'       => $status_3,
                'not_5'          => $not_5,
                'not_4'          => $not_4,
                'not_3'          => $not_3,
                'not_1'          => $not_1,
                'not_2'          => $not_2,
                'not_6'          => $not_6,
                'not_100'        => $not_100,
                'tuluv'          => $tuluv,
                'start_date'     => $start_date,
                'end_date'       => $end_date,
                'region'         => $region,
                'district'       => $district,
                'note'           => $note,
                'late'           => $late,
                'customer'       => $customer,
                'phone'          => $phone,
                'address'        => $address,
                'merchant_id'    => $merchant,
                'type'           => $type,
                'driverselected' => $driverselected,
            ];

            // Get the data first
            $dataExcel = Delivery::GetExcelData($Params);

            // Now generate the table with landscape and duplicates
            $table = '<div style="width: 100%; page-break-after: always;">';
            $table .= '<style>
                @media print {
                    @page {
                        size: landscape;
                        margin: 10mm;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                }
            </style>';

            // Generate the content
            $zarlagaContent = $this->generateZarlagaContent($dataExcel, $addressesString, $phoneNumbersString, $user_id);

            // Display two copies side by side
            $table .= '<div style="display: flex; justify-content: space-between; gap: 20px;">';
            $table .= '<div style="width: 48%;">' . $zarlagaContent . '</div>';
            $table .= '<div style="width: 48%;">' . $zarlagaContent . '</div>';
            $table .= '</div>';
            $table .= '</div>';

            echo $table;
        }
    }
}

private function generateZarlagaContent($dataExcel, $addressesString, $phoneNumbersString, $user_id)
{
    $content = '';
    
    // Fetch user fresh from database to ensure stamp is loaded
    $user = \App\Models\User::find($user_id);
    
    // Title Section
    $content .= '<div style="text-align: center; font-weight: bold; width: 100%; margin-top: 20px;">';
    $content .= '<h3>Зарлагын баримт №</h3>';
    $content .= '</div><br><br>';

    // Two Column Layout - Company Info and Receiver Info side by side
    $content .= '<div style="display: flex; justify-content: space-between; gap: 15px; margin-bottom: 15px;">';
    
        // First Column - Company Info (Left side)
        $content .= '<div style="width: 48%; box-sizing: border-box;">';
        $content .= '<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">';
        // Add user image inline with name
        if (!empty($user->image)) {
            $content .= '<img src="' . asset('storage/' . $user->image) . '" style="width: 40px; height: 40px; border-radius: 5px; object-fit: cover;">';
        }
        $content .= '<h5 style="text-decoration: underline; text-transform: uppercase; margin: 0; font-size: 14px;">' . $user->name . '</h5>';
        $content .= '</div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Хаяг: ' . $addressesString . '</div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Утас, Факс: <i style="text-decoration: underline;">' . $phoneNumbersString . '</i></div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Э-шуудан: <i style="text-decoration: underline;">' . $user->email . '</i></div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Банкны нэр: <i style="text-decoration: underline;">' . $user->bank . '</i></div>';
        $content .= '<div style="padding: 3px; font-size: 12px;">Банкны дансны дугаар: <i style="text-decoration: underline;">' . $user->account . '</i></div>';
        $content .= '</div>';

        // Second Column - Receiver Info (Right side)
        if ($dataExcel && count($dataExcel) > 0) {
            $firstItem = $dataExcel[0];
            
            // Prepare receiver phone numbers
            $receiverPhones = [];
            if (!empty($firstItem->phone)) {
                $receiverPhones[] = $firstItem->phone;
            }
            if (!empty($firstItem->phone2)) {
                $receiverPhones[] = $firstItem->phone2;
            }
            $receiverPhoneString = implode(', ', $receiverPhones);

            $content .= '<div style="width: 48%; box-sizing: border-box;">';
            $content .= '<div style="padding: 3px; font-size: 12px;"><b>Хүлээн авагч: </b> ' . $firstItem->receivername . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Байгууллагын нэр: ' . $firstItem->receivername . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Хаяг:' . $firstItem->address . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Утас: ' . $receiverPhoneString . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Гэрээний №: ........................</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Баримтын огноо: ' . date('Y-m-d') . '</div>';
            $content .= '<div style="padding: 3px; font-size: 12px;">Төлбөр хийх хугацаа:</div>';
            $content .= '</div>';
        } else {
            // Empty receiver column if no data
            $content .= '<div style="width: 48%; box-sizing: border-box;">';
            $content .= '<div style="padding: 3px; font-size: 12px; color: #999;">Хүлээн авагчийн мэдээлэл байхгүй</div>';
            $content .= '</div>';
        }

    $content .= '</div>'; // End of two-column layout

    // Table
        // Table
    $i = 0;
    $totalSum = 0;
    
    $content .= '<table class="table table-striped table-bordered" style="border-width: 1px; border-style: solid; border-color: black; margin-top: 20px; width: 100%; font-size: 10px;">';
    $content .= '<thead>
        <tr>
            <th class="text-center whitespace-nowrap">№</th>
            <th class="text-center whitespace-nowrap">Барааны нэр</th>
            <th class="whitespace-nowrap">Тоо ширхэг</th>
            <th class="text-center whitespace-nowrap">Нэгжийн үнэ</th>
            <th class="text-center whitespace-nowrap">Нийт үнэ</th>
        </tr>
    </thead>
    <tbody>';
    
    if ($dataExcel) {
        foreach ($dataExcel as $key => $row) {
            // Handle price safely - remove commas for numeric check
            $priceValue = $row->price;
            
            // Remove commas and convert to numeric
            $cleanPrice = str_replace(',', '', $priceValue);
            $numericPrice = floatval($cleanPrice);
            $isValidPrice = is_numeric($cleanPrice) && $numericPrice > 0;
            
            if ($isValidPrice) {
                $cost = number_format($numericPrice, 0, ',', ',');
                $totalSum += $numericPrice;
            } else {
                $cost = " ";
            }
            
            $content .= "<tr class='text-center'>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>" . ++$i . "</td>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->parcel_info}</td>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->number}</td>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>{$cost}</td>
                <td style='border-width: 1px; border-style: solid; border-color: black;'>{$cost}</td>
            </tr>";
        }
    }

    $content .= '</tbody></table><br>';

    // Add Total Sum row below the table
    $content .= '<div style="display: flex; justify-content: flex-end; margin-top: 10px;">';
    $content .= '<div style="font-weight: bold; font-size: 12px; padding: 8px; border: 1px solid black; background-color: #f8f9fa;">';
    $content .= 'Нийт дүн: ' . number_format($totalSum, 0, ',', ',') . '₮';
    $content .= '</div>';
    $content .= '</div><br>';

    // Signatures with stamp
    $content .= '<div style="display: flex; align-items: flex-start; margin-top: 20px;">';
    if (!empty($user->stamp)) {
        // Use Storage::url() for reliable path generation, with fallback
        try {
            if (Storage::disk('public')->exists($user->stamp)) {
                $stampPath = Storage::url($user->stamp);
            } else {
                $stampPath = asset('storage/' . $user->stamp);
            }
        } catch (\Exception $e) {
            $stampPath = asset('storage/' . $user->stamp);
        }
        $content .= '<img src="' . $stampPath . '" style="width: 60px; height: 60px; margin-right: 15px; object-fit: contain;" onerror="this.style.display=\'none\';">';
    }
    $content .= '<div style="font-size: 11px;">';
    $content .= '<div>Хүлээлгэн өгсөн................................/................./</div><br>';
    $content .= '<div>Хүлээн авсан................................/................./</div>';
    $content .= '</div>';
    $content .= '</div>';

    // Footer note
    $content .= '<div style="margin-top: 15px; font-size: 10px; font-family: \'Arial\', sans-serif; font-weight: bold; font-style: italic;">';
    $content .= 'Та төлбөр шилжүүлэхдээ гүйлгээний утга дээр<br> захиалга өгсөн утасны дугаараа заавал бичнэ үү.<br>Баярлалаа';
    $content .= '</div>';

    return $content;
}


public function getDriverItems($driverId)

    {

        try {

            $driverItems = DB::table('driver_items')

                ->join('items', 'driver_items.item_id', '=', 'items.id')

                ->where('driver_items.driver_id', $driverId)

                ->select(

                    'driver_items.id',

                    'driver_items.driver_id',

                    'driver_items.item_id',

                    'driver_items.quantity as selected_quantity', // driver's selected quantity

                    'items.name as item_name',

                )

                ->get();



            return response()->json([

                'success' => true,

                'data' => $driverItems,

                'message' => 'Driver items retrieved successfully'

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' => 'Failed to retrieve driver items: ' . $e->getMessage()

            ], 500);

        }

    }
public function report(Request $request)
{
    $data = collect();

    if ($request->start_date && $request->end_date && ($request->driver_id || $request->customer_id)) {
        $query = DB::table('deliveries')
           ->select(
                DB::raw('DATE(deliveries.created_at) as date'),
                'users.name as driver_name',
                DB::raw('SUM(CASE WHEN deliveries.status = 3 THEN 1 ELSE 0 END) as delivered_count'),
                DB::raw('SUM(CASE WHEN deliveries.status = 4 THEN 1 ELSE 0 END) as declined_count'),
                DB::raw('SUM(CASE WHEN deliveries.status = 3 THEN deliveries.price ELSE 0 END) as total_price'),
                DB::raw('SUM(CASE WHEN deliveries.status = 3 THEN deliveries.number ELSE 0 END) as total_number'),
                DB::raw('COUNT(CASE WHEN deliveries.status = 3 THEN 1 END) as total_count')
            )
            ->leftJoin('users', 'deliveries.driver', '=', 'users.id');

        if ($request->driver_id) {
            $query->where('driver', $request->driver_id);
        } elseif ($request->customer_id) {
            $query->where('shop', $request->customer_id);
        }

        $data = $query->whereBetween('deliveries.created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59'])
            ->groupBy('date', 'users.name')
            ->orderBy('date')
            ->get();
    }

    return view('admin.delivery.report', compact('data'));
}

public function reportExport(Request $request)
{
    // Get filter parameters
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');
    $driver_id = $request->input('driver_id');
    $customer_id = $request->input('customer_id');
    $report_type = $request->input('report_type', 1); // Default to 1 (Summary)
    
    // If user is customer, set customer_id to their name
    if (auth()->user()->role == 'customer') {
        $customer_id = auth()->user()->name;
    }
    
    // Get customers and drivers for the filter dropdowns (for HTML view)
    $customers = DB::table('users')
        ->where('role', 'customer')
        ->where('active', 1)
        ->orderBy('id', 'DESC')
        ->get();
        
    $drivers = DB::table('users')
        ->where('role', 'driver')
        ->where('active', 1)
        ->orderBy('id', 'DESC')
        ->get();
    
    // Check report type
    if ($report_type == 2) { // Detailed report
        // Build query for detailed delivery data
        $query = DB::table('deliveries')
            ->select(
                'deliveries.*',
                DB::raw("DATE_FORMAT(COALESCE(deliveries.delivered_at, deliveries.created_at), '%Y-%m-%d') as date_only"),
                DB::raw("DATE_FORMAT(COALESCE(deliveries.delivered_at, deliveries.created_at), '%H:%i:%s') as time_only")
            )
            ->whereIn('status', [1, 2, 3, 4]); // Only include status 1, 2, 3, 4
        
        // Apply date filter - use delivered_at, fallback to created_at if NULL
        if ($start_date && $end_date) {
            $query->where(function($q) use ($start_date, $end_date) {
                $q->whereBetween('delivered_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                  ->orWhere(function($subQ) use ($start_date, $end_date) {
                      $subQ->whereNull('delivered_at')
                           ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
                  });
            });
        }
        
        // Apply driver filter
        if ($driver_id) {
            $query->where('driver', $driver_id);
        }
        
        // Apply customer filter
        if ($customer_id) {
            $query->where('shop', $customer_id);
        }
        
        // Order by date - use delivered_at, fallback to created_at
        $query->orderBy(DB::raw('COALESCE(delivered_at, created_at)'), 'DESC');
        
        // Get the detailed data
        $detailed_data = $query->get();
        
        // Group data by date for display
        $grouped_data = [];
        foreach ($detailed_data as $item) {
            $date = $item->date_only;
            if (!isset($grouped_data[$date])) {
                $grouped_data[$date] = [
                    'date' => $date,
                    'deliveries' => [],
                    'delivered_count' => 0,
                    'declined_count' => 0,
                    'total_count' => 0,
                    'total_price' => 0,
                    'total_number' => 0,
                ];
            }
            
            $grouped_data[$date]['deliveries'][] = $item;
            $grouped_data[$date]['total_count']++;
            
            if ($item->status == 3) {
                $grouped_data[$date]['delivered_count']++;
                $grouped_data[$date]['total_price'] += $item->price ?? 0;
                $grouped_data[$date]['total_number'] += $item->number ?? 0;
            } elseif ($item->status == 4) {
                $grouped_data[$date]['declined_count']++;
            }
        }
        
        // Convert to array for easy use in view
        $data = array_values($grouped_data);
        
        // Calculate totals
        $total_delivered = 0;
        $total_declined = 0;
        $total_all = 0;
        $total_price = 0;
        $total_number = 0;
        
        foreach ($data as $row) {
            $total_delivered += $row['delivered_count'];
            $total_declined += $row['declined_count'];
            $total_all += $row['total_count'];
            $total_price += $row['total_price'];
            $total_number += $row['total_number'];
        }
        
        // For detailed view, we need different data structure
        $detailed_view_data = [
            'data' => $data,
            'detailed_data' => $detailed_data,
            'report_type' => $report_type,
            'total_delivered' => $total_delivered,
            'total_declined' => $total_declined,
            'total_all' => $total_all,
            'total_price' => $total_price,
            'total_number' => $total_number,
            'customers' => $customers,
            'drivers' => $drivers,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'driver_id' => $driver_id,
            'customer_id' => $customer_id,
        ];
        
        // Check if it's an Excel export request for detailed report
        if ($request->has('export')) {
            $totals = [
                'total_delivered' => $total_delivered,
                'total_declined' => $total_declined,
                'total_all' => $total_all,
                'total_price' => $total_price,
                'total_number' => $total_number,
            ];
            
            $filters = [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'driver_id' => $request->driver_id,
                'customer_id' => auth()->user()->role == 'customer' ? auth()->user()->name : $request->customer_id,
                'report_type' => $report_type,
            ];
            
            $filename = 'delivery_detailed_report_' . date('Y-m-d_His') . '.xlsx';
            
            return Excel::download(new DeliveryDetailedReportExport($detailed_data, $totals, $filters), $filename);
        }
        
        return view('admin.delivery.report', $detailed_view_data);
        
    } else { // Summary report (default)
        // Build query for delivery data - only status 1, 2, 3, 4
        $query = DB::table('deliveries')
            ->select(
                DB::raw('DATE(COALESCE(delivered_at, created_at)) as date'),
                DB::raw('SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as delivered_count'),
                DB::raw('SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as declined_count'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN status = 3 THEN price ELSE 0 END) as total_price'), // Price only for status 3
                DB::raw('SUM(CASE WHEN status = 3 THEN number ELSE 0 END) as total_number') // Number only for status 3
            )
            ->whereIn('status', [1, 2, 3, 4]) // Only include status 1, 2, 3, 4
            ->groupBy(DB::raw('DATE(COALESCE(delivered_at, created_at))'))
            ->orderBy('date', 'DESC');
        
        // Apply date filter - use delivered_at, fallback to created_at if NULL
        if ($start_date && $end_date) {
            $query->where(function($q) use ($start_date, $end_date) {
                $q->whereBetween('delivered_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                  ->orWhere(function($subQ) use ($start_date, $end_date) {
                      $subQ->whereNull('delivered_at')
                           ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
                  });
            });
        }
        
        // Apply driver filter
        if ($driver_id) {
            $query->where('driver', $driver_id);
        }
        
        // Apply customer filter
        if ($customer_id) {
            $query->where('shop', $customer_id);
        }
        
        // Get the data
        $data = $query->get();
        
        // Calculate totals
        $total_delivered = 0;
        $total_declined = 0;
        $total_all = 0;
        $total_price = 0;
        $total_number = 0;
        
        foreach ($data as $row) {
            $total_delivered += $row->delivered_count ?? 0;
            $total_declined += $row->declined_count ?? 0;
            $total_all += $row->total_count ?? 0;
            $total_price += $row->total_price ?? 0;
            $total_number += $row->total_number ?? 0;
        }
        
        // Check if it's an Excel export request
        if ($request->has('export')) {
            $totals = [
                'total_delivered' => $total_delivered,
                'total_declined' => $total_declined,
                'total_all' => $total_all,
                'total_price' => $total_price,
                'total_number' => $total_number,
            ];
            
            $filters = [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'driver_id' => $request->driver_id,
                'customer_id' => auth()->user()->role == 'customer' ? auth()->user()->name : $request->customer_id,
                'report_type' => $report_type,
            ];
            
            $filename = 'delivery_summary_report_' . date('Y-m-d_His') . '.xlsx';
            
            return Excel::download(new DeliveryReportExport($data, $totals, $filters), $filename);
        }
        
        // Add empty deliveries array for summary report to avoid blade errors
        $data = $data->map(function($item) {
            $item->deliveries = collect(); // Add empty deliveries collection
            return $item;
        });
        
        // Your existing return for HTML view
        return view('admin.delivery.report', compact(
            'data',
            'total_delivered',
            'total_declined',
            'total_all',
            'total_price',
            'total_number',
            'customers',
            'drivers',
            'start_date',
            'end_date',
            'driver_id',
            'customer_id',
            'report_type'
        ));
    }
}
}
