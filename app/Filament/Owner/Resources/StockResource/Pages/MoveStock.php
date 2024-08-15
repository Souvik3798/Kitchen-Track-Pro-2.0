<?php

namespace App\Filament\Owner\Resources\StockResource\Pages;

use App\Filament\Owner\Resources\StockResource;
use App\Models\Stock;
use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class MoveStock extends Page
{
    public Stock $stock;
    public $inventory_name;
    public $inventory_id;
    public $store_quantity;
    public $quantity_to_move;
    public $unit;

    protected static string $resource = StockResource::class;

    protected static string $view = 'filament.owner.resources.stock-resource.pages.move-stock';

    public function mount(int | string $record): void
    {
        $this->stock = Stock::findOrFail($record);

        if ($this->stock->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        // Initialize form data
        $this->inventory_name = $this->stock->inventory->name;
        $this->inventory_id = $this->stock->inventory_id;
        $this->store_quantity = $this->stock->store_quantity;
        $this->quantity_to_move = null; // Set this to null initially
        $this->unit = $this->stock->inventory->unit;
    }

    public function moveStock()
    {
        // Validate and process the stock movement
        if ($this->quantity_to_move > $this->store_quantity) {
            Notification::make()
                ->title('Insufficient quantity in store')
                ->danger()
                ->send();
            return;
        }

        // Update the stock quantities
        $this->stock->store_quantity -= $this->quantity_to_move;
        $this->stock->kitchen_quantity += $this->quantity_to_move;
        $move = $this->stock->save();

        if ($move) {
            Transaction::create([
                'inventory_id' => $this->inventory_id,
                'organization_id' => auth()->user()->organization_id,
                'user_id' => auth()->id(),
                'source' => 'store',
                'destination' => 'kitchen',
                'quantity' => $this->quantity_to_move,
                'price' => 0
            ]);
        }

        Notification::make()
            ->title('Stock moved successfully')
            ->success()
            ->send();

        return $this->redirectRoute('filament.owner.resources.stocks.index');
    }
}
