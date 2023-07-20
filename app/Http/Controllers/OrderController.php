<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

use Illuminate\Http\Request;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
class OrderController extends Controller
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
        return view('admin.order.index');
    }

    public function create(Request $request){ 
        
        $order = new Order();
        $order->shop = $request->shop;
        $order->phone = $request-> phone;
        $order->address = $request->address;
        $order->comment = $request-> comment;
        $order->status = 1;
        $order->save();
        return redirect('/order/list')->with('message','Амжилттай хадгалагдлаа');

    }

    public function list(){
       
        return view('admin.order.list');
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
