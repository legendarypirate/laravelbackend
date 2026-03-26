<?php
  
namespace App\Imports;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Marks;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Merchant;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithLimit;
use Illuminate\Support\Facades\Auth;
class RequestImportExcel implements ToCollection, WithStartRow,WithMultipleSheets, WithValidation, WithLimit
{
    
    /**
    * @return int
    */
    public function startRow(): int
    {
        return 1;
    }
    public function limit(): int
    {
        return 200;
    }
    public function startCell(): string
    {
        return 'A1';
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Sheet1' => $this,
        ];
    }

    public function rules(): array
    {
        return [];
    }

    
    public function collection(Collection $rows)
    {
    

       
        foreach ($rows as $key=>$row) {
           

            if($key>=1){
               
                if(Auth::user()->role!='Customer'){
                    $data=$row[0];
                    
                }
                else {
                    $data=Auth::user()->name;
                }
             
            
                $merchant=Merchant::where('merchantName',$row[2])->where('merchantPhone1',$row[3])->where('merchantAddress', $row[5])->first();
               
                if(isset($merchant->id)){
                $number = (int) preg_replace("/[^0-9]/", "", $row[7]);
                $price = (int) preg_replace("/[^0-9]/", "", $row[13]);


                if (strcasecmp($row[0], 'Энгийн') === 0) {
                    $status_id = 1;
           
                        if(isset(Auth::user()->engiin)){
                           $deliveryprice = Auth::user()->engiin;
                        }else{
                           $defaultPrice = Setting::where('type', 1)->first();
                           $deliveryprice = $defaultPrice->price;
                        } 
            

                } else if (strcasecmp($row[0], 'Цагтай') === 0) {
                    $status_id = 2;
                    if(isset(Auth::user()->tsagtai)){
                    $deliveryprice = Auth::user()->tsagtai;
                        }else{
                        $defaultPrice = Setting::where('type', 2)->first();
                            $deliveryprice = $defaultPrice->price;
                        } 
                }else if (strcasecmp($row[0], 'Яаралтай') === 0) {
                    $status_id = 3;
                     if(isset(Auth::user()->yaraltai)){
                    $deliveryprice = Auth::user()->yaraltai;
                }else{
                    $defaultPrice = Setting::where('type', 3)->first();
                    $deliveryprice = $defaultPrice->price;
                } 
                }else if (strcasecmp($row[0], 'Онц яаралтай') === 0) {
                    $status_id = 4;
                    if(isset(Auth::user()->onts_yaraltai)){
                    $deliveryprice = Auth::user()->onts_yaraltai;
                }else{
                   $defaultPrice = Setting::where('type', 4)->first();
                    $deliveryprice = $defaultPrice->price;
                } 
                }else{
                      $status_id = 1;
                       if(isset(Auth::user()->engiin)){
                           $deliveryprice = Auth::user()->engiin;
                        }else{
                           $defaultPrice = Setting::where('type', 1)->first();
                           $deliveryprice = $defaultPrice->price;
                        } 
                }
                    $id = $merchant->id;
                    $data_array = [
                    'track' => 'CH'.rand(100000,999999).Auth::user()->id,
                    'status'=> 1,
                    'shop'=> Auth::user()->name,
                    'created_at' => Carbon::now(),
                    'type'=>$status_id,
                    'order_code'=>$row[1],
                    'merchant_id'=> $id,
                    'parcel_info'=> $row[6],
                    'number'=> $number,
                    'receivername'=> $row[8],
                    'phone'=> $row[9],
                    'phone2'=> $row[10],
                    'address'=> $row[11],
                    'comment'=>$row[12],
                    'price'=>$price,
                    'deliveryprice'=>$deliveryprice,
                ];
                }else{
                   
                $merchantData = [
                    'user_id' => Auth::user()->id,
                    'merchantName' => $row[2],
                    'merchantAddress' => $row[5],
                    'merchantPhone1' => $row[3],
                    'merchantPhone2' => $row[4],
                    'merchantWhat3Words' => '',
                ];
                $id = DB::table('merchant')->insertGetId($merchantData);
                 $number = (int) preg_replace("/[^0-9]/", "", $row[7]);
                $price = (int) preg_replace("/[^0-9]/", "", $row[13]);
                if (strcasecmp($row[0], 'Энгийн') === 0) {
                    $status_id = 1;
           
                        if(isset(Auth::user()->engiin)){
                           $deliveryprice = Auth::user()->engiin;
                        }else{
                           $defaultPrice = Setting::where('type', 1)->first();
                           $deliveryprice = $defaultPrice->price;
                        } 
            

                } else if (strcasecmp($row[0], 'Цагтай') === 0) {
                    $status_id = 2;
                    if(isset(Auth::user()->tsagtai)){
                    $deliveryprice = Auth::user()->tsagtai;
                        }else{
                        $defaultPrice = Setting::where('type', 2)->first();
                            $deliveryprice = $defaultPrice->price;
                        } 
                }else if (strcasecmp($row[0], 'Яаралтай') === 0) {
                    $status_id = 3;
                     if(isset(Auth::user()->yaraltai)){
                    $deliveryprice = Auth::user()->yaraltai;
                }else{
                    $defaultPrice = Setting::where('type', 3)->first();
                    $deliveryprice = $defaultPrice->price;
                } 
                }else if (strcasecmp($row[0], 'Онц яаралтай') === 0) {
                    $status_id = 4;
                    if(isset(Auth::user()->onts_yaraltai)){
                    $deliveryprice = Auth::user()->onts_yaraltai;
                }else{
                   $defaultPrice = Setting::where('type', 4)->first();
                    $deliveryprice = $defaultPrice->price;
                } 
                }else{
                      $status_id = 1;
                       if(isset(Auth::user()->engiin)){
                           $deliveryprice = Auth::user()->engiin;
                        }else{
                           $defaultPrice = Setting::where('type', 1)->first();
                           $deliveryprice = $defaultPrice->price;
                        } 
                }

                $data_array = [
                    'track' => 'CH'.rand(100000,999999).Auth::user()->id,
                    'status'=> 1,
                    'shop'=> Auth::user()->name,
                    'created_at' => Carbon::now(),
                    'type'=>$status_id,
                    'order_code'=>$row[1],
                    'merchant_id'=> $id,
                    'parcel_info'=> $row[6],
                    'number'=> $number,
                    'receivername'=> $row[8],
                    'phone'=> $row[9],
                    'phone2'=> $row[10],
                    'address'=> $row[11],
                    'comment'=>$row[12],
                    'price'=>$price,
                    'deliveryprice'=>$deliveryprice,
                ];
                }
               
                //dd($data_array);
                DB::table('deliveries')->insert($data_array);
                  
            }
            
        }

        return back()->with('success', 'Request data imported successfully.');
    }

}

