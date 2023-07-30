<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::truncate();
        $permissionsToSeed = [
            [
                'name' => 'manage_dashboard',
                'category_name' => 'dashboard',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_regional',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_address',
                'category_name' => 'address',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_goods',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_category',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_phone',
                'category_name' => 'phone',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_operation_log',
                'category_name' => 'operation',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_consumer',
                'category_name' => 'consumer',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_users',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_role',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],

            /*orders*/
            [
                'name' => 'create_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'order_details',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'change_order_status',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'change_region',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'change_driver',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],

            /*regional*/
            [
                'name' => 'create_regional',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'regional_details',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_regional',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],

            /*warehouse*/
            [
                'name' => 'create_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'warehouse_details',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],

            /*goods*/
            [
                'name' => 'create_good',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'good_details',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_goods',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],

            /*delivery*/
            [
                'name' => 'create_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delivery_details',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'change_delivery_status',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'change_delivery_regional',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'change_delivery_driver',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],

            /*category*/
            [
                'name' => 'create_category',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'category_details',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_category',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],

            /*consumer*/
            [
                'name' => 'create_consumer',
                'category_name' => 'consumer',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_consumer',
                'category_name' => 'consumer',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'consumer_details',
                'category_name' => 'consumer',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_consumer',
                'category_name' => 'consumer',
                'default_roles' => ['Admin']
            ],

            /*users*/
            [
                'name' => 'create_user',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_user',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],

            /*role*/
            [
                'name' => 'create_role',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_role',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],
        ];


        foreach ($permissionsToSeed as $record) {

            Permission::create([
                'name' => $record['name'],
                'category_id' => \App\PermissionCategory::where('name', $record['category_name'])->first()->id,
            ]);

            foreach ($record['default_roles'] as $roleName) {
                $adminRole = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                $adminRole->givePermissionTo($record['name']);
            }
        }

    }
}
