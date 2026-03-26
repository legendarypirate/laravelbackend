<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    protected $fillable = [
        'phone',
        'staff',
        'value',
    ];

    protected $table = 'logs';
}