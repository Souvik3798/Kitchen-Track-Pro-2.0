<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['organization_id', 'supplier_id', 'product_id', 'shopstock_id', 'quantity'];

    //relation with organization
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    //relation with supplier
    public function supplier()
    {
        return $this->belongsTo(Organization::class, 'supplier_id');
    }

    //relation with product
    public function product()
    {
        return $this->belongsTo(Inventory::class, 'product_id');
    }

    //relation with shopstock
    public function shopstock()
    {
        return $this->belongsTo(ShopStock::class, 'shopstock_id');
    }
}
