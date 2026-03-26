<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\Log;

use Illuminate\Http\Request;
use App\Models\Notification;
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
        
        $region = new Notification();
        $region->type = $request->type;
        $region->title = $request->title;
        $region->description = $request->description;
        $region->sent_by_id = Auth::user()->id;
        $region->sent_by_image = Auth::user()->image;

        $region->save();

     
        return redirect('/notification/index')->with('message','Амжилттай хадгалагдлаа');

    }

}
