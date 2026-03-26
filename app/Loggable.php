<?php

namespace App\Traits;

use App\Services\SystemLogger;

trait Loggable
{
    /**
     * Boot the trait and register model events
     *
     * @return void
     */
    public static function bootLoggable()
    {
        // Log when a model is created
        static::created(function ($model) {
            SystemLogger::logCreate($model);
        });

        // Log when a model is updated
        static::updated(function ($model) {
            SystemLogger::logUpdate($model);
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            SystemLogger::logDelete($model);
        });
    }

    /**
     * Manually log a create operation with custom message
     *
     * @param string|null $phone
     * @param string|null $staff
     * @param string|null $customMessage
     * @return void
     */
    public function logCreate($phone = null, $staff = null, $customMessage = null)
    {
        SystemLogger::logCreate($this, $phone, $staff, $customMessage);
    }

    /**
     * Manually log an update operation with custom message
     *
     * @param string|null $phone
     * @param string|null $staff
     * @param string|null $customMessage
     * @return void
     */
    public function logUpdate($phone = null, $staff = null, $customMessage = null)
    {
        SystemLogger::logUpdate($this, $phone, $staff, $customMessage);
    }

    /**
     * Manually log a delete operation with custom message
     *
     * @param string|null $phone
     * @param string|null $staff
     * @param string|null $customMessage
     * @return void
     */
    public function logDelete($phone = null, $staff = null, $customMessage = null)
    {
        SystemLogger::logDelete($this, $phone, $staff, $customMessage);
    }
}

