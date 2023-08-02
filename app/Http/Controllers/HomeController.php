<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\User;
class HomeController extends Controller
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
        $record = Delivery::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
        ->where('created_at', '>', Carbon::today()->subDay(6))
        ->groupBy('day_name','day')
        ->orderBy('day')
        ->get();
    
        $data = [];

        foreach($record as $row) {
            $data['label'][] = $row->day_name;
            $data['data'][] = (int) $row->count;
        }

        $chart_data= json_encode($data);
    
        $delivery=Delivery::where('created_at','>=',Carbon::now()->subDays(30))->count();
        $customer=User::where('created_at','>=',Carbon::now()->subDays(30))->where('role','customer')->count();
        $driver=User::where('created_at','>=',Carbon::now()->subDays(30))->where('role','driver')->count();
        $order=Order::where('created_at','>=',Carbon::now()->subDays(30))->count();
        $ware=User::all()->take(5);

        return view('admin.home.homeContent',compact('delivery','customer','driver','order','chart_data','ware'));
    }
}
