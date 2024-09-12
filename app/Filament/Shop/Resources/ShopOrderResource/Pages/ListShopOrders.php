<?php

namespace App\Filament\Shop\Resources\ShopOrderResource\Pages;

use App\Filament\Shop\Resources\ShopOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopOrders extends ListRecords
{
    protected static string $resource = ShopOrderResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
