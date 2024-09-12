<?php

namespace App\Filament\Owner\Pages;

use App\Models\OrderTracking;
use App\Models\ShopOrder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CanceledOrders extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-archive-box-x-mark';
    protected static ?string $navigationGroup = 'Purchase';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.owner.pages.canceled-orders';

    public $orders;

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $orgId = $user->organization_id;
        $canceledOrderCount = ShopOrder::where('organization_id', $orgId)->where('status', 'cancelled')->count();

        return $canceledOrderCount > 0 ? (string) $canceledOrderCount : null;
    }

    public function mount()
    {
        $this->orders = ShopOrder::with('details.product', 'details.shopstock')
            ->where('organization_id', auth()->user()->organization_id)
            ->where('status', 'cancelled')
            ->get();
    }

    public function deleteOrder($orderId)
    {
        // Find the order by ID and delete it
        $order = ShopOrder::find($orderId);

        if ($order) {
            $order->delete();
            Notification::make()
                ->title('Order Deleted')
                ->success()
                ->body('The order has been deleted successfully.')
                ->send();
        } else {
            Notification::make()
                ->title('Order Not Found')
                ->danger()
                ->body('The order Has Nogt Been found')
                ->send();
        }

        // Refresh the list of orders after deletion
        $this->mount();
    }

    public function reverseOrder($orderId)
    {
        // Find the order by ID and change its status to 'pending'
        $order = ShopOrder::find($orderId);

        if ($order) {
            $order->status = 'pending';
            $order->save();


            OrderTracking::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'description' => 'Order Reordered',
            ]);


            Notification::make()
                ->title('Reordered')
                ->info()
                ->body('The order again added to cart.')
                ->send();
        } else {
            Notification::make()
                ->title('Not Found')
                ->info()
                ->body('The order not Found.')
                ->send();
        }

        // Refresh the list of orders after updating
        $this->mount();
    }

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->role === 'owner' || Auth::user()->role === 'store_keeper');
    }
}
