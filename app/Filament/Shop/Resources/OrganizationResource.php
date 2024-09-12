<?php

namespace App\Filament\Shop\Resources;

use App\Filament\Shop\Resources\OrganizationResource\Pages;
use App\Filament\Shop\Resources\OrganizationResource\RelationManagers;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-s-building-office-2';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Hotels';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                        ImageColumn::make('logo')
                            ->circular()
                            ->visibility('private')
                            ->defaultImageUrl(url('https://media.istockphoto.com/id/1396814518/vector/image-coming-soon-no-photo-no-thumbnail-image-available-vector-illustration.jpg?s=612x612&w=0&k=20&c=hnh2OZgQGhf0b46-J2z7aHbIWwq8HNlSDaNp2wn_iko='))
                            ->height('200px'),
                        Tables\Columns\TextColumn::make('name')
                            ->icon('heroicon-s-building-office')
                            ->iconColor('primary')
                            ->weight(FontWeight::Bold)
                            ->sortable()
                            ->searchable(),
                        Tables\Columns\TextColumn::make('email')
                            ->icon('heroicon-s-envelope')
                            ->iconColor('success')
                            ->fontFamily(FontFamily::Mono)
                            ->copyable()
                            ->copyMessage('Email address copied')
                            ->copyMessageDuration(1500)
                            ->sortable()
                            ->searchable(),
                        Tables\Columns\TextColumn::make('contact_number')
                            ->icon('heroicon-s-phone')
                            ->iconColor('info')
                            ->url(fn($record) => 'https://wa.me/91' . $record->contact_number)
                            ->url(fn($record) => 'https://wa.me/91' . $record->contact_number, true)
                            ->openUrlInNewTab()
                            ->sortable()
                            ->searchable()
                            ->prefix('+91-'),
                        Tables\Columns\TextColumn::make('address')
                            ->icon('heroicon-s-map-pin')
                            ->iconColor('secondary')
                            ->sortable()
                            ->searchable(),
                    ])


            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('type', 'hotel');
            });
        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
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
            'index' => Pages\ListOrganizations::route('/'),
            // 'create' => Pages\CreateOrganization::route('/create'),
            // 'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
