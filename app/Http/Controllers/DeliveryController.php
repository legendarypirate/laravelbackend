<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Log;

use App\Models\Good;
use RealRashid\SweetAlert\Facades\Alert;

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

    public function delivery($name){
        $list=Delivery::where('driver',$name)->where('status',2)->get();
        return response()->json(['data'=>$list,'success'=>true]);
    }

    public function createdelivery(Request $request){
        $order=new Delivery();
        $order->shop=$request->name;
        $order->phone=$request->phone;
        $order->address=$request->address;
        $order->comment=$request->comment;
        $order->status=1;
        $order->track=rand(100000,999999).'S'.Auth::user()->id;
        $order->save();

        $log=new Log();
        $log->phone=$request->phone;
        $log->staff=$request->name;
        $log->value=$request->name.' '.$order->track.' дугаартай хүргэлт үүсгэлээ';
        $log->save();
        return response()->json(['data'=>$order,'success'=>true]);
    }
    public function donedelivery($name){
        $delivery=Delivery::where('driver', '=', $name)
        ->where(function ($query) {
            $query->where('status', "=", "3");
            $query->orWhere('status', "=", "4");
            $query->orWhere('status', "=", "5");
        })->orderBy('deliveries.id','DESC')->get();
        return response()->json([
           'success' => true,
           'message' => 'Амжилттай',
           'data'=>$delivery
       ], 200);
    }

    public function write(Request $request){
        $order=Delivery::find($request->id);
        $order->comment=$request->comm;
        $order->save();
        return response()->json(['data'=>$order,'success'=>true]);
    }

    public function decline_delivery(Request $request){
      
        $delivery = Delivery::find($request->id);
        $delivery->status=$request->status;
        if($request->status=="Цуцалсан"){
            $delivery->status=4;
            $delivery->note=$request->comm;
            $delivery->received=0;
            $delivery->save(); 

            $log=new Log();
            $log->phone=$delivery->phone;
            $log->staff=$delivery->shop;
            $log->value=$delivery->name.' '.$delivery->track.' дугаартай хүргэлт цуцалсан төлөвт орууллаа';
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
            
        } elseif($request->status=="Буцаасан") {
            $delivery->status=5;
            $delivery->note=$request->comm;
            $delivery->received=0;
            $delivery->save();
          
            $log=new Log();
            $log->phone=$delivery->phone;
            $log->staff=$delivery->shop;
            $log->value=$delivery->name.' '.$delivery->track.' дугаартай хүргэлт Буцаасан төлөвт орууллаа';
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
                $delivery->status=6;
                $delivery->note=$request->comm;

                if($request->status=='Утсаа аваагүй'){
                        $delivery->note=$request->status;
                        $delivery->save();
                      
                } elseif($request->status=='Хэрэглэгч хойшлуулсан') {
                    $delivery->note=$request->status;
                    $delivery->save();
               
                } elseif($request->status=='Хаяг солигдсон'){
                    $delivery->note=$request->status;
                    $delivery->save();
               
                } elseif($request->status=='Хаасан байсан'){
                    $delivery->note=$request->status;
                    $delivery->save();
               
                } elseif($request->status=='Жолоочийн машинд асуудал гарсан'){
                    $delivery->note=$request->status;
                    $delivery->save();

                } else {
                    $delivery->note=$request->status;
                    $delivery->save();
                }     
    }
    
    
    return response()->json([
        'success' => true,
        'message' => 'Амжилттай',
        'data'=>$delivery
    ], 200);
      
    }


    public function deliverydetail($id){
        $list=Delivery::where('id',$id)->get();
        return response()->json(['data'=>$list,'success'=>true]);
    }

    public function create(Request $request){ 
        $cookie_data = stripslashes(Cookie::get('shopping_cart'));
        $cart_data = json_decode($cookie_data, true, 512, JSON_UNESCAPED_UNICODE);
        $order = new Delivery();
        $order->shop = $request->shop;
        $order->phone = $request-> phone;
        $order->address = $request->address;
        $order->comment = $request-> comment;
        $order->receivername = $request-> receivername;
        $order->size = $request-> size;
        $order->number = $request-> number;
        $order->price = $request-> price;
        $order->goodtype = $request-> goodtype;
        $order->verified = 0;
        $order->track=rand(100000,999999).'S'.Auth::user()->id;
        $order->status = 1;
        $order->save();

        $log=new Log();
        $log->phone=$request->phone;
        $log->staff=Auth::user()->name;
        $log->value=Auth::user()->name.' '.$order->track.' дугаартай захиалаг үүсгэлээ';
        $log->save();

        $psum=0;
        if($cart_data){
        foreach($cart_data as $cdata){
            // $order = new Order();
            // $order->reqid = $blackEntry->id;
            // $order->good = urldecode($cdata['item_name']);
            // $order->price=$cdata['item_price'];
            // $order->count=$cdata['item_quantity'];
            // $order->status=1;
            // $order->sid=Auth::user()->id;
            // $order->staff=Auth::user()->name;
            // $order -> save();
            $updatedgood=Good::find($cdata['item_id']);
            $updatedgood->count=Good::where('id','=',$cdata['item_id'])->first()->count-$cdata['item_quantity'];
            $updatedgood->indelivery=Good::where('id','=',$cdata['item_id'])->first()->indelivery+$cdata['item_quantity'];
            $updatedgood->save();
            // $ware = new Ware();
            // $ware->goodid = urldecode($cdata['item_id']);
            // $ware->deliverid = $blackEntry->tracking;
            // $ware->custname = $blackEntry->custname;
            // $ware->phone = $blackEntry->phone;
            // $ware->goodname = urldecode($cdata['item_name']);
            // $ware->count=$cdata['item_quantity'];
            // $ware->status=1;
            // $ware -> save();
            $psum+=$cdata['item_price']*$cdata['item_quantity'];
            $order->price=$psum;
            $order->received=$psum;
            $order->save();
            
        }
     }
        Cookie::queue(Cookie::forget('shopping_cart'));
        Cookie::queue(Cookie::forget('phone_cart'));
        Cookie::queue(Cookie::forget('address_cart'));
        return redirect('/delivery/new')->with('message','Амжилттай хадгалагдлаа');

    }

    public function good($shop){
        echo json_encode(DB::table('goods')->where('shop', $shop)->get());
    }
 
    
    public function addtocart(Request $request)
    {
        $prod_id = $request->input('product_id');
     
        $quantity = $request->input('quantity');
        $product_name = $request->input('product_name');

        if(Cookie::get('shopping_cart'))
        {
            $cookie_data = stripslashes(Cookie::get('shopping_cart'));
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
            $products = Good::find($prod_id);
            $prod_name = $products['goodname'];
            $priceval = $products['price'];
            $quantity = $request->input('quantity');
            if($products)
            {
                $item_array = array(
                    'item_id' => $prod_id,
                    'item_name' => urlencode($prod_name),
                    'item_quantity' => $quantity,
                    'item_price' => $priceval,
                
                );
                $cart_data[] = $item_array;
              
                $item_data = json_encode($cart_data);
                $minutes = 60;
                Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));
                return response()->json(['status'=>'"'.$prod_name.'" сагсанд нэмэгдлээ']);
            }
        }
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

    public function deliveryshop($name){
        $list=Delivery::where('shop',$name)->get();
        return response()->json(['data'=>$list,'success'=>true]);
    }

    public function done(){
        return view('admin.delivery.done');
    }

    public function received(){
        return view('admin.delivery.received');
    }

    public function report(){
        return view('admin.delivery.report');
    }

    public function deleted(){
        return view('admin.delivery.deleted');
    }

    public function delivered_delivery($id){
        $delivery=Delivery::find($id);
        $delivery->status=3;
        $delivery->deliveryprice=5000;
        $delivery->save();

        $log=new Log();
        $log->phone=$delivery->phone;
        $log->staff=$delivery->shop;
        $log->value=$delivery->shop.' '.$delivery->track.' дугаартай захиалаг үүсгэлээ';
        $log->save();

        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data'=>$delivery
        ], 200);
    }

    public function change_status_on_delivery(Request $request){

        $data = array();
        $data['status'] = 0;
    
        if($request->ids && $request->status){
            $ids = explode(',',$request->ids);
            $dddd=Delivery::whereIn('id',$ids)->where('verified','0')->count();
            $dddds=Delivery::whereIn('id',$ids)->where('driver',NULL)->count();
          
            if($request->status==10){
                // $data=Order::where('reqid','=',$ids)->get();
                $data['status'] = 1;
                $data['message'] = "Success";
         
                if($request->status==3){
                    foreach($data as $datas){
                        $good=Good::where('goodname',$datas->good)->first();
                        $good->inprogress=$good->inprogress-$datas->count;
                        $good->delivered=$good->delivered+$datas->count;
                        $good->save();
                    }
                }
             
                $array_ids = array_filter(explode(',',$request->ids));
                $ids= implode(',',$array_ids);
                $idss = explode(',',$request->ids);
                Delivery::whereIn('id',$idss)->update(['status'=>'10']);
                for($i=0; $i<count($array_ids);$i++){
                    $dddd=Delivery::where('id','=',$array_ids[$i])->first();
                    $log = new Log();
                    $log -> value = Auth::user()->name.', нь'.$dddd["track"].' ID-тай хүргэлтийг хүлээн авсан төлөвт орууллаа.';
                    $log -> phone = $dddd['phone'];
                    $log->staff=Auth::user()->name;
                    $log -> save();
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
            }
            elseif($dddd>0||$dddds>0)
            {
                // dd('www');
                Alert::error('Хүргэлт', 'Баталгаажаагүй эсвэл жолоочгүй хүргэлт байна');
            } else {
                // dd('qqq');
                $idss = explode(',',$request->ids);
                $array_ids = array_filter(explode(',',$request->ids));
                // $data=Order::where('reqid','=',$ids)->get();
                $data['status'] = 1;
                $data['message'] = "Success";
                // $data=Order::where('reqid','=',$ids)->get();
                if($request->status==3){
                    $ids= implode(',',$array_ids);
                    $array_ids = array_filter(explode(',',$request->ids));

                    Delivery::whereIn('id',$idss)->update(['status'=>'3']);
               
                    $cc=Delivery::whereIn('id',$array_ids)->get();
                    // $data=Order::where('reqid','=',$ids)->get();
                    $data1=Delivery::whereIn('id',$array_ids)->get();
                    $arr_ware = array();
                    $arr_tracking = array();
                    for($i=0; $i<count($array_ids);$i++){
                        $dddd=Delivery::where('id','=',$array_ids[$i])->first();
                        $arr_tracking[]=$dddd['track'];
                        $phone=$dddd['phone'];
                       
                        $log = new Log();
                        $log -> value = Auth::user()->name.', нь'.$dddd["track"].' ID-тай хүргэлтийг хүргэсэн төлөвт орууллаа.';
                        $log -> phone = $dddd['phone'];
                        $log->staff=Auth::user()->name;
                        $log -> save();
                    }   
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
                 $idss = explode(',',$request->ids);
                if($request->status==3)
                {
                    Delivery::whereIn('id',$idss)->update(['deliveryprice'=>5000]);
                } else {
                    Delivery::whereIn('id',$idss)->update(['deliveryprice'=>0]);
                }
                if($request->status==6){
                    $array_ids = array_filter(explode(',',$request->ids));
                    $ids= implode(',',$array_ids);
                    $idss = explode(',',$request->ids);
                    Delivery::whereIn('id',$idss)->update(['status'=>'6']);
                    $cc=Delivery::whereIn('id',$array_ids)->get();
                    // $data=Order::where('reqid','=',$ids)->get();
                    $data1=Delivery::whereIn('id',$array_ids)->get();
                    $arr_ware = array();
                 
                    $arr_tracking = array();
                    for($i=0; $i<count($array_ids);$i++){
                        // Req::where('id','=',$array_ids[$i])->delete();
                        $dddd=Delivery::where('id','=',$array_ids[$i])->first();
                        $arr_tracking[]=$dddd['track'];
                        $phone=$dddd['phone'];
                       
                        $log = new Log();
                        $log -> value = Auth::user()->name.', нь'.$dddd["track"].' ID-тай хүргэлтийг хүлээгдэж буй төлөвт орууллаа.';
                        $log -> phone = $dddd['phone'];
                        $log->staff=Auth::user()->name;
                        $log -> save();
                    }   
            
                }
                if($request->status==2)
                {   
                    $array_ids = array_filter(explode(',',$request->ids));
                    $ids= implode(',',$array_ids);
                  
                    $idss = explode(',',$request->ids);
                    Delivery::whereIn('id',$idss)->update(['status'=>'2']);
                    for($i=0; $i<count($array_ids);$i++){
                        $dddd=Delivery::where('id','=',$array_ids[$i])->first();
                        $log = new Log();
                        $log -> value = Auth::user()->name.', нь'.$dddd["track"].' ID-тай хүргэлтийг жолоочид хуваарилсан төлөвт орууллаа.';
                        $log -> phone = $dddd['phone'];
                        $log->staff=Auth::user()->name;
                        $log -> save();
                    }
                }
                if($request->status==4||$request->status==5||$request->status==6){
                 
                $array_ids = array_filter(explode(',',$request->ids));
                $ids= implode(',',$array_ids);
                // Req::whereIn('id',$ids)->update(['status'=>'8']);
                Delivery::where('id',$ids)->orWhere('status','4')->orWhere('status','6')->update(['deliveryprice'=>'0']);
                $cc=Delivery::whereIn('id',$array_ids)->get();
                Delivery::where('id',$ids)->orWhere('status','4')->orWhere('status','5')->orWhere('status','6')->update(['received'=>'0']);
                // $data=Order::where('reqid','=',$ids)->get();
                $data1=Delivery::whereIn('id',$array_ids)->get();
                $arr_ware = array();
                $arr_tracking = array();
                for($i=0; $i<count($array_ids);$i++){
                    // Req::where('id','=',$array_ids[$i])->delete();
                     $dddd=Delivery::where('id','=',$array_ids[$i])->first();
                     $arr_tracking[]=$dddd['track'];
                    if($request->status==4){
                        $idss = explode(',',$request->ids);
                        Delivery::whereIn('id',$idss)->update(['status'=>'4']);
                        $cust=$dddd['custname'];
                        $phone=$dddd['phone'];
                        $log = new Log();
                        $log -> value = Auth::user()->name.', нь'.$dddd["track"].' ID-тай хүргэлтийг цуцалсан төлөвт орууллаа.';
                        $log -> phone = $dddd['phone'];
                        $log->staff=Auth::user()->name;
                        $log -> save();
                    }
                    if($request->status==5){
                        $idss = explode(',',$request->ids);
                        Delivery::whereIn('id',$idss)->update(['status'=>'5']);
                        $cust=$dddd['custname'];
                        $phone=$dddd['phone'];
                       
                        $log = new Log();
                        $log -> value = Auth::user()->name.', нь'.$dddd["track"].' ID-тай хүргэлтийг буцаасан төлөвт орууллаа.';
                        $log -> phone = $dddd['phone'];
                        $log->staff=Auth::user()->name;
                        $log -> save();
                    }

                }   
                // $qqq=Ware::where('deliverid',$arr_tracking)->get();
                    // $wareg= Ware::whereIn('deliverid',$arr_tracking)->get();
                    $arr_goodid = array();
                    for($i=0; $i<count($arr_tracking);$i++){
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

   
    public function change_bus_on_delivery(Request $request){

        $data = array();
        $data['status'] = 0;
        if($request->ids && $request->region){
            $ids = explode(',',$request->ids);
            Delivery::whereIn('id',$ids)->update(['region'=>$request->region]);
            $data['region'] = 1;
            $data['message'] = "Success";
        }
        Alert::success('Хүргэлт', 'Бүс солигдлоо');
        return json_encode($data);
    }

    public function cartDetailsAjaxS(){
        
        if(Cookie::get('shopping_cart')){
            $cookie_data = stripslashes(Cookie::get('shopping_cart'));
         $cart_data = json_decode($cookie_data, true);
           $total=0;
        $html ='<div class="col-md-7 ms-auto">
            <div class="cart-page-header"><h6 class="cart-page-header-title">Order list</h6></div>
            <div class="d-flex flex-column gap-3">';
            
                foreach ($cart_data as $data){
                    $html .='<label class="order-card col-12 cartpage" data-cart-item-id="123" data-product-id="12">
                        <!-- <input class="order-card__input" type="checkbox" checked /> -->
                        <div class="order-card__body">
                    
                            <input type="hidden" class="product_id" value="'. $data['item_id'] .'" >
                            <div class="product-row">
                        
                                <div class="product-row__content">
                                    <h6 class="product-row__content-title"><div style="width:200px;">'.urldecode($data['item_name']).'</div>  Тоо:'. number_format($data['item_quantity']) .' <div style="display:inline;margin-left:50px;">Үнэ:'. number_format($data['item_price']) .'</div></h6>
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
                $html .='</div>
        </div>
    
    
        <div class="col-md-3 me-auto">
      
        <div class="cart-page__purchase">
            <div class="cart-page__purchase-lists">';
                foreach ($cart_data as $data):
                    $html .='<div class="cart-page__purchase-lists-item">
                
                </div>';
                   $total = $total + ( $data["item_price"] * $data["item_quantity"]);
                endforeach;
            
            $html .='</div>
            <div class="cart-page__purchase-total">
                <div class="cart-page__purchase-total-item">
                    <div class="span">Total sum:</div>
                    <div class="total-price">₮ '. number_format($total, 2) .'</div>
                </div>
            </div>
            
        </div>
        </div>';
        }else {
            $html .='<div class="row">
            <div class="col-md-12 mycard py-5 text-center">
                <div class="mycards">
                    <h4>Таны сагс одоогоор хоосон байна.</h4>
                
                </div>
            </div>
        </div>';
        }
    
    
        return $html;
    }
    

    public function change_driver_on_delivery(Request $request){
   
      
        $data = array();
        $data['status'] = 0;
        $array_ids = array_filter(explode(',',$request->ids));
        $arr_tracking = array();
     
        $ids = explode(',',$request->ids);
        Delivery::whereIn('id',$ids)->update(['driver'=>$request->driverselected]);
        Delivery::whereIn('id',$ids)->update(['status'=>'2']);

        $data['driverselected'] = 1;
        $data['message'] = "Success";

            // for($i=0; $i<count($array_ids);$i++){
            //     $dddd=Delivery::where('id','=',$array_ids[$i])->first();
               
                
            //     $arr_tracking[] = $dddd['organization'];

            //     $SERVER_API_KEY = 'AAAA2aRXNbE:APA91bFEfJsbgOnLV7Y3VWKRNhyR7TX8hrXjO6YxKbp5CDBqFJDhvYddPfRUx38-0mi9UMPO5uoasAmesn2HfLIPtd5kky34WbsDXzDwG3UR7JSVlUy5NiWJKKpCCoACPkazcpkGeQS6';

            //     $tk=Token::where('userid',$request->driverselected)->latest()->first();
            //     if($tk){
            //     $tkn=$tk->token;
            //     $token_1 = $tkn;
            //     $ssq='Танд '.$dddd["organization"].' дэлгүүрээс захиалга ирлээ';
            //     $data = [
            
            //         "registration_ids" => [
            //             $token_1
            //         ],
            
            //         "notification" => [
            
            //             "title" => 'Захиалга',
            
            //             "body" => $ssq,
            
            //             "sound"=> "default" // required for sound on ios
            
            //         ],
            
            //     ];
            
            //     $dataString = json_encode($data);
            
            //     $headers = [
            
            //         'Authorization: key=' . $SERVER_API_KEY,
            
            //         'Content-Type: application/json',
            
            //     ];
            
            //     $ch = curl_init();
            
            //     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            
            //     curl_setopt($ch, CURLOPT_POST, true);
            
            //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            //     curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            
            //     $response = curl_exec($ch);
            // }
            
               
            // }   
         
        Alert::success('Захиалга', 'Жолооч солигдлоо');

        // $dds= implode(",",$arr_tracking);
               
        // $log = new Log();
        // $log -> desc = Auth::user()->name.' ажилтан нь '.$dds.' захиалгыг '.$request->driverselected.'-д хуваариллаа';
        // $log->staff=Auth::user()->name;
        // $log->value='';

        // $log -> save();
        return json_encode($data);
    }


    public function change_verify_on_delivery(Request $request){

        $data = array();
        $data['status'] = 0;

        if($request->ids){
            $ids = explode(',',$request->ids);
            $array_ids = array_filter(explode(',',$request->ids));
            if($request->verified==1){
            for($i=0; $i<count($array_ids);$i++){
                // $delivery=Delivery::where('id','=',$array_ids[$i])->first();
           
                // $log = new Log();
                // $log -> desc = Auth::user()->name.', нь '.$dddd["tracking"].' ID-тай хүргэлтийг баталгаажууллаа.';
                // $log -> phone = $dddd['phone'];
                // $log -> value = $dddd['tracking'];
                // $log->staff=Auth::user()->name;
                // $log -> save();
            }
        }  else {
            for($i=0; $i<count($array_ids);$i++){
                // $delivery=Delivery::where('id','=',$array_ids[$i])->first();
            
                // $log = new Log();
                // $log -> desc = Auth::user()->name.', нь '.$dddd["tracking"].' ID-тай хүргэлтийг баталсныг цуцаллаа.';
                // $log -> phone = $dddd['phone'];
                // $log -> value = $dddd['tracking'];
                // $log->staff=Auth::user()->name;
                // $log -> save();
            }
        }

        Delivery::whereIn('id',$ids)->update(['verified'=>$request->verified]);

            // $req=Delivery::where('id',$ids)->get();
            // $good = Ware::where('deliverid',$req[0]['tracking'])->get();
            // foreach($good as $goods){
            //     $goods->verify=1;
            //     $goods->save();
            // }
            $data['status'] = 1;
            $data['message'] = "Success";
        }
        Alert::success('Хүргэлт', 'Баталгаажлаа');
     
        return json_encode($data);
    }

    public function change_delete_on_delivery(Request $request){

        $data = array();
        $data['status'] = 0;
        $ids = explode(',',$request->ids);
        $dddd=Delivery::whereIn('id',$ids)->where('verified','1')->count();
        $st=Delivery::whereIn('id',$ids)->where('status','10')->count();
        if($dddd>0||$st>0){
         Alert::error('Хүргэлт', 'Баталгаажсан хүргэлт устгах боломжгүй');
        } else {
         if($request->ids){
             $array_ids = array_filter(explode(',',$request->ids));
             $ids= implode(',',$array_ids);
             // Req::whereIn('id',$ids)->update(['status'=>'8']);
             Delivery::where('id',$ids)->orWhere('status','4')->orWhere('status','5')->update(['deliveryprice'=>'0']);
             $cc=Delivery::whereIn('id',$array_ids)->get();
             $data['status'] = 1;
             $data['message'] = "Success";
            //  $data=Order::where('reqid','=',$ids)->get();
             $data1=Delivery::whereIn('id',$array_ids)->get();
             $arr_ware = array();
             $arr_tracking = array();
             for($i=0; $i<count($array_ids);$i++){
                 // Req::where('id','=',$array_ids[$i])->delete();
                 $dddd=Delivery::where('id','=',$array_ids[$i])->first();
                 $dddd->status=100;
                 $dddd->save();
                //  $arr_tracking[]=$dddd['tracking'];
                 $log = new Log();
                 $log -> value = Auth::user()->name.', нь'.$dddd["track"].' ID-тай хүргэлтийг устгалаа.';
                 $log -> phone = $dddd['phone'];
                 $log->staff=Auth::user()->name;
                 $log -> save();
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
            if($file){
                Excel::import(new RequestImportExcel,$file); 
                return back();        
            }else{
                return back()->with('error', 'Please Select File');
            }
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
            $not_3 = $request->get('not_3',0);
            $not_4 = $request->get('not_4',0);
            $not_2 = $request->get('not_2',0);
            $not_6 = $request->get('not_6',0);
            $not_5 = $request->get('not_5',0);
            $not_1 = $request->get('not_1',0);
            $not_100 = $request->get('not_100',0);
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
                'not_5' => $not_5,
                'not_4' => $not_4,
                'not_3' => $not_3,
                'not_1' => $not_1,
                'not_2' => $not_2,
                'not_6' => $not_6,
                'not_100' => $not_100,
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
                        ->addColumn('verified', function ($row) {
                            if($row->verified==1){
                                return 'Тийм';
                            } else {
                                return 'Үгүй';
                            }
                        })
                        ->addColumn('status', function ($row) {
                            if($row->status==1){
                                return 'Бүртгэгдсэн';
                            }elseif($row->status==2){
                                return 'Жолоочид хуваарилсан';
                            }elseif($row->status==6) {
                                return 'Хүлээгдэж буй';
                            } elseif($row->status==3) {
                                return 'Хүргэгдсэн';
                            }elseif($row->status==4) {
                                return 'Цуцалсан';
                            }elseif($row->status==5) {
                                return 'Буцаасан';
                            }
                            elseif($row->status==4) {
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

    public function ExcelExport(Request $request)
    {
        if ($request->ajax()) {
            if(isset($request->excel)){
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $arr_ids = explode(",",$request->post('ids'));
                $ids = implode(",",array_filter($arr_ids));
                $excel = $request->get('excel', 0);
                $Params = [
                'ids' => $ids,
                'user_id' => $user_id,
                'role' => $role,
                ];
                $excel_data = array();
                $dataExcel = Delivery::GetExcelData($Params);
                $excel_data = [];
                foreach($dataExcel as $key=>$row)
                    {
                        $item='';
                        $excel_data[$key]['id']= $row->id;
                        $excel_data[$key]['organization']= $row->organization;
                        $excel_data[$key]['organization']= $row->organization;
                        $excel_data[$key]['phone']= $row->phone;
                        $excel_data[$key]['address']= $row->address;
                        $excel_data[$key]['created_at']= $row->created_at;
                        if($row->status==1){
                            $excel_data[$key]['status']='Бүртгэгдсэн';
                        }elseif($row->status==2){
                            $excel_data[$key]['status']='Жолоочид хуваарилсан';
                        }elseif($row->status==3){
                            $excel_data[$key]['status']='Хүргэгдсэн';
                        }elseif($row->status==4){
                            $excel_data[$key]['status']='Цуцалсан';
                        }elseif($row->status==5){
                            $excel_data[$key]['status']='Буцаасан';
                        }elseif($row->status==6){
                            $excel_data[$key]['status']='Хүлээгдэж буй';
                        }elseif($row->status==10){
                            $excel_data[$key]['status']='Хүлээн авсан';
                        }
                        $excel_data[$key]['driverselected']= $row->driverselected;
                        $excel_data[$key]['tracking']= $row->tracking;
                        $excel_data[$key]['city']= $row->city;
                        $excel_data[$key]['dist']= $row->dist;
                        $excel_data[$key]['khoroo']= $row->khoroo;
                        $excel_data[$key]['khotkhon']= $row->khotkhon;
                        $excel_data[$key]['street']= $row->street;
                        $excel_data[$key]['orts']= $row->orts;
                        $excel_data[$key]['code']= $row->code;
                        $excel_data[$key]['floor']= $row->floor;
                        $excel_data[$key]['toot']= $row->toot;
                        $excel_data[$key]['price']= $row->price;
                        $excel_data[$key]['received']= $row->received;
                        $excel_data[$key]['deliveryprice']= $row->deliveryprice;

                        $excel_data[$key]['comm']= $row->comm;
                        $excel_data[$key]['note']= $row->note;
                        $excel_data[$key]['comm3']= $row->comm3;
                        $excel_data[$key]['updated_at']= $row->updated_at;
                        $good=Ware::where('deliverid','=',$row->tracking)->get();
                        foreach($good as $goods){
                            $item.=$goods->goodname.' '.$goods->count.",";
                        }
                        $excel_data[$key]['item']=$item;
                    }
                $export_request = new DeliveryExport($excel_data);
                $excel = Excel::download($export_request, 'req.xlsx');
                $excel->setContentDisposition('attachment','req')->getFile()->move(public_path('/req'), 'req'.time().'.xlsx');
                return asset('req').'/req'.time().'.xlsx';
            }
        }
    }

    
    public function PrintdeliveryData(Request $request)
    {
        if ($request->ajax()) {
            if(isset($request->print)){
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $arr_ids = explode(",",$request->post('ids'));
                $ids = implode(",",array_filter($arr_ids));
                $print = $request->get('print', 0);
                
                $Params = [
                    'ids' => $ids,
                'user_id' => $user_id,
                'role' => $role,
                ];
                $i=0;
                $print_data = array();
                $dataExcel = Delivery::GetExcelData($Params);

                $table = '<table class="table table-striped  table-bordered" style="border-width: 1px;border-style: solid;border-color: black;">
                <thead>
                    <tr>
                    <th class="text-center whitespace-nowrap">#</th>

                        <th class="text-center whitespace-nowrap">Үүссэн цаг</th>
                        <th class="whitespace-nowrap">Нэр</th>
                        <th class="text-center whitespace-nowrap">Утас</th>
                        <th class="text-center whitespace-nowrap">Хаягийн мэдээлэл</th>
                        <th class="text-center whitespace-nowrap">Барааны тоо</th>
                      
                        <th class="text-center whitespace-nowrap">Жолооч</th>
                    </tr>
                </thead>
                <tbody>';
                foreach($dataExcel as $key=>$row)
                    {
                        $table .= "<tr>
                            <td style='border-width: 1px;border-style: solid;border-color: black;'>".++$i."</td>
                           <td style='border-width: 1px;border-style: solid;border-color: black;'>".$row->created_at."</td>
                           <td style='border-width: 1px;border-style: solid;border-color: black;'>".$row->shop."</td>
                           <td style='border-width: 1px;border-style: solid;border-color: black;'>".$row->phone."</td>
                           <td style='border-width: 1px;border-style: solid;border-color: black;'>".$row->address."</td>
                           <td style='border-width: 1px;border-style: solid;border-color: black;'>".$row->comment."</td>
                        
                           <td style='border-width: 1px;border-style: solid;border-color: black;'>".$row->driver."</td>
                           </tr>";
                    }
                echo $table;
            }
        }
    }
}
