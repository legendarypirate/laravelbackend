<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    
    public function index()
    {
        return view('admin.good.index');
    }

    public function income()
    {
        return view('admin.good.income');
    }

    public function gooddata($name){
        $list=Good::where('shop',$name)->get();
        return response()->json(['data'=>$list,'success'=>true]);
    }

    public function gd($name){
        $list=Good::where('shop',$name)->get();
        return response()->json(['data'=>$list,'success'=>true]);
    }


public function create(Request $request)
{
    // Validation
    $request->validate([
        'goodname' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'count' => 'required|integer|min:0',
    ]);

    // Handle image upload
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('items', 'public');
    }

    // Create new item
    $item = Item::create([
        'name' => $request->goodname,
        'image' => $imagePath,
        'quantity' => $request->count,
        'in_delivery' => 0,
        'delivered' => 0,
    ]);

    // Get current user info
    $currentUser = auth()->user();
    $userId = $currentUser ? $currentUser->id : '0';
    $userName = $currentUser ? $currentUser->name : 'System';

    // Create complete history record (filling all columns except image)
    DB::table('histories')->insert([
        'item_id' => $item->id,
        'user_id' => $userId, // Use string as per your table structure
        'goodname' => $request->goodname,
        'comment' => 'Анхны бараа бүртгэл: ' . $request->count . ' ширхэг',
        'quantity' => $request->count,
        'type' => 'in',
        'driver_id' => 0, // Default 0 for system operations
        'operation_by' => $userName,
        'image' => null, // Explicitly set to null (not required)
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('item.list')->with('success', 'Бараа амжилттай нэмэгдлээ!');
}

    public function add(Request $request){
        $good=Good::find($request->goodid);
        if($request->type==1){
            $good->count=$good->count+$request->count;
            $value=Auth::user()->name.', нь '.$request->goodname.' бараа орлогодлоо.';
        } else {
            $good->count=$good->count-$request->count;
            $value=Auth::user()->name.', нь '.$request->goodname.' бараа зарлагадлаа.';
        }
        $good->save();

        $log = new Log();
        $log -> value = $value;
        $log -> phone = '';
        $log->staff=Auth::user()->name;
        $log -> save();

        Alert::success('Бараа', 'Тоо өөрчлөгдлөө');

        return redirect('/good/income')->with('message','Амжилттай хадгалагдлаа');

    }

    public function good($name){
        echo json_encode(DB::table('items')->where('shop', $name)->get());
    }

    public function list(){
        if(Auth::user()->role!='customer'){
            $good=Item::all();
        } else {
            $good=Item::where('user_id',Auth::user()->id)->get();
        }
        return view('admin.item.list',compact('good'));
    }

    public function gooddetail($id){
        $black = DB::table('items')->where('id','=',$id)->get();
            return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data'=>$black
        ], 200);
    }

    public function goodpost(Request $request){
      
        $good=DB::table('items')->where('shop',$request->cname)->where('goodname',$request->goodname)->get();
            return response()->json([
            'success' => true,
            'message' => 'Амжилттай',
            'data'=>$good
        ], 200);
    }

    public function delete($id){
        $user = Item::find($id);
        $user->delete();


        Alert::success('Бараа', 'Амжилттай устгагдлаа');

        return redirect('/item/list')->with('message','deleted');
    }

    public function addQuantity(Request $request)
    {
        \Log::info('Add Quantity Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'driver_id' => 'nullable|exists:users,id',
            'reason' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Баталгаажуулалт амжилтгүй боллоо.');
        }

        try {
            DB::beginTransaction();

            // Get the item
            $item = DB::table('items')->where('id', $request->item_id)->first();
            if (!$item) {
                throw new \Exception('Item not found');
            }

            // 1. Update main item quantity (INCREASE warehouse)
            $updated = DB::table('items')
                ->where('id', $request->item_id)
                ->update([
                    'quantity' => DB::raw('quantity + ' . $request->quantity),
                    'updated_at' => now()
                ]);

            if (!$updated) {
                throw new \Exception('Failed to update item quantity');
            }

            // 2. If driver is selected, MOVE quantity FROM driver TO warehouse
            if ($request->driver_id) {
                // Check if driver has sufficient quantity to move
                $driverItem = DB::table('driver_items')
                    ->where('driver_id', $request->driver_id)
                    ->where('item_id', $request->item_id)
                    ->first();

                if (!$driverItem || $driverItem->quantity < $request->quantity) {
                    throw new \Exception('Жолооч дээр хүрэлцэхгүй байна. Боломжит үлдэгдэл: ' . ($driverItem->quantity ?? 0));
                }

                // Update driver's item assignment - DECREASE driver (move FROM driver)
                DB::table('driver_items')
                    ->where('driver_id', $request->driver_id)
                    ->where('item_id', $request->item_id)
                    ->update([
                        'quantity' => DB::raw('quantity - ' . $request->quantity),
                        'updated_at' => now()
                    ]);

                // Update in_delivery count - DECREASE (less items with drivers)
                DB::table('items')
                    ->where('id', $request->item_id)
                    ->update([
                        'in_delivery' => DB::raw('in_delivery - ' . $request->quantity),
                        'updated_at' => now()
                    ]);

                // Remove record if quantity becomes 0
                DB::table('driver_items')
                    ->where('driver_id', $request->driver_id)
                    ->where('item_id', $request->item_id)
                    ->where('quantity', '<=', 0)
                    ->delete();
                
                $comment = $request->reason ?? 'Жолоочноос агуулах руу шилжүүлсэн: ' . $request->quantity;
            } else {
                $comment = $request->reason ?? 'Агуулахад нэмэгдсэн: ' . $request->quantity;
            }

            // 3. Record transaction history
            $historyData = [
                'item_id' => $request->item_id,
                'user_id' => auth()->id(),
                'goodname' => $item->name,
                'comment' => $comment,
                'quantity' => $request->quantity,
                'type' => 'in',
                'driver_id' => $request->driver_id,
                'operation_by' => auth()->user()->name,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (isset($item->image) && !empty($item->image)) {
                $historyData['image'] = $item->image;
            }

            DB::table('histories')->insert($historyData);

            DB::commit();

            $successMessage = $request->driver_id
                ? 'Тоо хэмжээ жолоочноос агуулах руу шилжүүллээ!'
                : 'Тоо хэмжээ агуулахад амжилттай нэмэгдлээ!';

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error adding quantity: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Алдаа гарлаа: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Decrease quantity from item (with optional driver assignment)
     */
    public function decreaseQuantity(Request $request)
    {
        \Log::info('Decrease Quantity Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'driver_id' => 'nullable|exists:users,id',
            'reason' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Баталгаажуулалт амжилтгүй боллоо.');
        }

        try {
            DB::beginTransaction();

            // Get the item
            $item = DB::table('items')->where('id', $request->item_id)->first();
            if (!$item) {
                throw new \Exception('Бараа олдсонгүй');
            }

            // Check if warehouse has sufficient quantity
            if ($item->quantity < $request->quantity) {
                throw new \Exception('Агуулахад хүрэлцэхгүй байна. Боломжит үлдэгдэл: ' . $item->quantity);
            }

            // 1. Update main item quantity (DECREASE warehouse)
            $updated = DB::table('items')
                ->where('id', $request->item_id)
                ->update([
                    'quantity' => DB::raw('quantity - ' . $request->quantity),
                    'updated_at' => now()
                ]);

            if (!$updated) {
                throw new \Exception('Барааны тоо хэмжээ шинэчлэхэд алдаа гарлаа');
            }

            // 2. If driver is selected, MOVE quantity FROM warehouse TO driver
            if ($request->driver_id) {
                $driverItem = DB::table('driver_items')
                    ->where('driver_id', $request->driver_id)
                    ->where('item_id', $request->item_id)
                    ->first();

                if ($driverItem) {
                    // Update existing record - ADD to driver
                    DB::table('driver_items')
                        ->where('driver_id', $request->driver_id)
                        ->where('item_id', $request->item_id)
                        ->update([
                            'quantity' => DB::raw('quantity + ' . $request->quantity),
                            'updated_at' => now()
                        ]);
                } else {
                    // Create new record - ADD to driver
                    DB::table('driver_items')->insert([
                        'driver_id' => $request->driver_id,
                        'item_id' => $request->item_id,
                        'quantity' => $request->quantity,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Update in_delivery count - INCREASE (more items with drivers)
                DB::table('items')
                    ->where('id', $request->item_id)
                    ->update([
                        'in_delivery' => DB::raw('in_delivery + ' . $request->quantity),
                        'updated_at' => now()
                    ]);
                
                $comment = $request->reason . ' (Агуулахаас жолооч руу шилжүүлсэн)';
            } else {
                $comment = $request->reason;
            }

            // 3. Record transaction history
            $historyData = [
                'item_id' => $request->item_id,
                'user_id' => auth()->id(),
                'goodname' => $item->name,
                'comment' => $comment,
                'quantity' => $request->quantity,
                'type' => 'out',
                'driver_id' => $request->driver_id,
                'operation_by' => auth()->user()->name,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (isset($item->image) && !empty($item->image)) {
                $historyData['image'] = $item->image;
            }

            DB::table('histories')->insert($historyData);

            DB::commit();

            $successMessage = $request->driver_id
                ? 'Тоо хэмжээ агуулахаас жолооч руу шилжүүллээ!'
                : 'Тоо хэмжээ агуулахаас амжилттай хасагдлаа!';

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error decreasing quantity: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Алдаа гарлаа: ' . $e->getMessage())->withInput();
        }
    }

     public function checkWarehouseQuantity($itemId, $quantity)
    {
        try {
            $item = DB::table('items')->where('id', $itemId)->first();
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Бараа олдсонгүй'
                ]);
            }

            $hasSufficientQuantity = $item->quantity >= $quantity;

            return response()->json([
                'success' => $hasSufficientQuantity,
                'current_quantity' => $item->quantity,
                'requested_quantity' => $quantity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Шалгахад алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if driver has sufficient quantity for the operation
     */
    public function checkDriverQuantity($itemId, $driverId, $quantity, $operation)
    {
        try {
            $driverItem = DB::table('driver_items')
                ->where('driver_id', $driverId)
                ->where('item_id', $itemId)
                ->first();

            // For ADD operation with driver: we're moving FROM driver TO warehouse
            // So we need to check if driver has enough quantity to give
            if ($operation === 'add') {
                $hasSufficientQuantity = $driverItem && $driverItem->quantity >= $quantity;
                $currentQuantity = $driverItem ? $driverItem->quantity : 0;
            }
            // For DECREASE operation with driver: we're moving FROM warehouse TO driver
            // No need to check driver quantity since we're adding to driver
            else {
                $hasSufficientQuantity = true;
                $currentQuantity = $driverItem ? $driverItem->quantity : 0;
            }

            return response()->json([
                'success' => $hasSufficientQuantity,
                'current_quantity' => $currentQuantity,
                'requested_quantity' => $quantity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Шалгахад алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get item distribution by drivers
     */
    public function getItemDrivers($itemId)
    {
        try {
            $driverItems = DB::table('driver_items')
                ->join('users', 'driver_items.driver_id', '=', 'users.id')
                ->join('items', 'driver_items.item_id', '=', 'items.id')
                ->where('driver_items.item_id', $itemId)
                ->where('driver_items.quantity', '>', 0)
                ->select(
                    'driver_items.quantity',
                    'users.name as driver_name',
                    'items.name as item_name'
                )
                ->orderBy('driver_items.quantity', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $driverItems,
                'total_in_delivery' => $driverItems->sum('quantity')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching item drivers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Жолоочдын мэдээлэл авахад алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get item history for modal - FIXED to use item_id
     */
    public function getHistory($id)
    {
        try {
            \Log::info('Fetching history for item:', ['item_id' => $id]);

            // Check if item exists
            $item = DB::table('items')->where('id', $id)->first();
            
            if (!$item) {
                \Log::warning('Item not found:', ['item_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Бараа олдсонгүй'
                ], 404);
            }

            \Log::info('Found item:', ['item_name' => $item->name]);

            // Get histories for this specific item using item_id (include delivery info when present)
            $histories = DB::table('histories')
                ->leftJoin('users', 'histories.driver_id', '=', 'users.id')
                ->leftJoin('deliveries', 'histories.delivery_id', '=', 'deliveries.id')
                ->where('histories.item_id', $id)
                ->select(
                    'histories.*',
                    'users.name as driver_name',
                    'deliveries.id as delivery_display_id',
                    'deliveries.address as delivery_address',
                    'deliveries.phone as delivery_phone',
                    'deliveries.number as delivery_number',
                    'deliveries.shop as delivery_shop'
                )
                ->orderBy('histories.created_at', 'desc')
                ->get();

            \Log::info('Retrieved histories for item:', [
                'count' => $histories->count(),
                'item_id' => $id,
                'item_name' => $item->name
            ]);

            return response()->json([
                'success' => true,
                'data' => $histories,
                'item_name' => $item->name
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Түүх ачаалахад алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }
    }
 
    
}
