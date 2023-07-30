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
        $delivery=Delivery::where('created_at','>=',Carbon::now()->subDays(30))->count();
        $customer=User::where('created_at','>=',Carbon::now()->subDays(30))->where('role','customer')->count();
        $driver=User::where('created_at','>=',Carbon::now()->subDays(30))->where('role','driver')->count();
        $order=Order::where('created_at','>=',Carbon::now()->subDays(30))->count();

        return view('admin.home.homeContent',compact('delivery','customer','driver','order'));
    }
}
