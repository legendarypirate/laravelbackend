<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\Log;

use Illuminate\Http\Request;
use App\Models\Setting;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
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
        $region=Setting::All();
        return view('admin.setting.list',compact('region'));
    }

    public function getbanner(){
        $banner=Banner::All();
        return response()->json(['data'=>$banner,'success'=>true]);
    }
    
    public function edit($id){

        $userEdit = Setting::where('id', $id)->first();
        return view('admin.setting.edit', ['user'=>$userEdit]);

    }
    
    public function update(Request $request){
      
        
        $user= Setting::find($request->settingId);
        $user->price=$request->price;
        $user->driver=$request->driver;
        $user->save();

   
        return redirect('/setting/list')->with('message','Амжилттай хадгалагдлаа');

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

