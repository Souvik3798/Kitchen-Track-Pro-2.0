<?php

namespace App\Filament\Shop\Resources\ShopStockResource\Pages;

use App\Filament\Shop\Resources\ShopStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopStocks extends ListRecords
{
    protected static string $resource = ShopStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
