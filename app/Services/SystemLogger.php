<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SystemLogger
{
    /**
     * Log a create operation
     *
     * @param Model $model
     * @param string|null $phone
     * @param string|null $staff
     * @param string|null $customMessage
     * @return void
     */
    public static function logCreate(Model $model, $phone = null, $staff = null, $customMessage = null)
    {
        $staff = $staff ?? self::getStaffName();
        $phone = $phone ?? self::getPhoneFromRequest($model);
        
        $modelName = self::getModelDisplayName($model);
        $identifier = self::getModelIdentifier($model);
        
        $message = $customMessage ?? $staff . ' ' . $identifier . ' дугаартай ' . $modelName . ' үүсгэлээ';
        
        self::createLog($phone, $staff, $message);
    }

    /**
     * Log an update operation
     *
     * @param Model $model
     * @param string|null $phone
     * @param string|null $staff
     * @param string|null $customMessage
     * @return void
     */
    public static function logUpdate(Model $model, $phone = null, $staff = null, $customMessage = null)
    {
        $staff = $staff ?? self::getStaffName();
        $phone = $phone ?? self::getPhoneFromRequest($model);
        
        $modelName = self::getModelDisplayName($model);
        $identifier = self::getModelIdentifier($model);
        
        $message = $customMessage ?? $staff . ', нь ' . $identifier . ' дугаартай ' . $modelName . ' шинэчлэлээ';
        
        self::createLog($phone, $staff, $message);
    }

    /**
     * Log a delete operation
     *
     * @param Model $model
     * @param string|null $phone
     * @param string|null $staff
     * @param string|null $customMessage
     * @return void
     */
    public static function logDelete(Model $model, $phone = null, $staff = null, $customMessage = null)
    {
        $staff = $staff ?? self::getStaffName();
        $phone = $phone ?? self::getPhoneFromRequest($model);
        
        $modelName = self::getModelDisplayName($model);
        $identifier = self::getModelIdentifier($model);
        
        // Try to get model name attribute if available
        $nameAttribute = self::getModelNameAttribute($model);
        $nameText = $nameAttribute ? $nameAttribute . ' ' : '';
        
        $message = $customMessage ?? $staff . ', нь ' . $nameText . $identifier . ' дугаартай ' . $modelName . ' устгалаа';
        
        self::createLog($phone, $staff, $message);
    }

    /**
     * Create a log entry
     *
     * @param string|null $phone
     * @param string|null $staff
     * @param string $value
     * @return void
     */
    protected static function createLog($phone, $staff, $value)
    {
        try {
            $log = new Log();
            $log->phone = $phone ?? '';
            $log->staff = $staff ?? 'SYSTEM';
            $log->value = $value;
            $log->save();
        } catch (\Exception $e) {
            // Silently fail to prevent breaking the main operation
            \Log::error('SystemLogger failed: ' . $e->getMessage());
        }
    }

    /**
     * Get staff name from authenticated user or default
     *
     * @return string
     */
    protected static function getStaffName()
    {
        if (Auth::check()) {
            return Auth::user()->name ?? 'SYSTEM';
        }
        return 'EZPAY';
    }

    /**
     * Get phone from current request or model
     *
     * @param Model|null $model
     * @return string|null
     */
    protected static function getPhoneFromRequest($model = null)
    {
        try {
            // First, check if phone is in the model being logged
            if ($model && isset($model->phone) && !empty($model->phone)) {
                return $model->phone;
            }
            
            // Check if we're in a web/API request context
            if (app()->runningInConsole()) {
                return null;
            }
            
            $request = request();
            if ($request && $request->has('phone')) {
                return $request->phone;
            }
        } catch (\Exception $e) {
            // Ignore - request might not be available in all contexts
        }
        return null;
    }

    /**
     * Get model display name in Mongolian
     *
     * @param Model $model
     * @return string
     */
    protected static function getModelDisplayName(Model $model)
    {
        $modelClass = class_basename($model);
        
        $names = [
            'Delivery' => 'хүргэлт',
            'Order' => 'захиалаг',
            'User' => 'хэрэглэгч',
            'Item' => 'бараа',
            'Good' => 'бараа',
            'Region' => 'бүс',
            'Phone' => 'утас',
            'Address' => 'хаяг',
            'Invoice' => 'нэхэмжлэх',
            'InvoiceProfile' => 'профайл',
            'Banner' => 'баннер',
            'Merchant' => 'худалдаачин',
            'Driver' => 'жолооч',
        ];
        
        return $names[$modelClass] ?? strtolower($modelClass);
    }

    /**
     * Get model identifier (track, id, or other unique field)
     *
     * @param Model $model
     * @return string
     */
    protected static function getModelIdentifier(Model $model)
    {
        // Check for common identifier fields
        if (isset($model->track)) {
            return $model->track;
        }
        
        if (isset($model->id)) {
            return $model->id;
        }
        
        return 'unknown';
    }

    /**
     * Get model name attribute if available
     *
     * @param Model $model
     * @return string|null
     */
    protected static function getModelNameAttribute(Model $model)
    {
        if (isset($model->name)) {
            return $model->name;
        }
        
        return null;
    }
}

