<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\SupplierResource\Pages;
use App\Filament\Owner\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Supplier Name')
                    ->required(),
                Textarea::make('contact_info')
                    ->label('Contact Information')
                    ->rows(5)
                    ->required(),
                Hidden::make('organization_id')
                    ->default(auth()->user()->organization_id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Supplier Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contact_info')
                    ->label('Contact Information')
                    ->sortable()
                    ->searchable(),

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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
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
