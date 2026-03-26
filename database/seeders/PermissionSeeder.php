<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::truncate();
        $permissionsToSeed = [
            // Dashboard
            [
                'name' => 'manage_dashboard',
                'category_name' => 'dashboard',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'хянах_самбар',
                'category_name' => 'dashboard',
                'default_roles' => ['Admin']
            ],

            // Orders - Full CRUD
            [
                'name' => 'manage_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'захиалга_үүсгэх',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'захиалга_жагсаалт',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_order',
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
            [
                'name' => 'export_order',
                'category_name' => 'order',
                'default_roles' => ['Admin']
            ],

            // Regional - Full CRUD
            [
                'name' => 'manage_regional',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_regional',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'бүс_жагсаалт',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'бүс_үүсгэх',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_regional',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_regional',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_regional',
                'category_name' => 'regional',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_regional',
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

            // Warehouse - Full CRUD
            [
                'name' => 'manage_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'warehouse_details',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_warehouse',
                'category_name' => 'warehouse',
                'default_roles' => ['Admin']
            ],

            // Address - Full CRUD
            [
                'name' => 'manage_address',
                'category_name' => 'address',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_address',
                'category_name' => 'address',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_address',
                'category_name' => 'address',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_address',
                'category_name' => 'address',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_address',
                'category_name' => 'address',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_address',
                'category_name' => 'address',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_address',
                'category_name' => 'address',
                'default_roles' => ['Admin']
            ],

            // Goods - Full CRUD
            [
                'name' => 'manage_goods',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_good',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_good',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_good',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_good',
                'category_name' => 'goods',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_good',
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

            // Delivery - Full CRUD
            [
                'name' => 'manage_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_delivery',
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
            [
                'name' => 'verify_req',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'import_excel',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'export_delivery',
                'category_name' => 'delivery',
                'default_roles' => ['Admin']
            ],

            // Category - Full CRUD
            [
                'name' => 'manage_category',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_category',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_category',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_category',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_category',
                'category_name' => 'category',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_category',
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

            // Phone - Full CRUD
            [
                'name' => 'manage_phone',
                'category_name' => 'phone',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_phone',
                'category_name' => 'phone',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_phone',
                'category_name' => 'phone',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_phone',
                'category_name' => 'phone',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_phone',
                'category_name' => 'phone',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_phone',
                'category_name' => 'phone',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_phone',
                'category_name' => 'phone',
                'default_roles' => ['Admin']
            ],

            // Operation Log
            [
                'name' => 'manage_operation_log',
                'category_name' => 'operation',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_operation_log',
                'category_name' => 'operation',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_operation_log',
                'category_name' => 'operation',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'үйлдлийн_лог',
                'category_name' => 'operation',
                'default_roles' => ['Admin']
            ],

            // Consumer - Full CRUD
            [
                'name' => 'manage_consumer',
                'category_name' => 'consumer',
                'default_roles' => ['Admin']
            ],
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
                'name' => 'update_consumer',
                'category_name' => 'consumer',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_consumer',
                'category_name' => 'consumer',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_consumer',
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

            // Users - Full CRUD
            [
                'name' => 'manage_users',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'хэрэглэгч',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],
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
            [
                'name' => 'update_user',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_user',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_user',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_user',
                'category_name' => 'user',
                'default_roles' => ['Admin']
            ],

            // Role - Full CRUD
            [
                'name' => 'manage_role',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'эрхийн_зохицуулалт',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],
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
            [
                'name' => 'update_role',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_role',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_role',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_role',
                'category_name' => 'role',
                'default_roles' => ['Admin']
            ],

            // Invoice - Full CRUD
            [
                'name' => 'manage_invoice',
                'category_name' => 'invoice',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_invoice',
                'category_name' => 'invoice',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_invoice',
                'category_name' => 'invoice',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_invoice',
                'category_name' => 'invoice',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_invoice',
                'category_name' => 'invoice',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_invoice',
                'category_name' => 'invoice',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_invoice',
                'category_name' => 'invoice',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'manage_invoice_profile',
                'category_name' => 'invoice',
                'default_roles' => ['Admin']
            ],

            // Feedback - Full CRUD
            [
                'name' => 'manage_feedback',
                'category_name' => 'feedback',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_feedback',
                'category_name' => 'feedback',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_feedback',
                'category_name' => 'feedback',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_feedback',
                'category_name' => 'feedback',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_feedback',
                'category_name' => 'feedback',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_feedback',
                'category_name' => 'feedback',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_feedback',
                'category_name' => 'feedback',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'resolve_feedback',
                'category_name' => 'feedback',
                'default_roles' => ['Admin']
            ],

            // Banner - Full CRUD
            [
                'name' => 'manage_banner',
                'category_name' => 'banner',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_banner',
                'category_name' => 'banner',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_banner',
                'category_name' => 'banner',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_banner',
                'category_name' => 'banner',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_banner',
                'category_name' => 'banner',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_banner',
                'category_name' => 'banner',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_banner',
                'category_name' => 'banner',
                'default_roles' => ['Admin']
            ],

            // Setting - Full CRUD
            [
                'name' => 'manage_setting',
                'category_name' => 'setting',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_setting',
                'category_name' => 'setting',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_setting',
                'category_name' => 'setting',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_setting',
                'category_name' => 'setting',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_setting',
                'category_name' => 'setting',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_setting',
                'category_name' => 'setting',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_setting',
                'category_name' => 'setting',
                'default_roles' => ['Admin']
            ],

            // Report
            [
                'name' => 'manage_report',
                'category_name' => 'report',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_report',
                'category_name' => 'report',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_report',
                'category_name' => 'report',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'export_report',
                'category_name' => 'report',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_driver_report',
                'category_name' => 'report',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_customer_report',
                'category_name' => 'report',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_general_report',
                'category_name' => 'report',
                'default_roles' => ['Admin']
            ],

            // Driver
            [
                'name' => 'manage_driver',
                'category_name' => 'driver',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_driver',
                'category_name' => 'driver',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_driver',
                'category_name' => 'driver',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'export_driver',
                'category_name' => 'driver',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_driver_location',
                'category_name' => 'driver',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_driver_request',
                'category_name' => 'driver',
                'default_roles' => ['Admin']
            ],

            // Item - Full CRUD
            [
                'name' => 'manage_item',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_item',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_item',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_item',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_item',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_item',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_item',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'add_item_quantity',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'decrease_item_quantity',
                'category_name' => 'item',
                'default_roles' => ['Admin']
            ],

            // Notification - Full CRUD
            [
                'name' => 'manage_notification',
                'category_name' => 'notification',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_notification',
                'category_name' => 'notification',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'масс_мэдэгдэл',
                'category_name' => 'notification',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'send_notification',
                'category_name' => 'notification',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_notification',
                'category_name' => 'notification',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_notification',
                'category_name' => 'notification',
                'default_roles' => ['Admin']
            ],

            // Ware - Full CRUD
            [
                'name' => 'manage_ware',
                'category_name' => 'ware',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'барааны_цэс',
                'category_name' => 'ware',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'create_ware',
                'category_name' => 'ware',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'edit_ware',
                'category_name' => 'ware',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'update_ware',
                'category_name' => 'ware',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'view_ware',
                'category_name' => 'ware',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'list_ware',
                'category_name' => 'ware',
                'default_roles' => ['Admin']
            ],
            [
                'name' => 'delete_ware',
                'category_name' => 'ware',
                'default_roles' => ['Admin']
            ],
        ];


        foreach ($permissionsToSeed as $record) {
            $category = \App\Models\PermissionCategory::where('name', $record['category_name'])->first();
            
            if (!$category) {
                \Log::warning("Permission category '{$record['category_name']}' not found. Skipping permission '{$record['name']}'.");
                continue;
            }

            $permission = Permission::firstOrCreate(
                ['name' => $record['name'], 'guard_name' => 'web'],
                ['category_id' => $category->id]
            );

            // Update category_id if permission already existed but category_id was missing
            if (!$permission->category_id) {
                $permission->category_id = $category->id;
                $permission->save();
            }

            foreach ($record['default_roles'] as $roleName) {
                $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                if ($role && !$role->hasPermissionTo($record['name'])) {
                    $role->givePermissionTo($record['name']);
                }
            }
        }

    }
}
