<?php
  
namespace App\Imports;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Marks;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

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

        // user validation
        foreach ($rows as $key=>$row) {
            $user = DB::table('users')
            ->where('name', '=', $row[0])
            ->get();
            
            

            if(($key >= 1 && empty($user[0]))){
                return back()->with('error', 'User  not valid');
            }
        }
        foreach ($rows as $key=>$row) {
            $missing_column = array();
            if( $key == 0 ){
              
                
                if(trim($row[0]) != 'Хүлээн авагчийн нэр'){
                    $missing_column[] = 'Хүлээн авагчийн нэр баганыг оруулна уу';
                }
                if(trim($row[1]) != 'Утасны дугаар'){
                    $missing_column[] = 'Утасны дугаар баганыг оруулна уу';
                }

                if(trim($row[11]) != 'Дэлгэрэнгүй хаяг'){
                    $missing_column[] = 'Дэлгэрэнгүй хаяг баганыг оруулна уу';
                }

                if(trim($row[12]) != 'Өртөг'){
                    $missing_column[] = 'Өртөг баганыг оруулна уу';
                }

                if(trim($row[13]) != 'Баглаа боодлын тоо'){
                    $missing_column[] = 'Баглаа боодлын тоо баганыг оруулна уу';
                }

                if(trim($row[14]) != 'Овор'){
                    $missing_column[] = 'Овор баганыг оруулна уу';
                }

                if(trim($row[15]) != 'Жин'){
                    $missing_column[] = 'Жин баганыг оруулна уу';
                }

                if(trim($row[16]) != 'Хүргэлтийн төрөл'){
                    $missing_column[] = 'Хүргэлтийн төрөл баганыг оруулна уу';
                }

                if(trim($row[17]) != 'Нэмэлт тайлбар'){
                    $missing_column[] = 'Нэмэлт тайлбар баганыг оруулна уу';
                }
                if(!empty($missing_column)){
                    $errors = implode("<br> ",$missing_column);
                    return back()->with('error', $errors);
                }

            }

            if($key>=1){
               
                if(Auth::user()->role!='Customer'){
                    $data=$row[0];
                }
                else {
                    $data=Auth::user()->name;
                }
                
            
                $data_array = [
                    'shop'=>$data,
                    'phone'=> $row[1],
                    'address'=> $row[2],
                    'price'=> $row[3],
                    'received'=> $row[4],
                    'number'=> $row[5],
                    'size'=> $row[6],
                    'mass'=> $row[7],
                    'type'=> $row[8],
                    'comm'=> $row[9],
                    'region'=> $row[10],
                    'bgood'=> $row[11],
                    'deliveryprice'=> 5000,
                    'created_at' => Carbon::now(),

                ];
                DB::table('deliveries')->insert($data_array);
            

                
            }
            
        }

        return back()->with('success', 'Request data imported successfully.');
    }

}

