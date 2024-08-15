<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DishIngredient extends Model
{
    use HasFactory;

    protected $fillable = ['dish_id', 'inventory_id', 'quantity'];

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
