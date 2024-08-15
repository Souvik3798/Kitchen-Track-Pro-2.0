<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'organization_id', 'price'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(DishIngredient::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function inventoryItems(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'dish_ingredients')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
