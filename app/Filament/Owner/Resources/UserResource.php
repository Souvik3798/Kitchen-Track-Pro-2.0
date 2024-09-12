<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\UserResource\Pages;
use App\Filament\Owner\Resources\UserResource\Pages\CreateUser;
use App\Filament\Owner\Resources\UserResource\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?int $navigationSort = 1;

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
                        Hidden::make('organization_id')
                            ->default(fn($state) => auth()->user()->organization_id),
                        Select::make('role')
                            ->options([
                                'owner' => 'Owner',
                                'store_keeper' => 'Store Keeper',
                                'hotel_staff' => 'Kitchen Staff',
                            ])
                            ->required()
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
                BadgeColumn::make('role')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        $roles = [
                            'owner' => 'Owner',
                            'store_keeper' => 'Store Keeper',
                            'hotel_staff' => 'Kitchen Staff',
                        ];
                        return $roles[$record->role] ?? $record->role;
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'Owner' => 'success',
                            'Store Keeper' => 'warning',
                            'Kitchen Staff' => 'danger',
                            default => 'gray',
                        };
                    })

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
