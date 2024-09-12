<?php

namespace App\Filament\Owner\Widgets;

use App\Models\ShopOrder; // Assuming your ShopOrder model is named ShopOrder
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPurchases extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Purchases';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ShopOrder::query()
                    ->where('status', 'completed')
                    ->where('organization_id', auth()->user()->organization->id)
                    ->latest() // Orders by most recent first
                    ->limit(10) // Limits to 10 results
            )
            ->columns([
                Tables\Columns\TextColumn::make('supplier_order_id')->label('Order ID'),
                Tables\Columns\TextColumn::make('created_at')->label('Date'),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Items')
                    ->getStateUsing(function ($record) {
                        return $record->details->sum('quantity') . ' Nos.'; // Sum the quantities in details
                    }),
                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->getStateUsing(function ($record) {
                        $total = 0;
                        foreach ($record->details as $detail) {
                            $total += $detail->shopstock->price * $detail->quantity;
                        }
                        return '₹.' . $total;
                    }), // Sum the costs in details
            ])->paginated(false);
    }
}
