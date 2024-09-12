<?php

namespace App\Filament\Shop\Resources\ShopOrderResource\Pages;

use App\Filament\Shop\Resources\ShopOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShopOrder extends EditRecord
{
    protected static string $resource = ShopOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
