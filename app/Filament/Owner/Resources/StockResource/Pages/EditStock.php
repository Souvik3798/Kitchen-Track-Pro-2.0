<?php

namespace App\Filament\Owner\Resources\StockResource\Pages;

use App\Filament\Owner\Resources\StockResource;
use App\Models\Stock;
use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStock extends EditRecord
{
    protected static string $resource = StockResource::class;

    // Store the original quantity
    protected $originalStoreQuantity;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Capture the original store quantity before the update
        $this->originalStoreQuantity = $this->record->store_quantity;

        return $data;
    }

    protected function afterSave(): void
    {
        // Calculate the difference in store quantity
        $quantityDifference = $this->record->store_quantity - $this->originalStoreQuantity;

        // If there's a change in store quantity, create a transaction record
        if ($quantityDifference != 0) {
            Transaction::create([
                'inventory_id' => $this->record->inventory_id,
                'organization_id' => auth()->user()->organization_id,
                'user_id' => auth()->id(),
                'source' => 'store',
                'destination' => 'store',
                'quantity' => $quantityDifference,
                'price' => $this->record->price,
            ]);

            // Send a success notification
            Notification::make()
                ->title('Stock Updated')
                ->body('Stock Data Edited Successfully')
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        // Redirect to the index page of the StockResource
        return $this->getResource()::getUrl('index');
    }
}
