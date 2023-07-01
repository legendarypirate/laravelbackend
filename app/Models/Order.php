<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class Order extends Model
{
    /**
     * Query for `Delivery` data
     *
     * @return mixed
     */
    public static function GetExcelData($Params=null)
    {
        $deliveryTable = (new Order())->getTable();
        $offset = isset($Params['offset']) ? $Params['offset'] : 0;
        $limit = isset($Params['limit']) ? $Params['limit'] : 1000;

        $idsFilter = NULL;
        $statusFilter = NULL;
        $tuluvFilter=NULL;
        $regionFilter = NULL;
        $driverFilter = NULL;
        $limitFilter = NULL;
        $exceptStatusFilter = NULL;
        $exceptStatFilter = NULL;
        $custFilter = NULL;

        $joinUsersTable = NULL;
        $roleFilter = NULL;
        $date_filter = NULL;
        $late = NULL;

        
        if (!empty($Params['ids'])) {
            $idsFilter = "AND orders.id in ({$Params['ids']})";
        }

        if (!empty($Params['status'])) {
            $statusFilter = "AND `status`= {$Params['tuluv']}";
        }

        if (!empty($Params['tuluv'])) {
            $tuluvFilter = "AND `status`= {$Params['tuluv']}";
        }

        if (!empty($Params['customer'])) {
            $custFilter = "AND organization= '{$Params['customer']}'";
        }

        if (!empty($Params['region'])) {
            $regionFilter = "AND region= '{$Params['region']}'";
        }

        if (!empty($Params['driverselected'])) {
            $driverFilter = "AND driverselected= '{$Params['driverselected']}'";
        }

        if ($Params['role']=='Customer') {
            $joinUsersTable = "LEFT JOIN users on users.name = orders.shop";
            $roleFilter = "AND users.id={$Params['user_id']} AND users.role='Customer'";
        }
        
        if (!empty($Params['start_date']) && empty($Params['end_date'])) {
            $date_filter = "AND (DATE(orders.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['start_date']}')";
        }

        if (!empty($Params['start_date']) && !empty($Params['end_date'])) {
            $date_filter = "AND (DATE(orders.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['end_date']}')";
        }
        // if ($Params['late']) {

        //     $late = "AND (DATE(deliveries.created_at) >= NOW() - INTERVAL 2 DAY )";

        // }
        $orderByFilter = "ORDER BY orders.id DESC ";

        if ($limit > 0) {
            $limitFilter = "LIMIT {$limit} OFFSET {$offset}";
        }

        if (!empty($Params['except_status'])) {
            $exceptStatusFilter = "AND status not in('{$Params['except_status']}')";
        }
        if (!empty($Params['except_stat'])) {
            $exceptStatFilter = "AND status not in('{$Params['except_stat']}')";
        }

        return DB::select(DB::raw("SELECT $deliveryTable.* 
                        FROM
                            $deliveryTable
                            {$joinUsersTable}
                        WHERE 1 = 1
                        {$idsFilter}
                        {$statusFilter}
                        {$tuluvFilter}
                        {$regionFilter}
                        {$custFilter}
                        {$driverFilter}
                        {$exceptStatusFilter}
                        {$exceptStatFilter}
                        {$roleFilter}
                        {$date_filter}
                        {$orderByFilter}
                        {$limitFilter}
                "));
    }

    /**
     * Query for `Delivery` data Count
     *
     * @return mixed
     */
    public static function GetExcelDataCount($Params=null)
    {
        $deliveryTable = (new Order())->getTable();
        $offset = isset($Params['offset']) ? $Params['offset'] : 0;
        $limit = isset($Params['limit']) ? $Params['limit'] : 1000;

        $idsFilter = NULL;
        $statusFilter = NULL;
        $tuluvFilter = NULL;
        $exceptStatFilter = NULL;
        $custFilter = NULL;
        $regionFilter = NULL;
        $driverFilter = NULL;
        $limitFilter = NULL;
        $exceptStatusFilter = NULL;
        $joinUsersTable = NULL;
        $roleFilter = NULL;
        $date_filter = NULL;
        $late = NULL;


        if (!empty($Params['ids'])) {
            $idsFilter = "AND orders.id in ({$Params['ids']})";
        }

        if (!empty($Params['status'])) {
            $statusFilter = "AND `status`= {$Params['status']}";
           }

           if (!empty($Params['tuluv'])) {
            $tuluvFilter = "AND `status`= {$Params['tuluv']}";
         }

         if (!empty($Params['customer'])) {
            $custFilter = "AND shop= '{$Params['customer']}'";
        }
   
           if (!empty($Params['region'])) {
               $regionFilter = "AND region= '{$Params['region']}'";
           }
   
           if (!empty($Params['driverselected'])) {
               $driverFilter = "AND driverselected= '{$Params['driverselected']}'";
           }

           if (!empty($Params['except_status'])) {
                $exceptStatusFilter = "AND status not in('{$Params['except_status']}')";
            }

            if (!empty($Params['except_stat'])) {
                $exceptStatFilter = "AND status not in('{$Params['except_stat']}')";
            }
            if ($Params['role']=='Customer') {
                $joinUsersTable = "LEFT JOIN users on users.name = orders.organization";
                $roleFilter = "AND users.id={$Params['user_id']} AND users.role='Customer'";
            }

            if (!empty($Params['start_date']) && empty($Params['end_date'])) {
                $date_filter = "AND (DATE(orders.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['start_date']}')";
            }
            if ($Params['late']!=4) {

              $late = "AND (DATE(orders.created_at) >= NOW() - INTERVAL 2 DAY )";
            }
            if (!empty($Params['start_date']) && !empty($Params['end_date'])) {
                $date_filter = "AND (DATE(orders.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['end_date']}')";
            }
            


        $resultQuery = DB::select(DB::raw("SELECT COUNT(*) AS total
                    FROM
                        $deliveryTable
                        {$joinUsersTable}
                    WHERE 1 = 1
                    {$idsFilter}
                    {$statusFilter}
                    {$tuluvFilter}
                    {$regionFilter}
                    {$custFilter}
                    {$driverFilter}
                    {$exceptStatusFilter}
                    {$exceptStatFilter}
                    {$roleFilter}
                    {$date_filter}
                    {$late}

                "));
        return $resultQuery[0]->total ?? 0;
    }
}