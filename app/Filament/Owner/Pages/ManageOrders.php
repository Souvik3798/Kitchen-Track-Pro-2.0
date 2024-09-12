<?php

namespace App\Filament\Owner\Pages;

use App\Models\OrderTracking;
use App\Models\ShopOrder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ManageOrders extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-bookmark-square';
    protected static ?string $navigationGroup = 'Purchase';
    protected static ?string $title = 'Manage Purchase Orders';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.owner.pages.manage-orders';

    public $orders = [];
    public $paginationData = [];
    public $currentPage = 1;
    public $perpage = 10;

    public function mount()
    {
        $this->updateOrders();
    }

    private function updateOrders()
    {
        $paginatedOrders = ShopOrder::with('details.product', 'details.shopstock')
            ->where('organization_id', auth()->user()->organization_id)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'accepted')
                    ->orWhere('status', 'shipped')
                    ->orWhere('status', 'completed')
                    ->orWhere(function ($query) {
                        $query->where('status', 'rejected')
                            ->where('updated_at', '>=', Carbon::now()->subDay());
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perpage, ['*'], 'page', $this->currentPage);

        $this->orders = $paginatedOrders->items();
        $this->paginationData = [
            'current_page' => $paginatedOrders->currentPage(),
            'last_page' => $paginatedOrders->lastPage(),
            'total' => $paginatedOrders->total(),
            'per_page' => $paginatedOrders->perPage(),
        ];
    }

    public function goToPage($page)
    {
        $this->currentPage = $page;
        $this->updateOrders();
    }

    public function cancelOrder($orderId)
    {
        $order = ShopOrder::findOrFail($orderId);
        $order->status = 'cancelled';
        $order->save();

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'description' => 'Order cancelled',
        ]);

        Notification::make()
            ->title('Order Cancelled')
            ->success()
            ->send();

        $this->updateOrders(); // Reload the orders after cancellation
    }

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->role === 'owner' || Auth::user()->role === 'store_keeper');
    }
}
