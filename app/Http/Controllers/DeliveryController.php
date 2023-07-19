<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Good;

class DeliveryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.delivery.index');
    }

    public function create(Request $request){ 
        
        $order = new Delivery();
        $order->shop = $request->shop;
        $order->phone = $request-> phone;
        $order->address = $request->address;
        $order->comment = $request-> comment;
        $order->save();
        return redirect('/delivery/new')->with('message','Амжилттай хадгалагдлаа');

    }

    public function addToCart($id)
    {
        $product = Product::findOrFail($id);
          
        $cart = session()->get('cart', []);
  
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price
            ];
        }
          
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }

    public function list(){
        return view('admin.delivery.list');
    }

    public function new(){
        return view('admin.delivery.new');
    }

    public function done(){
        return view('admin.delivery.done');
    }

    public function received(){
        return view('admin.delivery.received');
    }

    public function deleted(){
        return view('admin.delivery.deleted');
    }

    public function loadDeliveryDataTable(Request $request)
    {
        if ($request->ajax()) {
            $user_id = Auth::user()->id;
            $role = Auth::user()->role;
            $ids = $request->get('ids', array());
            $status = $request->get('status',0);
            $region = $request->get('region',0);
            $phone = $request->get('phone',0);
            $address = $request->get('address',0);
            $note = $request->get('note',0);
            $tuluv = $request->get('tuluv',0);
            $start_date = $request->get('start_date',0);
            $late = $request->get('late',0);
            $customer = $request->get('customer',0);
            $status_100 = $request->get('status_100',0);
            $end_date = $request->get('end_date',0);
            $driverselected = $request->get('driver',0);
            $except_status = $request->get('except_status',0);
            $except_stat = $request->get('except_stat',0);
            $status_10 = $request->get('status_10',0);
            $status_1 = $request->get('status_1',0);
            $status_6 = $request->get('status_6',0);
            $status_2 = $request->get('status_2',0);
            $status_3 = $request->get('status_3',0);
            $status_4 = $request->get('status_4',0);
            $status_5 = $request->get('status_5',0);
            $offset = $request->get('start', 0);
            $limit = $request->get('length', 10);
            if ($limit < 1 OR $limit > 500) {
                $limit = 500;
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
                 'actions'
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
                'tuluv' => $tuluv,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'region' => $region,
                'note' => $note,
                'late' => $late,
                'customer' => $customer,
                'phone' => $phone,
                'address' => $address,
                'driverselected' => $driverselected,
            ];
            $data = Delivery::GetExcelData($Params);
            $dataCount = Delivery::GetExcelDataCount($Params);
            $table = Datatables::of($data)
                        ->addColumn('checkbox', function ($row) {
                            return '<input type="checkbox" style="width:20px;height:20px;" class="checkbox" onclick="updateCount()" name="foo" data-id="'.$row->id.'" value="'.$row->id.'">';
                        })
                        ->addColumn('id', function ($row) {
                            return $row->id;
                        })
                        ->addColumn('shop', function ($row) {
                            return $row->shop;
                        })
                        ->addColumn('phone', function ($row) {
                            return $row->phone;
                        })
                        ->addColumn('address', function ($row) {
                            return $row->address;
                        })
                        ->addColumn('comment', function ($row) {
                            return  '
                            <input class="font-medium whitespace-nowrap input" id="note_'.$row->id.'"  style="width:80px;"  value="'.$row->comment.'" name="note"/>
                            <input type="hidden" value="'.$row->id.'" name="realid"> 
                            <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_note" >  Засах </button>
                            <a class="font-medium whitespace-nowrap"></a>
                       ';
                        })
                        ->addColumn('created_at', function ($row) {
                            return $row->created_at;
                        })
                        ->addColumn('status', function ($row) {
                            if($row->status==1){
                                return 'Бүртгэгдсэн';
                            }elseif($row->status==2){
                                return 'Жолоочид хуваарилсан';
                            }elseif($row->status==6) {
                                return 'Хүлээгдэж буй';
                            } elseif($row->status==3) {
                                return 'Жолооч хүлээн авсан';
                            }elseif($row->status==4) {
                                return 'Дууссан';
                            }                   
                        })
                        ->addColumn('region', function ($row) {
                            if(Auth::user()->role=='Customer'){
                                return '';
                            } else {
                                return $row->region;
                            }
                        })
                        ->addColumn('driver', function ($row) {
                                       if(Auth::user()->role=='Customer'){
                                           return '';
                                       } else {
                                           return $row->driver;
                                       }              
                        })
                        ->addColumn('actions', function ($row) {
                                    if(Auth::user()->role=='Customer')
                                        {
                                            $actions = '
                                        <div class="flex justify-center items-center">
                                            
                                            <a class="flex items-center text-theme-6" onclick="return confirm("Are you sure?")" href="'.url('/deliveries/delete/'.$row->id).'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 w-4 h-4 mr-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            Устгах</a>
                                        </div>
                                        ';
                                        } elseif(Auth::user()->role=='operator'){
                                            $actions = '
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center text-theme-9" href="'.url('/deliveries/detail/'.$row->id).'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-maximize-2 w-4 h-4 mr-1"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                                                Дэлгэрэнгүй  </a>
                                                </div>';
                                        }
                                        
                                        else {
                                            $actions = '
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center text-theme-9" href="'.url('/deliveries/detail/'.$row->id).'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-maximize-2 w-4 h-4 mr-1"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                                                Дэлгэрэнгүй  </a>
                                                <a class="flex items-center text-theme-6" onclick="return confirm("Are you sure?")" href="'.url('/deliveries/delete/'.$row->id).'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 w-4 h-4 mr-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                Устгах</a>
                                            </div>';
                                        }
                                        return $actions;
                        })
                        ->rawColumns(['checkbox','actions','comment'])
                        ->skipPaging()
                        ->setTotalRecords($dataCount)
                        ->make(true);
            return $table;
        }
    }
    
}
