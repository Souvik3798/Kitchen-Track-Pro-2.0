<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'description'
    ];

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }
}
