<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\InventoryResource\Pages;
use App\Filament\Owner\Resources\InventoryResource\RelationManagers;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Item name')
                    ->placeholder('Eg: Chicken, Mutton, Milk, Chilli Powder')
                    ->required(),
                Forms\Components\Select::make('unit')
                    ->label('Unit')
                    ->options([
                        'kg' => 'Kg',
                        'Litre' => 'Litre'
                    ])
                    ->required(),
                Hidden::make('organization_id')
                    ->default(auth()->user()->organization_id),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('organization_id', auth()->user()->organization_id);
            })
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
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
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
