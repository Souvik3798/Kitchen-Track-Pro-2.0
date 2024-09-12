<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrderDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'shopstock_id',
        'quantity',
    ];

    public function order()
    {
        return $this->belongsTo(ShopOrder::class);
    }

    /**
     * Get the product associated with this detail.
     */
    public function product()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the shop stock associated with this detail.
     */
    public function shopstock()
    {
        return $this->belongsTo(ShopStock::class);
    }
}
