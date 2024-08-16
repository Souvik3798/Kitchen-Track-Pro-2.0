<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\StockResource\Pages;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required(),
                Hidden::make('organization_id')
                    ->default(auth()->user()->organization_id),
                Hidden::make('user_id')
                    ->default(auth()->id()),
                Repeater::make('stocks')
                    ->label('Add Items to Store')
                    ->schema([
                        Select::make('inventory_id')
                            ->relationship('inventory', 'name', function ($query) {
                                $query->where('organization_id', auth()->user()->organization_id);
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Set the unit dynamically based on selected inventory for the specific repeater item
                                $unit = $state ? \App\Models\Inventory::find($state)?->unit : '';
                                $set($get('statePath') . '.unit', $unit);
                            }),
                        TextInput::make('store_quantity')
                            ->label('Quantity For Store')
                            ->numeric()
                            ->suffix(fn($get) => $get($get('statePath') . '.unit')) // Use the 'get' function with the repeater item path
                            ->reactive(),
                        Hidden::make('unit'), // Hidden field to store the unit per repeater item
                        TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ])
                    ->createItemButtonLabel('Add Item')
                    ->columns(2)
                    ->required()
                    ->visibleOn('create'),
                Select::make('inventory_id')
                    ->relationship('inventory', 'name', function ($query) {
                        $query->where('organization_id', auth()->user()->organization_id);
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Set the unit dynamically based on selected inventory
                        $unit = $state ? \App\Models\Inventory::find($state)?->unit : '';
                        $set('unit', $unit);
                    })
                    ->visibleOn('edit'),
                TextInput::make('store_quantity')
                    ->label('Quantity For Store')
                    ->numeric()
                    ->suffix(fn($get) => $get('unit')) // Use the 'get' function to access reactive values
                    ->reactive()
                    ->visibleOn('edit'), // Make the TextInput reactive as well
                Hidden::make('unit')
                    ->visibleOn('edit'), // Hidden field to store the unit
                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->visibleOn('edit')
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inventory.name')->label('Inventory Item')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Updated By')->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('store_quantity')->label('Store Quantity')
                    ->suffix(function ($record) {
                        // Access the related inventory unit directly
                        $inventory = $record->inventory;
                        return $inventory ? ' ' . $inventory->unit : '';
                    })
                    ->formatStateUsing(fn($state) => number_format($state, 2))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kitchen_quantity')->label('Kitchen Quantity')->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => number_format($state, 2))
                    ->suffix(function ($record) {
                        // Access the related inventory unit directly
                        $inventory = $record->inventory;
                        return $inventory ? ' ' . $inventory->unit : '';
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')
                    ->sortable()
                    ->dateTime('d M, Y h:i:s A'),
            ])->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('move')
                    ->label('Move Stock')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle') // You can use any icon
                    ->url(fn($record) => StockResource::getUrl('move', ['record' => $record->id]))
                    ->color('success')

            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('organization_id', auth()->user()->organization_id);
            })
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
            'move' => Pages\MoveStock::route('/{record}/move'), // Custom page for moving stock
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->role === 'owner' || Auth::user()->role === 'store_keeper'; // Replace 1 with the ID of the user who should have access
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->role === 'owner'  || Auth::user()->role === 'store_keeper'; // Replace 1 with the ID of the user who should have access
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Auth::user()->role === 'owner' || Auth::user()->role === 'store_keeper'; // Replace 1 with the ID of the user who should have access
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Auth::user()->role === 'owner' || Auth::user()->role === 'store_keeper'; // Replace 1 with the ID of the user who should have access
    }
}
