<?php

namespace App\Filament\Owner\Widgets;

use App\Models\Dish;
use App\Models\Order;
use App\Models\Stock;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Stats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Dishes', Dish::where('organization_id', auth()->user()->organization->id)->count() . ' Nos.')
                ->extraAttributes(['style' => 'color: white; background-color: #ddfdd6; font-weight: bold;']),
            Stat::make('Total Sales Orders', Order::where('organization_id', auth()->user()->organization->id)->count() . ' Nos.')
                ->extraAttributes(['style' => 'color: white; background-color: #d6dafd; font-weight: bold;']),
            Stat::make('Store Stocks', Stock::select('store_quantity')->where('store_quantity', '>', '0')->where('organization_id', auth()->user()->organization->id)->count() . ' Items.')
                ->extraAttributes(['style' => 'color: white; background-color: #fdf1d6; font-weight: bold;']),
            Stat::make('Kitchen Stocks', Stock::select('kitchen_quantity')->where('kitchen_quantity', '>', '0')->where('organization_id', auth()->user()->organization->id)->count() . ' Items.')
                ->extraAttributes(['style' => 'color: white; background-color: #fdd6d6; font-weight: bold;']),
        ];
    }
}
