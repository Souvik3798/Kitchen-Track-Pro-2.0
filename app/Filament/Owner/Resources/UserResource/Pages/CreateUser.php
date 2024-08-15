<?php

namespace App\Filament\Owner\Resources\UserResource\Pages;

use App\Filament\Owner\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect to the index page of the StockResource
        return $this->getResource()::getUrl('index');
    }
}
