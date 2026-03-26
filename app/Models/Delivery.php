<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;
use App\Models\Merchant;
use App\Models\User;
use App\Traits\Loggable;

class Delivery extends Model
{
    use Loggable;

    /**
     * Query for `Delivery` data
     *
     * @return mixed
     */

    protected $table = 'deliveries';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public static function GetExcelData($Params = null)
    {
        $deliveryTable = (new Delivery())->getTable();
        $offset = isset($Params['offset']) ? $Params['offset'] : 0;
        $limit = isset($Params['limit']) ? $Params['limit'] : 3500;
        
        $idsFilter = NULL;
        $tuluvFilter = NULL;
        $regionFilter = NULL;
        $driverFilter = NULL;
        $limitFilter = NULL;
        $statusFilter = NULL;
        $notFilter = NULL;
        $verified = NULL;
        $district = NULL;
        $custFilter = NULL;
        $estimated = NULL;
        $joinUsersTable = NULL;
        $joinMerchantTable = NULL;
        $roleFilter = NULL;
        $date_filter = NULL;
        $late = NULL;
        $merchant = NULL;
        $type = NULL;

        // FIX: IDs filter - properly handle array
        if (!empty($Params['ids'])) {
            if (is_array($Params['ids'])) {
                // Convert array to comma-separated string
                $idsString = implode(',', $Params['ids']);
                $idsFilter = "AND deliveries.id in ({$idsString})";
                
                // When specific IDs are provided, IGNORE all other filters
                $statusFilter = NULL;
                $notFilter = NULL;
                $tuluvFilter = NULL;
                $regionFilter = NULL;
                $driverFilter = NULL;
                $verified = NULL;
                $district = NULL;
                $custFilter = NULL;
                $estimated = NULL;
                $date_filter = NULL;
                $merchant = NULL;
                $type = NULL;
            } else {
                // It's already a string
                $idsFilter = "AND deliveries.id in ({$Params['ids']})";
                
                // When specific IDs are provided, IGNORE all other filters
                $statusFilter = NULL;
                $notFilter = NULL;
                $tuluvFilter = NULL;
                $regionFilter = NULL;
                $driverFilter = NULL;
                $verified = NULL;
                $district = NULL;
                $custFilter = NULL;
                $estimated = NULL;
                $date_filter = NULL;
                $merchant = NULL;
                $type = NULL;
            }
        }

        // Only apply other filters if no specific IDs are provided
        if (empty($Params['ids'])) {
            // Build status filter
            $statusConditions = [];

            // Check if we have individual status parameters
            $statusParams = [
                'status_1', 'status_2', 'status_3', 'status_4',
                'status_5', 'status_6', 'status_10', 'status_100'
            ];

            foreach ($statusParams as $param) {
                if (!empty($Params[$param])) {
                    $statusConditions[] = $Params[$param];
                }
            }

            // Apply status filter
            if (!empty($Params['status'])) {
                // Use single status filter
                $statusFilter = "AND `status` = {$Params['status']}";
            } elseif (!empty($statusConditions)) {
                // Use multiple status filter
                $statusList = implode(',', array_unique($statusConditions));
                $statusFilter = "AND `status` IN ({$statusList})";
            }

            // Build NOT filter
            $notConditions = [];

            $notParams = [
                'not_1', 'not_2', 'not_3', 'not_4',
                'not_5', 'not_6', 'not_10', 'not_100'
            ];

            foreach ($notParams as $param) {
                if (!empty($Params[$param])) {
                    $notConditions[] = $Params[$param];
                }
            }

            if (!empty($notConditions)) {
                $notList = implode(',', array_unique($notConditions));
                $notFilter = "AND status NOT IN ({$notList})";
            }

            // Other filters (only apply when no specific IDs)
            if (!empty($Params['estimated'])) {
                $estimated = "AND deliveries.estimated = '{$Params['estimated']}'";
            }
            
            if (!empty($Params['customer'])) {
                $custFilter = "AND shop= '{$Params['customer']}'";
            }

            if (!empty($Params['verified'])) {
                $verified = "AND verified in('{$Params['verified']}')";
            }
            
            if (!empty($Params['district'])) {
                $district = "AND district in('{$Params['district']}')";
            }

            if (!empty($Params['region'])) {
                $regionFilter = "AND region= '{$Params['region']}'";
            }

            if (!empty($Params['driverselected'])) {
                $driverFilter = "AND driver= '{$Params['driverselected']}'";
            }

            // Date filters
            if (!empty($Params['start_date']) && empty($Params['end_date'])) {
                $date_filter = "AND DATE(deliveries.created_at) = '{$Params['start_date']}'";
            }

            if (!empty($Params['start_date']) && !empty($Params['end_date'])) {
                $date_filter = "AND (DATE(deliveries.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['end_date']}')";
            }

            if (!empty($Params['merchant_id'])) {
                $merchant = "AND deliveries.merchant_id = {$Params['merchant_id']}";
            }

            if (!empty($Params['type'])) {
                $type = "AND deliveries.type = {$Params['type']}";
            }
        }

        // Role-based joins and filters (always apply)
        $role = strtolower($Params['role'] ?? '');
        
        if ($role == 'customer') {
            $joinUsersTable = "INNER JOIN users on users.name = deliveries.shop";
            $joinMerchantTable = "INNER JOIN merchant ON merchant.id = deliveries.merchant_id";
            $roleFilter = "AND users.id={$Params['user_id']} AND users.role='customer'";
        } elseif ($role == 'admin' || $role == 'manager') {
            $joinMerchantTable = "LEFT JOIN merchant ON merchant.id = deliveries.merchant_id";
        } else {
            // Default: join merchant table for other roles to avoid SQL errors when selecting merchant columns
            $joinMerchantTable = "LEFT JOIN merchant ON merchant.id = deliveries.merchant_id";
        }

        $orderByFilter = "ORDER BY deliveries.id DESC ";

        if ($limit > 0) {
            $limitFilter = "LIMIT {$limit} OFFSET {$offset}";
        }

        return DB::select(DB::raw("SELECT $deliveryTable.* ,merchant.merchantName AS merchantName,merchant.merchantPhone1 AS merchantPhone1,merchant.merchantPhone2 AS merchantPhone2,merchant.merchantAddress AS merchantAddress
                        FROM
                            $deliveryTable
                            {$joinUsersTable}
                            {$joinMerchantTable}
                        WHERE 1 = 1
                        {$idsFilter}
                        {$tuluvFilter}
                        {$estimated}
                        {$district}
                        {$statusFilter}
                        {$notFilter}
                        {$verified}
                        {$regionFilter}
                        {$custFilter}
                        {$driverFilter}
                        {$roleFilter}
                        {$date_filter}
                        {$merchant}
                        {$type}
                        {$orderByFilter}
                        {$limitFilter}
                "));
    }
    /**
     * Query for `Delivery` data Count
     *
     * @return mixed
     */
    public static function GetExcelDataCount($Params = null)
    {
        $deliveryTable = (new Delivery())->getTable();
        $offset = isset($Params['offset']) ? $Params['offset'] : 0;
        $limit = isset($Params['limit']) ? $Params['limit'] : 1000;

        $idsFilter = NULL;
        $statusFilter = NULL;
        $notFilter = NULL;
        $tuluvFilter = NULL;
        $exceptStatFilter = NULL;
        $custFilter = NULL;
        $regionFilter = NULL;
        $driverFilter = NULL;
        $limitFilter = NULL;
        $exceptStatusFilter = NULL;
        $joinUsersTable = NULL;
        $joinMerchantTable = NULL;
        $roleFilter = NULL;
        $date_filter = NULL;
        $late = NULL;
        $verified = NULL;
        $estimated = NULL;
        $merchant = NULL;
        $type = NULL;

        // IDs filter
        if (!empty($Params['ids'])) {
            $idsFilter = "AND deliveries.id in ({$Params['ids']})";
        }

        // Build status filter (same logic as GetExcelData)
        $statusConditions = [];

        $statusParams = [
            'status_1', 'status_2', 'status_3', 'status_4', 
            'status_5', 'status_6', 'status_10', 'status_100'
        ];

        foreach ($statusParams as $param) {
            if (!empty($Params[$param])) {
                $statusConditions[] = $Params[$param];
            }
        }

        // Apply status filter
        if (!empty($Params['status'])) {
            $statusFilter = "AND `status` = {$Params['status']}";
        } elseif (!empty($statusConditions)) {
            $statusList = implode(',', array_unique($statusConditions));
            $statusFilter = "AND `status` IN ({$statusList})";
        }

        // Build NOT filter
        $notConditions = [];

        $notParams = [
            'not_1', 'not_2', 'not_3', 'not_4', 
            'not_5', 'not_6', 'not_10', 'not_100'
        ];

        foreach ($notParams as $param) {
            if (!empty($Params[$param])) {
                $notConditions[] = $Params[$param];
            }
        }

        if (!empty($notConditions)) {
            $notList = implode(',', array_unique($notConditions));
            $notFilter = "AND status NOT IN ({$notList})";
        }

        // Other filters
        if (!empty($Params['estimated'])) {
            $estimated = "AND deliveries.estimated = '{$Params['estimated']}'";
        }

        if (!empty($Params['tuluv'])) {
            $tuluvFilter = "AND `status`= {$Params['tuluv']}";
        }

        if (!empty($Params['customer'])) {
            $custFilter = "AND shop= '{$Params['customer']}'";
        }

        if (!empty($Params['verified'])) {
            $verified = "AND verified in('{$Params['verified']}')";
        }

        if (!empty($Params['region'])) {
            $regionFilter = "AND region= '{$Params['region']}'";
        }

        if (!empty($Params['driverselected'])) {
            $driverFilter = "AND driver= '{$Params['driverselected']}'";
        }

        if (!empty($Params['merchant_id'])) {
            $merchant = "AND deliveries.merchant_id = {$Params['merchant_id']}";
        }

        if (!empty($Params['type'])) {
            $type = "AND deliveries.type = {$Params['type']}";
        }

        if (!empty($Params['except_status'])) {
            $exceptStatusFilter = "AND status not in('{$Params['except_status']}')";
        }

        if (!empty($Params['except_stat'])) {
            $exceptStatFilter = "AND status not in('{$Params['except_stat']}')";
        }

        // Role-based joins and filters
        $role = strtolower($Params['role'] ?? '');
        
        if ($role == 'customer') {
            $joinUsersTable = "INNER JOIN users on users.name = deliveries.shop";
            $joinMerchantTable = "INNER JOIN merchant ON merchant.id = deliveries.merchant_id";
            $roleFilter = "AND users.id={$Params['user_id']} AND users.role='customer'";
        } elseif ($role == 'admin' || $role == 'manager') {
            $joinMerchantTable = "LEFT JOIN merchant ON merchant.id = deliveries.merchant_id";
        } else {
            // Default: join merchant table for other roles to avoid SQL errors when selecting merchant columns
            $joinMerchantTable = "LEFT JOIN merchant ON merchant.id = deliveries.merchant_id";
        } 

        // Date filters
        if (!empty($Params['start_date']) && empty($Params['end_date'])) {
            $date_filter = "AND DATE(deliveries.created_at) = '{$Params['start_date']}'";
        }

        if (!empty($Params['start_date']) && !empty($Params['end_date'])) {
            $date_filter = "AND (DATE(deliveries.created_at) BETWEEN '{$Params['start_date']}' AND '{$Params['end_date']}')";
        }

        $resultQuery = DB::select(DB::raw("SELECT COUNT(*) AS total
                    FROM
                         $deliveryTable
                        {$joinUsersTable}
                        {$joinMerchantTable}
                    WHERE 1 = 1
                    {$idsFilter}
                    {$statusFilter}
                    {$notFilter}
                    {$estimated}
                    {$tuluvFilter}
                    {$regionFilter}
                    {$custFilter}
                    {$driverFilter}
                    {$verified}
                    {$exceptStatusFilter}
                    {$exceptStatFilter}
                    {$roleFilter}
                    {$date_filter}
                    {$late}
                    {$merchant}
                    {$type}
                "));

        return $resultQuery[0]->total ?? 0;
    }

    public static function GetQRData($Params = null)
    {
        $reqTable = (new Delivery())->getTable();

        $idsFilter = NULL;
        $joinUsersTable = NULL;
        $joinMerchantTable = NULL;
        $roleFilter = NULL;

        if (!empty($Params['ids'])) {
            $idsFilter = "AND deliveries.id in ({$Params['ids']})";
        }

        $orderByFilter = "ORDER BY deliveries.id DESC ";
        $joinMerchantTable = "INNER JOIN merchant ON merchant.id = deliveries.merchant_id";
        
        return DB::select(DB::raw("SELECT $reqTable.*,merchant.merchantName AS merchantName
                    FROM
                        $reqTable
                        {$joinUsersTable}
                        {$joinMerchantTable}
                    WHERE 1 = 1
                    {$idsFilter}
                    {$orderByFilter}
                "));
    }
}
