<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer', 'contact', 'user_id', 'organization_id', 'status', 'phone'];

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }

    public function orderdish(): HasMany
    {
        return $this->hasMany(OrderDishPivot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Generate a unique invoice ID
            $order->invoice_id = 'INV-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        });

        static::updating(function ($order) {
            if ($order->status === 'delivered' && $order->getOriginal('status') !== 'delivered') {
                // If order is being delivered, deduct ingredients from kitchen
                $order->deductIngredientsFromKitchen();
            } elseif ($order->getOriginal('status') === 'delivered' && $order->status !== 'delivered') {
                // If order is being changed from delivered to another status, reverse the deduction
                $order->reverseIngredientsFromKitchen();
            }
        });
    }

    public function deductIngredientsFromKitchen()
    {
        foreach ($this->orderdish as $orderDishPivot) {
            $dish = $orderDishPivot->dish;
            $quantityOrdered = $orderDishPivot->quantity;

            foreach ($dish->ingredients as $ingredient) {
                $inventoryStocks = $ingredient->inventory->stocks;

                // Calculate the amount to deduct from kitchen stock
                $amountToDeduct = $ingredient->quantity * $quantityOrdered / 1000;

                // Update kitchen stock for each stock record
                foreach ($inventoryStocks as $stock) {
                    $stock->decrement('kitchen_quantity', $amountToDeduct);

                    Transaction::create([
                        'inventory_id' => $ingredient->inventory->id,
                        'organization_id' => auth()->user()->organization_id,
                        'user_id' => auth()->id(),
                        'source' => 'kitchen',
                        'destination' => 'sold',
                        'quantity' => $amountToDeduct,
                        'price' => $orderDishPivot->price
                    ]);
                }
            }
        }
    }

    public function reverseIngredientsFromKitchen()
    {
        foreach ($this->orderdish as $orderDishPivot) {
            $dish = $orderDishPivot->dish;
            $quantityOrdered = $orderDishPivot->quantity;

            foreach ($dish->ingredients as $ingredient) {
                $inventoryItem = $ingredient->inventory->stocks;

                // Calculate the amount to add back to kitchen stock
                $amountToAdd = $ingredient->quantity * $quantityOrdered / 1000;

                // Update kitchen stock
                foreach ($inventoryItem as $stock) {
                    $stock->increment('kitchen_quantity', $amountToAdd);

                    Transaction::create([
                        'inventory_id' => $ingredient->inventory->id,
                        'organization_id' => auth()->user()->organization_id,
                        'user_id' => auth()->id(),
                        'source' => 'sold',
                        'destination' => 'kitchen',
                        'quantity' => $amountToAdd,
                        'price' => $dish->price
                    ]);
                }
            }
        }
    }
}
