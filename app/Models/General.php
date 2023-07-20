<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpMyAdmin\SqlParser\Utils\Table;
use Illuminate\Database\Eloquent\SoftDeletes;
class General extends Model
{
    /**
     * Query for `Delivery` data
     *
     * @return mixed
     */
    public static function GetExcelData($Params=null)
    {
        $deliveryTable = (new General())->getTable();
        $offset = isset($Params['offset']) ? $Params['offset'] : 0;
        $limit = isset($Params['limit']) ? $Params['limit'] : 500;

        $idsFilter = NULL;
        $statusFilter = NULL;
        $regionFilter = NULL;
        $driverFilter = NULL;
        $limitFilter = NULL;
        $dr = NULL;
        $customer = NULL;

        $exceptStatusFilter = NULL;
        $joinUsersTable = NULL;
        $roleFilter = NULL;
        $date_filter = NULL;

        
        if (!empty($Params['ids'])) {
            $idsFilter = "AND generals.id in ({$Params['ids']})";
        }

        if (!empty($Params['status'])) {
            $statusFilter = "AND `status`= {$Params['status']}";
        }
        if (!empty($Params['dr'])) {
            $dr = "AND `users`= '{$Params['dr']}'";
        }
        if (!empty($Params['customer'])) {
            $customer = "AND `users`= '{$Params['customer']}'";
        }

        if (!empty($Params['region'])) {
            $regionFilter = "AND region= '{$Params['region']}'";
        }

        if (!empty($Params['driverselected'])) {
            $driverFilter = "AND `type`= '{$Params['driverselected']}'";
        }

        if ($Params['role']=='Customer') {
            $joinUsersTable = "LEFT JOIN users on users.id = generals.sid";
            $roleFilter = "AND users.id={$Params['user_id']} AND users.role='Customer' AND generals.type='2'";
        }
        
        if (!empty($Params['start_date']) && empty($Params['end_date'])) {
            $date_filter = "AND (DATE(created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['start_date']}')";
        }

        if (!empty($Params['start_date']) && !empty($Params['end_date'])) {
            $date_filter = "AND (DATE(created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['end_date']}')";
        }

        $orderByFilter = "ORDER BY generals.id DESC ";

        if ($limit > 0) {
            $limitFilter = "LIMIT {$limit} OFFSET {$offset}";
        }

        if (!empty($Params['except_status'])) {
            $exceptStatusFilter = "AND status not in('{$Params['except_status']}')";
        }
        

        return DB::select(DB::raw("SELECT $deliveryTable.* 
                        FROM
                            $deliveryTable
                            {$joinUsersTable}
                        WHERE 1 = 1
                        {$idsFilter}
                        {$statusFilter}
                        {$regionFilter}
                        {$driverFilter}
                        {$dr}
                        {$customer}
                        {$exceptStatusFilter}
                        {$date_filter}
                        {$roleFilter}
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
        $deliveryTable = (new General())->getTable();
        $offset = isset($Params['offset']) ? $Params['offset'] : 0;
        $limit = isset($Params['limit']) ? $Params['limit'] : 500;

        $idsFilter = NULL;
        $statusFilter = NULL;
        $regionFilter = NULL;
        $driverFilter = NULL;
        $limitFilter = NULL;
        $dr = NULL;
        $customer = NULL;

        $exceptStatusFilter = NULL;
        $joinUsersTable = NULL;
        $roleFilter = NULL;
        $date_filter = NULL;

        
        if (!empty($Params['ids'])) {
            $idsFilter = "AND generals.id in ({$Params['ids']})";
        }

        if (!empty($Params['status'])) {
            $statusFilter = "AND `status`= {$Params['status']}";
        }
        if (!empty($Params['dr'])) {
            $dr = "AND `users`= '{$Params['dr']}'";
        }
        if (!empty($Params['customer'])) {
            $customer = "AND `users`= '{$Params['customer']}'";
        }

        if (!empty($Params['region'])) {
            $regionFilter = "AND region= '{$Params['region']}'";
        }

        if (!empty($Params['driverselected'])) {
            $driverFilter = "AND `type`= '{$Params['driverselected']}'";
        }

        if ($Params['role']=='Customer') {
            $joinUsersTable = "LEFT JOIN users on users.id = generals.sid";
            $roleFilter = "AND users.id={$Params['user_id']} AND users.role='Customer' AND generals.type='2'";
        }
        
        if (!empty($Params['start_date']) && empty($Params['end_date'])) {
            $date_filter = "AND (DATE(created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['start_date']}')";
        }

        if (!empty($Params['start_date']) && !empty($Params['end_date'])) {
            $date_filter = "AND (DATE(created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['end_date']}')";
        }

        $orderByFilter = "GROUP BY generals.id ORDER BY generals.id DESC";
           if ($limit > 0) {
               $limitFilter = "LIMIT {$limit} OFFSET {$offset}";
           }

           $resultQuery = DB::select(DB::raw("SELECT COUNT(*) AS total
                    FROM
                        $deliveryTable
                        {$joinUsersTable}
                    WHERE 1 = 1
                    {$idsFilter}
                        {$statusFilter}
                        {$regionFilter}
                        {$driverFilter}
                        {$dr}
                        {$customer}
                        {$exceptStatusFilter}
                        {$date_filter}
                        {$roleFilter}
                        {$orderByFilter}
                        {$limitFilter}

                "));
        return $resultQuery[0]->total ?? 0;
    }
}
