<?php

namespace App\Filament\Owner\Resources\DishResource\Pages;

use App\Filament\Owner\Resources\DishResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDish extends CreateRecord
{
    protected static string $resource = DishResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect to the index page of the StockResource
        return $this->getResource()::getUrl('index');
    }
}
