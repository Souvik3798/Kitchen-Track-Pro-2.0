<?php

namespace App\Filament\Shop\Resources\ShopStockResource\Pages;

use App\Filament\Shop\Resources\ShopStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShopStock extends EditRecord
{
    protected static string $resource = ShopStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
