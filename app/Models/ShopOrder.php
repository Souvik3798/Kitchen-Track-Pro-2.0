<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShopOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'supplier_id',
        'supplier_order_id',
        'status',
        'review',
    ];

    protected static function boot()
    {
        parent::boot();

        // Automatically generate the supplier_order_id with custom format
        static::creating(function ($order) {
            $organizationName = Str::upper(Str::limit(auth()->user()->organization->name, 3, ''));
            $dateTime = Carbon::now()->format('Ymd-His');
            $order->supplier_order_id = 'ODR-' . $organizationName . '-' . $dateTime . '-' . Str::random(3);
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the supplier who is fulfilling the order.
     */
    public function supplier()
    {
        return $this->belongsTo(Organization::class, 'supplier_id');
    }

    /**
     * Get the details (items) associated with this order.
     */
    public function details()
    {
        return $this->hasMany(ShopOrderDetails::class, 'order_id');
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class, 'order_id');
    }
}
