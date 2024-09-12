<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\Pages\CreateUser;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Details')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(table: User::class, ignoreRecord: true),
                        TextInput::make('password')
                            ->required()
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->visible(fn($livewire) => $livewire instanceof CreateUser)
                            ->rule(Password::default()),
                        Select::make('organization_id')
                            ->label('Organization')
                            ->options(Organization::all()->pluck('name', 'id'))
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'shop' => 'Shop',
                                'owner' => 'Owner',
                                'store_keeper' => 'Store Keeper',
                                'hotel_staff' => 'Kitchen Staff',
                            ])

                    ])->columns(2),
                Section::make('User New password')
                    ->schema([
                        TextInput::make('new_password')
                            ->nullable()
                            ->password()
                            ->rule(Password::default()),
                        TextInput::make('new_password_confirmation')
                            ->password()
                            ->same('new_password')
                            ->requiredWith('new_password'),

                    ])->visible(fn($livewire) => $livewire instanceof EditUser),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()
                    ->sortable(),
                TextColumn::make('organization.name')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('role')
                    ->getStateUsing(function ($record) {
                        $roles = [
                            'admin' => 'Admin',
                            'owner' => 'Owner',
                            'store_keeper' => 'Store Keeper',
                            'hotel_staff' => 'Kitchen Staff',
                            'shop' => 'Shop'
                        ];
                        return $roles[$record->role] ?? $record->role;
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'Admin' => 'gray',
                            'Shop' => 'info',
                            'Owner' => 'success',
                            'Store Keeper' => 'warning',
                            'Kitchen Staff' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
