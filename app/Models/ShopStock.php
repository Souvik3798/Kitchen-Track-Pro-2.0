<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopStock extends Model
{
    use HasFactory;
    protected $fillable = [
        'inventory_id',
        'organization_id',
        'quantity',
        'price',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
