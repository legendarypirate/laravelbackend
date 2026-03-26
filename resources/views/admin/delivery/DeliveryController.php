<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cookie;
use App\Imports\RequestImportExcel;
use App\Exports\DeliveryExport;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\DeliveryDownload;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Log;
use Redirect;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Good;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.delivery.index');
    }
    public function phone($id)
    {
        echo json_encode(DB::table('merchant')->where('id', $id)->get());
    }

    public function address($id)
    {
        echo json_encode(DB::table('merchant')->where('id', $id)->get());
    }

    public function detail($id)
    {
        $list = Delivery::find($id);
        $merchant_info = Merchant::find($list['merchant_id']);
        //dd($merchant);
        return view('admin.delivery.detail', compact('list', 'merchant_info'));
    }

    public function delivery($name)
    {
        $list = Delivery::with(['merchant', 'user'])
            ->join('users', 'deliveries.shop', '=', 'users.name')
            ->where('driver', '=', $name)
            ->where(function ($query) {
                $query->where('status', "=", "2");
                $query->orWhere('status', "=", "10");
            })
            ->orderBy('deliveries.id', 'DESC')
            ->select('users.name as shop', 'users.image as customer_image',  'deliveries.*')
            ->get();

        return response()->json(['data' => $list, 'success' => true]);
    }

    //merchant list haruulah
    public function merchant($id)
    {
        $list = User::find($id);
        $merchant_info = Merchant::where('user_id','=',$list['id'])->get();
    
        return response()->json(['data' => $merchant_info, 'success' => true]);
    }


    //Driver Request
    public function driverRequest($deliveryId, $name)
    {
        $delivery = Delivery::find($deliveryId);
        if (!$delivery->driver_request) {
            $delivery->driver_request = $name;
        } else {
            $delivery->driver_request = trim($delivery->driver_request . ',' . $name, ',');
        }
        $delivery->save();
        return response()->json(['data' => $delivery, 'success' => true]);
    }


    public function newDelivery(Request $request)
    {
        $list = Delivery::with('merchant')
            ->where('driver', '=', $request->name)
            ->where('status', "=", "1")
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(['data' => $list, 'success' => true]);
    }
    public function newDeliveryStatus($name)
    {
        $list = Delivery::with('merchant')
            ->where('status', "=", "1")
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($list as $delivery) {
            if ($delivery->driver_request) {
                $nameArray = explode(',', $delivery->driver_request);
                $nameExists = in_array($name, $nameArray);
                $delivery->requestStatus = $nameExists ? 1 : 0;
            } else {
                $delivery->requestStatus = 0;
            }
        }
        return response()->json(['data' => $list,  'success' => true]);
    }





    public function typeSearch($id)
    {
        $list = Delivery::with('merchant')
            ->where('type', '=', $id)
            ->orderBy('id', 'DESC')
            ->get();
        return response()->json(['data' => $list, 'success' => true]);
    }
    //create delivary bank api
    public function createDeliveryBank(Request $request)
    {
        $user = User::where('name', 'EZPAY')->first();
        $userid = $user->id;

        $order = new Delivery();
        $request->type = 1;
        if ($user->role == 'customer') {
            $order->shop = 'EZPAY';
            if ($request->type == 1) {
                if (isset(Auth::user()->engiin)) {
                    $order->deliveryprice = Auth::user()->engiin;
                } else {
                    $defaultPrice = Setting::where('type', 1)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 2) {
                if (isset(Auth::user()->tsagtai)) {
                    $order->deliveryprice = Auth::user()->tsagtai;
                } else {
                    $defaultPrice = Setting::where('type', 2)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 3) {
                if (isset(Auth::user()->yaraltai)) {
                    $order->deliveryprice = Auth::user()->yaraltai;
                } else {
                    $defaultPrice = Setting::where('type', 3)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 4) {
                if (isset(Auth::user()->onts_yaraltai)) {
                    $order->deliveryprice = Auth::user()->onts_yaraltai;
                } else {
                    $defaultPrice = Setting::where('type', 4)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            }
        } else {
            $order->shop = 'EZPAY';
        }

        $order->phone = $request->phone;
        $order->phone2 = $request->phone2;
        $order->address = $request->address;
        $order->comment = $request->comment;
        $order->price = $request->price;
        $order->received = $request->received;
        $order->type = 1;
        $order->merchant_id = 47;
        $order->parcel_info = "Посын цаас Изипей";
        $order->order_code = $request->order_code;
        $order->download_time = $request->download_time;
        $order->district = $request->district;
        $order->receivername = $request->receivername;
        $order->size = $request->size;
        $order->number = $request->number;  //get
        $order->region = $request->region;
        $order->goodtype = $request->goodtype;
        $order->verified = 0;
        $order->track = 'CH' . rand(100000, 999999) . $userid;
        $order->status = 1;
        $order->save();

        $log = new Log();
        $log->phone = $request->phone;
        $log->staff = 'EZPAY';
        $log->value = 'EZPAY' . ' ' . $order->track . ' дугаартай хүргэлт үүсгэлээ';
        $log->save();

        $string = str_replace('\n', '', $request->data);

        $strings = rtrim($string, ',');
        $convst = str_replace('\\', '+', $strings);
        $qq = json_decode($convst, true);

        if ($request->data) {
            foreach ($qq as $dd) {
                $idk = Good::where('goodname', str_replace('+', '\\', $dd['name']))->where('shop', $dd['cname'])->first();
                $od = $idk->id;
                $updatedgood = Good::find($od);
                $updatedgood->count = Good::where('id', '=', $od)->first()->count - $dd['count'];
                $updatedgood->indelivery = Good::where('id', '=', $od)->first()->indelivery + $dd['count'];
                $updatedgood->save();
            }
        }

        return response()->json(['success' => true]);
    }


    //Create delivery api
    public function createdelivery(Request $request)
    {
        //   dd($request->name);
        $user = User::where('name', $request->name)->first();
        $userid = $user->id;

        $order = new Delivery();

        if ($user->role == 'customer') {
            $order->shop = $request->name;
            if ($request->type == 1) {
                if (isset(Auth::user()->engiin)) {
                    $order->deliveryprice = Auth::user()->engiin;
                } else {
                    $defaultPrice = Setting::where('type', 1)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 2) {
                if (isset(Auth::user()->tsagtai)) {
                    $order->deliveryprice = Auth::user()->tsagtai;
                } else {
                    $defaultPrice = Setting::where('type', 2)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 3) {
                if (isset(Auth::user()->yaraltai)) {
                    $order->deliveryprice = Auth::user()->yaraltai;
                } else {
                    $defaultPrice = Setting::where('type', 3)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 4) {
                if (isset(Auth::user()->onts_yaraltai)) {
                    $order->deliveryprice = Auth::user()->onts_yaraltai;
                } else {
                    $defaultPrice = Setting::where('type', 4)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            }
        } else {
            $order->shop = $request->shop;
        }

        $order->phone = $request->phone;
        $order->phone2 = $request->phone2;
        $order->address = $request->detailadd;
        $order->comment = $request->comment;
        $order->price = $request->price;
        $order->received = $request->received;
        $order->type = $request->type;
        $order->merchant_id = $request->merchant_id;
        $order->parcel_info = $request->parcel_info;
        $order->order_code = $request->order_code;
        $order->download_time = $request->download_time;
        $order->district = $request->district;
        $order->receivername = $request->receivername;
        $order->size = $request->size;
        $order->number = $request->number;;
        $order->region = $request->region;
        $order->goodtype = $request->goodtype;
        $order->verified = 0;
        $order->track = 'CH' . rand(100000, 999999) . $userid;
        $order->status = 1;
        //   $imageUrls = ;
        // if ($request->hasFile('image')) {
        //     foreach ($request->file('image') as $image) {
        //         $picUrl = $image->store('baraa', 'public');
        //         $imageUrls[] = $picUrl;
        //     }
        //    $order->image =   $imageUrls;
        // }
        //   if($picInfo = $request->file('image')){
        //   $picUrl = $request->file('image')->store('baraa', 'public');
        //   $order->image = $picUrl;
        // }


        if ($request->hasFile('image')) {
    $files = "";
    foreach ($request->file('image') as $image_url) {
        $fileExt = $image_url->getClientOriginalExtension();
        $randomString = substr(uniqid(rand()), 0, 15);
        $fileNameToStore = $randomString . '_' . time() . '.' . $fileExt;

        // Store the image and get the storage path
        $picUrl = $image_url->store('baraa', 'public');
        
        // Use the storage path instead of just the file name
        $files .= $picUrl . "|";
    }

    // Remove the last "|" character
    $files = rtrim($files, "|");

    // Store the file paths in the database
    $order->image = $files;
}


        //dd($order);
           $order->save();

        $log = new Log();
        $log->phone = $request->phone;
        $log->staff = $request->name;
        $log->value = $request->name . ' ' . $order->track . ' дугаартай хүргэлт үүсгэлээ';
        $log->save();

        $string = str_replace('\n', '', $request->data);

        $strings = rtrim($string, ',');
        $convst = str_replace('\\', '+', $strings);
        $qq = json_decode($convst, true);

        if ($request->data) {
            foreach ($qq as $dd) {
                $idk = Good::where('goodname', str_replace('+', '\\', $dd['name']))->where('shop', $dd['cname'])->first();
                $od = $idk->id;
                $updatedgood = Good::find($od);
                $updatedgood->count = Good::where('id', '=', $od)->first()->count - $dd['count'];
                $updatedgood->indelivery = Good::where('id', '=', $od)->first()->indelivery + $dd['count'];
                $updatedgood->save();
            }
        }

        return response()->json(['data' => $order, 'success' => true]);
    }



    public function donedelivery($name)
    {
        $delivery = Delivery::where('driver', '=', $name)
            ->where(function ($query) {
                $query->where('status', "=", "3");
                $query->orWhere('status', "=", "4");
                $query->orWhere('status', "=", "5");
                $query->orWhere('status', "=", "10");
            })->orderBy('deliveries.id', 'DESC')->get();
        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data' => $delivery
        ], 200);
    }

    public function editNoteOnDataTable(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->get('id', 0);
            $note = $request->get('note');
            Delivery::where('id', $id)->update(['note' => $note]);
        }
    }
    public function editCommentDataTable(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id', 0);
            $comment = $request->get('comment');
            Delivery::where('id', $id)->update(['comment' => $comment]);
            // Alert::success('Comment', 'Амжилттай шинэчлэгдлээ');
        }
    }

    //driver statistic begins

    public function todeliver($name)
    {
        $list = Delivery::where('driver', $name)->where('status', 2)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function totalforcust($name)
    {
        $list = Delivery::where('shop', $name)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function donedeliver($name)
    {
        $list = Delivery::where('driver', $name)->where('status', 3)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function declinedeliver($name)
    {
        $list = Delivery::where('driver', $name)->where('status', 4)->orWhere('status', 6)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function totaldeliver($name)
    {
        $list = Delivery::where('driver', $name)->count();
        return response()->json(['data' => $list, 'success' => true]);
    }
    //driver stat ends

    public function write(Request $request)
    {
        $order = Delivery::find($request->id);
        $order->note = $request->comm;
        $order->save();
        return response()->json(['data' => $order, 'success' => true]);
    }

    public function sign(Request $request)
    {

        $user = Delivery::where('id', $request->phone)->first();

        if ($picInfo = $request->file('image')) {
            $picUrl = $request->file('image')->store('signImage', 'public');
            $user->sign_image = $picUrl;
        }
        $user->rating = $request->rating;
        $user->status = 3;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Гарын үсэг хүлээн авлаа'
        ]);
    }



    public function receive($id)
    {
        $user = Delivery::where('id', $id)->first();
        $user->status = 10;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Утасны дугаарыг хүлээн авлаа',
            'data' => $user
        ]);
    }

    public function declinefromshop(Request $request)
    {
        $order = Delivery::find($request->id);
        $order->status = 4;
        $order->deliveryprice = 0;
        $order->save();
        return response()->json(['data' => $order, 'success' => true]);
    }


    public function editing(Request $request)
    {
        $order = Delivery::find($request->id);
        $order->note = $request->note;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->received = $request->received;
        $order->receivername = $request->receivername;
        $order->save();
        return response()->json(['data' => $order, 'success' => true]);
    }

    public function decline_delivery(Request $request)
    {

        $delivery = Delivery::find($request->id);
        $delivery->status = $request->status;
        if ($request->status == "Цуцалсан") {
            $delivery->status = 4;
            $delivery->note = $request->comm;
            $delivery->received = 0;
            $delivery->save();

            $log = new Log();
            $log->phone = $delivery->phone;
            $log->staff = $delivery->shop;
            $log->value = $delivery->name . ' ' . $delivery->track . ' дугаартай хүргэлт цуцалсан төлөвт орууллаа';
            $log->save();

            // $ware=Ware::where('deliverid',$black->tracking)->first();
            // $wareg=Ware::where('deliverid',$black->tracking)->get();
            //     if($ware){
            //         $ware->delete();
            //         foreach($wareg as $wares){
            //             $good=Good::where('id',$wares->goodid)->first();
            //             $good->inprogress=$good->inprogress-$wares->count;
            //             $good->count=$good->count+$wares->count;
            //             $good->save();
            //         }
            //     }

        } elseif ($request->status == "Буцаасан") {
            $delivery->status = 5;
            $delivery->note = $request->comm;
            $delivery->received = 0;
            $delivery->save();

            $log = new Log();
            $log->phone = $delivery->phone;
            $log->staff = $delivery->shop;
            $log->value = $delivery->name . ' ' . $delivery->track . ' дугаартай хүргэлт Буцаасан төлөвт орууллаа';
            $log->save();
            // if($ware){
            //     $ware->delete();
            //     foreach($wareg as $wares){
            //     $good=Good::where('id',$wares->goodid)->first();
            //     $good->inprogress=$good->inprogress-$wares->count;
            //     $good->count=$good->count+$wares->count;
            //     $good->save();
            //             }
            // }

        } else {
            $delivery->status = 6;
            $delivery->note = $request->comm;

            if ($request->status == 'Утсаа аваагүй') {
                $delivery->note = $request->status;
                $delivery->save();
            } elseif ($request->status == 'Хэрэглэгч хойшлуулсан') {
                $delivery->note = $request->status;
                $delivery->save();
            } elseif ($request->status == 'Хаяг солигдсон') {
                $delivery->note = $request->status;
                $delivery->save();
            } elseif ($request->status == 'Хаасан байсан') {
                $delivery->note = $request->status;
                $delivery->save();
            } elseif ($request->status == 'Жолоочийн машинд асуудал гарсан') {
                $delivery->note = $request->status;
                $delivery->save();
            } else {
                $delivery->note = $request->status;
                $delivery->save();
            }
        }


        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data' => $delivery
        ], 200);
    }
    public function settings()
    {
        $defaultPrice = Setting::get();
        return response()->json(['data' => $defaultPrice, 'success' => true]);
    }


    // Хүргэлт дэлгэрэнгүй харах
    public function deliverydetail($id)
    {
        $list = Delivery::with('merchant')->where('id', $id)->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function create(Request $request)
    {

        $cookie_data = stripslashes(Cookie::get('shopping_cart'));
        $cart_data = json_decode($cookie_data, true, 512, JSON_UNESCAPED_UNICODE);
        $order = new Delivery();

        $rules = [

            'address' => 'required',
            'phone' => 'required|numeric|digits:8',
            'receivername' => 'required',
            'number' => 'required|numeric',
            'price' => 'numeric',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'

        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        if (isset($request->merchant_id)) {
            $order->merchant_id = $request->merchant_id;
        } else {
            $merchant = new Merchant;
            $rules = [

                'merchantName' => 'required',
                'merchantAddress' => 'required',
                'merchantPhone1' => 'required',
            ];

            $merchant->user_id = Auth::user()->id;
            $merchant->merchantName = $request->merchantName;
            $merchant->merchantAddress = $request->merchantAddress;
            $merchant->merchantPhone1 = $request->merchantPhone1;
            $merchant->merchantPhone2 = $request->merchantPhone2;
            $merchant->merchantWhat3Words = $request->merchantWhat3Words;
            // dd($merchant);
            $merchant->save();
            $order->merchant_id = $merchant->id;
        }

        if (Auth::user()->role == 'customer') {
            $order->shop = Auth::user()->name;
            if ($request->type == 1) {
                if (isset(Auth::user()->engiin)) {
                    $order->deliveryprice = Auth::user()->engiin;
                } else {
                    $defaultPrice = Setting::where('type', 1)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 2) {
                if (isset(Auth::user()->tsagtai)) {
                    $order->deliveryprice = Auth::user()->tsagtai;
                } else {
                    $defaultPrice = Setting::where('type', 2)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 3) {
                if (isset(Auth::user()->yaraltai)) {
                    $order->deliveryprice = Auth::user()->yaraltai;
                } else {
                    $defaultPrice = Setting::where('type', 3)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            } else if ($request->type == 4) {
                if (isset(Auth::user()->onts_yaraltai)) {
                    $order->deliveryprice = Auth::user()->onts_yaraltai;
                } else {
                    $defaultPrice = Setting::where('type', 4)->first();
                    $order->deliveryprice = $defaultPrice->price;
                }
            }
        } else {
            $order->shop = $request->shop;
        }
        $order->phone = $request->phone;
        $order->phone2 = $request->phone2;
        $order->type = $request->type;
        $order->parcel_info = $request->parcel_info;
        $order->order_code = $request->order_code;
        $order->download_time = $request->download_time;
        $order->district = $request->district;
        $order->address = $request->address;
        $order->comment = $request->comment;
        $order->receivername = $request->receivername;
        $order->size = $request->size;
        $order->number = $request->number;
        $order->price = $request->price;
        $order->region = $request->region;
        $order->goodtype = $request->goodtype;
        $order->verified = 0;
        $order->track = 'CH' . rand(100000, 999999) . Auth::user()->id;
        $order->status = 1;
        if ($picInfo = $request->file('image')) {
            $picUrl = $request->file('image')->store('baraa', 'public');
            $order->image = $picUrl;
        }
        $order->save();

        $log = new Log();
        $log->phone = $request->phone;
        $log->staff = Auth::user()->name;
        $log->value = Auth::user()->name . ' ' . $order->track . ' дугаартай захиалаг үүсгэлээ';
        $log->save();

        $psum = 0;
        if ($cart_data) {
            foreach ($cart_data as $cdata) {
                // $order = new Order();
                // $order->reqid = $blackEntry->id;
                // $order->good = urldecode($cdata['item_name']);
                // $order->price=$cdata['item_price'];
                // $order->count=$cdata['item_quantity'];
                // $order->status=1;
                // $order->sid=Auth::user()->id;
                // $order->staff=Auth::user()->name;
                // $order -> save();
                $updatedgood = Good::find($cdata['item_id']);
                $updatedgood->count = Good::where('id', '=', $cdata['item_id'])->first()->count - $cdata['item_quantity'];
                $updatedgood->indelivery = Good::where('id', '=', $cdata['item_id'])->first()->indelivery + $cdata['item_quantity'];
                $updatedgood->save();
                // $ware = new Ware();
                // $ware->goodid = urldecode($cdata['item_id']);
                // $ware->deliverid = $blackEntry->tracking;
                // $ware->custname = $blackEntry->custname;
                // $ware->phone = $blackEntry->phone;
                // $ware->goodname = urldecode($cdata['item_name']);
                // $ware->count=$cdata['item_quantity'];
                // $ware->status=1;
                // $ware -> save();
                $psum += $cdata['item_price'] * $cdata['item_quantity'];
                $order->price = $psum;
                $order->received = $psum;
                $order->save();
            }
        }
        Cookie::queue(Cookie::forget('shopping_cart'));
        Cookie::queue(Cookie::forget('phone_cart'));
        Cookie::queue(Cookie::forget('address_cart'));
        Alert::success('Хүргэлт', 'Амжилттай хадгалагдлаа');
        return redirect('/delivery/new');
    }

    public function good($shop)
    {
        echo json_encode(DB::table('goods')->where('shop', $shop)->get());
    }


    public function addtocart(Request $request)
    {
        $prod_id = $request->input('product_id');

        $quantity = $request->input('quantity');
        $product_name = $request->input('product_name');

        if (Cookie::get('shopping_cart')) {
            $cookie_data = stripslashes(Cookie::get('shopping_cart'));
            $cart_data = json_decode($cookie_data, true);
        } else {
            $cart_data = array();
        }

        $item_id_list = array_column($cart_data, 'item_id');
        $prod_id_is_there = $prod_id;

        if (in_array($prod_id_is_there, $item_id_list)) {
            foreach ($cart_data as $keys => $values) {
                if ($cart_data[$keys]["item_id"] == $prod_id) {
                    $cart_data[$keys]["item_quantity"] = $request->input('quantity');
                    $cart_data[$keys]["item_name"] = urldecode($request->input('product_name'));
                    $item_data = json_encode($cart_data);
                    $minutes = 60;
                    // Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));
                    return response()->json(['status' => '"' . $cart_data[$keys]["item_name"] . '" Уг бүтээгдэхүүн сагсанд байна']);
                }
            }
        } else {
            $products = Good::find($prod_id);
            $prod_name = $products['goodname'];
            $priceval = $products['price'];
            $quantity = $request->input('quantity');
            if ($products) {
                $item_array = array(
                    'item_id' => $prod_id,
                    'item_name' => urlencode($prod_name),
                    'item_quantity' => $quantity,
                    'item_price' => $priceval,

                );
                $cart_data[] = $item_array;

                $item_data = json_encode($cart_data);
                $minutes = 60;
                Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));
                return response()->json(['status' => '"' . $prod_name . '" сагсанд нэмэгдлээ']);
            }
        }
    }



    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }

    public function recover($id)
    {
        $delivery = Delivery::find($id);
        $delivery->status = 1;
        $delivery->driver = '';
        $delivery->save();
        return Redirect::back();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }

    public function list()
    {
        return view('admin.delivery.list');
    }

    public function new()
    {
        return view('admin.delivery.new');
    }



    public function deliveryshop($name)
    {
        $list = Delivery::with('merchant')->where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "1");
        })->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function newDeliveryShopCount($name)
    {
        $list = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "1");
        })->count();
        return response()->json(['total' => $list, 'success' => true]);
    }

    public function activeDeliveryShop($name)
    {
        $list = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "2");
        })->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function activeDeliveryShopCount($name)
    {
        $list = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "2");
        })->count();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function declineddelivery($name)
    {
        $list = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "4");
            $query->orWhere('deliveries.status', "=", "5");
        })->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function totake($name)
    {
        $list = Delivery::where('shop', $name)->sum('price');
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function taken($name)
    {
        $list = Delivery::where('shop', $name)->sum('received');
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function successdelivery($name)
    {
        $list = Delivery::where('shop', $name)->where('status', 3)->get();
        return response()->json(['data' => $list, 'success' => true]);
    }

    public function done()
    {
        return view('admin.delivery.done');
    }

    public function allDelivery()
    {
        return view('admin.delivery.all');
    }
    //Жолоочийн хүргэлт татан авалт
    public function deliveryDownload()
    {
        return view('admin.delivery.delivery_download');
    }
    //Жолоочийн хүргэлт татан авалтын мэдээлэл татах
    public function getDeliveryDownload()
    {
        //$deliveryDownloaded = DB::table('deliveries_download')->orderBy('id', 'DESC') ->get();
        // $deliveryDownloaded = DeliveryDownload::with('delivery')->orderBy('id', 'DESC')
        // ->get();
        $deliveryDownloaded = DeliveryDownload::join('deliveries', 'deliveries.id', '=', 'deliveries_download.deliveries_id')
            ->select(
                'deliveries_download.id',
                'deliveries_download.driver_id',
                'deliveries_download.deliveries_id',
                'deliveries_download.download_price',
                'deliveries_download.created_at',
                'deliveries_download.updated_at',
                'deliveries.shop' // Include the 'shop' column from the associated 'Delivery' model
            )
            ->latest('deliveries_download.id')
            ->get();

        // dd($deliveryDownloaded);
        $table = Datatables::of($deliveryDownloaded)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" style="width:20px;height:20px;" class="checkbox" onclick="updateCount()" name="foo" data-id="' . $row->id . '" value="' . $row->id . '">';
            })
            ->addColumn('driver_id', function ($row) {
                return $row->driver_id;
            })
            ->addColumn('shop', function ($row) {
                $deliveryInfo = $row->delivery;
                return $deliveryInfo ? ($deliveryInfo->shop ?? '-') : '-';
            })
            ->addColumn('deliveries_id', function ($row) {
                return $row->deliveries_id;
            })
            ->addColumn('download_price', function ($row) {
                return $row->download_price;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at;
            })

            ->rawColumns(['checkbox', 'driver_id', 'deliveries_id', 'download_price', 'shop', 'created_at'])
            // ->setTotalRecords($dataCount)
            ->skipPaging()
            ->make(true);
        //  dd($table);
        return $table;
    }


    public function received()
    {
        return view('admin.delivery.received');
    }

    public function report()
    {
        return view('admin.delivery.report');
    }

    public function delprice($name)
    {
        $delprice = Delivery::where('shop', $name)->sum('deliveryprice');
        return response()->json(['data' => $delprice, 'success' => true]);
    }


    public function declined($name)
    {
        $active = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "4");
            $query->orWhere('deliveries.status', "=", "5");
        })->count();

        return response()->json(['data' => $active, 'success' => true]);
    }


    public function active($name)
    {
        $active = Delivery::where('shop', $name)->where(function ($query) {
            $query->where('deliveries.status', "=", "1");
            $query->orWhere('deliveries.status', "=", "2");
        })->count();

        return response()->json(['data' => $active, 'success' => true]);
    }


    public function success($name)
    {
        $active = Delivery::where('shop', $name)->where('status', 3)->count();

        return response()->json(['data' => $active, 'success' => true]);
    }

    public function updatedel(Request $request)
    {
        $del = Delivery::find($request->delId);
        $del->merchant_id = $request->merchant_id;
        $del->parcel_info = $request->parcel_info;
        $del->order_code = $request->order_code;
        $del->download_time = $request->download_time;
        $del->phone = $request->phone;
        $del->address = $request->address;
        $del->comment = $request->comment;
        $del->price = $request->price;
        $del->region = $request->region;

        $del->type = $request->type;

        if ($request->driver_request) {
            $del->driver = $request->driver_request;
            $del->status = 2;
        }

        $userName = User::find($request->shop);
        if ($request->type == 1) {
            if (isset($userName->engiin)) {
                $del->deliveryprice = $userName->engiin;
            } else {
                $defaultPrice = Setting::where('type', 1)->first();
                $del->deliveryprice = $defaultPrice->price;
            }
        } else if ($request->type == 2) {
            if (isset($userName->tsagtai)) {
                $del->deliveryprice = $userName->tsagtai;
            } else {
                $defaultPrice = Setting::where('type', 2)->first();
                $del->deliveryprice = $defaultPrice->price;
            }
        } else if ($request->type == 3) {
            if (isset($userName->yaraltai)) {
                $del->deliveryprice = $userName->yaraltai;
            } else {
                $defaultPrice = Setting::where('type', 3)->first();
                $del->deliveryprice = $defaultPrice->price;
            }
        } else if ($request->type == 4) {
            if (isset($userName->onts_yaraltai)) {
                $del->deliveryprice = $userName->onts_yaraltai;
            } else {
                $defaultPrice = Setting::where('type', 4)->first();
                $del->deliveryprice = $defaultPrice->price;
            }
        }



        $del->size = $request->size;
        if ($picInfo = $request->file('image')) {
            $picUrl = $request->file('image')->store('baraa', 'public');
            $del->image = $picUrl;
        }

        $del->save();
        Alert::success('Хүргэлт', 'Амжилттай шинэчлэгдлээ');
        return redirect()->route('deliveryNew');
    }

    public function deleted()
    {
        return view('admin.delivery.deleted');
    }



    public function delivered_delivery($id)
    {
        $delivery = Delivery::find($id);
        $delivery->status = 3;
        $delivery->save();

        $log = new Log();
        $log->phone = $delivery->phone;
        $log->staff = $delivery->shop;
        $log->value = $delivery->shop . ' ' . $delivery->track . ' дугаартай захиалаг үүсгэлээ';
        $log->save();

        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data' => $delivery
        ], 200);
    }

    public function updateindex(Request $request)
    {


        $string = str_replace('\n', '', $request->data);

        $strings = rtrim($string, ',');
        $convst = str_replace('\\', '+', $strings);
        $qq = json_decode($convst, true);

        foreach ($qq as $dds) {
            $delivery = Delivery::where('id', $dds['id'])->first();
            $delivery->ordering = $dds['ordering'];
            $delivery->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
        ], 200);
    }

    public function change_status_on_delivery(Request $request)
    {

        $data = array();
        $data['status'] = 0;

        if ($request->ids && $request->status) {
            $ids = explode(',', $request->ids);
            $dddd = Delivery::whereIn('id', $ids)->where('verified', '0')->count();
            $dddds = Delivery::whereIn('id', $ids)->where('driver', NULL)->count();

            if ($request->status == 10) {
                // $data=Order::where('reqid','=',$ids)->get();
                $data['status'] = 1;
                $data['message'] = "Success";

                if ($request->status == 3) {
                    foreach ($data as $datas) {
                        $good = Good::where('goodname', $datas->good)->first();
                        $good->inprogress = $good->inprogress - $datas->count;
                        $good->delivered = $good->delivered + $datas->count;
                        $good->save();
                    }
                }

                $array_ids = array_filter(explode(',', $request->ids));
                $ids = implode(',', $array_ids);
                $idss = explode(',', $request->ids);
                Delivery::whereIn('id', $idss)->update(['status' => '10']);



                foreach ($idss as $id) {
                    $delivery = Delivery::find($id)->first();
                    // dd($delivery->driver);
                    $delivery_download = new DeliveryDownload();
                    $delivery_download->deliveries_id =  $id;
                    $delivery_download->driver_id =  $delivery->driver;

                    $userName = User::find($delivery->shop);

                    if (isset($userName->tatan_avalt)) {
                        $delivery_download->download_price = $userName->tatan_avalt;
                    } else {
                        $defaultPrice = Setting::where('type', 5)->first();
                        $delivery_download->download_price = $defaultPrice->price;
                    }

                    // $delivery_download->download_price =  '4500';
                    $delivery_download->created_at =  now();
                    $delivery_download->updated_at =  now();
                    $delivery_download->save();
                }
                for ($i = 0; $i < count($array_ids); $i++) {
                    $dddd = Delivery::where('id', '=', $array_ids[$i])->first();
                    $log = new Log();
                    $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг хүлээн авсан төлөвт орууллаа.';
                    $log->phone = $dddd['phone'];
                    $log->staff = Auth::user()->name;
                    $log->save();
                }
                // if($request->status==3)
                // {
                //     $ido = explode(',',$request->ids);
                //     Req::whereIn('id',$ido)->update(['deliveryprice'=>5000]);
                // } else {
                //     $ido = explode(',',$request->ids);

                //     Req::whereIn('id',$ido)->update(['deliveryprice'=>0]);
                // }

                Alert::success('Хүргэлт', 'Төлөв солигдлоо');
            } elseif ($request->status == 4 || $request->status == 5) {
                $array_ids = array_filter(explode(',', $request->ids));
                $ids = implode(',', $array_ids);
                $idss = explode(',', $request->ids);
                Delivery::whereIn('id', $idss)->update(['status' => $request->status]);
            } elseif ($dddd > 0 || $dddds > 0) {
                // dd('www');
                Alert::error('Хүргэлт', 'Баталгаажаагүй эсвэл жолоочгүй хүргэлт байна');
            } else {
                // dd('qqq');
                $idss = explode(',', $request->ids);
                $array_ids = array_filter(explode(',', $request->ids));
                // $data=Order::where('reqid','=',$ids)->get();
                $data['status'] = 1;
                $data['message'] = "Success";
                // $data=Order::where('reqid','=',$ids)->get();
                if ($request->status == 3) {
                    $ids = implode(',', $array_ids);
                    $array_ids = array_filter(explode(',', $request->ids));

                    Delivery::whereIn('id', $idss)->update(['status' => '3']);

                    $cc = Delivery::whereIn('id', $array_ids)->get();
                    // $data=Order::where('reqid','=',$ids)->get();
                    $data1 = Delivery::whereIn('id', $array_ids)->get();
                    $arr_ware = array();
                    $arr_tracking = array();
                    for ($i = 0; $i < count($array_ids); $i++) {
                        $dddd = Delivery::where('id', '=', $array_ids[$i])->first();
                        $arr_tracking[] = $dddd['track'];
                        $phone = $dddd['phone'];

                        $log = new Log();
                        $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг хүргэсэн төлөвт орууллаа.';
                        $log->phone = $dddd['phone'];
                        $log->staff = Auth::user()->name;
                        $log->save();
                    }
                    // $qqq=Ware::where('deliverid',$arr_tracking)->get();
                    // $wareg= Ware::whereIn('deliverid',$arr_tracking)->get();
                    // $arr_goodid = array();
                    // for($i=0; $i<count($arr_tracking);$i++){
                    //     $good=Ware::where('deliverid',$arr_tracking[$i])->first();
                    //     if($good){
                    //     $arr_goodid[]=$good['goodid'];
                    //     }
                    // }
                    // if(!empty($wareg)){
                    //     foreach($wareg as $wares){
                    //         $good=Good::where('id',$wares['goodid'])->first();
                    //         $good->inprogress=$good->inprogress-$wares['count'];
                    //         $good->delivered=$good->delivered+$wares['count'];
                    //         $good->save();
                    //     }
                    // }
                }
                $idss = explode(',', $request->ids);
                if ($request->status == 3) {
                    Delivery::whereIn('id', $idss)->update(['deliveryprice' => 5000]);
                } else {
                    Delivery::whereIn('id', $idss)->update(['deliveryprice' => 0]);
                }
                if ($request->status == 6) {
                    $array_ids = array_filter(explode(',', $request->ids));
                    $ids = implode(',', $array_ids);
                    $idss = explode(',', $request->ids);
                    Delivery::whereIn('id', $idss)->update(['status' => '6']);
                    $cc = Delivery::whereIn('id', $array_ids)->get();
                    // $data=Order::where('reqid','=',$ids)->get();
                    $data1 = Delivery::whereIn('id', $array_ids)->get();
                    $arr_ware = array();

                    $arr_tracking = array();
                    for ($i = 0; $i < count($array_ids); $i++) {
                        // Req::where('id','=',$array_ids[$i])->delete();
                        $dddd = Delivery::where('id', '=', $array_ids[$i])->first();
                        $arr_tracking[] = $dddd['track'];
                        $phone = $dddd['phone'];

                        $log = new Log();
                        $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг хүлээгдэж буй төлөвт орууллаа.';
                        $log->phone = $dddd['phone'];
                        $log->staff = Auth::user()->name;
                        $log->save();
                    }
                }
                if ($request->status == 2) {
                    $array_ids = array_filter(explode(',', $request->ids));
                    $ids = implode(',', $array_ids);

                    $idss = explode(',', $request->ids);
                    Delivery::whereIn('id', $idss)->update(['status' => '2']);
                    for ($i = 0; $i < count($array_ids); $i++) {
                        $dddd = Delivery::where('id', '=', $array_ids[$i])->first();
                        $log = new Log();
                        $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг жолоочид хуваарилсан төлөвт орууллаа.';
                        $log->phone = $dddd['phone'];
                        $log->staff = Auth::user()->name;
                        $log->save();
                    }
                }
                if ($request->status == 4 || $request->status == 5 || $request->status == 6) {

                    $array_ids = array_filter(explode(',', $request->ids));
                    $ids = implode(',', $array_ids);
                    // Req::whereIn('id',$ids)->update(['status'=>'8']);
                    Delivery::where('id', $ids)->orWhere('status', '4')->orWhere('status', '6')->update(['deliveryprice' => '0']);
                    $cc = Delivery::whereIn('id', $array_ids)->get();
                    Delivery::where('id', $ids)->orWhere('status', '4')->orWhere('status', '5')->orWhere('status', '6')->update(['received' => '0']);
                    // $data=Order::where('reqid','=',$ids)->get();
                    $data1 = Delivery::whereIn('id', $array_ids)->get();
                    $arr_ware = array();
                    $arr_tracking = array();
                    for ($i = 0; $i < count($array_ids); $i++) {
                        // Req::where('id','=',$array_ids[$i])->delete();
                        $dddd = Delivery::where('id', '=', $array_ids[$i])->first();
                        $arr_tracking[] = $dddd['track'];
                        if ($request->status == 4) {
                            $idss = explode(',', $request->ids);
                            Delivery::whereIn('id', $idss)->update(['status' => '4']);
                            $cust = $dddd['custname'];
                            $phone = $dddd['phone'];
                            $log = new Log();
                            $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг цуцалсан төлөвт орууллаа.';
                            $log->phone = $dddd['phone'];
                            $log->staff = Auth::user()->name;
                            $log->save();
                        }
                        if ($request->status == 5) {
                            $idss = explode(',', $request->ids);
                            Delivery::whereIn('id', $idss)->update(['status' => '5']);
                            $cust = $dddd['custname'];
                            $phone = $dddd['phone'];

                            $log = new Log();
                            $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг буцаасан төлөвт орууллаа.';
                            $log->phone = $dddd['phone'];
                            $log->staff = Auth::user()->name;
                            $log->save();
                        }
                    }
                    // $qqq=Ware::where('deliverid',$arr_tracking)->get();
                    // $wareg= Ware::whereIn('deliverid',$arr_tracking)->get();
                    $arr_goodid = array();
                    for ($i = 0; $i < count($arr_tracking); $i++) {
                        // $good=Ware::where('deliverid',$arr_tracking[$i])->first();
                        // if($good){
                        //     $arr_goodid[]=$good['goodid'];
                        // }

                    }
                    // if(!empty($wareg)){
                    //     foreach($wareg as $wares){
                    //         $good=Good::where('id',$wares['goodid'])->first();
                    //         $good->inprogress=$good->inprogress-$wares['count'];
                    //         $good->count=$good->count+$wares['count'];
                    //         $good->save();
                    //     }
                    // }
                    // Ware::whereIn('deliverid',$arr_tracking)->delete();
                }
                Alert::success('Хүргэлт', 'Төлөв солигдлоо');
            }
        }

        return json_encode($data);
    }


    public function change_bus_on_delivery(Request $request)
    {

        $data = array();
        $data['status'] = 0;
        if ($request->ids && $request->region) {
            $ids = explode(',', $request->ids);
            Delivery::whereIn('id', $ids)->update(['region' => $request->region]);
            $data['region'] = 1;
            $data['message'] = "Success";
        }
        Alert::success('Хүргэлт', 'Бүс солигдлоо');
        return json_encode($data);
    }

    public function cartDetailsAjaxS()
    {

        if (Cookie::get('shopping_cart')) {
            $cookie_data = stripslashes(Cookie::get('shopping_cart'));
            $cart_data = json_decode($cookie_data, true);
            $total = 0;
            $html = '<div class="col-md-7 ms-auto">
            <div class="cart-page-header"><h6 class="cart-page-header-title">Order list</h6></div>
            <div class="d-flex flex-column gap-3">';

            foreach ($cart_data as $data) {
                $html .= '<label class="order-card col-12 cartpage" data-cart-item-id="123" data-product-id="12">
                        <!-- <input class="order-card__input" type="checkbox" checked /> -->
                        <div class="order-card__body">
                    
                            <input type="hidden" class="product_id" value="' . $data['item_id'] . '" >
                            <div class="product-row">
                        
                                <div class="product-row__content">
                                    <h6 class="product-row__content-title"><div style="width:200px;">' . urldecode($data['item_name']) . '</div>  Тоо:' . number_format($data['item_quantity']) . ' <div style="display:inline;margin-left:50px;">Үнэ:' . number_format($data['item_price']) . '</div></h6>
                                    <div class="product-row__content-author">
                                    </div>
                                </div>
                                <div class="product-row__tally" style="display:inline;">
                                    <div class="product-row__tally--price">
                                    
                                    </div>
                                 
                                </div>
                            </div>
                        </div>
                    </label>
                    <script>
                    
                    $(".delete_cart_data_bask").click(function (e) {
    e.preventDefault();
    
    var product_id = $(this).closest(".cartpage").find(".product_id").val();
    
    var data = {
        "_token": $("input[name=_token]").val(),
        "product_id": product_id,
    };
    
    // $(this).closest(".cartpage").remove();
    
    $.ajax({
        url: "/delete-from",
        type: "DELETE",
    
        data: data,
        success: function (response) {
            window.location.reload();
        }
    });
    });
    
                </script>
                    ';
            }
            $html .= '</div>
        </div>
    
    
        <div class="col-md-3 me-auto">
      
        <div class="cart-page__purchase">
            <div class="cart-page__purchase-lists">';
            foreach ($cart_data as $data) :
                $html .= '<div class="cart-page__purchase-lists-item">
                
                </div>';
                $total = $total + ($data["item_price"] * $data["item_quantity"]);
            endforeach;

            $html .= '</div>
            <div class="cart-page__purchase-total">
                <div class="cart-page__purchase-total-item">
                    <div class="span">Total sum:</div>
                    <div class="total-price">₮ ' . number_format($total, 2) . '</div>
                </div>
            </div>
            
        </div>
        </div>';
        } else {
            $html .= '<div class="row">
            <div class="col-md-12 mycard py-5 text-center">
                <div class="mycards">
                    <h4>Таны сагс одоогоор хоосон байна.</h4>
                
                </div>
            </div>
        </div>';
        }


        return $html;
    }


    public function change_driver_on_delivery(Request $request)
    {


        $data = array();
        $data['status'] = 0;
        $array_ids = array_filter(explode(',', $request->ids));
        $arr_tracking = array();

        $ids = explode(',', $request->ids);
        Delivery::whereIn('id', $ids)->update(['driver' => $request->driverselected]);
        Delivery::whereIn('id', $ids)->update(['status' => '2']);

        $data['driverselected'] = 1;
        $data['message'] = "Success";

        Alert::success('Захиалга', 'Жолооч солигдлоо');

        return json_encode($data);
    }


    public function change_verify_on_delivery(Request $request)
    {

        $data = array();
        $data['status'] = 0;

        if ($request->ids) {
            $ids = explode(',', $request->ids);
            $array_ids = array_filter(explode(',', $request->ids));
            if ($request->verified == 1) {
                for ($i = 0; $i < count($array_ids); $i++) {
                    // $delivery=Delivery::where('id','=',$array_ids[$i])->first();

                    // $log = new Log();
                    // $log -> desc = Auth::user()->name.', нь '.$dddd["tracking"].' ID-тай хүргэлтийг баталгаажууллаа.';
                    // $log -> phone = $dddd['phone'];
                    // $log -> value = $dddd['tracking'];
                    // $log->staff=Auth::user()->name;
                    // $log -> save();
                }
            } else {
                for ($i = 0; $i < count($array_ids); $i++) {
                    // $delivery=Delivery::where('id','=',$array_ids[$i])->first();

                    // $log = new Log();
                    // $log -> desc = Auth::user()->name.', нь '.$dddd["tracking"].' ID-тай хүргэлтийг баталсныг цуцаллаа.';
                    // $log -> phone = $dddd['phone'];
                    // $log -> value = $dddd['tracking'];
                    // $log->staff=Auth::user()->name;
                    // $log -> save();
                }
            }

            Delivery::whereIn('id', $ids)->update(['verified' => $request->verified]);

            // $req=Delivery::where('id',$ids)->get();
            // $good = Ware::where('deliverid',$req[0]['tracking'])->get();
            // foreach($good as $goods){
            //     $goods->verify=1;
            //     $goods->save();
            // }
            $data['status'] = 1;
            $data['message'] = "Success";
        }
        Alert::success('Хүргэлт', 'Баталгаажлаа');

        return json_encode($data);
    }

    public function change_delete_on_delivery(Request $request)
    {

        $data = array();
        $data['status'] = 0;
        $ids = explode(',', $request->ids);
        $dddd = Delivery::whereIn('id', $ids)->where('verified', '1')->count();
        $st = Delivery::whereIn('id', $ids)->where('status', '10')->count();
        if ($dddd > 0 || $st > 0) {
            Alert::error('Хүргэлт', 'Баталгаажсан хүргэлт устгах боломжгүй');
        } else {
            if ($request->ids) {
                $array_ids = array_filter(explode(',', $request->ids));
                $ids = implode(',', $array_ids);
                // Req::whereIn('id',$ids)->update(['status'=>'8']);
                Delivery::where('id', $ids)->orWhere('status', '4')->orWhere('status', '5')->update(['deliveryprice' => '0']);
                $cc = Delivery::whereIn('id', $array_ids)->get();
                $data['status'] = 1;
                $data['message'] = "Success";
                //  $data=Order::where('reqid','=',$ids)->get();
                $data1 = Delivery::whereIn('id', $array_ids)->get();
                $arr_ware = array();
                $arr_tracking = array();
                for ($i = 0; $i < count($array_ids); $i++) {
                    // Req::where('id','=',$array_ids[$i])->delete();
                    $dddd = Delivery::where('id', '=', $array_ids[$i])->first();
                    $dddd->status = 100;
                    $dddd->save();
                    //  $arr_tracking[]=$dddd['tracking'];
                    $log = new Log();
                    $log->value = Auth::user()->name . ', нь' . $dddd["track"] . ' ID-тай хүргэлтийг устгалаа.';
                    $log->phone = $dddd['phone'];
                    $log->staff = Auth::user()->name;
                    $log->save();
                }
                // $qqq=Ware::where('deliverid',$arr_tracking)->get();
                //  $wareg= Ware::whereIn('deliverid',$arr_tracking)->get();
                //  $arr_goodid = array();
                //  for($i=0; $i<count($arr_tracking);$i++){
                //      $good=Ware::where('deliverid',$arr_tracking[$i])->first();
                //      if($good){
                //          $arr_goodid[]=$good['goodid'];
                //      }
                //  }
                //      if(!empty($wareg)){
                //          foreach($wareg as $wares){
                //              $good=Good::where('id',$wares['goodid'])->first();
                //              $good->inprogress=$good->inprogress-$wares['count'];
                //              $good->count=$good->count+$wares['count'];
                //              $good->save();
                //          }
                //      }
                //  Ware::whereIn('deliverid',$arr_tracking)->delete();
            }
            Alert::success('Хүргэлт', 'Амжилттай устгагдлаа');
        }
        return json_encode($data);
    }

    public function excelImport()
    {
        $file = request()->file('file');
        if ($file) {
            Excel::import(new RequestImportExcel, $file);
            return back();
        } else {
            return back()->with('error', 'Please Select File');
        }
    }

    public function showQRData(Request $request)
    {
        $arr_ids = explode(",", $request->post('ids'));
        $ids = implode(",", array_filter($arr_ids));
        $user_id = Auth::user()->id;
        $role = Auth::user()->role;
        $offset = $request->get('start', 0);
        $limit = $request->get('length', 10);
        if ($limit < 1 or $limit > 100) {
            $limit = 100;
        }
        $Params = [
            'ids' => $ids,
            'user_id' => $user_id,
            'role' => $role,
            'limit' => $limit,
            'offset' => $offset,
        ];
        $dataQR = Delivery::GetQRData($Params);
        return view('admin.delivery.bulkQRPrint', compact('dataQR'));
    }

    public function loadDeliveryDataTable(Request $request)
    {

        if ($request->ajax()) {

            $user_id = Auth::user()->id;
            $role = Auth::user()->role;
            $ids = $request->get('ids', array());
            $status = $request->get('status', 0);
            $district = $request->get('district', 0);
            $region = $request->get('region', 0);
            $phone = $request->get('phone', 0);
            $address = $request->get('address', 0);
            $note = $request->get('note', 0);
            $tuluv = $request->get('tuluv', 0);
            $start_date = $request->get('start_date', 0);
            $merchant = $request->get('merchant', 0);
            $type = $request->get('type', 0);
            $estimated = $request->get('estimated', 0);
            //   dd( $estimated);
            $late = $request->get('late', 0);
            $customer = $request->get('customer', 0);
            $status_100 = $request->get('status_100', 0);
            $end_date = $request->get('end_date', 0);
            $driverselected = $request->get('driver', 0);
            $except_status = $request->get('except_status', 0);
            $except_stat = $request->get('except_stat', 0);
            $status_10 = $request->get('status_10', 0);
            $status_1 = $request->get('status_1', 0);
            $status_2 = $request->get('status_2', 0);
            $status_3 = $request->get('status_3', 0);
            $status_6 = $request->get('status_6', 0);
            $status_4 = $request->get('status_4', 0);
            $status_5 = $request->get('status_5', 0);
            $not_3 = $request->get('not_3', 0);
            $not_4 = $request->get('not_4', 0);
            $not_2 = $request->get('not_2', 0);
            $not_6 = $request->get('not_6', 0);
            $not_5 = $request->get('not_5', 0);
            $not_1 = $request->get('not_1', 0);
            $not_10 = $request->get('not_10', 0);
            $not_100 = $request->get('not_100', 0);
            $offset = $request->get('start', 0);
            $limit = $request->get('length', 10);
            if ($limit < 1 or $limit > 3500) {
                $limit = 3500;
            }

            $search = isset($request->get('search')['value'])
                ? $request->get('search')['value']
                : null;
            //dd($search);
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
                'status_10' => $status_10,
                'status_100' => $status_100,
                'status_1' => $status_1,
                'status_6' => $status_6,
                'status_2' => $status_2,
                'status_5' => $status_5,
                'status_4' => $status_4,
                'status_3' => $status_3,
                'not_5' => $not_5,
                'not_4' => $not_4,
                'not_3' => $not_3,
                'not_1' => $not_1,
                'not_2' => $not_2,
                'not_6' => $not_6,
                'not_10' => $not_10,
                'not_100' => $not_100,
                'tuluv' => $tuluv,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'region' => $region,
                'district' => $district,
                'note' => $note,
                'late' => $late,
                'customer' => $customer,
                'phone' => $phone,
                'address' => $address,
                'merchant_id' => $merchant,
                'type' => $type,
                'estimated' => $estimated,
                'driverselected' => $driverselected,

            ];
            // dd($Params);
            $data = Delivery::GetExcelData($Params);
            $dataCount = Delivery::GetExcelDataCount($Params);

            $table = Datatables::of($data)
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" style="width:20px;height:20px;" class="checkbox" onclick="updateCount()" name="foo" data-id="' . $row->id . '" value="' . $row->id . '">';
                })
                ->addColumn('id', function ($row) {
                    return $row->id;
                })
                ->addColumn('track', function ($row) {
                    if ($row->driver_request) {
                        return '<a href="' . url('/delivery/detail/' . $row->id) . '"><div style="color:white;background-color:red">' . $row->track . '</div></a>';
                      //  return '<div style="color:white;background-color:red">' . $row->track . '</div>';
                    } else {
                        return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 100% !important; height:100% !important" class="text-center whitespace-nowrap table-info">' . $row->track . '</div>';
                    }
                })
                ->addColumn('region', function ($row) {
                    if (Auth::user()->role != 'Customer') {
                        if ($row->driver_request) {
                            return '<div style="color:white;background-color:red">' . $row->region . '</div>';
                        } else {
                            return $row->region;
                        }
                    } else {
                        return '';
                    }
                })
                ->addColumn('type', function ($row) {
                    if ($row->type == '1') {
                        return '<div style="color:green;">Энгийн</div>';
                    } elseif ($row->type == '2') {
                        return '<div style="color:orange;">Цагтай</div>';
                    } elseif ($row->type == '3') {
                        return '<div style="color:pink;">Яаралтай</div>';
                    } elseif ($row->type == '4') {
                        return '<div style="color:red;">Онц яаралтай</div>';
                    }
                })
                ->addColumn('address', function ($row) {
                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">' . $row->address . '</div>';
                })
                ->addColumn('receivername', function ($row) {
                    $mergedInfo = '';
                    if (isset($row->receivername) && !empty($row->receivername)) {
                        $mergedInfo .= $row->receivername;
                    }
                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 170px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                })
                ->addColumn('order_code', function ($row) {
                    $mergedInfo = '';
                    if (isset($row->order_code) && !empty($row->order_code)) {
                        $mergedInfo .= $row->order_code;
                    }
                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 100px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                })
                ->addColumn('shop', function ($row) {
                    $mergedInfo = '';

                    $user = User::where('name', $row->shop)->first();
                    if (isset($row->shop) && !empty($row->shop)) {
                        $mergedInfo .= $row->shop;
                    }
                    if ($user) {
                        if (isset($user->image) && !empty($user->image)) {
                            
                            $mergedInfo .= (!empty($mergedInfo) ? ', ' : '') . '<img src="' . asset('storage/') . '/' . $user->image . '" width="30"  style="float:right">';
                        }
                    } else {
                        $mergedInfo .= "";
                    }

                    if (empty($mergedInfo)) {
                        $mergedInfo = "Мэдээлэл байхгүй";
                    }
                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 170px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                })
                ->addColumn('mergedMerchantParcel', function ($row) {
                    $mergedInfo = '';
                    if (isset($row->goodtype) && !empty($row->goodtype)) {
                        $mergedInfo .= $row->goodtype;
                    }
                    if (isset($row->parcel_info) && !empty($row->parcel_info)) {
                        $mergedInfo .= (!empty($mergedInfo) ? ', ' : '') . $row->parcel_info;
                    }

                    if (isset($row->image) && !empty($row->image)) {
                          $imagesArray = explode('|', $row->image);
                            $firstImagePath = $imagesArray[0];
                        $mergedInfo .= (!empty($mergedInfo) ? ', ' : '') . '<img src="' . asset('storage/') . '/' .  $firstImagePath . '" width="30" style="float:right">';
                    }

                    if (empty($mergedInfo)) {
                        $mergedInfo = "Мэдээлэл байхгүй";
                    }

                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                })


                ->addColumn('comment', function ($row) {
                    return  '
                            <input class="font-medium whitespace-nowrap input" id="note_' . $row->id . '"  style="width:150px;"  value="' . $row->comment . '" name="comment"/>
                            <input type="hidden" value="' . $row->id . '" name="realid">
                            <button data-id="' . $row->id . '" class="btn btn-primary button_edit_comment" >  Засах </button>
                            
                            <a class="font-medium whitespace-nowrap"></a>
                       ';
                })
                ->addColumn('note', function ($row) {
                    return  '
                            <input class="font-medium whitespace-nowrap input" id="not_' . $row->id . '"  style="width:150px;"  value="' . $row->note . '" name="note"/>
                            <input type="hidden" value="' . $row->id . '" name="realid">
                            <button data-id="' . $row->id . '" class="btn btn-primary button_edit_note" >  Засах </button>
                            
                            <a class="font-medium whitespace-nowrap"></a>
                       ';
                })
                ->addColumn('rating', function ($row) {
                    return '<div class="star-rating" data-rating="0">
                                  <span class="star">&#9733;</span>
                                  <span class="star">&#9733;</span>
                                  <span class="star">&#9733;</span>
                                  <span class="star">&#9733;</span>
                                  <span class="star">&#9733;</span>
                                </div>';
                })
                ->addColumn('created_at', function ($row) {
                    return substr($row->created_at, 5, -3);
                })
                ->addColumn('verified', function ($row) {
                    if ($row->verified == 1) {
                        return 'Тийм';
                    } else {
                        return 'Үгүй';
                    }
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<div class="status1">Бүртгэгдсэн</div>';
                    } elseif ($row->status == 2) {
                        return '<div class="status2">Хуваарилсан</div>';
                    } elseif ($row->status == 6) {
                        return '<div class="status6">Хүлээгдэж буй</div>';
                    } elseif ($row->status == 3) {
                        return '<div class="status3">Хүргэгдсэн</div>';
                    } elseif ($row->status == 4) {
                        return 'Цуцалсан';
                    } elseif ($row->status == 5) {
                        return 'Буцаасан';
                    } elseif ($row->status == 10) {
                        return '<div class="status10">Хүлээн авсан</div>';
                    } elseif ($row->status == 4) {
                        return 'Дууссан';
                    }
                })

                ->addColumn('merchantName', function ($row) {
                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 150px !important;" class="text-center whitespace-nowrap">' . $row->merchantName . '</div>';
                })

                ->addColumn('merchantPhone1', function ($row) {
                    $mergedInfo = '';
                    if (isset($row->merchantPhone1) && !empty($row->merchantPhone1)) {
                        $mergedInfo .= $row->merchantPhone1;
                    }
                    if (isset($row->merchantPhone2) && !empty($row->merchantPhone2)) {
                        $mergedInfo .= (!empty($mergedInfo) ? ', ' : '') . $row->merchantPhone2;
                    }


                    if (empty($mergedInfo)) {
                        $mergedInfo = "Мэдээлэл байхгүй";
                    }

                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 200px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                })
                ->addColumn('phone', function ($row) {
                    $mergedInfo = '';
                    if (isset($row->phone) && !empty($row->phone)) {
                        $mergedInfo .= $row->phone;
                    }
                    if (isset($row->phone2) && !empty($row->phone)) {
                        $mergedInfo .= (!empty($mergedInfo) ? ', ' : '') . $row->phone2;
                    }


                    if (empty($mergedInfo)) {
                        $mergedInfo = "Мэдээлэл байхгүй";
                    }

                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">' . $mergedInfo . '</div>';
                })
                ->addColumn('merchantPhone2', function ($row) {
                    return $row->merchantPhone2;
                })
                ->addColumn('merchantAddress', function ($row) {
                    return '<div style="overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;" class="text-center whitespace-nowrap">' . $row->merchantAddress . '</div>';
                })
                ->addColumn('driver', function ($row) {
                    if (Auth::user()->role == 'Customer') {
                        return '';
                    } else {
                        return $row->driver;
                    }
                })
                ->addColumn('actions', function ($row) {
                    $actions = '
                                                       <button type="submit" class="btn btn-info"><a href="' . url('/delivery/detail/' . $row->id) . '" style="color:white;">Дэлгэрэнгүй</a></button>';

                    return $actions;
                })
                ->addColumn('recover', function ($row) {

                    return '<button type="submit" class="btn btn-info" style="margin-bottom:2px;"><a href="' . url('/delivery/recover/' . $row->id) . '"style="color:white;">Сэргээх</a></button><br>
                                    <button type="submit" class="btn btn-info"><a href="' . url('/delivery/detail/' . $row->id) . '" style="color:white;">Дэлгэрэнгүй</a></button>
                           ';
                })
                ->rawColumns(['checkbox', 'track', 'merchantAddress', 'region', 'mergedMerchantParcel', 'actions', 'note', 'comment', 'address', 'status', 'recover', 'shop', 'rating', 'type', 'receivername', 'order_code', 'merchantName', 'merchantPhone1', 'phone'])

                ->setTotalRecords($dataCount)
                ->skipPaging()
                ->make(true);
            return $table;
        }
    }

    public function ExcelExport(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->excel)) {
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $ids = $request->get('ids', array());
                $excel = $request->get('excel', 0);
                $status = $request->get('status', 0);
                $district = $request->get('district', 0);
                $region = $request->get('region', 0);
                $phone = $request->get('phone', 0);
                $address = $request->get('address', 0);
                $note = $request->get('note', 0);
                $tuluv = $request->get('tuluv', 0);
                $start_date = $request->get('start_date', 0);
                $merchant = $request->get('merchant', 0);
                $type = $request->get('type', 0);
                $late = $request->get('late', 0);
                $customer = $request->get('customer', 0);
                $status_100 = $request->get('status_100', 0);
                $end_date = $request->get('end_date', 0);
                $driverselected = $request->get('driver', 0);
                $except_status = $request->get('except_status', 0);
                $except_stat = $request->get('except_stat', 0);
                $status_10 = $request->get('status_10', 0);
                $status_1 = $request->get('status_1', 0);
                $status_6 = $request->get('status_6', 0);
                $status_2 = $request->get('status_2', 0);
                $status_3 = $request->get('status_3', 0);
                $status_4 = $request->get('status_4', 0);
                $status_5 = $request->get('status_5', 0);
                $not_3 = $request->get('not_3', 0);
                $not_4 = $request->get('not_4', 0);
                $not_2 = $request->get('not_2', 0);
                $not_6 = $request->get('not_6', 0);
                $not_5 = $request->get('not_5', 0);
                $not_1 = $request->get('not_1', 0);
                $not_100 = $request->get('not_100', 0);
                $offset = $request->get('start', 0);
                $limit = $request->get('length', 0);

                $Params = [
                    'user_id' => $user_id,
                    'role' => $role,
                    'limit' => $limit,
                    'offset' => $offset,

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
                    'not_5' => $not_5,
                    'not_4' => $not_4,
                    'not_3' => $not_3,
                    'not_1' => $not_1,
                    'not_2' => $not_2,
                    'not_6' => $not_6,
                    'not_100' => $not_100,
                    'tuluv' => $tuluv,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'region' => $region,
                    'district' => $district,
                    'note' => $note,
                    'late' => $late,
                    'customer' => $customer,
                    'phone' => $phone,
                    'address' => $address,
                    'merchant_id' => $merchant,
                    'type' => $type,
                    'driverselected' => $driverselected,

                ];
                $excel_data = array();
                $dataExcel = Delivery::GetExcelData($Params);
                //    dd($dataExcel);
                if (is_array($dataExcel)) {
                    foreach ($dataExcel as $key => $row) {

                        $item = '';
                        $excel_data[$key]['track'] = $row->track;
                        $excel_data[$key]['created_at'] = $row->created_at;

                        if ($row->type == '1') {
                            $excel_data[$key]['type'] = 'Энгийн';
                        } else if ($row->type == '2') {
                            $excel_data[$key]['type'] = 'Цагтай';
                        } else if ($row->type == '3') {
                            $excel_data[$key]['type'] = 'Яаралтай';
                        } else if ($row->type == '4') {
                            $excel_data[$key]['type'] = 'Онц яаралтай';
                        }

                        $excel_data[$key]['shop'] = $row->shop;
                        $excel_data[$key]['order_code'] = $row->order_code;
                        $excel_data[$key]['merchantName'] = $row->merchantName;

                        if (($row->merchantPhone1 != null) && ($row->merchantPhone2 != null)) {
                            $mergedPhones = $row->merchantPhone1 . ',' . $row->merchantPhone2;
                        } else if (($row->merchantPhone1 === null) && ($row
                            ->merchantPhone2 != null)) {
                            $mergedPhones = $row
                                ->merchantPhone2;
                        } else if (($row->merchantPhone1 != null) && ($row
                            ->merchantPhone2 === null)) {
                            $mergedPhones = $row->merchantPhone1;
                        }
                        $excel_data[$key]['merchantPhone'] = $mergedPhones;
                        $excel_data[$key]['merchantAddress'] = $row->merchantAddress;

                        if (($row->goodtype != null) && ($row->parcel_info != null)) {
                            $mergedInfo = $row->goodtype . ',' . $row->parcel_info;
                        } else  if (($row->goodtype === null) && ($row->parcel_info != null)) {
                            $mergedInfo =  $row->parcel_info;
                        } else  if (($row->goodtype != null) && ($row->parcel_info === null)) {
                            $mergedInfo =  $row->goodtype;
                        } else if (($row->goodtype === null) && ($row->parcel_info === null)) {
                            $mergedInfo =  "мэдээлэл алга";
                        }
                        $excel_data[$key]['goodtype'] = $mergedInfo;


                        $excel_data[$key]['number'] = $row->number;
                        $excel_data[$key]['receivername'] = $row->receivername;


                        if (($row->phone != null) && ($row->phone2 != null)) {
                            $mergedPhone = $row->phone . ',' . $row->phone2;
                        } else if (($row->phone === null) && ($row
                            ->phone2 != null)) {
                            $mergedPhone = $row
                                ->phone2;
                        } else if (($row->phone != null) && ($row
                            ->phone2 === null)) {
                            $mergedPhone = $row->phone;
                        }

                        $excel_data[$key]['phone'] = $mergedPhone;
                        $excel_data[$key]['address'] = $row->address;
                        $excel_data[$key]['comment'] = $row->comment;
                        $excel_data[$key]['price'] = $row->price;
                        if ($row->verified == 1) {
                            $ver =  'Тийм';
                        } else {
                            $ver =  'Үгүй';
                        }
                        $excel_data[$key]['verified'] = $ver;
                        $excel_data[$key]['driver'] = $row->driver;
                        if ($row->status == 1) {
                            $excel_data[$key]['status'] = 'Бүртгэгдсэн';
                        } elseif ($row->status == 2) {
                            $excel_data[$key]['status'] = 'Жолоочид хуваарилсан';
                        } elseif ($row->status == 3) {
                            $excel_data[$key]['status'] = 'Хүргэгдсэн';
                        } elseif ($row->status == 4) {
                            $excel_data[$key]['status'] = 'Цуцалсан';
                        } elseif ($row->status == 5) {
                            $excel_data[$key]['status'] = 'Буцаасан';
                        } elseif ($row->status == 6) {
                            $excel_data[$key]['status'] = 'Хүлээгдэж буй';
                        } elseif ($row->status == 10) {
                            $excel_data[$key]['status'] = 'Хүлээн авсан';
                        }
                    }
                }

                //       $export_request = new DeliveryExport($excel_data);

                $export = new DeliveryExport($excel_data);
                $excelFile = Excel::store($export, 'delivery.xlsx', 'public');

                return asset('storage/' . 'delivery.xlsx');
                // $excel = Excel::download($export_request, 'delivery.xlsx');
                // $excel->setContentDisposition('attachment','delivery')->getFile()->move(public_path('/delivery'), 'delivery'.time().'.xlsx');
                // return asset('delivery').'/delivery'.time().'.xlsx';
            }
        } else {
            dd('Err');
        }
    }

    public function changeEstimateData(Request $request)
    {
        if ($request->ajax()) {
            // Check if $request->ids is set and is a non-empty string
            if (is_string($request->ids) && !empty($request->ids)) {
                // Convert the comma-separated string to an array
                $ids = array();
                $ids =  explode(',', $request->ids);
                // $ids = explode(',', $request->ids);

                $updateData = [
                    'estimated' => 2,
                ];

                foreach ($ids as $id) {
                    // Trim each ID to remove potential whitespace
                    $id = trim($id);
                    // Perform your update logic here using $id and $updateData
                    Delivery::where('id', $id)->update($updateData);
                }

                return "Updated successfully";
            } else {
                return "Invalid data format for IDs";
            }
        }

        return "Invalid request";
    }



    public function PrintdeliveryData(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->print)) {
                $user_id = Auth::user()->id;
                $role = Auth::user()->role;
                $arr_ids = explode(",", $request->post('ids'));
                $ids = implode(",", array_filter($arr_ids));
                $print = $request->get('print', 0);
                $excel = $request->get('excel', 0);
                $status = $request->get('status', 0);
                $district = $request->get('district', 0);
                $region = $request->get('region', 0);
                $phone = $request->get('phone', 0);
                $address = $request->get('address', 0);
                $note = $request->get('note', 0);
                $tuluv = $request->get('tuluv', 0);
                $start_date = $request->get('start_date', 0);
                $merchant = $request->get('merchant', 0);
                $type = $request->get('type', 0);
                $late = $request->get('late', 0);
                $customer = $request->get('customer', 0);
                $status_100 = $request->get('status_100', 0);
                $end_date = $request->get('end_date', 0);
                $driverselected = $request->get('driver', 0);
                $except_status = $request->get('except_status', 0);
                $except_stat = $request->get('except_stat', 0);
                $status_10 = $request->get('status_10', 0);
                $status_1 = $request->get('status_1', 0);
                $status_6 = $request->get('status_6', 0);
                $status_2 = $request->get('status_2', 0);
                $status_3 = $request->get('status_3', 0);
                $status_4 = $request->get('status_4', 0);
                $status_5 = $request->get('status_5', 0);
                $not_3 = $request->get('not_3', 0);
                $not_4 = $request->get('not_4', 0);
                $not_2 = $request->get('not_2', 0);
                $not_6 = $request->get('not_6', 0);
                $not_5 = $request->get('not_5', 0);
                $not_1 = $request->get('not_1', 0);
                $not_100 = $request->get('not_100', 0);
                $offset = $request->get('start', 0);
                $limit = $request->get('length', 0);

                $Params = [
                    'ids' => $ids,
                    'user_id' => $user_id,
                    'role' => $role,

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
                    'not_5' => $not_5,
                    'not_4' => $not_4,
                    'not_3' => $not_3,
                    'not_1' => $not_1,
                    'not_2' => $not_2,
                    'not_6' => $not_6,
                    'not_100' => $not_100,
                    'tuluv' => $tuluv,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'region' => $region,
                    'district' => $district,
                    'note' => $note,
                    'late' => $late,
                    'customer' => $customer,
                    'phone' => $phone,
                    'address' => $address,
                    'merchant_id' => $merchant,
                    'type' => $type,
                    'driverselected' => $driverselected,
                ];
                $i = 0;
                $print_data = array();
                $dataExcel = Delivery::GetExcelData($Params);
                $table = '<div class="text-center" style="font-weight:bold;"><h1>' . 'ИЛГЭЭМЖ ХҮЛЭЭЛЦЭХ АКТ №' . '</h1></div><br><br>';
                $table .= '<div class="row"><div class="col-md-6"><div>' . 'LOGO Байгууллагын нэр:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '<div>' . 'Регистрийн дугаар: .........' . '</div>';
                $table .= '<div>' . 'Утас:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '<div>' . 'Хаяг:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '<div>' . 'Огноо:' . '</div>';
                $table .= '<div>' . date('Y-m-d H:i:s') . '</div></div>';
                $table .= '<div class="col-md-6"><div>' . 'Хүлээн авагчийн нэр:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '<div>' . 'Регистрийн дугаар: .........' . '</div>';
                $table .= '<div>' . 'Утас:' . '</div>';
                $table .= '<div>' . Auth::user()->name . '</div>';
                $table .= '</div>';
                $table .= '<table class="table table-striped  table-bordered" style="border-width: 1px;border-style: solid;border-color: black;">
                <thead>
                    <tr>
                    <th class="text-center whitespace-nowrap">#</th>
                    <th class="text-center whitespace-nowrap">Track ID</th>
                    <th class="whitespace-nowrap">Мерчант нэр</th>
                    <th class="whitespace-nowrap">Мерчант хаяг</th>
                    <th class="text-center whitespace-nowrap">Z-код</th>
                    <th class="whitespace-nowrap">Илгээмж</th>
                    <th class="whitespace-nowrap">Тоо ширхэг</th>
                    <th class="text-center whitespace-nowrap">Хүлээн авагч</th>
                    <th class="text-center whitespace-nowrap">Хаягийн мэдээлэл</th>
                    <th class="text-center whitespace-nowrap">Жолооч</th>
                    <th class="text-center whitespace-nowrap">Гарын үсэг</th>
                    </tr>
                </thead>
                <tbody>';
                foreach ($dataExcel as $key => $row) {
                    $table .= "<tr>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>" . ++$i . "</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->track}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->merchantName} {$row->merchantPhone1},{$row->merchantPhone2}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->merchantAddress}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->order_code},{$row->shop}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->parcel_info}, {$row->goodtype}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->number}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->receivername},{$row->phone},{$row->phone2}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black; overflow-wrap: break-word; white-space: pre-wrap; width: 270px !important;'>{$row->district} , {$row->address}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'>{$row->driver}</td>
        <td style='border-width: 1px; border-style: solid; border-color: black;'><img src='" . asset('storage/') . "/{$row->sign_image}' width='100'/></td>
    </tr>";
                }

                $table .= '</tbody></table><br>' . '' . '</div><br>';
                $table .= '<div style="margin-left:350px;">' . 'Хүлээлгэж өгсөн эд хариуцагч................................/................./' . '</div><br>';
                $table .= '<div style="margin-left:350px;">' . 'Хүлээн авагч................................/................./' . '</div><br>';
                $table .= '<div style="margin-left:350px;">' . 'Шалгасан нягтлан бодогч................................/................./' . '</div><br><br>';
                $table .= '<div style="margin-left:350px;">' . 'Тамга/ Тэмдэг' . '</div><br>';

                echo $table;
            }
        }
    }
}
