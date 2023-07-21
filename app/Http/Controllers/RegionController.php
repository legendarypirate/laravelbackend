<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;
use App\Models\Region;
use RealRashid\SweetAlert\Facades\Alert;

class RegionController extends Controller
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
        return view('admin.region.index');
    }

    public function create(Request $request){ 
        
        $region = new Region();
        $region->name = $request->name;
        $region->save();
        return redirect('/region/list')->with('message','Амжилттай хадгалагдлаа');

    }

    public function list(){
        $region=Region::All();
        return view('admin.region.list',compact('region'));
    }

    public function delete($id){
        $user = Region::find($id);
        $user->delete();
        Alert::success('Бүс', 'Амжилттай устгагдлаа');

        return redirect('/region/list')->with('message','deleted');
    }
}