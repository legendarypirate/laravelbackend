<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Merchant;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Models\DeliveryDownload;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['getDashboardData']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
  public function index(Request $request)
{
    // Get date range from request or set default to last 90 days
    $startDate = $request->input('start_date', Carbon::today()->subDays(90)->toDateString());
    $endDate = $request->input('end_date', Carbon::today()->toDateString());
    
    // Get merchant filter (for admin users)
    $merchantFilter = $request->input('merchant_id', null);
    
    // Convert to Carbon instances
    $start = Carbon::parse($startDate)->startOfDay();
    $end = Carbon::parse($endDate)->endOfDay();

    // Get all merchants/customers for dropdown (only for admin)
    $merchants = collect([]);
    if(Auth::user()->role != 'customer'){
        $merchants = User::where('role', 'customer')
            ->orderBy('name', 'asc')
            ->get();
    }

    // Debug info
    \Log::info('Dashboard loaded', [
        'user' => Auth::user()->name,
        'role' => Auth::user()->role,
        'date_range' => $startDate . ' to ' . $endDate,
        'merchant_filter' => $merchantFilter
    ]);

    // Chart data - Show all deliveries grouped by date
    if(Auth::user()->role == 'customer'){
        $record = Delivery::where('shop', Auth::user()->name)
            ->select(
                \DB::raw("COUNT(*) as count"),
                \DB::raw("DATE(created_at) as date"),
                \DB::raw("DAYNAME(created_at) as day_name"),
                \DB::raw("MONTH(created_at) as month"),
                \DB::raw("DAY(created_at) as day")
            )
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', [1, 2, 3, 4, 5])
            ->groupBy('date', 'day_name', 'month', 'day')
            ->orderBy('date')
            ->get();
    } else {
        $recordQuery = Delivery::select(
            \DB::raw("COUNT(*) as count"),
            \DB::raw("DATE(created_at) as date"),
            \DB::raw("DAYNAME(created_at) as day_name"),
            \DB::raw("MONTH(created_at) as month"),
            \DB::raw("DAY(created_at) as day")
        )
        ->whereBetween('created_at', [$start, $end])
        ->whereIn('status', [1, 2, 3, 4, 5]);
        
        // Apply merchant filter if selected
        if($merchantFilter){
            $recordQuery->where('shop', $merchantFilter);
        }
        
        $record = $recordQuery->groupBy('date', 'day_name', 'month', 'day')
            ->orderBy('date')
            ->get();
    }

    // Generate labels and data arrays
    $data = ['label' => [], 'data' => []];
    
    // Create a map of dates to counts
    $dateMap = [];
    foreach($record as $row) {
        $dateMap[$row->date] = (int) $row->count;
    }
    
    // Fill in all dates in the range, including those with zero deliveries
    $currentDate = $start->copy();
    while ($currentDate <= $end) {
        $dateStr = $currentDate->format('Y-m-d');
        $label = $currentDate->format('m-d');
        $data['label'][] = $label;
        $data['data'][] = isset($dateMap[$dateStr]) ? $dateMap[$dateStr] : 0;
        $currentDate->addDay();
    }

    $chart_data = $data;
    
    // Delivery counts
    if(Auth::user()->role == 'customer'){
        $delivery = Delivery::where('shop', Auth::user()->name)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    } else {
        $deliveryQuery = Delivery::whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $deliveryQuery->where('shop', $merchantFilter);
        }
        $delivery = $deliveryQuery->count();
    }

    // Order counts
    if(Auth::user()->role == 'customer'){
        $order = Order::where('shop', Auth::user()->name)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    } else {
        $orderQuery = Order::whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $orderQuery->where('shop', $merchantFilter);
        }
        $order = $orderQuery->count();
    }

    // Unique receiver counts (by phone) for the authenticated merchant in date range
    if(Auth::user()->role == 'customer'){
        $customerResult = Delivery::where('shop', Auth::user()->name)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->selectRaw('COUNT(DISTINCT phone) as count')
            ->first();
        $customer = $customerResult ? (int)$customerResult->count : 0;
    } else {
        $customerQuery = Delivery::whereBetween('created_at', [$start, $end])
            ->whereNotNull('phone')
            ->where('phone', '!=', '');
        if($merchantFilter){
            $customerQuery->where('shop', $merchantFilter);
        }
        $customerResult = $customerQuery->selectRaw('COUNT(DISTINCT phone) as count')
            ->first();
        $customer = $customerResult ? (int)$customerResult->count : 0;
    }
        
    // Average rating for authenticated merchant's deliveries
    if(Auth::user()->role == 'customer'){
        $ratingResult = Delivery::where('shop', Auth::user()->name)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('rating')
            ->where('rating', '!=', '')
            ->where('rating', '!=', 'null')
            ->selectRaw('AVG(CAST(rating AS DECIMAL(10,2))) as average_rating')
            ->first();
        $driver = $ratingResult && $ratingResult->average_rating ? round($ratingResult->average_rating, 1) : 0;
    } else {
        // For admin/manager, show average rating of all deliveries
        $ratingQuery = Delivery::whereBetween('created_at', [$start, $end])
            ->whereNotNull('rating')
            ->where('rating', '!=', '')
            ->where('rating', '!=', 'null');
        if($merchantFilter){
            $ratingQuery->where('shop', $merchantFilter);
        }
        $ratingResult = $ratingQuery->selectRaw('AVG(CAST(rating AS DECIMAL(10,2))) as average_rating')
            ->first();
        $driver = $ratingResult && $ratingResult->average_rating ? round($ratingResult->average_rating, 1) : 0;
    }

    // Ware data
    if(Auth::user()->role == 'customer'){
        $ware = Delivery::where('shop', Auth::user()->name)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
    } else {
        $ware = User::whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    // Active deliveries (status 2)
    if(Auth::user()->role == 'customer'){
        $active = Delivery::where('shop', Auth::user()->name)
            ->where('status', 2)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    } else {
        $activeQuery = Delivery::where('status', 2)
            ->whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $activeQuery->where('shop', $merchantFilter);
        }
        $active = $activeQuery->count();
    }

    // New deliveries (status 1)
    if(Auth::user()->role == 'customer'){
        $new = Delivery::where('shop', Auth::user()->name)
            ->where('status', 1)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    } else {
        $newQuery = Delivery::where('status', 1)
            ->whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $newQuery->where('shop', $merchantFilter);
        }
        $new = $newQuery->count();
    }

    // Sum of number column (Барааны тоо)
    if(Auth::user()->role == 'customer'){
        $urgent = Delivery::where('shop', Auth::user()->name)
            ->where('status', 3)
            ->whereBetween('created_at', [$start, $end])
            ->sum('number');
    } else {
        $urgentQuery = Delivery::where('status', 3)
            ->whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $urgentQuery->where('shop', $merchantFilter);
        }
        $urgent = $urgentQuery->sum('number');
    }

    // Success deliveries (status 3 with estimated)
    if(Auth::user()->role == 'customer'){
        $success = Delivery::where('shop', Auth::user()->name)
            ->where('status', 3)
            ->where('estimated', 1)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    } else {
        $successQuery = Delivery::where('status', 3)
            ->where('estimated', 1)
            ->whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $successQuery->where('shop', $merchantFilter);
        }
        $success = $successQuery->count();
    }

    // Received amount
    if(Auth::user()->role == 'customer'){
        $received = Delivery::where('shop', Auth::user()->name)
            ->whereBetween('created_at', [$start, $end])
            ->sum('received');
    } else {
        $receivedQuery = Delivery::whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $receivedQuery->where('shop', $merchantFilter);
        }
        $received = $receivedQuery->sum('received');
    }

    // Total delivery price and product price
    if(Auth::user()->role == 'customer'){
        $deliveryPriceAndSum = Delivery::where('shop', Auth::user()->name)
            ->where('status', 3)
            ->where('estimated', 1)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('SUM(deliveryprice) as total_deliveryprice, SUM(price) as total_price')
            ->first();
    } else {
        $deliveryPriceAndSumQuery = Delivery::where('status', 3)
            ->where('estimated', 1)
            ->whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $deliveryPriceAndSumQuery->where('shop', $merchantFilter);
        }
        $deliveryPriceAndSum = $deliveryPriceAndSumQuery->selectRaw('SUM(deliveryprice) as total_deliveryprice, SUM(price) as total_price')
            ->first();
    }

    $totalDeliveryPrice = $deliveryPriceAndSum->total_deliveryprice ?? 0;
    $totalPrice = $deliveryPriceAndSum->total_price ?? 0;

    // Today's calculation (always for current day regardless of filter)
    $todayStart = Carbon::today()->startOfDay();
    $todayEnd = Carbon::today()->endOfDay();
    
    if(Auth::user()->role == 'customer'){
        $todayDeliveryPriceAndSum = Delivery::where('shop', Auth::user()->name)
            ->whereIn('status', [3, 4, 5])
            ->whereBetween('updated_at', [$todayStart, $todayEnd])
            ->selectRaw('SUM(deliveryprice) as total_deliveryprice, SUM(price) as total_price')
            ->first();
    } else {
        $todayDeliveryPriceAndSumQuery = Delivery::whereIn('status', [3, 4, 5])
            ->whereBetween('updated_at', [$todayStart, $todayEnd]);
        if($merchantFilter){
            $todayDeliveryPriceAndSumQuery->where('shop', $merchantFilter);
        }
        $todayDeliveryPriceAndSum = $todayDeliveryPriceAndSumQuery->selectRaw('SUM(deliveryprice) as total_deliveryprice, SUM(price) as total_price')
            ->first();
    }

    $totalDeliveryPriceToday = $todayDeliveryPriceAndSum->total_deliveryprice ?? 0;

    // Declined deliveries (status 4 or 5)
    if(Auth::user()->role == 'customer'){
        $declined = Delivery::where('shop', Auth::user()->name)
            ->where(function ($query) {
                $query->where('status', 4)
                      ->orWhere('status', 5);
            })
            ->where('estimated', 1)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    } else {
        $declinedQuery = Delivery::where(function ($query) {
                $query->where('status', 4)
                      ->orWhere('status', 5);
            })
            ->where('estimated', 1)
            ->whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $declinedQuery->where('shop', $merchantFilter);
        }
        $declined = $declinedQuery->count();
    }

    // Total deliveries for percentage calculation
    if(Auth::user()->role == 'customer'){
        $total = Delivery::where('shop', Auth::user()->name)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    } else {
        $totalQuery = Delivery::whereBetween('created_at', [$start, $end]);
        if($merchantFilter){
            $totalQuery->where('shop', $merchantFilter);
        }
        $total = $totalQuery->count();
    }

    // Calculate percentages
    $percent = $total > 0 ? round($success * 100 / $total, 2) : 0;
    $unsuccess = $percent > 0 ? 100 - $percent : 0;

    // Delivery downloaded amount
    if(Auth::user()->role == 'customer'){
        $deliveryDownloaded = DeliveryDownload::join('deliveries', 'deliveries.id', '=', 'deliveries_download.deliveries_id')
            ->where('deliveries.shop', Auth::user()->name)
            ->whereBetween('deliveries_download.created_at', [$start, $end])
            ->sum('deliveries_download.download_price');
    } else {
        $deliveryDownloadedQuery = DeliveryDownload::join('deliveries', 'deliveries.id', '=', 'deliveries_download.deliveries_id')
            ->whereBetween('deliveries_download.created_at', [$start, $end]);
        if($merchantFilter){
            $deliveryDownloadedQuery->where('deliveries.shop', $merchantFilter);
        }
        $deliveryDownloaded = $deliveryDownloadedQuery->sum('deliveries_download.download_price');
    }

    $dateRange = [
        'start_date' => $startDate,
        'end_date' => $endDate
    ];

    // Get latest 100 deliveries with status 1 and 2 (Шинэ and Хүргэлтэнд гарсан) for the dashboard
    // These deliveries show the latest 100 regardless of date range, but respect merchant filters
    if(Auth::user()->role == 'customer'){
        $deliveries = Delivery::where('shop', Auth::user()->name)
            ->whereIn('status', [1, 2])
            ->select('id', 'track', 'order_code', 'address', 'phone', 'shop', 'status', 'created_at', 'updated_at', 'estimated', 'driver', 'price', 'deliveryprice', 'comment', 'note', 'latitude', 'longitude', 'download_time')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    } else {
        $deliveriesQuery = Delivery::whereIn('status', [1, 2]);
        if($merchantFilter){
            $deliveriesQuery->where('shop', $merchantFilter);
        }
        $deliveries = $deliveriesQuery->select('id', 'track', 'order_code', 'address', 'phone', 'shop', 'status', 'created_at', 'updated_at', 'estimated', 'driver', 'price', 'deliveryprice', 'comment', 'note', 'latitude', 'longitude', 'download_time')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    // Log final counts for debugging
    \Log::info('Dashboard statistics', [
        'new_deliveries' => $new,
        'active_deliveries' => $active,
        'success_deliveries' => $success,
        'declined_deliveries' => $declined,
        'total_deliveries' => $total
    ]);

    return view('admin.home.homeContent', compact(
        'delivery', 'customer', 'driver', 'order', 'chart_data', 'ware',
        'percent', 'unsuccess', 'urgent', 'success', 'declined', 'received',
        'totalDeliveryPrice', 'totalPrice', 'active', 'new', 'deliveryDownloaded',
        'totalDeliveryPriceToday', 'dateRange', 'deliveries', 'merchants'
    ));
}

    /**
     * Get dashboard data as JSON for API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardData(Request $request)
    {
        // Check if user is authenticated
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid API token.',
            ], 401);
        }

        // Get date range from request or set default to last 90 days
        $startDate = $request->input('start_date', Carbon::today()->subDays(90)->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        
        // Get merchant filter (for admin users)
        $merchantFilter = $request->input('merchant_id', null);
        
        // Convert to Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get all merchants/customers for dropdown (only for admin)
        $merchants = collect([]);
        if($user->role != 'customer'){
            $merchants = User::where('role', 'customer')
                ->orderBy('name', 'asc')
                ->get();
        }

        // Chart data - Show all deliveries grouped by date
        if($user->role == 'customer'){
            $record = Delivery::where('shop', $user->name)
                ->select(
                    \DB::raw("COUNT(*) as count"),
                    \DB::raw("DATE(created_at) as date")
                )
                ->whereBetween('created_at', [$start, $end])
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            $recordQuery = Delivery::select(
                \DB::raw("COUNT(*) as count"),
                \DB::raw("DATE(created_at) as date")
            )
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', [1, 2, 3, 4, 5]);
            
            if($merchantFilter){
                $recordQuery->where('shop', $merchantFilter);
            }
            
            $record = $recordQuery->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        // Generate chart data
        $chartData = [];
        $dateMap = [];
        foreach($record as $row) {
            $dateMap[$row->date] = (int) $row->count;
        }
        
        $currentDate = $start->copy();
        while ($currentDate <= $end) {
            $dateStr = $currentDate->format('Y-m-d');
            $label = $currentDate->format('m-d');
            $chartData[] = [
                'label' => $label,
                'value' => isset($dateMap[$dateStr]) ? $dateMap[$dateStr] : 0
            ];
            $currentDate->addDay();
        }

        // Total deliveries
        if($user->role == 'customer'){
            $totalDeliveries = Delivery::where('shop', $user->name)
                ->whereBetween('created_at', [$start, $end])
                ->count();
        } else {
            $totalDeliveriesQuery = Delivery::whereBetween('created_at', [$start, $end]);
            if($merchantFilter){
                $totalDeliveriesQuery->where('shop', $merchantFilter);
            }
            $totalDeliveries = $totalDeliveriesQuery->count();
        }

        // New deliveries (status 1)
        if($user->role == 'customer'){
            $new = Delivery::where('shop', $user->name)
                ->where('status', 1)
                ->whereBetween('created_at', [$start, $end])
                ->count();
        } else {
            $newQuery = Delivery::where('status', 1)
                ->whereBetween('created_at', [$start, $end]);
            if($merchantFilter){
                $newQuery->where('shop', $merchantFilter);
            }
            $new = $newQuery->count();
        }

        // Active deliveries (status 2)
        if($user->role == 'customer'){
            $active = Delivery::where('shop', $user->name)
                ->where('status', 2)
                ->whereBetween('created_at', [$start, $end])
                ->count();
        } else {
            $activeQuery = Delivery::where('status', 2)
                ->whereBetween('created_at', [$start, $end]);
            if($merchantFilter){
                $activeQuery->where('shop', $merchantFilter);
            }
            $active = $activeQuery->count();
        }

        // Success deliveries (status 3 with estimated)
        if($user->role == 'customer'){
            $success = Delivery::where('shop', $user->name)
                ->where('status', 3)
                ->where('estimated', 1)
                ->whereBetween('created_at', [$start, $end])
                ->count();
        } else {
            $successQuery = Delivery::where('status', 3)
                ->where('estimated', 1)
                ->whereBetween('created_at', [$start, $end]);
            if($merchantFilter){
                $successQuery->where('shop', $merchantFilter);
            }
            $success = $successQuery->count();
        }

        // Declined deliveries (status 4 or 5)
        if($user->role == 'customer'){
            $declined = Delivery::where('shop', $user->name)
                ->where(function ($query) {
                    $query->where('status', 4)
                          ->orWhere('status', 5);
                })
                ->where('estimated', 1)
                ->whereBetween('created_at', [$start, $end])
                ->count();
        } else {
            $declinedQuery = Delivery::where(function ($query) {
                    $query->where('status', 4)
                          ->orWhere('status', 5);
                })
                ->where('estimated', 1)
                ->whereBetween('created_at', [$start, $end]);
            if($merchantFilter){
                $declinedQuery->where('shop', $merchantFilter);
            }
            $declined = $declinedQuery->count();
        }

        // Item count (sum of number column)
        if($user->role == 'customer'){
            $itemCount = Delivery::where('shop', $user->name)
                ->where('status', 3)
                ->whereBetween('created_at', [$start, $end])
                ->sum('number');
        } else {
            $itemCountQuery = Delivery::where('status', 3)
                ->whereBetween('created_at', [$start, $end]);
            if($merchantFilter){
                $itemCountQuery->where('shop', $merchantFilter);
            }
            $itemCount = $itemCountQuery->sum('number');
        }

        // Total delivery price and product price
        if($user->role == 'customer'){
            $deliveryPriceAndSum = Delivery::where('shop', $user->name)
                ->where('status', 3)
                ->where('estimated', 1)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('SUM(deliveryprice) as total_deliveryprice, SUM(price) as total_price')
                ->first();
        } else {
            $deliveryPriceAndSumQuery = Delivery::where('status', 3)
                ->where('estimated', 1)
                ->whereBetween('created_at', [$start, $end]);
            if($merchantFilter){
                $deliveryPriceAndSumQuery->where('shop', $merchantFilter);
            }
            $deliveryPriceAndSum = $deliveryPriceAndSumQuery->selectRaw('SUM(deliveryprice) as total_deliveryprice, SUM(price) as total_price')
                ->first();
        }

        $totalDeliveryPrice = $deliveryPriceAndSum->total_deliveryprice ?? 0;
        $totalPrice = $deliveryPriceAndSum->total_price ?? 0;

        // Customer count
        if($user->role == 'customer'){
            $customerResult = Delivery::where('shop', $user->name)
                ->whereBetween('created_at', [$start, $end])
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->selectRaw('COUNT(DISTINCT phone) as count')
                ->first();
            $customer = $customerResult ? (int)$customerResult->count : 0;
        } else {
            $customerQuery = Delivery::whereBetween('created_at', [$start, $end])
                ->whereNotNull('phone')
                ->where('phone', '!=', '');
            if($merchantFilter){
                $customerQuery->where('shop', $merchantFilter);
            }
            $customerResult = $customerQuery->selectRaw('COUNT(DISTINCT phone) as count')
                ->first();
            $customer = $customerResult ? (int)$customerResult->count : 0;
        }

        // Average driver rating
        if($user->role == 'customer'){
            $ratingResult = Delivery::where('shop', $user->name)
                ->whereBetween('created_at', [$start, $end])
                ->whereNotNull('rating')
                ->where('rating', '!=', '')
                ->where('rating', '!=', 'null')
                ->selectRaw('AVG(CAST(rating AS DECIMAL(10,2))) as average_rating')
                ->first();
            $driverRating = $ratingResult && $ratingResult->average_rating ? round($ratingResult->average_rating, 1) : 0;
        } else {
            $ratingQuery = Delivery::whereBetween('created_at', [$start, $end])
                ->whereNotNull('rating')
                ->where('rating', '!=', '')
                ->where('rating', '!=', 'null');
            if($merchantFilter){
                $ratingQuery->where('shop', $merchantFilter);
            }
            $ratingResult = $ratingQuery->selectRaw('AVG(CAST(rating AS DECIMAL(10,2))) as average_rating')
                ->first();
            $driverRating = $ratingResult && $ratingResult->average_rating ? round($ratingResult->average_rating, 1) : 0;
        }

        // Get latest 100 deliveries with status 1 and 2
        if($user->role == 'customer'){
            $deliveries = Delivery::where('shop', $user->name)
                ->whereIn('status', [1, 2])
                ->select('id', 'track', 'order_code', 'address', 'phone', 'shop', 'status', 'created_at', 'updated_at', 'estimated', 'driver', 'price', 'deliveryprice', 'comment', 'note', 'latitude', 'longitude')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();
        } else {
            $deliveriesQuery = Delivery::whereIn('status', [1, 2]);
            if($merchantFilter){
                $deliveriesQuery->where('shop', $merchantFilter);
            }
            $deliveries = $deliveriesQuery->select('id', 'track', 'order_code', 'address', 'phone', 'shop', 'status', 'created_at', 'updated_at', 'estimated', 'driver', 'price', 'deliveryprice', 'comment', 'note', 'latitude', 'longitude')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();
        }

        // Format deliveries for JSON
        $deliveriesData = $deliveries->map(function($delivery) {
            return [
                'id' => $delivery->id,
                'track' => $delivery->track ?? $delivery->order_code ?? '',
                'shop' => $delivery->shop ?? '',
                'address' => $delivery->address ?? '',
                'phone' => $delivery->phone ?? '',
                'status' => $delivery->status,
                'estimated' => $delivery->estimated ?? 0,
                'driver' => $delivery->driver ?? '',
                'comment' => $delivery->comment ?? '',
                'created_at' => $delivery->created_at->toIso8601String(),
                'latitude' => $delivery->latitude,
                'longitude' => $delivery->longitude,
            ];
        })->toArray();

        // Format merchants for JSON
        $merchantsData = $merchants->map(function($merchant) {
            return [
                'id' => $merchant->id,
                'name' => $merchant->name,
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'total_deliveries' => $totalDeliveries,
                'new_deliveries' => $new,
                'active_deliveries' => $active,
                'success_deliveries' => $success,
                'declined_deliveries' => $declined,
                'item_count' => $itemCount,
                'total_delivery_price' => $totalDeliveryPrice,
                'total_price' => $totalPrice,
                'customer_count' => $customer,
                'driver_rating' => $driverRating,
                'chart_data' => $chartData,
                'recent_deliveries' => $deliveriesData,
                'merchants' => $merchantsData,
                'date_range' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ],
        ]);
    }

    public function profile()
    {
        $user=User::find(Auth::user()->id);
        
        return view('admin.home.profile',compact('user'));
  
    }


    public function editprofile(Request $request)
    {
        $user=User::where('id',Auth::user()->id)->first();
        $user->what3words=$request->what3words;
        $user->save();
        return redirect()->back();
    }

    public function createMerchant(Request $request)
    {
        $merchant = new Merchant;
              $rules = [
                    
                    'merchantName' => 'required',
                    'merchantAddress' => 'required',
                    'merchantPhone1' => 'required|numeric|digits:8',
                    'merchantPhone2' => 'numeric|digits:8',
                   
                ];

       $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $merchant->user_id = Auth::user()->id;
        $merchant->merchantName = $request->merchantName;
        $merchant->merchantAddress = $request->merchantAddress;
        $merchant->merchantPhone1 = $request->merchantPhone1;
        $merchant->merchantPhone2 = $request->merchantPhone2;
        $merchant->merchantWhat3Words = $request->merchantWhat3Words;
       // dd($merchant);
        $merchant->save();
        return redirect()->back();
    }
    public function editMerchant(Request $request)
    {
        $merchant=Merchant::where('id',$request->merchantId)->first();
        $merchant->merchantName = $request->merchantName;
        $merchant->merchantAddress = $request->merchantAddress;
        $merchant->merchantPhone1 = $request->merchantPhone1;
        $merchant->merchantPhone2 = $request->merchantPhone2;
        $merchant->merchantWhat3Words = $request->merchantWhat3Words;
       // dd($merchant);
        $merchant->save();
        return redirect()->back();
    }
     public function deleteMerchant($id){
            $merchant=Merchant::where('id',$id)->first();
            $merchant->deleted = 1;      
            $merchant->save();
            return redirect()->back();
    }
       public function searchType(){
        return "aaa";
          //  dd("Hello");
            // $delivery = Delivery::get();
            // return response()->json(['data'=>$delivery,'success'=>true]);
        }

    /**
     * Get top 10 most repeated phone numbers with receivername and shop
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopPhones(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            // Get date range from request or set default to last 90 days
            $startDate = $request->input('start_date', Carbon::now()->subDays(90)->toDateString());
            $endDate = $request->input('end_date', Carbon::today()->toDateString());
            
            // Get merchant filter from request
            $merchantFilter = $request->input('merchant_id', null);
            
            // Convert to Carbon instances
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            // Get top 10 most repeated combinations of phone, receivername, and shop
            // Group by phone, receivername, and shop together
            // Only include deliveries with status 3 (Амжилттай хүргэгдсэн)
            $topPhonesQuery = Delivery::select(
                    'phone',
                    'receivername',
                    'shop',
                    \DB::raw('COUNT(*) as count')
                )
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->where('status', 3)
                ->whereBetween('created_at', [$start, $end]);
            
            // Apply merchant filter if selected
            if($merchantFilter){
                $topPhonesQuery->where('shop', $merchantFilter);
            }
            
            $topPhones = $topPhonesQuery->groupBy('phone', 'receivername', 'shop')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            // Format the result
            $result = [];
            foreach ($topPhones as $item) {
                $result[] = [
                    'phone' => $item->phone ?? 'N/A',
                    'receivername' => $item->receivername ?? 'N/A',
                    'shop' => $item->shop ?? 'N/A',
                    'count' => $item->count
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'date_range' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'merchant_filter' => $merchantFilter
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getTopPhones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top 10 phone numbers ordered by sum of price
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopPhonesByPrice(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            // Get date range from request or set default to last 90 days
            $startDate = $request->input('start_date', Carbon::now()->subDays(90)->toDateString());
            $endDate = $request->input('end_date', Carbon::today()->toDateString());
            
            // Get merchant filter from request
            $merchantFilter = $request->input('merchant_id', null);
            
            // Convert to Carbon instances
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            // Get top 10 phones ordered by sum of price
            // Group by phone (repeated phone is key)
            // Only include deliveries with status 3 (Амжилттай хүргэгдсэн)
            $topPhonesQuery = Delivery::select(
                    'phone',
                    'receivername',
                    'shop',
                    \DB::raw('SUM(price) as total_price'),
                    \DB::raw('COUNT(*) as count')
                )
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->where('status', 3)
                ->whereBetween('created_at', [$start, $end]);
            
            // Apply merchant filter if selected
            if($merchantFilter){
                $topPhonesQuery->where('shop', $merchantFilter);
            }
            
            $topPhones = $topPhonesQuery->groupBy('phone', 'receivername', 'shop')
                ->orderBy('total_price', 'desc')
                ->limit(10)
                ->get();

            // Format the result
            $result = [];
            foreach ($topPhones as $item) {
                $result[] = [
                    'phone' => $item->phone ?? 'N/A',
                    'receivername' => $item->receivername ?? 'N/A',
                    'shop' => $item->shop ?? 'N/A',
                    'total_price' => $item->total_price ?? 0,
                    'count' => $item->count
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'date_range' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'merchant_filter' => $merchantFilter
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getTopPhonesByPrice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }
    }

}
