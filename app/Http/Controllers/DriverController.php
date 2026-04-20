<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Exports\DeliveryExport;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Driver;
use App\Models\City;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
class DriverController extends Controller
{
    public function drivermonitoring()
    {
        return view('admin.report.drivermonitoring');
    }
    public function driverLocation()
    {
        return view('admin.driver.location');
    }
    public function driverRequestShow()
    {
        return view('admin.driver.driver_request');
    }
    public function detail($driver)
    {
        // Get active users (where active = 1 and role = 'customer') for merchant dropdown
        $activeUsers = User::where('active', 1)
            ->where('role', 'customer')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        
        return view('admin.report.detail', compact('driver', 'activeUsers'));
    }
  public function driverDetail(Request $request)
{
    // Get driver from request
    $driver = $request->get('driver');
    
    // If no driver parameter, show error or redirect
    if (!$driver) {
        if ($request->ajax()) {
            return response()->json(['error' => 'Driver parameter is required'], 400);
        }
        abort(400, 'Driver parameter is required. Please provide driver name.');
    }

    // Get driver info from users table
    $driverInfo = User::where('name', $driver)->first();
    
    if (!$driverInfo) {
        if ($request->ajax()) {
            return response()->json(['error' => 'Driver not found'], 404);
        }
        abort(404, 'Driver not found');
    }

    if ($request->ajax()) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $merchant = $request->get('merchant');
        
        $query = Delivery::with('merchant')
            ->where('driver', '=', $driver);

        // Add status filtering - if status is provided, filter by it; otherwise use default [3, 4, 5]
        if (!empty($status)) {
            $query->where('status', '=', $status);
        } else {
            $query->whereIn('status', [3, 4, 5]);
        }

        // Add merchant filtering - now using exact match with user name
        if (!empty($merchant)) {
            $query->where('shop', '=', $merchant);
        }

        // Add date filtering
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $totalRecords = $query->count();
        
        $table = Datatables::of($query)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" style="width:20px;height:20px;" class="checkbox" onclick="updateCount()" name="foo" data-id="' . $row->userid . '" value="' . $row->userid . '">';
            })
            ->addColumn('track', function ($row) {
                return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 100% !important; height:100% !important" class="text-center whitespace-nowrap table-info">' . $row->track . '</div>';
            })
            ->addColumn('type', function ($row) {
                $types = [
                    '1' => '<div style="color:green;">Энгийн</div>',
                    '2' => '<div style="color:orange;">Цагтай</div>',
                    '3' => '<div style="color:pink;">Яаралтай</div>',
                    '4' => '<div style="color:red;">Онц яаралтай</div>'
                ];
                return $types[$row->type] ?? '<div>Тодорхойгүй</div>';
            })
            ->addColumn('created_at', function ($row) {
                return substr($row->created_at, 5, -3);
            })
            ->addColumn('status', function ($row) {
                $statusMap = [
                    1 => '<div class="status1">Бүртгэгдсэн</div>',
                    2 => '<div class="status2">Хуваарилсан</div>',
                    3 => '<div class="status3">Хүргэгдсэн</div>',
                    4 => '<div class="status4">Цуцалсан</div>',
                    5 => '<div class="status5">Буцаасан</div>',
                    6 => '<div class="status6">Хүлээгдэж буй</div>',
                    10 => '<div class="status10">Хүлээн авсан</div>'
                ];
                return $statusMap[$row->status] ?? 'Тодорхойгүй';
            })
            ->addColumn('merchantName', function ($row) {
                return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 110px !important;" class="text-center whitespace-nowrap">' . $row->merchantName . '</div>';
            })
            ->rawColumns(['checkbox', 'track', 'type', 'created_at', 'status', 'merchantName'])
            ->setTotalRecords($totalRecords)
            ->make(true);
            
        return $table;
    } else {
        // For non-ajax requests, return the view with driver info
        return view('admin.report.detail', [
            'driver' => $driver,
            'driverInfo' => $driverInfo
        ]);
    }
}
    public function getDriverRequest()
    {
        $driverRequest = Driver::orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
        $table = Datatables::of($driverRequest)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" style="width:20px;height:20px;" class="checkbox" onclick="updateCount()" name="foo" data-id="' . $row->id . '" value="' . $row->id . '">';
            })
            ->addColumn('lastname', function ($row) {
                return isset($row->lastname) ? $row->lastname : '';
            })
            ->addColumn('firstname', function ($row) {
                return isset($row->firstname) ? $row->firstname : '';
            })
            ->addColumn('email', function ($row) {
                return isset($row->email) ? $row->email : '';
            })
            ->addColumn('phone', function ($row) {
                return isset($row->phone) ? $row->phone : '';
            })
            ->addColumn('city', function ($row) {
                return isset($row->city) ? $row->city : '';
            })
            ->addColumn('address', function ($row) {
                return isset($row->address) ? $row->address : '';
            })
            ->addColumn('comment', function ($row) {
                return isset($row->comment) ? $row->comment : '';
            })
            ->addColumn('gender', function ($row) {
                return isset($row->gender) ? e($row->gender) : '';
            })
            ->addColumn('created_at', function ($row) {
                return !empty($row->created_at) ? date('Y-m-d H:i', strtotime($row->created_at)) : '';
            })

            ->rawColumns(['checkbox', 'phone', 'address', 'lastname', 'firstname', 'city', 'email', 'comment', 'gender', 'created_at'])
            // ->setTotalRecords($dataCount)
            ->skipPaging()
            ->make(true);
        return $table;


        // return response()->json(['data' => $mergedData]);
    }

public function exportDriverExcel(Request $request)
{
    $driver = $request->driver;
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    $merchant = $request->get('merchant');
    $selectedIds = $request->get('ids', []);
    
    \Log::info('Export Request Data:', [
        'driver' => $driver,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'merchant' => $merchant,
        'selected_ids' => $selectedIds,
        'selected_ids_count' => is_array($selectedIds) ? count($selectedIds) : 0,
        'all_request_params' => $request->all()
    ]);

    $query = Delivery::with('merchant')
        ->where('driver', '=', $driver)
        ->whereIn('status', [3, 4, 5]);
    
    // Add merchant filtering
    if (!empty($merchant)) {
        $query->where('shop', '=', $merchant);
        \Log::info('Applying merchant filter:', ['merchant' => $merchant]);
    }

    // Enhanced ID filtering with better debugging
    if (!empty($selectedIds) && is_array($selectedIds)) {
        // Clean the array - remove empty, null, zero values and convert to integers
        $cleanIds = array_filter(array_map('intval', $selectedIds), function($id) {
            return $id > 0;
        });
        
        \Log::info('Cleaned IDs for filtering:', [
            'ids' => $cleanIds, 
            'count' => count($cleanIds),
            'sample_ids' => array_slice($cleanIds, 0, 5)
        ]);

        if (!empty($cleanIds)) {
            $query->whereIn('id', $cleanIds);
            \Log::info('Applying ID filter with cleaned IDs:', $cleanIds);
            
            // Debug: Check how many records match these IDs
            $matchingCount = Delivery::whereIn('id', $cleanIds)->count();
            \Log::info('Records matching selected IDs:', ['count' => $matchingCount]);
        } else {
            \Log::warning('No valid IDs found after cleaning');
        }
    } else {
        \Log::info('No selected IDs provided or not an array');
    }

    // Add date filtering
    if (!empty($startDate)) {
        $query->whereDate('created_at', '>=', $startDate);
        \Log::info('Applying start date filter:', ['start_date' => $startDate]);
    }
    
    if (!empty($endDate)) {
        $query->whereDate('created_at', '<=', $endDate);
        \Log::info('Applying end date filter:', ['end_date' => $endDate]);
    }

    $deliveries = $query->orderBy('created_at', 'desc')->get();

    \Log::info('Final query results:', [
        'total_records' => $deliveries->count(),
        'sql_query' => $query->toSql(),
        'bindings' => $query->getBindings(),
        'sample_data' => $deliveries->count() > 0 ? [
            'first_track' => $deliveries->first()->track,
            'first_created_at' => $deliveries->first()->created_at
        ] : 'No data'
    ]);

    // If no data found, return informative message
    if ($deliveries->count() === 0) {
        \Log::warning('No deliveries found for export', [
            'driver' => $driver,
            'selected_ids_count' => count($selectedIds ?? []),
            'filters_applied' => [
                'has_date_filter' => !empty($startDate) || !empty($endDate),
                'has_id_filter' => !empty($selectedIds)
            ]
        ]);
    }

    // Prepare data for Excel
    $exportData = [];

    // Add headers
    $exportData[] = [
        'Track ID', 'Огноо', 'Төрөл', 'Харилцагч', 'Z-Код', 
        'Статус', 'Бүс', 'Хаяг', 'Утас', 'Хүргэлтийн үнэ', '80% үнэ'
    ];

    // Calculate totals
    $totalOriginalPrice = 0;
    $totalDiscountedPrice = 0;

    // Add data rows
    foreach ($deliveries as $delivery) {
        $typeMap = [
            '1' => 'Энгийн',
            '2' => 'Цагтай', 
            '3' => 'Яаралтай',
            '4' => 'Онц яаралтай'
        ];

        $statusMap = [
            1 => 'Бүртгэгдсэн',
            2 => 'Хуваарилсан',
            3 => 'Хүргэгдсэн',
            4 => 'Цуцалсан',
            5 => 'Буцаасан',
            6 => 'Хүлээгдэж буй',
            10 => 'Хүлээн авсан'
        ];

        // Get delivery price and calculate discounted price
        $originalPrice = $delivery->deliveryprice ?? 0;
        $discountedPrice = $originalPrice * 0.8;
        
        $totalOriginalPrice += $originalPrice;
        $totalDiscountedPrice += $discountedPrice;

        $exportData[] = [
            $delivery->track,
            $delivery->created_at ? $delivery->created_at->format('Y-m-d H:i') : 'N/A',
            $typeMap[$delivery->type] ?? 'Тодорхойгүй',
            $delivery->merchantName ?? ($delivery->merchant->name ?? 'N/A'),
            $delivery->order_code ?? 'N/A',
            $statusMap[$delivery->status] ?? 'Тодорхойгүй',
            $delivery->region ?? 'N/A',
            $delivery->address ?? 'N/A',
            $delivery->phone ?? 'N/A',
            $originalPrice,
            $discountedPrice
        ];
    }

    // Add summary rows
    if ($deliveries->count() > 0) {
        $exportData[] = []; // Empty row for separation
        
        // Summary rows
        $exportData[] = [
            '', '', '', '', '', '', '', '', '',
            'Нийт дүн:',
            $totalOriginalPrice
        ];
        
        $exportData[] = [
            '', '', '', '', '', '', '', '', '',
            '80% нийт дүн:',
            $totalDiscountedPrice
        ];

        // Additional summary information
        $exportData[] = [
            '', '', '', '', '', '', '', '', '',
            'Нийт хүргэлт:',
            $deliveries->count()
        ];
    }

    \Log::info('Export data prepared:', [
        'total_rows' => count($exportData) - 1, // minus header
        'has_data' => count($exportData) > 1,
        'total_original_price' => $totalOriginalPrice,
        'total_discounted_price' => $totalDiscountedPrice,
        'total_deliveries' => $deliveries->count()
    ]);

    $export = new class($exportData) implements FromCollection, WithStyles, WithHeadings
    {
        private $data;

        public function __construct($data)
        {
            $this->data = $data;
        }

        public function collection()
        {
            return collect($this->data);
        }

        public function headings(): array
        {
            return $this->data[0] ?? [];
        }

        public function styles(Worksheet $sheet)
        {
            $lastRow = count($this->data);
            
            // Style for header row
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E86C1'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            // Style for total rows
            $totalStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '27AE60'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            // Style for discounted total row
            $discountedStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E74C3C'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            // Apply header style
            $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

            // Apply styles to summary rows if we have data
            if ($lastRow > 2) {
                // Original total row (second last row with data)
                if (!empty($this->data[$lastRow-3][9]) && $this->data[$lastRow-3][9] === 'Нийт дүн:') {
                    $sheet->getStyle('A' . ($lastRow-3) . ':K' . ($lastRow-3))->applyFromArray($totalStyle);
                }
                
                // Discounted total row
                if (!empty($this->data[$lastRow-2][9]) && $this->data[$lastRow-2][9] === '80% нийт дүн:') {
                    $sheet->getStyle('A' . ($lastRow-2) . ':K' . ($lastRow-2))->applyFromArray($discountedStyle);
                }
                
                // Delivery count row
                if (!empty($this->data[$lastRow-1][9]) && $this->data[$lastRow-1][9] === 'Нийт хүргэлт:') {
                    $sheet->getStyle('A' . ($lastRow-1) . ':K' . ($lastRow-1))->applyFromArray($totalStyle);
                }
            }

            // Auto-size columns
            foreach (range('A', 'K') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Number format for price columns
            $sheet->getStyle('J2:K' . $lastRow)
                  ->getNumberFormat()
                  ->setFormatCode('#,##0');

            return [];
        }
    };

    $fileName = 'driver_deliveries_' . $driver . '_' . date('Y-m-d') . '.xlsx';
    
    \Log::info('Initiating Excel download:', [
        'file_name' => $fileName,
        'data_rows' => count($exportData) - 1,
        'total_original_price' => $totalOriginalPrice,
        'total_discounted_price' => $totalDiscountedPrice
    ]);
    
    return Excel::download($export, $fileName);
}
    public function getDriverCounts(Request $request)
    {
        // One aggregated pass over deliveries (JOIN, not per-row correlated subqueries)
        $ratingAggSub = DB::table('deliveries')
            ->select(
                'driver',
                DB::raw('COUNT(*) as rated_deliveries_count'),
                DB::raw('AVG(CAST(rating AS DECIMAL(10,2))) as average_rating')
            )
            ->where('status', 3)
            ->whereNotNull('rating')
            ->whereRaw("TRIM(CAST(rating AS CHAR)) <> ''")
            ->whereRaw("LOWER(TRIM(CAST(rating AS CHAR))) <> 'null'")
            ->groupBy('driver');

        $aggregated = DB::table('deliveries')
            ->join('users', 'users.name', '=', 'deliveries.driver')
            ->leftJoin('addresses', 'users.id', '=', 'addresses.userid')
            ->leftJoin('phones', 'users.id', '=', 'phones.userid')
            ->leftJoinSub($ratingAggSub, 'rating_agg', function ($join) {
                $join->on('users.name', '=', 'rating_agg.driver');
            });

        if (Schema::hasColumn('users', 'role')) {
            $aggregated->where('users.role', 'driver');
        }
        if (Schema::hasColumn('users', 'active')) {
            $aggregated->where('users.active', 1);
        }

        $aggregated->select(
            'users.id as userid',
            'users.name as driver',
            'addresses.address as address',
            'phones.phone as phone',
            DB::raw('SUM(CASE WHEN deliveries.status = 3 THEN 1 ELSE 0 END) as hvrgegdsen'),
            DB::raw('SUM(CASE WHEN deliveries.status = 4 THEN 1 ELSE 0 END) as tsutsalsan'),
            DB::raw('SUM(CASE WHEN deliveries.status = 5 THEN 1 ELSE 0 END) as butsaasan'),
            DB::raw('SUM(CASE WHEN deliveries.status = 2 THEN 1 ELSE 0 END) as huwiarlasan'),
            DB::raw('MAX(COALESCE(rating_agg.rated_deliveries_count, 0)) as rated_deliveries_count'),
            DB::raw('MAX(rating_agg.average_rating) as average_rating')
        )
            ->whereIn('deliveries.status', [3, 4, 5, 2])
            ->groupBy('users.id', 'users.name', 'addresses.address', 'phones.userid', 'phones.phone');

        // Wrap so Yajra can apply LIMIT/OFFSET on the outer query (serverSide: true + skipPaging caused full scans / timeouts)
        $base = DB::query()->fromSub($aggregated, 'dm');

        return Datatables::of($base)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" style="width:20px;height:20px;" class="checkbox" onclick="updateCount()" name="foo" data-id="' . $row->userid . '" value="' . $row->userid . '">';
            })
            ->addColumn('phone', function ($row) {
                return isset($row->phone) ? $row->phone : '';
            })
            ->addColumn('actions', function ($row) {
                return '<button type="submit" class="btn btn-info"><a href="' . url('/driver/detail/' . $row->driver) . '" style="color:white;">Дэлгэрэнгүй</a></button>';
            })
            ->addColumn('address', function ($row) {
                return isset($row->address) ? $row->address : '';
            })
            ->editColumn('rated_deliveries_count', function ($row) {
                return (int) ($row->rated_deliveries_count ?? 0);
            })
            ->editColumn('average_rating', function ($row) {
                if ($row->average_rating === null || $row->average_rating === '') {
                    return '<span class="text-muted">—</span>';
                }

                return e(number_format((float) $row->average_rating, 1));
            })
            ->addColumn('total_revenue', function () {
                return '';
            })
            ->rawColumns(['checkbox', 'actions', 'phone', 'address', 'average_rating', 'total_revenue'])
            ->make(true);
    }
    // $driverCounts = DB::table('deliveries')
    //         ->join('users', 'users.name', '=', 'deliveries.driver')
    //         ->select(
    //             'users.id as userid', // Assuming 'id' is the primary key in the 'users' table
    //             'users.name as driver',
    //             DB::raw('SUM(CASE WHEN deliveries.status = 3 THEN 1 ELSE 0 END) as hvrgegdsen'),
    //             DB::raw('SUM(CASE WHEN deliveries.status = 4 THEN 1 ELSE 0 END) as tsutsalsan'),
    //             DB::raw('SUM(CASE WHEN deliveries.status = 5 THEN 1 ELSE 0 END) as butsaasan'),
    //             DB::raw('SUM(CASE WHEN deliveries.status = 2 THEN 1 ELSE 0 END) as huwiarlasan')
    //         )
    //         ->whereIn('deliveries.status', [3, 4, 5, 2])
    //         ->groupBy('users.id', 'users.name')
    //         ->get();

    //      $driverAddress = Delivery::select(           
    //             'users.id as userid',
    //             'users.name as driver',
    //             'addresses.address',
    //              DB::raw('COUNT(*) as total_revenue'),
    //              DB::raw('AVG(rating) as average_rating')
    //         )
    //         ->leftJoin('users', 'users.name', '=', 'deliveries.driver')
    //         ->leftJoin('addresses', 'addresses.userid', '=', 'users.id')
    //         ->whereNotNull('rating')
    //         ->groupBy('deliveries.driver', 'users.id', 'users.name', 'addresses.address')
    //         ->get();

    //     $mergedData = $driverCounts->map(function ($item) use ($driverAddress) {
    //         $matchingRow = $driverAddress->firstWhere('userid', $item->userid);
    //         return (object) array_merge((array) $item, (array) $matchingRow);
    //     });
    //     return response()->json(['data' => $mergedData]);
    // 

    //Driver orlogo harah
    public function getDriverOrlogo($driver)
    {
        $driverResult = DB::table('deliveries')
            ->select(
                'driver',
                'deliveries.type',
                'deliveryprice',
                'merchant.merchantName',
                'users.name as userName',
                'users.engiin as en',
                'users.tsagtai as ts',
                'users.yaraltai as ya',
                'users.onts_yaraltai as o_ya'
            )
            ->join('merchant', 'deliveries.merchant_id', '=', 'merchant.id')
            ->join('users', 'merchant.user_id', '=', 'users.id')
            ->where(function ($query) {
                $query->where('status', 3)
                    ->orWhere('status', 5);
            })
            ->where('driver', $driver)
            ->where('income', 0)
            ->get();
        $count = 0;

        foreach ($driverResult as $dr) {
            if ($dr->type == 1) {
                if ($dr->en) {
                    $count = $count + ($dr->en * 0.8);
                } else {
                    $defaultPrice = Setting::where('type', 1)->first();
                    $count = $count + $defaultPrice->driver;
                }
            } else  if ($dr->type == 2) {
                if ($dr->ts) {
                    $count = $count + ($dr->ts * 0.8);
                } else {
                    $defaultPrice = Setting::where('type', 2)->first();
                    $count = $count + $defaultPrice->driver;
                }
            } else  if ($dr->type == 3) {
                if ($dr->ya) {
                    $count = $count + ($dr->ya * 0.8);
                } else {
                    $defaultPrice = Setting::where('type', 3)->first();
                    $count = $count + $defaultPrice->driver;
                }
            } else  if ($dr->type == 4) {
                if ($dr->o_ya) {
                    $count = $count + ($dr->o_ya * 0.8);
                } else {
                    $defaultPrice = Setting::where('type', 4)->first();
                    $count = $count + $defaultPrice->driver;
                }
            }
        }
        return response()->json(['data' => $count, 'success' => true]);
    }

    //print Driver requests
    public function printDriverRequest(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->print)) {
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $arr_ids = explode(",", $request->post('ids'));
                $driverRequest = Driver::orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

                $i = 0;
                $print_data = array();
                $table = '<div class="text-center" style="font-weight:bold;"><h1>' . 'Жолоочийн мэдээлэл №' . '</h1></div><br><br>';
                $table .= '<div>' . 'Огноо:' . '</div>';
                $table .= '<div>' . date('Y-m-d H:i:s') . '</div></div>';
                $table .= '<table class="table table-striped  table-bordered" style="border-width: 1px;border-style: solid;border-color: black;">
                <thead>
                    <tr>
                    <th class="text-center whitespace-nowrap">#</th>
                    <th class="text-center whitespace-nowrap table-info">Овог</th>
                                                <th class="text-center whitespace-nowrap table-info">Нэр</th>
                                                <th class="text-center whitespace-nowrap table-info">Утас</th>
                                                <th class="text-center whitespace-nowrap table-info">Имэйл</th>
                                                <th class="text-center whitespace-nowrap table-info">Хот</th>
                                                <th class="text-center whitespace-nowrap table-success">Гэрийн хаяг</th>
                                                <th class="text-center whitespace-nowrap table-success">Нэмэлт тайлбар</th>
                                                <th class="text-center whitespace-nowrap table-success">Хүйс</th>

                    </tr>
                </thead>
                <tbody>';
                foreach ($driverRequest as $key => $row) {
                    $table .= "<tr>
                                <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ++$i . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->lastname ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->firstname ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->phone ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->email ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->city ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" .  ($row->address ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" .  ($row->comment ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" .  ($row->gender ?? '') . "</td>
                    
                                  
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
    //print Driver
    public function printDriverData(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->print)) {
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $arr_ids = explode(",", $request->post('ids'));
                $driverCounts = DB::table('deliveries')
                    ->join('users', 'users.name', '=', 'deliveries.driver')
                    ->select(
                        'users.id as userid',
                        'users.name as driver',
                        DB::raw('SUM(CASE WHEN deliveries.status = 3 THEN 1 ELSE 0 END) as hvrgegdsen'),
                        DB::raw('SUM(CASE WHEN deliveries.status = 4 THEN 1 ELSE 0 END) as tsutsalsan'),
                        DB::raw('SUM(CASE WHEN deliveries.status = 5 THEN 1 ELSE 0 END) as butsaasan'),
                        DB::raw('SUM(CASE WHEN deliveries.status = 2 THEN 1 ELSE 0 END) as huwiarlasan')
                    )
                    ->whereIn('deliveries.status', [3, 4, 5, 2])
                    ->groupBy('users.id', 'users.name')
                    ->get();

                $driverAddress = DB::table('users')
                    ->join('addresses as a', 'a.userid', '=', 'users.id')
                    ->where('role', 'driver')
                    ->where('active', 1)
                    ->select(
                        'users.id as id',
                        'users.name as driver',
                        'a.address'
                    )
                    ->get();

                $mergedData = $driverCounts->map(function ($item) use ($driverAddress) {
                    $matchingRow = $driverAddress->firstWhere('userid', $item->userid);
                    return (object) array_merge((array) $item, (array) $matchingRow);
                });
                $filteredData = $mergedData->whereIn('userid', $arr_ids);

                $i = 0;
                $print_data = array();
                $table = '<div class="text-center" style="font-weight:bold;"><h1>' . 'Жолоочийн мэдээлэл №' . '</h1></div><br><br>';
                $table .= '<div>' . 'Огноо:' . '</div>';
                $table .= '<div>' . date('Y-m-d H:i:s') . '</div></div>';
                $table .= '<table class="table table-striped  table-bordered" style="border-width: 1px;border-style: solid;border-color: black;">
                <thead>
                    <tr>
                    <th class="text-center whitespace-nowrap">#</th>
                    <th class="text-center whitespace-nowrap table-info">Жолоочийн нэр</th>
                    <th class="text-center whitespace-nowrap table-info">Утас</th>
                    <th class="text-center whitespace-nowrap table-info">Гэрийн хаяг</th>
                    <th class="text-center whitespace-nowrap table-warning">Идэвхитэй хүргэлтийн
                        тоо</th>
                    <th class="text-center whitespace-nowrap table-warning">Нийт хүргэлт</th>
                    <th class="text-center whitespace-nowrap table-warning">Цуцалсан хүргэлт
                    </th>
                    <th class="text-center whitespace-nowrap table-warning">Буцаасан хүргэлт
                    </th>
                    <th class="text-center whitespace-nowrap table-warning">Үнэлгээтэй хүргэлтын
                        тоо
                    </th>
                    <th class="text-center whitespace-nowrap table-success">Дундаж
                        үнэлгээ</th>
                    <th class="text-center whitespace-nowrap table-success">Нийт орлого</th>
                    </tr>
                </thead>
                <tbody>';
                foreach ($filteredData as $key => $row) {
                    $table .= "<tr>
                                <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ++$i . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->driver ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'></td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" .  ($row->address ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->huwiarlasan ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->hvrgegdsen ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->tsutsalsan ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'>" . ($row->butsaasan ?? '') . "</td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'></td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'></td>
                                    <td style='border-width: 1px;border-style: solid;border-color: black;'></td>
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

    //excel export driver
    public function excelExportDriver(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->excel)) {
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $arr_ids = explode(",", $request->post('ids'));
                $driverCounts = DB::table('deliveries')
                    ->join('users', 'users.name', '=', 'deliveries.driver')
                    ->select(
                        'users.id as userid',
                        'users.name as driver',
                        DB::raw('SUM(CASE WHEN deliveries.status = 3 THEN 1 ELSE 0 END) as hvrgegdsen'),
                        DB::raw('SUM(CASE WHEN deliveries.status = 4 THEN 1 ELSE 0 END) as tsutsalsan'),
                        DB::raw('SUM(CASE WHEN deliveries.status = 5 THEN 1 ELSE 0 END) as butsaasan'),
                        DB::raw('SUM(CASE WHEN deliveries.status = 2 THEN 1 ELSE 0 END) as huwiarlasan')
                    )
                    ->whereIn('deliveries.status', [3, 4, 5, 2])
                    ->groupBy('users.id', 'users.name')
                    ->get();

                $driverAddress = DB::table('users')
                    ->join('addresses as a', 'a.userid', '=', 'users.id')
                    ->where('role', 'driver')
                    ->where('active', 1)
                    ->select(
                        'users.id as id',
                        'users.name as driver',
                        'a.address'
                    )
                    ->get();

                $mergedData = $driverCounts->map(function ($item) use ($driverAddress) {
                    $matchingRow = $driverAddress->firstWhere('userid', $item->userid);
                    return (object) array_merge((array) $item, (array) $matchingRow);
                });
                $dataExcel = $mergedData->whereIn('userid', $arr_ids);

                $excel_data = array();



                if (is_array($dataExcel)) {
                    foreach ($dataExcel as $key => $row) {

                        $item = '';
                        $excel_data[$key]['driver'] =  ($row->driver ?? '');
                        $excel_data[$key]['huwiarlasan'] = ($row->huwiarlasan ?? '');
                    }
                }

                //       $export_request = new DeliveryExport($excel_data);

                $export = new DeliveryExport($excel_data);
                $excelFile = Excel::store($export, 'delivery.xlsx', 'public');

                return asset('storage/' . 'delivery.xlsx');
                // $excel = Excel::download($export_request, 'delivery.xlsx');
                // $excel->setContentDisposition('attachment','delivery')->getFile()->move(public_path('/delivery'), 'delivery'.time().'.xlsx');
                // return asset('delivery').'/delivery'.time().'.xlsx';
            }
        } else {
            dd('Err');
        }
    }


    //excel export driver requests
    public function excelExportDriverRequest(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->excel)) {
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $arr_ids = explode(",", $request->post('ids'));
                $excel_data = array();

                if (is_array($dataExcel)) {
                    foreach ($dataExcel as $key => $row) {
                        $item = '';
                        $excel_data[$key]['driver'] =  ($row->driver ?? '');
                        $excel_data[$key]['huwiarlasan'] = ($row->huwiarlasan ?? '');
                    }
                }

                $excelFile = Excel::store($export, 'delivery.xlsx', 'public');

                return asset('storage/' . 'delivery.xlsx');
            }
        } else {
            dd('Err');
        }
    }


    //Driver rating harah
    public function getDriverRating($driver)
    {
        $driverResult = DB::table('deliveries')
            ->select(
                'driver',
                'rating',
            )
            ->where('driver', $driver)
            ->get();
        $count = 0;
        $counter = 0;
        $averageRating = 0;
        foreach ($driverResult as $dr) {
            if ($dr->rating != null && $dr->rating != "null") {
                $r = (string)$dr->rating;
                $count = $count + intval($r);
                $counter++;
            }
        }
        if ($counter == 0) {
            $averageRating = 0;
        } else {
            $averageRating = $count / $counter;
        }
        return response()->json(['data' => $averageRating, 'success' => true]);
        //   dd($averageRating."/". $counter."/".$count);

    }
    //Driver orlogo tatah
    public function getDriverIncome(Request $request, $driver)
    {
        $driverResult = DB::table('deliveries')
            ->select(
                'driver',
                'deliveries.type',
                'deliveryprice',
                'merchant.merchantName',
                'users.name as userName',
                'users.engiin as en',
                'users.tsagtai as ts',
                'users.yaraltai as ya',
                'users.onts_yaraltai as o_ya'
            )
            ->join('merchant', 'deliveries.merchant_id', '=', 'merchant.id')
            ->join('users', 'merchant.user_id', '=', 'users.id')
            ->where(function ($query) {
                $query->where('status', 3)
                    ->orWhere('status', 5);
            })
            ->where('driver', $driver)
            ->where('income', 0)
            ->update(['income' => 1]);

        return response()->json(['data' => $driverResult, 'success' => true]);
        //   dd($averageRating."/". $counter."/".$count);

    }

    /**
     * Public home page: driver join request (web form, CSRF).
     */
    public function storeDriverJoinRequest(Request $request)
    {
        $validated = $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'comment' => 'nullable|string|max:2000',
            'gender' => 'nullable|string|max:32',
        ]);

        Driver::create($validated);

        return redirect()->route('welcome')->with('success', 'Жолоочоор элсэх хүсэлт амжилттай илгээгдлээ. Баярлалаа!');
    }

    //Шинэ жолооч хүсэлт (API — аль талбар ирсэн тэрээр хадгална)
    public function driverRequestApi(Request $request)
    {
        $driver = new Driver();
        $driver->lastname = $request->input('lastname');
        $driver->firstname = $request->input('firstname');
        $driver->phone = $request->input('phone');
        $driver->email = $request->input('email');
        $driver->city = $request->input('city');
        $driver->address = $request->input('address');
        $driver->comment = $request->input('comment');
        $driver->gender = $request->input('gender');
        $driver->save();

        return response()->json(['data' => $driver, 'message' => 'Хүсэлт амжилттай илгээлээ', 'success' => true]);
    }
    public function cityApi(Request $request)
    {
        $city = City::get();
        return response()->json(['data' => $city, 'success' => true]);
    }

    // Update driver location from mobile app
    public function updateDriverLocation(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $driver = User::where('name', $request->name)
                ->where('role', 'driver')
                ->where('active', 1)
                ->first();

            if (!$driver) {
                return response()->json([
                    'message' => 'Driver not found',
                    'success' => false
                ], 404);
            }

            // Update or create driver location
            DB::table('users')
                ->where('id', $driver->id)
                ->update([
                    'latitude' => (float) $request->latitude,
                    'longitude' => (float) $request->longitude,
                    'location_updated_at' => now(),
                ]);

            return response()->json([
                'message' => 'Location updated successfully',
                'success' => true,
                'data' => [
                    'driver_id' => $driver->id,
                    'name' => $driver->name,
                    'latitude' => (float) $request->latitude,
                    'longitude' => (float) $request->longitude,
                ]
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating driver location: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating location: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    // Get all driver locations for real-time tracking
    public function getDriverLocations(Request $request)
    {
        try {
            // Check if 'active' column exists in users table
            $hasActiveColumn = \Schema::hasColumn('users', 'active');
            $hasRoleColumn = \Schema::hasColumn('users', 'role');
            
            // Build query for users with location data
            // First, get all users with location data
            $query = User::whereNotNull('latitude')
                ->whereNotNull('longitude');
            
            // Filter by role if role column exists in users table
            if ($hasRoleColumn) {
                $query->where('role', 'driver');
            } else {
                // If no role column, use Spatie Permission relationship
                $query->whereHas('roles', function($q) {
                    $q->where('name', 'driver');
                });
            }
            
            // Only filter by active if the column exists
            if ($hasActiveColumn) {
                $query->where('active', 1);
            }
            
            // Get drivers with location data (don't select phone from users, it's in phones table)
            $drivers = $query->select('id', 'name', 'latitude', 'longitude', 'location_updated_at')
                ->get();

            // Debug: Log the query results
            Log::info('getDriverLocations query results', [
                'count' => $drivers->count(),
                'has_active_column' => $hasActiveColumn,
                'has_role_column' => $hasRoleColumn,
                'drivers' => $drivers->toArray()
            ]);

            // Initialize empty array - this will always be an array
            $locations = [];

            // Process each driver and build location array
            foreach ($drivers as $driver) {
                // Get phone number from phones table (phone is stored in separate table)
                $phoneRecord = DB::table('phones')
                    ->where('userid', $driver->id)
                    ->first();
                $phone = $phoneRecord ? $phoneRecord->phone : null;
                
                // Add driver location to array
                $locations[] = [
                    'id' => (int) $driver->id,
                    'name' => $driver->name ?? '',
                    'phone' => $phone ?? '',
                    'latitude' => (float) $driver->latitude,
                    'longitude' => (float) $driver->longitude,
                    'updated_at' => $driver->location_updated_at ? 
                        ($driver->location_updated_at instanceof \DateTime ? $driver->location_updated_at->toDateTimeString() : (string) $driver->location_updated_at) : 
                        null,
                ];
            }

            // Ensure $locations is always an array (double check)
            if (!is_array($locations)) {
                Log::warning('getDriverLocations: $locations is not an array!', [
                    'type' => gettype($locations),
                    'value' => $locations
                ]);
                $locations = [];
            }

            // Always return an array, even if empty
            // This ensures we never return 0 or any other non-array value
            $response = [
                'data' => $locations, // This is always an array
                'success' => true
            ];

            // Log the response before sending
            Log::info('getDriverLocations response', [
                'data_type' => gettype($response['data']),
                'data_count' => is_array($response['data']) ? count($response['data']) : 'not_array',
                'response' => $response
            ]);

            return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
            
        } catch (\Exception $e) {
            Log::error('Error in getDriverLocations: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            // Always return array, even on error
            return response()->json([
                'data' => [], // Always an array
                'success' => false,
                'message' => 'Error loading driver locations: ' . $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    // Get all active drivers (regardless of location data)
    public function getAllActiveDrivers(Request $request)
    {
        try {
            // Check if 'active' column exists in users table
            $hasActiveColumn = \Schema::hasColumn('users', 'active');
            $hasRoleColumn = \Schema::hasColumn('users', 'role');
            
            // Build query for all active drivers (not just those with location)
            $query = User::query();
            
            // Filter by role if role column exists in users table
            if ($hasRoleColumn) {
                $query->where('role', 'driver');
            } else {
                // If no role column, use Spatie Permission relationship
                $query->whereHas('roles', function($q) {
                    $q->where('name', 'driver');
                });
            }
            
            // Only filter by active if the column exists
            if ($hasActiveColumn) {
                $query->where('active', 1);
            }
            
            // Get all active drivers
            $drivers = $query->select('id', 'name', 'latitude', 'longitude', 'location_updated_at')
                ->orderBy('name', 'asc')
                ->get();

            // Debug: Log the query results
            Log::info('getAllActiveDrivers query results', [
                'count' => $drivers->count(),
                'has_active_column' => $hasActiveColumn,
                'has_role_column' => $hasRoleColumn,
            ]);

            // Initialize empty array
            $driversList = [];

            // Process each driver and build driver array
            foreach ($drivers as $driver) {
                // Get phone number from phones table (phone is stored in separate table)
                $phoneRecord = DB::table('phones')
                    ->where('userid', $driver->id)
                    ->first();
                $phone = $phoneRecord ? $phoneRecord->phone : null;
                
                // Add driver to array (include even if no location data)
                $driversList[] = [
                    'id' => (int) $driver->id,
                    'name' => $driver->name ?? '',
                    'phone' => $phone ?? '',
                    'latitude' => $driver->latitude ? (float) $driver->latitude : null,
                    'longitude' => $driver->longitude ? (float) $driver->longitude : null,
                    'updated_at' => $driver->location_updated_at ? 
                        ($driver->location_updated_at instanceof \DateTime ? $driver->location_updated_at->toDateTimeString() : (string) $driver->location_updated_at) : 
                        null,
                ];
            }

            // Always return an array, even if empty
            $response = [
                'data' => $driversList, // This is always an array
                'success' => true
            ];

            // Log the response before sending
            Log::info('getAllActiveDrivers response', [
                'data_count' => count($response['data']),
            ]);

            return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
            
        } catch (\Exception $e) {
            Log::error('Error in getAllActiveDrivers: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            // Always return array, even on error
            return response()->json([
                'data' => [], // Always an array
                'success' => false,
                'message' => 'Error loading active drivers: ' . $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * New Driver Monitoring Page
     */
    public function driverMonitoringNew()
    {
        return view('admin.driver.monitoring');
    }

    /**
     * Get drivers for autocomplete/search
     */
    public function getDriversForMonitoring(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            $query = User::query();
            
            // Filter by role
            if (Schema::hasColumn('users', 'role')) {
                $query->where('role', 'driver');
            } else {
                $query->whereHas('roles', function($q) {
                    $q->where('name', 'driver');
                });
            }
            
            // Filter by active status if column exists
            if (Schema::hasColumn('users', 'active')) {
                $query->where('active', 1);
            }
            
            // Search by name, phone, or ID
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('id', 'like', '%' . $search . '%');
                });
                
                // Also search in phones table
                $query->orWhereHas('phone', function($q) use ($search) {
                    $q->where('phone', 'like', '%' . $search . '%');
                });
            }
            
            $drivers = $query->with('phone')
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($driver) {
                    return [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        'phone' => $driver->phone ? $driver->phone->phone : null,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $drivers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading drivers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver monitoring data (summary statistics)
     */
    public function getDriverMonitoringData(Request $request)
    {
        try {
            $driverId = $request->get('driver_id');
            $driverName = $request->get('driver_name');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $status = $request->get('status');
            $merchantId = $request->get('merchant_id');
            
            if (!$driverId && !$driverName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver ID or name is required'
                ], 400);
            }
            
            // Get driver info
            if ($driverId) {
                $driver = User::find($driverId);
            } else {
                $driver = User::where('name', $driverName)->first();
            }
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }
            
            $driverName = $driver->name;
            
            // Build delivery query
            $query = Delivery::with('merchant')
                ->where('driver', $driverName);
            
            // Apply filters
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            if ($status) {
                $query->where('status', $status);
            }
            if ($merchantId) {
                $query->where('merchant_id', $merchantId);
            }
            
            // Get all deliveries for statistics
            $allDeliveries = (clone $query)->get();
            
            // Calculate statistics
            $stats = [
                'total_deliveries' => $allDeliveries->count(),
                'active_deliveries' => $allDeliveries->whereIn('status', [2, 6, 10])->count(), // Assigned, Pending, Received
                'completed_deliveries' => $allDeliveries->where('status', 3)->count(), // Delivered
                'cancelled_deliveries' => $allDeliveries->whereIn('status', [4, 5])->count(), // Cancelled, Returned
                'total_items_carrying' => 0,
                'total_items_delivered' => 0,
                'total_delivery_price' => 0,
            ];
            
            // Get driver items (items currently with driver)
            $driverItems = DB::table('driver_items')
                ->where('driver_id', $driver->id)
                ->join('items', 'driver_items.item_id', '=', 'items.id')
                ->select('driver_items.quantity', 'items.name')
                ->get();
            
            $stats['total_items_carrying'] = $driverItems->sum('quantity');
            
            // Count items in completed deliveries (approximate - using delivery count)
            $stats['total_items_delivered'] = $stats['completed_deliveries']; // Simplified for now
            // Sum deliveryprice from completed deliveries only (status = 3)
            $completedPriceQuery = Delivery::query()
                ->where('driver', $driverName)
                ->where('status', 3);

            if ($startDate) {
                $completedPriceQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $completedPriceQuery->whereDate('created_at', '<=', $endDate);
            }
            if ($merchantId) {
                $completedPriceQuery->where('merchant_id', $merchantId);
            }

            $stats['total_delivery_price'] = (float) ($completedPriceQuery->sum('deliveryprice') ?? 0);
            
            // Get driver phone
            $phone = DB::table('phones')->where('userid', $driver->id)->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'driver' => [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        'phone' => $phone ? $phone->phone : null,
                    ],
                    'statistics' => $stats,
                    'current_items' => $driverItems,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading monitoring data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get "total deliveries" breakdown grouped by shop (merchant).
     * Used by the right-side drawer on /admin/driver-monitoring
     */
    public function getDriverMonitoringShopsBreakdown(Request $request)
    {
        try {
            $driverId = $request->get('driver_id');
            $driverName = $request->get('driver_name');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $status = $request->get('status');
            $merchantId = $request->get('merchant_id');

            if (!$driverId && !$driverName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver ID or name is required'
                ], 400);
            }

            if ($driverId) {
                $driver = User::find($driverId);
            } else {
                $driver = User::where('name', $driverName)->first();
            }

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            $driverName = $driver->name;

            // Group deliveries by deliveries.shop
            $query = Delivery::query()
                ->select(
                    'deliveries.shop as shop_name',
                    DB::raw('COUNT(*) as deliveries_count'),
                    DB::raw('COALESCE(SUM(deliveries.deliveryprice), 0) as delivery_price_sum')
                )
                ->where('deliveries.driver', $driverName);

            if ($startDate) {
                $query->whereDate('deliveries.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('deliveries.created_at', '<=', $endDate);
            }
            // Drawer is for "Дууссан хүргэлт", so always use completed status only
            $query->where('deliveries.status', 3);
            if ($merchantId) {
                $query->where('deliveries.merchant_id', $merchantId);
            }

            $shops = $query
                ->groupBy('deliveries.shop')
                ->orderByDesc('deliveries_count')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'shops' => $shops->map(function ($row) {
                        return [
                            'shop_name' => $row->shop_name ?? '-',
                            'deliveries_count' => (int) $row->deliveries_count,
                            'delivery_price_sum' => (float) $row->delivery_price_sum,
                        ];
                    })->values(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading shops breakdown: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get one shop's completed deliveries grouped by deliveryprice.
     * Used by expanding a shop row in the drawer.
     */
    public function getDriverMonitoringShopPriceBreakdown(Request $request)
    {
        try {
            $driverId = $request->get('driver_id');
            $driverName = $request->get('driver_name');
            $shopName = $request->get('shop'); // deliveries.shop value
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $merchantId = $request->get('merchant_id');

            if (!$shopName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shop is required'
                ], 400);
            }

            if (!$driverId && !$driverName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver ID or name is required'
                ], 400);
            }

            if ($driverId) {
                $driver = User::find($driverId);
            } else {
                $driver = User::where('name', $driverName)->first();
            }

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            $driverName = $driver->name;

            $query = Delivery::query()
                ->select(
                    'deliveries.deliveryprice',
                    DB::raw('COUNT(*) as deliveries_count'),
                    DB::raw('COALESCE(SUM(deliveries.deliveryprice), 0) as delivery_price_sum')
                )
                ->where('deliveries.driver', $driverName)
                ->where('deliveries.status', 3) // completed only
                ->where('deliveries.shop', $shopName);

            if ($startDate) {
                $query->whereDate('deliveries.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('deliveries.created_at', '<=', $endDate);
            }
            if ($merchantId) {
                $query->where('deliveries.merchant_id', $merchantId);
            }

            $rows = $query
                ->groupBy('deliveries.deliveryprice')
                ->orderBy('deliveries.deliveryprice', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'shop' => $shopName,
                    'prices' => $rows->map(function ($row) {
                        return [
                            'deliveryprice' => (float) ($row->deliveryprice ?? 0),
                            'deliveries_count' => (int) $row->deliveries_count,
                            'delivery_price_sum' => (float) $row->delivery_price_sum,
                        ];
                    })->values(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading price breakdown: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver deliveries with pagination
     */
    public function getDriverDeliveries(Request $request)
    {
        try {
            $driverId = $request->get('driver_id');
            $driverName = $request->get('driver_name');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $status = $request->get('status');
            $merchantId = $request->get('merchant_id');
            $city = $request->get('city');
            
            if (!$driverId && !$driverName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver ID or name is required'
                ], 400);
            }
            
            // Get driver info
            if ($driverId) {
                $driver = User::find($driverId);
            } else {
                $driver = User::where('name', $driverName)->first();
            }
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }
            
            $driverName = $driver->name;
            
            // Build delivery query
            $query = Delivery::with('merchant')
                ->where('driver', $driverName);
            
            // Apply filters
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            if ($status) {
                $query->where('status', $status);
            }
            if ($merchantId) {
                $query->where('merchant_id', $merchantId);
            }
            if ($city) {
                $query->where('region', $city);
            }
            
            $totalRecords = $query->count();
            
            $table = Datatables::of($query)
                ->addColumn('delivery_id', function ($row) {
                    return $row->id;
                })
                ->addColumn('status_badge', function ($row) {
                    $statusMap = [
                        1 => ['text' => 'Бүртгэгдсэн', 'class' => 'badge badge-secondary'],
                        2 => ['text' => 'Хуваарилсан', 'class' => 'badge badge-info'],
                        3 => ['text' => 'Хүргэгдсэн', 'class' => 'badge badge-success'],
                        4 => ['text' => 'Цуцалсан', 'class' => 'badge badge-danger'],
                        5 => ['text' => 'Буцаасан', 'class' => 'badge badge-warning'],
                        6 => ['text' => 'Хүлээгдэж буй', 'class' => 'badge badge-primary'],
                        10 => ['text' => 'Хүлээн авсан', 'class' => 'badge badge-primary'],
                    ];
                    $statusInfo = $statusMap[$row->status] ?? ['text' => 'Тодорхойгүй', 'class' => 'badge badge-secondary'];
                    return '<span class="' . $statusInfo['class'] . '">' . $statusInfo['text'] . '</span>';
                })
                ->addColumn('merchant_name', function ($row) {
                    return $row->merchant ? $row->merchant->merchantName : '-';
                })
                ->addColumn('customer_address', function ($row) {
                    return $row->address ?? '-';
                })
                ->addColumn('assigned_date', function ($row) {
                    return $row->created_at ? date('Y-m-d H:i', strtotime($row->created_at)) : '-';
                })
                ->addColumn('completed_date', function ($row) {
                    if ($row->status == 3 && $row->updated_at) {
                        return date('Y-m-d H:i', strtotime($row->updated_at));
                    }
                    return '-';
                })
                ->addColumn('item_count', function ($row) {
                    // This is a placeholder - you may need to adjust based on your data structure
                    return '<button class="btn btn-sm btn-info view-items" data-delivery-id="' . $row->id . '">Харах</button>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . url('/delivery/detail/' . $row->id) . '" class="btn btn-sm btn-primary" target="_blank">Дэлгэрэнгүй</a>';
                })
                ->rawColumns(['status_badge', 'item_count', 'actions'])
                ->setTotalRecords($totalRecords)
                ->make(true);
            
            return $table;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading deliveries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get items for a specific delivery
     */
    public function getDeliveryItems($deliveryId)
    {
        try {
            $delivery = Delivery::find($deliveryId);
            
            if (!$delivery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery not found'
                ], 404);
            }
            
            $items = [];
            
            // Check if delivery has parcel_info or goodtype fields
            if (!empty($delivery->parcel_info)) {
                // Parse parcel_info if it contains item information
                // This might be JSON, comma-separated, or plain text
                $parcelInfo = $delivery->parcel_info;
                
                // Try to parse as JSON first
                $parsed = json_decode($parcelInfo, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                    // If it's valid JSON array, use it
                    foreach ($parsed as $item) {
                        if (is_array($item) && isset($item['name'])) {
                            $items[] = [
                                'name' => $item['name'],
                                'quantity' => $item['quantity'] ?? 1
                            ];
                        }
                    }
                } else {
                    // If not JSON, treat as text description
                    $items[] = [
                        'name' => $parcelInfo,
                        'quantity' => 1
                    ];
                }
            }
            
            // Also check goodtype field
            if (!empty($delivery->goodtype) && empty($items)) {
                $items[] = [
                    'name' => $delivery->goodtype,
                    'quantity' => 1
                ];
            }
            
            // If still no items, check if there's a delivery_items table
            if (empty($items) && Schema::hasTable('delivery_items')) {
                $deliveryItems = DB::table('delivery_items')
                    ->where('delivery_id', $deliveryId)
                    ->join('items', 'delivery_items.item_id', '=', 'items.id')
                    ->select('items.name', 'delivery_items.quantity')
                    ->get();
                
                foreach ($deliveryItems as $item) {
                    $items[] = [
                        'name' => $item->name,
                        'quantity' => $item->quantity
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'delivery_id' => $deliveryId,
                    'items' => $items,
                    'total_items' => count($items),
                    'parcel_info' => $delivery->parcel_info ?? null,
                    'goodtype' => $delivery->goodtype ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading items: ' . $e->getMessage()
            ], 500);
        }
    }
}