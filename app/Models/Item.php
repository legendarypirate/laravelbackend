<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class Item extends Model
{
    use HasFactory, Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image', 
        'quantity',
        'in_delivery',
        'delivered',
        // Add any other fields you want to be mass assignable
    ];

    /**
     * Alternatively, you can use guarded to specify which fields are NOT mass assignable
     * But using fillable is more secure
     */
    // protected $guarded = [];
}