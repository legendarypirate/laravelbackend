<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Loggable;

class InvoiceProfile extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'name',
        'register_number',
        'email',
        'phone',
        'address',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(InvoiceProfileBank::class);
    }
}

