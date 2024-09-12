<?php

namespace App\Filament\Owner\Pages;

use App\Models\Cart as ModelsCart;
use App\Models\OrderTracking;
use App\Models\ShopOrder;
use App\Models\ShopOrderDetails;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Cart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';
    //navigation group as Purchase
    protected static ?string $navigationGroup = 'Purchase';
    //navigation sort 2
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.owner.pages.cart';

    public $cartItems;


    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $orgId = $user->organization_id;
        $cartCount = ModelsCart::where('organization_id', $orgId)->count();

        return $cartCount > 0 ? (string) $cartCount : null;
    }

    public function mount()
    {
        $this->cartItems = ModelsCart::where('organization_id', auth()->user()->organization_id)->with('product')->get();
        // dd($this->cartItems);
    }

    public function placeOrder($cartItemId)
    {
        DB::transaction(function () use ($cartItemId) {
            $cartItem = ModelsCart::findOrFail($cartItemId);

            // Create ShopOrder for the single item
            $order = ShopOrder::create([
                'organization_id' => auth()->user()->organization_id,
                'supplier_id' => $cartItem->supplier_id,
                'status' => 'pending',
            ]);

            // dd($order);

            // Create ShopOrderDetail
            ShopOrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'shopstock_id' => $cartItem->shopstock_id,
                'quantity' => $cartItem->quantity,
            ]);

            OrderTracking::create([
                'order_id' => $order->id,
                'description' => 'Order Placed',
            ]);

            // Remove the item from the cart
            $cartItem->delete();

            // Notify the user
            Notification::make()
                ->title('Order for ' . ucwords($cartItem->product->name) . ' has been placed successfully')
                ->success()
                ->send();

            // Refresh the cart items
            $this->mount();
        });
    }

    public function placeAllOrders()
    {
        DB::transaction(function () {
            $groupedItems = $this->cartItems->groupBy('supplier_id');

            foreach ($groupedItems as $supplierId => $items) {
                // Create a ShopOrder for each supplier
                $order = ShopOrder::create([
                    'organization_id' => auth()->user()->organization_id,
                    'supplier_id' => $supplierId,
                    'status' => 'pending',
                ]);

                // Create ShopOrderDetail for each item in the cart
                foreach ($items as $cartItem) {
                    ShopOrderDetails::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'shopstock_id' => $cartItem->shopstock_id,
                        'quantity' => $cartItem->quantity,
                    ]);

                    OrderTracking::create([
                        'order_id' => $order->id,
                        'description' => 'Order created',
                    ]);

                    // Remove the item from the cart
                    $cartItem->delete();
                }
            }

            // Notify the user
            Notification::make()
                ->title('All orders have been placed successfully')
                ->success()
                ->send();

            // Refresh the cart items
            $this->mount();
        });
    }

    public function deleteCartItem($cartItemId)
    {
        // Find the cart item by its ID and delete it
        $cartItem = ModelsCart::findOrFail($cartItemId);
        $cartItem->delete();

        // Notify the user
        Notification::make()
            ->title('Item removed from cart')
            ->success()
            ->send();

        // Re-render the cart page
        $this->mount();
    }

    public function decreaseQuantity($cartItemId)
    {
        $cartItem = ModelsCart::find($cartItemId);
        if ($cartItem->quantity > 1) {
            $cartItem->quantity--;
            $cartItem->save();
        }
        $this->mount();
    }

    public function increaseQuantity($cartItemId)
    {
        $cartItem = ModelsCart::find($cartItemId);
        if ($cartItem->shopstock->quantity > $cartItem->quantity) {
            $cartItem->quantity++;
            $cartItem->save();
        }
        $this->mount();
    }

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->role === 'owner' || Auth::user()->role === 'store_keeper');
    }
}
