<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Phone;
use App\Models\Address;

use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
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
        return view('admin.user.index');
    }

    public function create(Request $request){ 
        $cookie_data = stripslashes(Cookie::get('phone_cart'));
        $phone_data = json_decode($cookie_data, true, 512, JSON_UNESCAPED_UNICODE);
        $address_data = stripslashes(Cookie::get('address_cart'));
        $address = json_decode($address_data, true, 512, JSON_UNESCAPED_UNICODE);
        $user = new User();
        $user->name = $request->name;
        $user->password=bcrypt($request->password);
        $user->role = $request->role;
        $user->save();
        if($phone_data){
            foreach($phone_data as $cdata){
                $order = new Phone();
                $order->userid = $user->id;
                $order->phone = urldecode($cdata['item_id']);
                $order -> save();  
            }
        }
        if($address){
            foreach($address as $cdata){
                $order = new Address();
                $order->userid = $user->id;
                $order->address = urldecode($cdata['item_id']);
            
                $order -> save();  
            }
        }
        return redirect('/user/list')->with('message','Амжилттай хадгалагдлаа');

    }

    public function list(){
        $ware=User::all();
        return view('admin.user.list',compact('ware'));
    }

    public function delete($id){
        $user = User::find($id);
        $user->delete();
        Alert::success('Хэрэглэгч', 'Амжилттай устгагдлаа');

        return redirect('/user/list')->with('message','deleted');
    }

    public function clearcart()
    {
        Cookie::queue(Cookie::forget('shopping_cart'));
        Cookie::queue(Cookie::forget('phone_cart'));
        Cookie::queue(Cookie::forget('address_cart'));
        return response()->json(['status' => 'Your Cart is Cleared']);
    }
    
    public function addaddresscart(Request $request)
    {
        $prod_id = $request->input('product_id');
     
        $quantity = $request->input('quantity');
        $product_name = $request->input('product_name');

        if(Cookie::get('address_cart'))
        {
            $cookie_data1 = stripslashes(Cookie::get('address_cart'));
            $cart_data1 = json_decode($cookie_data1, true);
        }
        else
        {
            $cart_data1 = array();
        }

    
        $item_id_list = array_column($cart_data1, 'item_id');
        $prod_id_is_there = $prod_id;

        if(in_array($prod_id_is_there, $item_id_list))
        {
            foreach($cart_data1 as $keys => $values)
            {
                if($cart_data1[$keys]["item_id"] == $prod_id)
                {
                    $cart_data1[$keys]["item_quantity"] = $request->input('quantity');
                    $cart_data1[$keys]["item_name"] = urldecode($request->input('product_name'));
                    $item_data = json_encode($cart_data1);
                    $minutes = 60;
                    // Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));
                    return response()->json(['status'=>'"'.$cart_data1[$keys]["item_name"].'" Уг бүтээгдэхүүн сагсанд байна']);
                }
            }
        }
        else
        {
          
            $address = $request->input('address');
          
           
                $item_array = array(
                    'item_id' => urlencode($address),
                  
                
                );
                $cart_data1[] = $item_array;
              
                $item_data = json_encode($cart_data1);
                $minutes = 60;
                Cookie::queue(Cookie::make('address_cart', $item_data, $minutes));
                return response()->json(['status'=>'"'.$address.'" сагсанд нэмэгдлээ']);
           
        }
    }


    public function cartDetailsAjaxAdd(){
        
        if(Cookie::get('address_cart')){
            $cookie_data = stripslashes(Cookie::get('address_cart'));
         $cart_data = json_decode($cookie_data, true);
           $total=0;
        $html ='<div class="col-md-7 ms-auto">
            <div class="cart-page-header"><h6 class="cart-page-header-title">Хаягийн жагсаалт</h6></div>
            <div class="d-flex flex-column gap-3">';
            
                foreach ($cart_data as $data){
                    $html .='<label class="order-card col-12 cartpage" data-cart-item-id="123" data-product-id="12">
                        <!-- <input class="order-card__input" type="checkbox" checked /> -->
                        <div class="order-card__body">
                    
                            <div class="product-row">
                        
                                <div class="product-row__content">
                                    <h6 class="product-row__content-title"><div style="width:200px;">'.urldecode($data['item_id']).'</div>  </h6>
                                    <div class="product-row__content-author">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </label>';
                }
                $html .='</div>
        </div>
        <div class="col-md-3 me-auto">
        <div class="cart-page__purchase">
            <div class="cart-page__purchase-lists">';
                foreach ($cart_data as $data):
                    $html .='<div class="cart-page__purchase-lists-item">
                </div>';
                endforeach;
            $html .='</div>
            <div class="cart-page__purchase-total">
                <div class="cart-page__purchase-total-item">
                
                </div>
            </div>
        </div>
        </div>';
        }else {
            $html .='<div class="row">
        </div>';
        }
        return $html;
}

    public function loadOrderDataTable(Request $request)
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

            $end_date = $request->get('end_date',0);
            $driverselected = $request->get('driver',0);
            $except_status = $request->get('except_status',0);
            $except_stat = $request->get('except_stat',0);

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
                'except_status' => $except_status,
                'except_stat' => $except_stat,

            ];

          
            $data = Order::GetExcelData($Params);
           
            $dataCount = Order::GetExcelDataCount($Params);
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
    public function addphonecart(Request $request)
    {
        $prod_id = $request->input('product_id');
     
        $quantity = $request->input('quantity');
        $product_name = $request->input('product_name');

        if(Cookie::get('phone_cart'))
        {
            $cookie_data = stripslashes(Cookie::get('phone_cart'));
            $cart_data = json_decode($cookie_data, true);
        }
        else
        {
            $cart_data = array();
        }
        $item_id_list = array_column($cart_data, 'item_id');
        $prod_id_is_there = $prod_id;

        if(in_array($prod_id_is_there, $item_id_list))
        {
            foreach($cart_data as $keys => $values)
            {
                if($cart_data[$keys]["item_id"] == $prod_id)
                {
                    $cart_data[$keys]["item_quantity"] = $request->input('quantity');
                    $cart_data[$keys]["item_name"] = urldecode($request->input('product_name'));
                    $item_data = json_encode($cart_data);
                    $minutes = 60;
                    // Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));
                    return response()->json(['status'=>'"'.$cart_data[$keys]["item_name"].'" Уг бүтээгдэхүүн сагсанд байна']);
                }
            }
        }
        else
        {
            $phone = $request->input('phone');
                $item_array = array(
                    'item_id' => $phone,
                );
                $cart_data[] = $item_array;
              
                $item_data = json_encode($cart_data);
                $minutes = 60;
                Cookie::queue(Cookie::make('phone_cart', $item_data, $minutes));
                return response()->json(['status'=>'"'.$phone.'" сагсанд нэмэгдлээ']);
        }
    }

    public function cartDetailsAjax(){
        
        if(Cookie::get('phone_cart')){
            $cookie_data = stripslashes(Cookie::get('phone_cart'));
         $cart_data = json_decode($cookie_data, true);
           $total=0;
        $html ='
        <div class="row" id="cart_details">
    
        <div class="col-md-7 ms-auto">
            <div class="cart-page-header"><h6 class="cart-page-header-title">Утасны жагсаалт</h6></div>
            <div class="d-flex flex-column gap-3">';
            
                foreach ($cart_data as $data){
                    $html .='            <div class="row" id="cart_details">
                    <label class="order-card col-12 cartpage" data-cart-item-id="123" data-product-id="12">
                        <!-- <input class="order-card__input" type="checkbox" checked /> -->
                        <div class="order-card__body">
                    
                            <input type="hidden" class="product_id" value="'. $data['item_id'] .'" >
                            <div class="product-row">
                        
                                <div class="product-row__content">
                                    <div class="product-row__content-title"><div style="width:200px;">'.urldecode($data['item_id']).'</div>  </div>
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
                    </div>
                    <script>
                    
                    $(".delete_cart_data_phone").click(function (e) {
    e.preventDefault();
    
    var product_id = $(this).closest(".cartpage").find(".product_id").val();
    var thisarea=$(this);
    
    var data = {
        "_token": $("input[name=_token]").val(),
        "product_id": product_id,
    };
    
    // $(this).closest(".cartpage").remove();
    
    $.ajax({
        url: "/clear-cart",
        type: "GET",
        data: data,
        success: function (response) {
            thisarea.closest(".cartpage").remove();
            $("#cart_details").html(response);
    
          
        }
    });
    });  
    
                </script>
                    ';
                }
                $html .='</div>
        </div>
    
    
        <div class="col-md-3 me-auto">
      
        <div class="cart-page__purchase">
            <div class="cart-page__purchase-lists">';
                foreach ($cart_data as $data):
                    $html .='<div class="cart-page__purchase-lists-item">
                
                </div>';
               
                endforeach;
            
            $html .='</div>
            <div class="cart-page__purchase-total">
                <div class="cart-page__purchase-total-item">
                
                </div>
            </div>
            
        </div>
        </div>';
        }else {
            $html .='<div class="row">
          
        </div>';
        }
        return $html;
    }   
      
}
