<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\Log;

use Illuminate\Http\Request;
use App\Models\Region;
use RealRashid\SweetAlert\Facades\Alert;

class NotificationController extends Controller
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
        return view('admin.notification.index');
    }

    public function send(Request $request){ 
        
        $region = new Region();
        $region->name = $request->name;
        $region->save();

        $log = new Log();
        $log -> value = Auth::user()->name.', нь '.$request->name.' бүс үүсгэлээ.';
        $log -> phone = '';
        $log->staff=Auth::user()->name;
        $log -> save();
        return redirect('/region/list')->with('message','Амжилттай хадгалагдлаа');

    }

}