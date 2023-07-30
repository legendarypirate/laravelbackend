<?php
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionCategoriesSeeder extends Seeder
{
    public function run()
    {
        \App\Models\PermissionCategory::truncate();

        $data = [
            ['name'=>'dashboard','guard_name'=>'web'],
            ['name'=>'order','guard_name'=>'web'],
            ['name'=>'regional','guard_name'=>'web'],
            ['name'=>'warehouse','guard_name'=>'web'],
            ['name'=>'address','guard_name'=>'web'],
            ['name'=>'goods','guard_name'=>'web'],
            ['name'=>'delivery','guard_name'=>'web'],
            ['name'=>'category','guard_name'=>'web'],
            ['name'=>'phone','guard_name'=>'web'],
            ['name'=>'operation','guard_name'=>'web'],
            ['name'=>'consumer','guard_name'=>'web'],
            ['name'=>'user','guard_name'=>'web'],
            ['name'=>'role','guard_name'=>'web'],
        ];

        \App\Models\PermissionCategory::insert($data);
    }
}
