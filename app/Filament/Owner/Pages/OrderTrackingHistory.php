<?php

namespace App\Filament\Owner\Pages;

use App\Models\ShopOrder;
use Filament\Pages\Page;

class OrderTrackingHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-map-pin';
    protected static ?string $navigationGroup = 'Purchase';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.owner.pages.order-tracking-history';

    public $order, $trackings;
    public $search = '';

    public function updatedSearch()
    {
        if ($this->search) {
            $this->order = ShopOrder::where('supplier_order_id', $this->search)->first();

            if ($this->order) {
                $this->trackings = $this->order->trackings()->orderBy('created_at', 'asc')->get();
            } else {
                $this->trackings = collect();
            }
        } else {
            $this->order = null;
            $this->trackings = collect();
        }
    }
    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->role === 'owner' || Auth::user()->role === 'store_keeper');
    }
}
