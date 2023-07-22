<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;

class Delivery extends Model
{
    /**
     * Query for `Delivery` data
     *
     * @return mixed
     */
    public static function GetExcelData($Params=null)
    {
        $deliveryTable = (new Delivery())->getTable();
        $offset = isset($Params['offset']) ? $Params['offset'] : 0;
        $limit = isset($Params['limit']) ? $Params['limit'] : 1000;
        $status1=NULL;
        $status10=NULL;
        $status100=NULL;
        $idsFilter = NULL;
        $tuluvFilter=NULL;
        $regionFilter = NULL;
        $driverFilter = NULL;
        $limitFilter = NULL;
        $status2 = NULL;
        $status6 = NULL;
        $status3 = NULL;
        $status4 = NULL;
        $statusFilter=NULL;
        $status5 = NULL;
        $not1 = NULL;
        $not100 = NULL;
        $not3 = NULL;
        $not4 = NULL;
        $not5 = NULL;
        $verified = NULL;
        $custFilter = NULL;
        $joinUsersTable = NULL;
        $roleFilter = NULL;
        $date_filter = NULL;
        $late = NULL;

        if (!empty($Params['ids'])) {
            $idsFilter = "AND deliveries.id in ({$Params['ids']})";
        }

        if (!empty($Params['status'])) {
            $statusFilter = "AND `status`= {$Params['status']}";
        }

        if (!empty($Params['customer'])) {
            $custFilter = "AND shop= '{$Params['customer']}'";
        }
        if (!empty($Params['status_10'])) {
            $status10 = "AND status in('{$Params['status_10']}')";
        }

        if (!empty($Params['not_1'])) {
            $not1 = "AND status not in('{$Params['not_1']}')";
        }

        if (!empty($Params['not_3'])) {
            $not3 = "AND status not in('{$Params['not_3']}')";
        }

        if (!empty($Params['not_5'])) {
            $not5 = "AND status not in('{$Params['not_5']}')";
        }

        if (!empty($Params['not_4'])) {
            $not4 = "AND status not in('{$Params['not_4']}')";
        }

        if (!empty($Params['not_100'])) {
            $not_100 = "AND status not in('{$Params['not_100']}')";
        }

        if (!empty($Params['verified'])) {
            $verified = "AND verified in('{$Params['verified']}')";
        }

        if (!empty($Params['status_3'])) {
            $status3 = "AND status in('{$Params['status_3']}')";
        }
        if (!empty($Params['status_4'])) {
            $status4 = "OR status in('{$Params['status_4']}')";
        }
        if (!empty($Params['status_5'])) {
            $status5 = "OR status in('{$Params['status_5']}')";
        }

        if (!empty($Params['status_100'])) {
            $status100 = "AND status in('{$Params['status_100']}')";
        }

        if (!empty($Params['status_2'])) {
            $status2 = "AND status in ('{$Params['status_2']}')";
        }
        if (!empty($Params['status_6'])) {
            $status6 = "OR status in ('{$Params['status_6']}')";
        }
        
        if (!empty($Params['status_1'])) {
            $status1 = "AND status in('{$Params['status_1']}')";
        }
        if (!empty($Params['region'])) {
            $regionFilter = "AND region= '{$Params['region']}'";
        }
        

        if (!empty($Params['driverselected'])) {
            $driverFilter = "AND driver= '{$Params['driverselected']}'";
        }

        if ($Params['role']=='Customer') {
            $joinUsersTable = "LEFT JOIN users on users.name = deliveries.shop";
            $roleFilter = "AND users.id={$Params['user_id']} AND users.role='Customer'";
        }
        
        if (!empty($Params['start_date']) && empty($Params['end_date'])) {
            $date_filter = "AND (DATE(deliveries.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['start_date']}')";
        }

        if (!empty($Params['start_date']) && !empty($Params['end_date'])) {
            $date_filter = "AND (DATE(deliveries.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['end_date']}')";
        }
        // if ($Params['late']) {

        //     $late = "AND (DATE(deliveries.created_at) >= NOW() - INTERVAL 2 DAY )";

        // }
        $orderByFilter = "ORDER BY deliveries.id DESC ";

        if ($limit > 0) {
            $limitFilter = "LIMIT {$limit} OFFSET {$offset}";
        }

      
        return DB::select(DB::raw("SELECT $deliveryTable.* 
                        FROM
                            $deliveryTable
                            {$joinUsersTable}
                        WHERE 1 = 1
                        {$idsFilter}
                        {$tuluvFilter}
                        {$status1}
                        {$status10}
                        {$status100}
                        {$status2}
                        {$status6}
                        {$status3}
                        {$status4}
                        {$status5}
                        {$not1}
                        {$not3}
                        {$not4}
                        {$not5}
                        {$not100}
                        {$verified}
                        {$regionFilter}
                        {$custFilter}
                        {$statusFilter}
                        {$driverFilter}
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
        $deliveryTable = (new Delivery())->getTable();
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
        $not1 = NULL;
        $not3 = NULL;
        $not4 = NULL;
        $not5 = NULL;
        $not100 = NULL;
        $verified = NULL;
        $status10=NULL;
        $status1=NULL;
        $status100=NULL;
        $status2 = NULL;
        $status6 = NULL;
        $status3= NULL;
        $status4 = NULL;
        $status5 = NULL;

        if (!empty($Params['ids'])) {
            $idsFilter = "AND deliveries.id in ({$Params['ids']})";
        }

        if (!empty($Params['status'])) {
            $statusFilter = "AND `status`= {$Params['status']}";
        }

        if (!empty($Params['tuluv'])) {
            $tuluvFilter = "AND `status`= {$Params['tuluv']}";
        }

        if (!empty($Params['status_10'])) {
            $status10 = "AND status in('{$Params['status_10']}')";
        }

        if (!empty($Params['status_3'])) {
            $status3 = "AND status in ('{$Params['status_3']}')";
        }

        if (!empty($Params['not_3'])) {
            $not3 = "AND status not in('{$Params['not_3']}')";
        }

        if (!empty($Params['not_5'])) {
            $not5 = "AND status not in('{$Params['not_5']}')";
        }

        if (!empty($Params['not_4'])) {
            $not4 = "AND status not in('{$Params['not_4']}')";
        }

        if (!empty($Params['status_4'])) {
            $status4 = "OR status in ('{$Params['status_4']}')";
        }
        if (!empty($Params['status_5'])) {
            $status5 = "OR status in ('{$Params['status_5']}')";
        }

        if (!empty($Params['status_100'])) {
            $status100 = "AND status in('{$Params['status_100']}')";
        }

        if (!empty($Params['customer'])) {
            $custFilter = "AND shop= '{$Params['customer']}'";
        }

        if (!empty($Params['status_2'])) {
            $status2 = "AND status in ('{$Params['status_2']}')";
        }
        if (!empty($Params['status_6'])) {
            $status6 = "AND status in ('{$Params['status_6']}')";
        }   

        if (!empty($Params['not_1'])) {
            $not1 = "AND status not in('{$Params['not_1']}')";
        }

        if (!empty($Params['not_100'])) {
            $not_100 = "AND status not in('{$Params['not_100']}')";
        }

        if (!empty($Params['verified'])) {
            $verified = "AND verified in('{$Params['verified']}')";
        }

         if (!empty($Params['customer'])) {
            $custFilter = "AND shop= '{$Params['customer']}'";
        }
   
        if (!empty($Params['region'])) {
            $regionFilter = "AND region= '{$Params['region']}'";
        }
   
        if (!empty($Params['driverselected'])) {
            $driverFilter = "AND driver= '{$Params['driverselected']}'";
        }

        if (!empty($Params['except_status'])) {
            $exceptStatusFilter = "AND status not in('{$Params['except_status']}')";
        }

        if (!empty($Params['except_stat'])) {
            $exceptStatFilter = "AND status not in('{$Params['except_stat']}')";
        }
        if ($Params['role']=='Customer') {
            $joinUsersTable = "LEFT JOIN users on users.name = deliveries.shop";
            $roleFilter = "AND users.id={$Params['user_id']} AND users.role='Customer'";
        }

        if (!empty($Params['start_date']) && empty($Params['end_date'])) {
            $date_filter = "AND (DATE(deliveries.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['start_date']}')";
        }
        if ($Params['late']!=4) {

            $late = "AND (DATE(deliveries.created_at) >= NOW() - INTERVAL 2 DAY )";
        }
        if (!empty($Params['start_date']) && !empty($Params['end_date'])) {
            $date_filter = "AND (DATE(deliveries.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['end_date']}')";
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
                    {$status1}
                    {$status10}
                    {$status100}
                    {$status2}
                    {$status6}
                    {$status3}
                    {$status4}
                    {$status5}
                    {$not3}
                    {$not4}
                    {$not5}
                    {$not1}
                    {$not100}
                    {$verified}
                    {$exceptStatusFilter}
                    {$exceptStatFilter}
                    {$roleFilter}
                    {$date_filter}
                    {$late}

                "));
        return $resultQuery[0]->total ?? 0;
    }
}