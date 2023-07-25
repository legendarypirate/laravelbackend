<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Phone;
use App\Models\Address;
use App\Models\User;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
class OrderController extends Controller
{
  

    public function phone($id){
        echo json_encode(DB::table('phones')->where('userid', $id)->get());
    }

    public function address($id){
        echo json_encode(DB::table('addresses')->where('userid', $id)->get());
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.order.index');
    }

    public function create(Request $request){ 
        
        $order = new Order();
        $user=User::where('id',$request->shop)->first()->name;
        $order->shop = $user;
        $phone=Phone::where('phone',$request->phone)->first()->phone;
        $address=Address::where('address',$request->address)->first()->address;
        $order->phone = $phone;
        $order->address =  $request-> address;
        $order->comment = $request-> comment;
        $order->status = 1;
        $order->save();
        return redirect('/order/list')->with('message','Амжилттай хадгалагдлаа');

    }

    public function list(){
       
        return view('admin.order.list');
    }

    public function order($name){
        $list=Order::where('driver',$name)->where('status',2)->get();
        return response()->json(['data'=>$list,'success'=>true]);
    }

    public function delivered(Request $request){
        $order=Order::find($request->id);
        $order->comment=$request->comm;
        $order->status=3;
        $order->save();
        return response()->json(['data'=>$order,'success'=>true]);
    }

    public function writecomment(Request $request){
        $order=Order::find($request->id);
        $order->comment=$request->comm;
        $order->save();
        return response()->json(['data'=>$order,'success'=>true]);
    }

    public function decline(Request $request){
      
        $order = Order::find($request->id);
        $order->status=$request->status;
        if($request->status=="Цуцалсан"){
            $order->status=4;
        } else {
            $order->status=6;
        }
        $order->comment=$request->comm;
        $order->save();

        // if($request->status=="Цуцалсан"){
        //     $ssq='Таны захиалга цуцлагдлаа';
        // } else {
        //     $ssq='Таны захиалга '.$black->note.' шалтгаанаар барааг авалгүй буцлаа';
        // } 
        // $SERVER_API_KEY = 'AAAARg9HwNY:APA91bGKk4ebfKj1Kpq4cIG0TDpVCqfdrK1bdbZZVLZcBVOTiV_oyJC31EDDFYXZNfPIBdCC6KD32VcpT-CCkBEom0OXwWtUniURy-FGAhHq9jvx-F0zqM8TnLBlSHBmBBSkvpKu0nvI';

        // $tk=Token::where('userid',$black->organization)->latest()->first();
        // if($tk){
        //     $tkn=$tk->token;
        //     $token_1 = $tkn;
        
        //     $data = [
        
        //         "registration_ids" => [
        //             $token_1
        //                         ],
        
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


        // $phone=$black->phone;
        // $msg = "Tanii%20baraag%20".str_replace(' ','%20',$black->note)."%20shaltgaanaar%20baraag%20avalgui%20butslaa.%20Buuhia%20Elch%20Hurgeltiin%20Uilchilgee.";
        // $headers = array(
        //     "Accept:  application/json",
        //     "Content-Type: application/json",
        // );
        // $chsms = curl_init();
        // curl_setopt($chsms, CURLOPT_URL, "https://ebuuhia.mn/sendsms.php?phone=".$phone."&msg=".$msg);
        // // SSL important
        // curl_setopt($chsms, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($chsms, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($chsms, CURLOPT_HTTPHEADER, $headers);
        // $output = curl_exec($chsms);
        // curl_close($chsms);
            return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data'=>$order
        ], 200);
    }
    
    public function orderdetail($id){
        $list=Order::where('id',$id)->get();
        return response()->json(['data'=>$list,'success'=>true]);
    }

    public function doneorder($name){
        $list=Order::where('driver',$name)->where('status','!=',2)->where('status','!=',1)->get();
        return response()->json(['data'=>$list,'success'=>true]);
    }

    public function driver(){
        return view('admin.order.driver');
    }

    public function finished(){
        return view('admin.order.finished');
    }

    public function change_status_on_order(Request $request){

        $data = array();
        $data['status'] = 0;

        if($request->ids && $request->status){

            $ids = explode(',',$request->ids);
            Order::whereIn('id',$ids)->update(['status'=>$request->status]);

            $data['status'] = 1;
            $data['message'] = "Success";
        }

        
        Alert::success('Захиалга', 'Төлөв солигдлоо');

        return json_encode($data);
    }

    public function change_bus_on_order(Request $request){

        $data = array();
        $data['status'] = 0;

        if($request->ids && $request->region){

            $ids = explode(',',$request->ids);
            Order::whereIn('id',$ids)->update(['region'=>$request->region]);

            $data['region'] = 1;
            $data['message'] = "Success";
        }
        Alert::success('Захиалга', 'Бүс солигдлоо');

        return json_encode($data);
    }

    public function change_driver_on_order(Request $request){

            $data = array();
            $data['status'] = 0;
            $array_ids = array_filter(explode(',',$request->ids));
            $arr_tracking = array();
     
            $ids = explode(',',$request->ids);
            Order::whereIn('id',$ids)->update(['driver'=>$request->driver]);
            Order::whereIn('id',$ids)->update(['status'=>'2']);

            $data['driver'] = 1;
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

        $dds= implode(",",$arr_tracking);
               
        // $log = new Log();
        // $log -> desc = Auth::user()->name.' ажилтан нь '.$dds.' захиалгыг '.$request->driverselected.'-д хуваариллаа';
        // $log->staff=Auth::user()->name;
        // $log->value='';

        // $log -> save();
        return json_encode($data);
    }


    public function change_delete_on_order(Request $request){

        $data = array();
        $data['status'] = 0;
        $ids = explode(',',$request->ids);
    
        if($request->ids){
             $array_ids = array_filter(explode(',',$request->ids));
             $ids= implode(',',$array_ids);
             // Req::whereIn('id',$ids)->update(['status'=>'8']);
             $cc=Order::whereIn('id',$array_ids)->get();
             $data['status'] = 1;
             $data['message'] = "Success";
             $data1=Order::whereIn('id',$array_ids)->get();
             $arr_ware = array();
             $arr_tracking = array();
             for($i=0; $i<count($array_ids);$i++){
                 // Req::where('id','=',$array_ids[$i])->delete();
                  $dddd=Order::where('id','=',$array_ids[$i])->first();
                  $dddd->delete();
                //   $log = new Log();
                //   $log -> desc = Auth::user()->name.', нь'.$dddd["track"].' ID-тай захиалгыг устгалаа.';
                //   $log -> phone = $dddd['phone'];
                //   $log -> value = $dddd['track'];
                //   $log->staff=Auth::user()->name;
                //   $log -> save();
             }
         }
         Alert::success('Захиалга', 'Амжилттай устгагдлаа');
        return json_encode($data);
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
            $status_3 = $request->get('status_3',0);
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
                'status_3' => $status_3,
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

    public function ExcelExport(Request $request)
    {
        if ($request->ajax()) {
            if(isset($request->excel)){
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $ids = $request->get('ids', array());
                $excel = $request->get('excel', 0);
                
                $Params = [
                    'ids' => $ids,
                'user_id' => $user_id,
                'role' => $role,
               
                ];

                $excel_data = array();
                $dataExcel = Order::GetExcelData($Params);
                $excel_data = [];
                foreach($dataExcel as $key=>$row)
                    {
                        if($row->status==1){
                            $row->status='Бүртгэгдсэн';
                        } elseif($row->status==2){
                            $row->status='Жолоочид хуваарилсан';
                        } elseif($row->status==3){
                            $row->status='Жолооч хүлээж авсан';
                        } else {
                            $row->status='Дууссан';
                        }
                        $excel_data[$key]['id']= $row->id;
                        $excel_data[$key]['shop']= $row->shop;
                        $excel_data[$key]['phone']= $row->phone;
                        $excel_data[$key]['address']= $row->address;
                        $excel_data[$key]['comment']= $row->comment;

                        $excel_data[$key]['created_at']= $row->created_at;
                        $excel_data[$key]['status']= $row->status;
                        $excel_data[$key]['driver']= $row->driver;
                    }
                $export_order_analysis = new OrderExport($excel_data);
                $excel = Excel::download($export_order_analysis, 'delivery.xlsx');
                $excel->setContentDisposition('attachment','delivery')->getFile()->move(public_path('/delivery'), 'delivery'.time().'.xlsx');
                return asset('delivery').'/delivery'.time().'.xlsx';
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
                $dataExcel = Order::GetExcelData($Params);

                $table = '<table class="table table-striped  table-bordered" style="border-width: 1px;border-style: solid;border-color: black;">
                <thead>
                    <tr>
                    <th class="text-center whitespace-nowrap">#</th>

                        <th class="text-center whitespace-nowrap">Б/Авсан цаг</th>
                        <th class="whitespace-nowrap">Нэр</th>
                        <th class="text-center whitespace-nowrap">Утас</th>
                        <th class="text-center whitespace-nowrap">Захиалгын дэлгэрэнгүй хаягийн мэдээлэл</th>
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
