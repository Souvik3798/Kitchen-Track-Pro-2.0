<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'organization_id'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function dishIngredients()
    {
        return $this->hasMany(DishIngredient::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
