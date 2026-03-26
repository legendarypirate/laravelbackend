<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\Log;

use Illuminate\Http\Request;
use App\Models\Banner;
use RealRashid\SweetAlert\Facades\Alert;

class BannerController extends Controller
{
  
    
    public function index()
    {
        return view('admin.banner.index');
    }

    public function create(Request $request){ 
        
        $order = new Banner();
        $order->image = '';
        $order->save();
 
        $lastId=$order->id;
        $pictureInfo=$request->file('image');

        if($request->file('image')){
            $picName = $lastId.$pictureInfo->getClientOriginalName();
            $folder="bannerImage/";
            $pictureInfo->move($folder,$picName);
            $picUrl=$folder.$picName;
            $newsPic = Banner::find($lastId);
            $newsPic->image = $picUrl;
            $newsPic-> save(); 
        }
        return redirect('/banner/list')->with('message','Амжилттай хадгалагдлаа');

    }

    public function list(){
        $region=Banner::All();
        return view('admin.banner.list',compact('region'));
    }

    public function getbanner(){
        $banner=Banner::All();
        return response()->json(['data'=>$banner,'success'=>true]);
    }


    public function delete($id){
        $user = Banner::find($id);
        $user->delete();

        $log = new Log();
        $log -> value = Auth::user()->name.', нь '.$user->name.' бүс устгалаа.';
        $log -> phone = '';
        $log->staff=Auth::user()->name;
        $log -> save();
        Alert::success('Banner', 'Амжилттай устгагдлаа');

        return redirect('/banner/list')->with('message','deleted');
    }
}