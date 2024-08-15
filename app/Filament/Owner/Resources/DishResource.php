<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\DishResource\Pages;
use App\Filament\Owner\Resources\DishResource\RelationManagers;
use App\Models\Dish;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DishResource extends Resource
{
    protected static ?string $model = Dish::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Dish Name')
                    ->required(),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->prefix('₹')
                    ->suffix('/-'),
                Hidden::make('user_id')
                    ->default(auth()->id()),
                Hidden::make('organization_id')
                    ->default(auth()->user()->organization_id),
                Repeater::make('ingredients')
                    ->label('Ingredients')
                    ->relationship('ingredients')
                    ->schema([
                        Select::make('inventory_id')
                            ->label('Select Ingredient')
                            ->options(function () {
                                return \App\Models\Inventory::whereHas('stocks', function ($query) {
                                    $query->where('organization_id', auth()->user()->organization_id)
                                        ->where('store_quantity', '>', 0); // Ensure the ingredient is in stock
                                })->pluck('name', 'id');
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Retrieve the unit and set the display unit accordingly
                                $unit = \App\Models\Inventory::find($state)?->unit;
                                $displayUnit = $unit === 'kg' ? 'gm' : ($unit === 'Litre' ? 'ml' : $unit);
                                $set('unit', $unit);
                                $set('unit_display', $displayUnit);
                            })
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required(),
                        Hidden::make('unit')
                            ->reactive()
                            ->afterStateHydrated(function ($state, callable $set, $get) {
                                // Hydrate unit field with correct value on edit
                                $inventoryId = $get('inventory_id');
                                if ($inventoryId) {
                                    $unit = \App\Models\Inventory::find($inventoryId)?->unit;
                                    $displayUnit = $unit === 'kg' ? 'gm' : ($unit === 'Litre' ? 'ml' : $unit);
                                    $set('unit', $unit);
                                    $set('unit_display', $displayUnit);
                                }
                            }),
                        TextInput::make('unit_display')
                            ->label('Unit')
                            ->disabled()
                            ->required(),
                    ])
                    ->createItemButtonLabel('Add Ingredient')
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Dish')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->prefix('₹.')
                    ->suffix('/-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ingredients')
                    ->label('Ingredients')
                    ->formatStateUsing(function ($record) {
                        return count($record->ingredients) . ' No(s).';
                    }),
            ])->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListDishes::route('/'),
            'create' => Pages\CreateDish::route('/create'),
            'edit' => Pages\EditDish::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->role === 'owner' || Auth::user()->role === 'hotel_staff'; // Replace 1 with the ID of the user who should have access
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->role === 'owner'  || Auth::user()->role === 'hotel_staff'; // Replace 1 with the ID of the user who should have access
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Auth::user()->role === 'owner' || Auth::user()->role === 'hotel_staff'; // Replace 1 with the ID of the user who should have access
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Auth::user()->role === 'owner' || Auth::user()->role === 'hotel_staffr'; // Replace 1 with the ID of the user who should have access
    }
}
