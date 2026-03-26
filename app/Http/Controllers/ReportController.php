<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Good;
use App\Models\User;
use App\Models\Log;
use Carbon\Carbon;
use App\Models\General;

use RealRashid\SweetAlert\Facades\Alert;

class ReportController extends Controller
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
    public function driver()
    {
        return view('admin.report.driver');
    }

    public function driverdone()
    {
        return view('admin.report.driverdone');
    }
    public function drivermonitoring()
    {
        return view('admin.report.drivermonitoring');
    }

    public function customer()
    {
        return view('admin.report.customer');
    }

    public function general()
    {
        return view('admin.report.general');
    }

    
    public function customerdone()
    {
        return view('admin.report.customerdone');
    }

    public function loadGeneralDataTable(Request $request)
    {
        if ($request->ajax()) {
            $user_id = Auth::user()->id;
            $role = Auth::user()->role;
            $ids = $request->get('ids', array());
            $status = $request->get('status',0);
            $region = $request->get('region',0);
            $driverselected = $request->get('driver',0);  
            $dr = $request->get('dr',0);  
            $customer = $request->get('customer',0);  

            $start_date = $request->get('start_date',0);  
            $end_date = $request->get('end_date',0);  
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
                 'amount',
                 'created_at',
                 'type',

                 'staff'
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
                'driverselected' => $driverselected,
                'dr' => $dr,
                'customer' => $customer,

                'order_column' => $orderColumn,
                'order_dir' => $orderColumnDir,
                'ids' => $ids,
                'start_date' => $start_date,
                'end_date' => $end_date
             
            ];

            $data = General::GetExcelData($Params);
           
            $dataCount = General::GetExcelDataCount($Params);
            $table = Datatables::of($data)
                        ->addColumn('checkbox', function ($row) {
                            return '<input type="checkbox" style="width:30px;height:30px;" class="checkbox" onclick="updateCount()" name="foo" data-id="'.$row->id.'" value="'.$row->id.'">';
                        })
                        ->addColumn('id', function ($row) {
                            return '<button id="__genExcelExport" value="'.$row->id.'" style="  text-decoration: underline;">'.$row->id.'</button>';
                        })

                        ->addColumn('amount', function ($row) {
                            return $row->amount;
                        })
                        ->addColumn('sent', function ($row) {
                            
                            if($row->sent==0){
                            //     if(\Auth::user()->hasPermissionTo('money_sent')){
                            //     $delete = '
                            //     <div class="flex justify-center items-center">
                            //     <a class="flex items-center text-theme-6" href="'.url('/report/sent/'.$row->id).'">
                            //     Төлбөр шилжээгүй  </a>
                             
                            // </div>
                              
                            //    ';
                            //     return $delete;} else {
                                    return ' <a class="flex items-center text-theme-6" href="#">
                                    Төлбөр шилжээгүй  </a>';
                                // }
                            } else{
                                if(\Auth::user()->hasPermissionTo('money_sent')){
                                $delete = '
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center text-theme-9" href="'.url('/report/sentback/'.$row->id).'">
                                    Төлбөр шилжсэн  </a>
                                 
                                </div>
                               ';
                                return $delete;
                                } else {
                                    return ' <a class="flex items-center text-theme-9" href="#">
                                    Төлбөр шилжсэн  </a>';
                                }

                            }
                       
                        })
                        ->addColumn('count', function ($row) {
                                    if(Auth::user()->role!='Customer'){
                            return '    <input class="font-medium whitespace-nowrap input" id="recieved_'.$row->id.'"  style="width:80px;"  value="'.$row->count.'" name="count"/>
                            <input type="hidden" value="'.$row->id.'" name="countid">
                            
                            <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_count" >  Засах </button>
                            <a class="font-medium whitespace-nowrap"></a>';
                            } else {
                            return '    <input class="font-medium whitespace-nowrap input" id="recieved_'.$row->id.'"  style="width:80px;"  value="'.$row->count.'" name="count"/>
                            <input type="hidden" value="'.$row->id.'" name="countid">';
                            }
                            
                        })
                        ->addColumn('created_at', function ($row) {
                            return $row->created_at;
                        })
			            ->addColumn('cash', function ($row) {
                              return  '
                             
                                <input class="font-medium whitespace-nowrap input" id="cash_'.$row->id.'"  style="width:80px;"  value="'.$row->cash.'" name="cash"/>
                                <input type="hidden" value="'.$row->id.'" name="realid">
                                
                                <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_cash" style="color:red; text-decoration: underline;" >  Засах </button>
                                <a class="font-medium whitespace-nowrap"></a>  
                           ';  
                        })
			            ->addColumn('account', function ($row) {
                              return  '
                             
                                <input class="font-medium whitespace-nowrap input" id="account_'.$row->id.'"  style="width:80px;"  value="'.$row->account.'" name="account"/>
                                <input type="hidden" value="'.$row->id.'" name="realid">
                                
                                <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_account" style="color:red; text-decoration: underline;" >  Засах </button>
                                <a class="font-medium whitespace-nowrap"></a>  
                           ';  
                        })
                        ->addColumn('type', function ($row) {
                            if($row->type==1){
                                return 'Хүргэлтийн ажилтан';

                            }else {
                                return 'Харилцагч';

                            }
                        })
			
                        ->addColumn('staff', function ($row) {
                            return $row->staff;
                        })   
                        ->addColumn('delprice', function ($row) {
                            return $row->delprice;
                        })  
                        ->addColumn('sub', function ($row) {
                            return $row->sub;
                        })  
                        ->addColumn('actions', function ($row) {
                                 
                            $actions = '
                            <div class="flex justify-center items-center">
                                <a class="flex items-center text-theme-9" href="'.url('/report/gen/'.$row->rand).'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="edit edit w-4 h-4 mr-1"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                                Дэлгэрэнгүй  </a>
                             
                            </div>
                            
                            <div class="flex justify-center items-center">
                            <a class="flex items-center text-theme-6" onclick="return confirmation()" href="'.url('/report/gendel/'.$row->id).'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="edit edit w-4 h-4 mr-1"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                            Устгах  </a>
                         
                        </div> ';
                        $ust = '
                        <div class="flex justify-center items-center">
                        <a class="flex items-center text-theme-6" onclick="return confirmation()" href="'.url('/report/gendel/'.$row->id).'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="edit edit w-4 h-4 mr-1"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                        Устгах  </a>
                     
                    </div>
                       ';
                        $delete = '
                        <div class="flex justify-center items-center">
                            <a class="flex items-center text-theme-9" href="'.url('/report/gen/'.$row->rand).'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="edit edit w-4 h-4 mr-1"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                            Дэлгэрэнгүй  </a>
                         
                        </div>
                       ';
                            return $actions;
                        
                            
                            
                        })              
                        ->rawColumns(['checkbox','actions','count','cash','account','sent','id'])
                        ->skipPaging()
			            ->addIndexColumn()
                        ->setTotalRecords($dataCount)
                        ->make(true);

            return $table;

        }

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


 public function report_compile(Request $request){
    $data = array();
    $data['status'] = 0;
    $array_ids = array_filter(explode(',',$request->ids));
    $ids= implode(',',$array_ids);
    // Req::whereIn('id',$ids)->update(['status'=>'8']);

    $useridq= Delivery::where('id',$ids)->first();
    $uidq=User::where('name',$useridq->driver)->first();
    $urealname=$uidq->name;
    $arr_name = array();
    for($i=0; $i<count($array_ids);$i++){
        $dddd=Delivery::where('id','=',$array_ids[$i])->first();
        $arr_name[] = $dddd['driver'];
    }
    $countss = array_count_values($arr_name);
   
    $qwe=Delivery::whereIn('driver',$arr_name)->whereIn('id',$array_ids)->count();   
        if(count($array_ids)==count(array_keys($arr_name, $useridq->driver))){
        $array_ids = array_filter(explode(',',$request->ids));
        $ids= implode(',',$array_ids);
        // Req::whereIn('id',$ids)->update(['status'=>'8']);
        Delivery::whereIn('id',$array_ids)->where('status','4')->update(['deliveryprice'=>'0']);
        Delivery::whereIn('id',$array_ids)->where('status','5')->update(['deliveryprice'=>'0']);

      
        $userid= Delivery::where('id',$ids)->first();
        $uid=User::where('name',$userid->driver)->first();
        $urealid=$uid->id;
        $cc=Delivery::whereIn('id',$array_ids)->get();
        $data['status'] = 1;
        $data['message'] = "Success";
        // $data=Order::where('reqid','=',$ids)->get();
        $data1=Delivery::whereIn('id',$array_ids)->get();
        $cbt=Delivery::whereIn('id',$array_ids)->where('status','=','3')->get();
        $price=$data1[0]->price;
        $sum=0;
        $arr_tracking = array();
        for($i=0; $i<count($array_ids);$i++){
            $dddd=Delivery::where('id','=',$array_ids[$i])->first();
            if($dddd['status']==2||$dddd['status']==6){
                $dddd['status']='6';
                $dddd['verified']='0';
                $dddd['reportdate']=Carbon::now();
                $dddd->save();
            } else {
                $dddd['verified']='2';
                $dddd['reportdate']=Carbon::now();
                $dddd->save();
            }
            $sum+=$dddd['received'];
            $arr_tracking[] = $dddd['track'];

            $log = new Log();
            $log -> value = Auth::user()->name.', нь '.$dddd["track"].' ID-тай хүргэлтийг хүргэлтийн ажилтны тайлан нийлүүллээ.';
            $log -> phone = $dddd['phone'];
            $log->staff=Auth::user()->name;
            $log -> save();
        }   
        $general = new General();
        $general->amount=$sum;
        $general->count=count($cbt);
        // // $general->count=count($array_ids);
        $general->rand=Auth::user()->id.'R'.rand(100000,999999);
        $general->type='1';
        $general->users=Delivery::whereIn('id',$array_ids)->first()->driver;
        $general->sid=$urealid;
        $general->staff=Auth::user()->name;
        $general->save();  
        for($i=0; $i<count($array_ids);$i++){
            $dddd=Delivery::where('id','=',$array_ids[$i])->first();
            $dddd['geniddr']=$general->id;
            $dddd->save();
        }
        // $dlid=Ware::whereIn('deliverid',$arr_tracking)->get();            
        // if(!empty($dlid)){
        //     foreach($dlid as $item){
        //         $genrep = new Goodrep();
        //         $genrep->delid=$item['deliverid'];
        //         $genrep->randid=$general->rand;
        //         $genrep->goods=$item['goodname'];
        //         $genrep->numb=$item['count'] ? $item['count'] : 0;
        //         $genrep->start=$item['endcount'] ? $item['endcount'] : 0;
        //         $genrep->end=$item['endcount']-$item['count'];                
        //         $genrep->save();
        //     }
        // }
        if($request->status==3){
           foreach($data as $datas){
               $good=Good::where('goodname',$datas->good)->first();
               $good->inprogress=$good->inprogress-$datas->count;
               $good->delivered=$good->delivered+$datas->count;
               $good->save();
           }
        }
    } else {
        Alert::error('Анхаар', 'Өөр жолооч нарын тайлан нийлэх боломжгүй');

    }
    return json_encode($data);
    return redirect('/report/driver')->with('message','updated');

}   

public function report_compile_customer(Request $request){


    $data = array();
    $data['status'] = 0;
    $array_ids = array_filter(explode(',',$request->ids));
    $ids= implode(',',$array_ids);
    // Req::whereIn('id',$ids)->update(['status'=>'8']);

    $useridq= Delivery::where('id',$ids)->first();
    $uidq=User::where('name',$useridq->shop)->first();
    $urealname=$uidq->name;
    $arr_name = array();
    for($i=0; $i<count($array_ids);$i++){
        $dddd=Delivery::where('shop','=',$urealname)->first();
        $arr_name[] = $dddd['shop'];
    }   
    $qwe=Delivery::whereIn('shop',$arr_name)->whereIn('id',$array_ids)->count();  

    $data = array();
    $data['status'] = 0;
    if(count($array_ids)==$qwe){
        $array_ids = array_filter(explode(',',$request->ids));
        $ids= implode(',',$array_ids);
        $cbt=Delivery::whereIn('id',$array_ids)->where('status','=','3')->get();
        Delivery::whereIn('id',$array_ids)->update(['verified'=>'3']);
        $userid= Delivery::where('id',$ids)->first();
        $uid=User::where('name',$userid->shop)->first();
        $urealid=$uid->id;
        $data['status'] = 1;
        $data['message'] = "Success";
        // $data=Order::where('reqid','=',$ids)->get();
        $data1=Delivery::where('id','=',$array_ids)->get();
        $price=$data1[0]->price;
        $sum=0;
        $tot=0;
        for($i=0; $i<count($array_ids);$i++){
            $dddd=Delivery::where('id','=',$array_ids[$i])->first();
            $sum+=$dddd['received'];
            $tot+=$dddd['deliveryprice'];
            $arr_tracking[] = $dddd['tracking'];

            $log = new Log();
            $log -> value = Auth::user()->name.', нь '.$dddd["tracking"].' ID-тай хүргэлтийг харилцагчтай тайлан нийлүүллээ.';
            $log -> phone = $dddd['phone'];
            $log->staff=Auth::user()->name;
            $log -> save();
        }   
        $ddss=Delivery::whereIn('id',$array_ids)->first();
        $general = new General();
        $general->amount=$sum;
        $general->delprice=$tot;
        $general->sub=$sum-$tot;
        $general->count=count($cbt);
        $general->rand=Auth::user()->id.'R'.rand(100000,999999);
        $general->users=Delivery::whereIn('id',$array_ids)->first()->custname;
        $general->type='2';
        $general->sid=$urealid;
        $general->staff=Auth::user()->name;
        $general->save();  
    //   $dlid=Ware::whereIn('deliverid',$arr_tracking)->get();            
    //   if(!empty($dlid)){
    //       foreach($dlid as $item){
    //           $genrep = new Goodrep();
    //           $genrep->delid=$item['deliverid'];
    //           $genrep->randid=$general->rand;
    //           $genrep->goods=$item['goodname'];
    //           $genrep->phone=$item['phone'];
    //           $genrep->gid=$item['goodid'];
    //           $genrep->numb=$item['count'] ? $item['count'] : 0;
    //           $genrep->start=$item['endcount'] ? $item['endcount'] : 0;
    //           $genrep->end=$item['endcount']-$item['count'];                
    //           $genrep->save();
  
    //       }
    //   }
    //   if(!empty($dlid)){
    //     foreach($dlid as $data){
    //             $fb=Repgood::where('goodsid',$data['goodid'])->first();
    //             $fb->start=$fb->count;
    //             $fb->end=$data['count'];
    //             $fb->phone=$data['phone'];
    //             $fb->count=$fb->count-$data['count'];
    //             $fb->custname=$general->rand;
    //             $fb->save();
    //             $rec=new Finalrep();
    //             $rec->goodsid=$fb->goodsid;
    //             $rec->start=$fb->start;
    //             $rec->phone=$fb->phone;
    //             $rec->end=$fb->end;
    //             $rec->count=$fb->count;
    //             $rec->rid=$fb->custname;
    //             $rec->save();
    //     }
    // }
        if($request->status==3){
           foreach($data as $datas){
               $good=Good::where('goodname',$datas->good)->first();
               $good->inprogress=$good->inprogress-$datas->count;
               $good->delivered=$good->delivered+$datas->count;
               $good->save();
           }
        } 
    } 
    else {
        Alert::error('Анхаар', 'Өөр харилцагч нарын тайлан нийлэх боломжгүй');

    }
    return json_encode($data);
}

    public function loadDeliveryDataTableForReport(Request $request)
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
            $not_1 = $request->get('not_1',0);
            $not_100 = $request->get('not_100',0);
            $verified=$request->get('verified');
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
                 'shop',
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
                'not_1' => $not_1,
                'not_100' => $not_100,
                'verified' => $verified,
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
                            return $row->comment;
                        })
                        ->addColumn('created_at', function ($row) {
                            return $row->created_at;
                        })
                        ->addColumn('note', function ($row) {
                            return  '
                            <input class="font-medium whitespace-nowrap input" id="note_'.$row->id.'"  style="width:80px;"  value="'.$row->note.'" name="note"/>
                            <input type="hidden" value="'.$row->id.'" name="realid"> 
                            <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_note" >  Засах </button>
                            <a class="font-medium whitespace-nowrap"></a>
                       ';
                        })
                        ->addColumn('received', function ($row) {
                            return  '
                            <input class="font-medium whitespace-nowrap input" id="received_'.$row->id.'"  style="width:80px;"  value="'.$row->received.'" name="received"/>
                            <input type="hidden" value="'.$row->id.'" name="realid"> 
                            <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_received" >  Засах </button>
                            <a class="font-medium whitespace-nowrap"></a>
                       ';
                        })
                        ->addColumn('deliveryprice', function ($row) {
                            return  '
                            <input class="font-medium whitespace-nowrap input" id="deliveryprice_'.$row->id.'"  style="width:80px;"  value="'.$row->deliveryprice.'" name="received"/>
                            <input type="hidden" value="'.$row->id.'" name="realid"> 
                            <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_deliveryprice" >  Засах </button>
                            <a class="font-medium whitespace-nowrap"></a>
                       ';
                        })
                        ->addColumn('additional', function ($row) {
                            return  '
                            <input class="font-medium whitespace-nowrap input" id="additional_'.$row->id.'"  style="width:80px;"  value="'.$row->additional.'" name="received"/>
                            <input type="hidden" value="'.$row->id.'" name="realid"> 
                            <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_additional" >  Засах </button>
                            <a class="font-medium whitespace-nowrap"></a>
                       ';
                        })
                        ->addColumn('opservice', function ($row) {
                            return  '
                            <input class="font-medium whitespace-nowrap input" id="opservice_'.$row->id.'"  style="width:80px;"  value="'.$row->opservice.'" name="received"/>
                            <input type="hidden" value="'.$row->id.'" name="realid"> 
                            <button data-id="'.$row->id.'" class="font-medium whitespace-nowrap button_edit_opservice" >  Засах </button>
                            <a class="font-medium whitespace-nowrap"></a>
                       ';
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
                                                <a class="flex items-center text-theme-6" onclick="return confirm("Are you sure?")" href="'.url('/deliveries/delete/'.$row->id).'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 w-4 h-4 mr-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                Устгах</a>
                                            </div>';
                                        }
                                        return $actions;
                        })
                        ->rawColumns(['checkbox','actions','comment','note','received','deliveryprice','opservice','additional'])
                        ->skipPaging()
                        ->setTotalRecords($dataCount)
                        ->make(true);
            return $table;
        }

        
    }
    
}
