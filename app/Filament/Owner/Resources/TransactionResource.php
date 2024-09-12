<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static ?string $modelLabel = 'Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inventory.name')
                    ->label('Item')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('source')
                    ->label('Source')
                    ->color(function ($state) {
                        return match ($state) {
                            'purchase' => 'info',
                            'store' => 'success',
                            'kitchen' => 'warning',
                            'sold' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return strtoupper($state);
                    }),
                Tables\Columns\BadgeColumn::make('destination')
                    ->label('Destination')
                    ->color(function ($state) {
                        return match ($state) {
                            'purchase' => 'info',
                            'store' => 'success',
                            'kitchen' => 'warning',
                            'sold' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return strtoupper($state);
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->suffix(function ($record) {
                        // Access the related inventory unit directly
                        $inventory = $record->inventory;
                        return $inventory ? ' ' . $inventory->unit : '';
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->default(function ($record) {
                        $price = $record->price * $record->quantity;
                        return number_format($price, 2);
                    })
                    ->prefix('₹.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Transaction Date')
                    ->dateTime('d m,Y h:i A')
                    ->sortable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                // Add any necessary filters here
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('organization_id', auth()->user()->organization_id);
            });
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->role === 'owner'; // Replace 1 with the ID of the user who should have access
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->role === 'owner'; // Replace 1 with the ID of the user who should have access
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Auth::user()->role === 'owner'; // Replace 1 with the ID of the user who should have access
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Auth::user()->role === 'owner'; // Replace 1 with the ID of the user who should have access
    }
}
