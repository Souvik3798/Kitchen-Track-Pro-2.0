<?php

namespace App\Filament\Shop\Resources;

use App\Filament\Shop\Resources\ShopStockResource\Pages;
use App\Filament\Shop\Resources\ShopStockResource\RelationManagers;
use App\Models\ShopStock;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShopStockResource extends Resource
{
    protected static ?string $model = ShopStock::class;

    protected static ?string $navigationIcon = 'heroicon-s-inbox-stack';
    protected static ?string $modelLabel = 'Stock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('organization_id')
                    ->default(auth()->user()->organization_id),
                Repeater::make('stocks')
                    ->label('Add Items to Store')
                    ->schema([
                        Select::make('inventory_id')
                            ->relationship('inventory', 'name')
                            ->required()
                            ->reactive()
                            ->options(function () {
                                return \App\Models\Inventory::pluck('name', 'id')
                                    ->map(function ($name) {
                                        return ucwords($name);
                                    });
                            })
                            ->preload()
                            ->searchable()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Set the unit dynamically based on selected inventory for the specific repeater item
                                $unit = $state ? \App\Models\Inventory::find($state)?->unit : '';
                                $set($get('statePath') . '.unit', $unit);
                            }),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->suffix(fn($get) => $get($get('statePath') . '.unit')) // Use the 'get' function with the repeater item path
                            ->reactive(),
                        Hidden::make('unit'), // Hidden field to store the unit per repeater item
                        TextInput::make('price')
                            ->numeric()
                            ->label('Price Per Unit')
                            ->prefix('₹')
                            ->suffix('/-')
                            ->required()
                            ->default(0),
                    ])
                    ->createItemButtonLabel('Add Item')
                    ->columns(2)
                    ->required()
                    ->visibleOn('create'),

                Select::make('inventory_id')
                    ->relationship('inventory', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Set the unit dynamically based on selected inventory
                        $unit = $state ? \App\Models\Inventory::find($state)?->unit : '';
                        $set('unit', $unit);
                    })
                    ->afterStateHydrated(function ($state, callable $set) {
                        // Set the unit dynamically based on selected inventory
                        $unit = $state ? \App\Models\Inventory::find($state)?->unit : '';
                        $set('unit', $unit);
                    })
                    ->visibleOn('edit'),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->suffix(fn($get) => $get('unit')) // Use the 'get' function to access reactive values
                    ->reactive()
                    ->visibleOn('edit'), // Make the TextInput reactive as well
                Hidden::make('unit')
                    ->visibleOn('edit'), // Hidden field to store the unit
                TextInput::make('price')
                    ->label('Price Per Unit')
                    ->numeric()
                    ->prefix('₹')
                    ->suffix('/-')
                    ->required()
                    ->default(0)
                    ->visibleOn('edit')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        TextColumn::make('inventory.name')
                            ->label('Items')
                            ->icon('heroicon-s-archive-box')
                            ->iconColor('primary')
                            ->weight(FontWeight::Bold)
                            ->sortable()
                            ->formatStateUsing(fn($state) => ucwords($state))
                            ->searchable(),
                        TextColumn::make('quantity')
                            ->label('Quantity')
                            ->description(function ($record) {
                                if ($record->quantity <= '5' && $record->quantity >= '1') {
                                    return 'Low Stock';
                                }

                                if ($record->quantity < 1) {
                                    return 'Out of stock. Please increase inventory to make it available in the store.';
                                }
                            })
                            ->icon('heroicon-s-circle-stack')
                            ->iconColor('info')
                            ->formatStateUsing(fn($state) => number_format($state, 3))
                            ->suffix(function ($record) {
                                // Access the related inventory unit directly
                                $inventory = $record->inventory;
                                return $inventory ? ' ' . $inventory->unit : '';
                            })
                            ->sortable()
                            ->searchable(),
                        TextColumn::make('price')
                            ->label('Price')
                            ->icon('heroicon-s-currency-rupee')
                            ->iconColor('success')
                            ->suffix(function ($record) {
                                // Access the related inventory unit directly
                                $inventory = $record->inventory;
                                return $inventory ? '/- Per ' . $inventory->unit : '';
                            })
                            ->prefix('₹')
                            ->sortable()
                            ->searchable(),
                    ])

            ])->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('organization_id', auth()->user()->organization_id);
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListShopStocks::route('/'),
            'create' => Pages\CreateShopStock::route('/create'),
            'edit' => Pages\EditShopStock::route('/{record}/edit'),
        ];
    }
}
