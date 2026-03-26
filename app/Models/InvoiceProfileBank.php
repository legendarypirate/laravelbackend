<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceProfileBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_profile_id',
        'bank_name',
        'account_name',
        'account_number',
        'iban',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(InvoiceProfile::class, 'invoice_profile_id');
    }
}

