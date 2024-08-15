<?php

namespace App\Filament\Owner\Resources\StockResource\Pages;

use App\Filament\Owner\Resources\StockResource;
use App\Models\Stock;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStock extends CreateRecord
{
    protected static string $resource = StockResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Unset stocks from the main data array since we'll handle it separately
        $stocks = $data['stocks'] ?? []; // Ensure $stocks is defined
        unset($data['stocks']);

        foreach ($stocks as $stockData) {
            // Check if the stock item already exists for this organization and inventory
            $existingStock = Stock::where('inventory_id', $stockData['inventory_id'])
                ->where('organization_id', auth()->user()->organization_id)
                ->first();

            if ($existingStock) {
                // If the stock item exists, update the store quantity


                $done = $existingStock->update([
                    'store_quantity' => $existingStock->store_quantity + $stockData['store_quantity'],
                    'price' => $stockData['price'],
                ]);

                if ($done) {
                    Transaction::create([
                        'inventory_id' => $stockData['inventory_id'],
                        'organization_id' => auth()->user()->organization_id,
                        'supplier_id' => $data['supplier_id'],
                        'user_id' => auth()->id(),
                        'source' => 'purchase',
                        'destination' => 'store',
                        'quantity' => $stockData['store_quantity'],
                        'price' => $stockData['price']
                    ]);
                }
            } else {
                // If it doesn't exist, create a new stock entry
                $stk = Stock::create([
                    'supplier_id' => $data['supplier_id'],
                    'inventory_id' => $stockData['inventory_id'],
                    'store_quantity' => $stockData['store_quantity'],
                    'kitchen_quantity' => 0, // Kitchen quantity should always start at 0
                    'organization_id' => auth()->user()->organization_id,
                    'user_id' => auth()->id(),
                    'price' => $stockData['price']
                ]);

                if ($stk) {
                    Transaction::create([
                        'inventory_id' => $stockData['inventory_id'],
                        'supplier_id' => $data['supplier_id'],
                        'organization_id' => auth()->user()->organization_id,
                        'user_id' => auth()->id(),
                        'source' => 'purchase',
                        'destination' => 'store',
                        'quantity' => $stockData['store_quantity'],
                        'price' => $stockData['price']
                    ]);
                }
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
