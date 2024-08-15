<?php

namespace App\Filament\Owner\Resources\StockResource\Pages;

use App\Filament\Owner\Resources\StockResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStocks extends ListRecords
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'today' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('updated_at', Carbon::today())),
            'this_week' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])),
            'this_month' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('updated_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)),
            'this_year' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereYear('updated_at', Carbon::now()->year)),
        ];
    }
}
