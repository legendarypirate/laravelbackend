<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delivery;

class DeliveryDownload extends Model
{
    use HasFactory;
    protected $table = 'deliveries_download';
    protected $fillable = ['driver_id', 'deliveries_id', 'download_price', 'created_at', 'updated_at'];
    
    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'deliveries_id', 'id');
    }
}
