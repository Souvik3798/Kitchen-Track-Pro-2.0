<?php

namespace App\Filament\Shop\Resources\ShopStockResource\Pages;

use App\Filament\Shop\Resources\ShopStockResource;
use App\Models\ShopStock;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateShopStock extends CreateRecord
{
    protected static string $resource = ShopStockResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Unset stocks from the main data array since we'll handle it separately
        $stocks = $data['stocks'] ?? []; // Ensure $stocks is defined
        unset($data['stocks']);

        foreach ($stocks as $stockData) {
            // Check if the stock item already exists for this organization and inventory
            $existingStock = ShopStock::where('inventory_id', $stockData['inventory_id'])
                ->where('organization_id', auth()->user()->organization_id)
                ->first();

            if ($existingStock) {
                $existingStock->update([
                    'quantity' => $existingStock->store_quantity + $stockData['quantity'],
                    'price' => ($stockData['price'] != 0) ? $stockData['price'] : $existingStock->price,
                ]);
            } else {
                ShopStock::create([
                    'inventory_id' => $stockData['inventory_id'],
                    'quantity' => $stockData['quantity'],
                    'organization_id' => auth()->user()->organization_id,
                    'price' => $stockData['price']
                ]);
            }
        }

        // Return empty array since no actual main record is being created
        return [];
    }

    public function create(bool $another = false): void
    {
        // Handle record creation manually
        $data = $this->form->getState();
        $this->mutateFormDataBeforeCreate($data);

        Notification::make()
            ->title('Stock')
            ->body('Your Stock(s) updated Successfully')
            ->success()
            ->send();

        // Prevent the default Filament behavior of creating a main record
        $this->redirect($this->getRedirectUrl());
    }

    protected function getRedirectUrl(): string
    {
        // Redirect to the index page of the StockResource
        return $this->getResource()::getUrl('index');
    }
}
