<?php

namespace App\Filament\Owner\Resources\InventoryResource\Pages;

use App\Filament\Owner\Resources\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInventory extends CreateRecord
{
    protected static string $resource = InventoryResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect to the index page of the StockResource
        return $this->getResource()::getUrl('index');
    }
}
