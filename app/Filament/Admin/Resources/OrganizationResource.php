<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrganizationResource\Pages;
use App\Filament\Admin\Resources\OrganizationResource\RelationManagers;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\Textarea::make('address')
                    ->required(),
                TextInput::make('contact_number')
                    ->required()
                    ->numeric()
                    ->prefix('+91-'),
                TextInput::make('email')
                    ->required()
                    ->email(),
                TextInput::make('gst')
                    ->label('GST Number')
                    ->placeholder('Enter GST Number')
                    ->maxLength(15)
                    ->required(),
                TextInput::make('pan')
                    ->label('PAN Number')
                    ->placeholder('Enter PAN Number')
                    ->maxLength(10)
                    ->required(),
                FileUpload::make('signature')
                    ->label('Signature and Seal(optional)')
                    ->required()
                    ->image()
                    ->directory('uploads/signature'),
                FileUpload::make('logo')
                    ->image()
                    ->directory('uploads/logo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_number')
                    ->prefix('+91-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('logo')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
