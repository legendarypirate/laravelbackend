<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as BasePermission;

Class Permission extends BasePermission
{
    protected $table = 'permissions';

    // Add route to this array if only one permission should be checked against a route.
    // e.g. 'permission-name' => ['route-1', 'route-1']
    // access to route-1 will be grated if only user has access to permission 'permission-name'
    public const ROUTE_MATCH = [
        'manage_dashboard' => ['home'],

        /*orders*/
        'manage_order' => ['order.list'],
        'create_order' => ['orders.create','orders.store'],
        'order_details' => ['orders.detail'],
        'delete_order' => ['orders.delete'],
        'change_order_status' => ['change_status'],
        'change_region' => ['change_bus'],
        'change_driver' => ['change_driver'],

        /*regional*/
        'manage_regional' => ['region.manage'],
        'create_regional' => ['region.create','region.save'],
        'regional_details' => ['region.manage'],
        'delete_regional' => ['region.delete'],

        /*warehouse*/
        'manage_warehouse' => ['branch.manage'],
        'create_warehouse' => ['branch.create','branch.store'],
        'warehouse_details' => ['branch.detail'],
        'edit_warehouse' => ['branch.edit','branch.update'],
        'delete_warehouse' => ['branch.delete'],

        /*address*/
        'manage_address' => ['address.manage'],

        /*goods*/
        'manage_goods' => ['good.manage'],
        'create_good' => ['good.create','good.store'],
        'good_details' => ['good.manage'],
        'delete_goods' => ['good.delete'],

        /*delivery*/
        'manage_delivery' => ['request.manage'],
        'create_delivery' => ['request.create','request.store'],
        'delivery_details' => ['request.create','request.store'],
        'delete_delivery' => ['request.create','request.store'],
        'change_delivery_status' => ['request.change_status'],
        'change_delivery_regional' => ['request.change_bus'],
        'change_delivery_driver' => ['request.change_driver'],
        'verify_req' => ['request.change_verify'],
        'import_excel' =>['request.request.excel_import_file'],

        /*category*/
        'manage_category' => ['category.manage'],
        'create_category' => ['category.create','category.store'],
        'category_details' => ['category.manage'],
        'delete_category' => ['category.delete'],

        /*phone*/
        'manage_phone' => ['phone.manage'],

        /*operation*/
        'manage_operation_log' => ['log.manage'],

        /*consumer*/
        'manage_consumer' => ['user.manage'],
        'create_consumer' => ['user.create','user.store'],
        'edit_consumer' => ['user.edit','user.update'],
        'consumer_details' => ['getProfile'],
        'delete_consumer' => ['user.delete'],

        /*user*/
        'manage_users' => ['users.manage'],
        'create_user' => ['users.create','users.store'],
        'edit_user' => ['users.edit','users.update'],

        /*role*/
        'manage_role' => ['role.manage'],
        'create_role' => ['role.create','role.store'],
        'edit_role' => ['role.edit','role.update'],
    ];

}
