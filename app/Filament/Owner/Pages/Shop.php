<?php

namespace App\Filament\Owner\Pages;

use App\Models\Cart;
use Filament\Pages\Page;
use App\Models\ShopStock;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Shop extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-shopping-bag';

    // Navigation group as Purchase
    protected static ?string $navigationGroup = 'Purchase';

    // Navigation sort 2
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.owner.pages.shop';

    public $shopstocks = [];
    public $selectedQuantities = [];
    public $paginationData = [];
    public $search = '';
    public $cartCount = 0;
    public $currentPage = 1;
    public $perpage = 12;

    public function mount()
    {
        $this->updateShopStocks();
        $this->updateCartCount();
    }

    public function updatedSearch()
    {
        $this->currentPage = 1; // Reset to first page when search is updated
        $this->updateShopStocks();
    }

    private function updateShopStocks()
    {
        $paginatedShopStocks = ShopStock::with('inventory', 'organization')
            ->when($this->search, function ($query) {
                $query->whereHas('inventory', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('organization', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->paginate($this->perpage, ['*'], 'page', $this->currentPage);

        $this->shopstocks = $paginatedShopStocks->items();
        $this->paginationData = [
            'current_page' => $paginatedShopStocks->currentPage(),
            'last_page' => $paginatedShopStocks->lastPage(),
            'total' => $paginatedShopStocks->total(),
            'per_page' => $paginatedShopStocks->perPage(),
        ];
    }

    public function goToPage($page)
    {
        $this->currentPage = $page;
        $this->updateShopStocks();
    }



    public function updateCartCount()
    {
        $this->cartCount = Cart::where('organization_id', auth()->user()->organization_id)->count();
    }

    public function goToCart()
    {
        return redirect()->route('filament.owner.pages.cart');
    }

    public function addToCart($shopStockId, $inventory)
    {
        $quantity = $this->selectedQuantities[$shopStockId] ?? 1;

        // Find the ShopStock instance
        $shopStock = ShopStock::find($shopStockId);

        // Check if the item already exists in the cart
        $existingCartItem = Cart::where('organization_id', auth()->user()->organization_id)
            ->where('supplier_id', $shopStock->organization_id)
            ->where('product_id', $inventory)
            ->where('shopstock_id', $shopStockId)
            ->first();

        // Calculate the remaining quantity available for the product
        $remainingQuantity = $shopStock->quantity - ($existingCartItem->quantity ?? 0);

        if ($quantity > $remainingQuantity) {
            // If user tries to add more than available quantity, limit the addition
            $quantity = $remainingQuantity;

            Notification::make()
                ->title("Only $remainingQuantity quantity has been added because the cart already contains the maximum quantity available.")
                ->warning()
                ->send();
        }

        if ($existingCartItem) {
            // If it exists, update the quantity
            $existingCartItem->quantity += $quantity;
            $existingCartItem->save();

            Notification::make()
                ->title('Item quantity updated in cart successfully')
                ->success()
                ->send();
        } else {
            // If it doesn't exist, create a new cart item
            Cart::create([
                'organization_id' => auth()->user()->organization_id,
                'supplier_id' => $shopStock->organization_id,
                'product_id' => $inventory,
                'shopstock_id' => $shopStock->id,
                'quantity' => $quantity,
            ]);

            Notification::make()
                ->title('Item added to cart successfully')
                ->success()
                ->send();
        }

        $this->updateShopStocks();
        $this->updateCartCount();
    }

    public function incrementQuantity($shopstockId)
    {
        if (isset($this->selectedQuantities[$shopstockId])) {
            if ($this->selectedQuantities[$shopstockId] < $this->getStockQuantity($shopstockId)) {
                $this->selectedQuantities[$shopstockId]++;
            }
        } else {
            $this->selectedQuantities[$shopstockId] = 2;
        }
    }

    public function decrementQuantity($shopstockId)
    {
        if (isset($this->selectedQuantities[$shopstockId])) {
            if ($this->selectedQuantities[$shopstockId] > 1) {
                $this->selectedQuantities[$shopstockId]--;
            }
        }
    }

    private function getStockQuantity($shopstockId)
    {
        $shopstock = ShopStock::find($shopstockId);
        return $shopstock->quantity;
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['owner', 'store_keeper']);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['owner', 'store_keeper']);
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['owner', 'store_keeper']);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['owner', 'store_keeper']);
    }
}
