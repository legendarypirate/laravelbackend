<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;
class Merchant extends Model
{
            protected $table = 'merchant';
            protected $fillable = ['user_id','merchantName', 'merchantAddress', 'merchantPhone1', 'merchantPhone2', 'merchantWhat3Words'];
                        public function deliveries()
                        {
                        return $this->hasMany(Delivery::class);
                        }
}