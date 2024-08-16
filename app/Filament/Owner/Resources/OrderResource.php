<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\OrderResource\Pages;
use App\Filament\Owner\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-rupee';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Fieldset::make('Customer Details')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_id')
                            ->label('Invoice ID')
                            ->visibleOn('edit')
                            ->disabled(),
                        Forms\Components\TextInput::make('customer')
                            ->label('Customer')
                            ->required(),
                        Hidden::make('user_id')
                            ->default(Auth::id()),
                        Hidden::make('organization_id')
                            ->default(Auth::user()->organization_id),
                        TextInput::make('phone')
                            ->label('Mobile number')
                            ->prefix('+91-')
                            ->required(),
                        Textarea::make('contact')
                            ->label('Address')
                            ->required()
                            ->rows(5),
                    ]),

                Fieldset::make('Order Details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => '<span class="text-orange-500">Pending</span>',
                                'in_progress' => '<span class="text-yellow-500">In Progress</span>',
                                'ready' => '<span class="text-yellow-700">Ready</span>',
                                'delivered' => '<span class="text-green-500">Delivered</span>',
                                'cancelled' => '<span class="text-red-500">Cancelled</span>',
                            ])
                            ->allowHtml()
                            ->default('pending')
                            ->visibleOn('edit'),
                        Repeater::make('orderdish')
                            ->label('Dish Details')
                            ->disabled(function ($record) {
                                return ($record->status ?? '') === 'delivered';
                            })
                            ->relationship('orderdish')
                            ->schema([
                                Select::make('dish_id')
                                    ->label('Dish')
                                    ->relationship('dish', 'name')
                                    ->live()
                                    ->after(function (Select $select) {
                                        $select->options(
                                            $select->getRelationship()->getQuery()
                                                ->where('organization_id', Auth::user()->organization_id)
                                                ->get()
                                                ->pluck('name', 'id')
                                        );
                                    })
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        // Fetch the selected dish's price
                                        $price = \App\Models\Dish::find($state)?->price ?? 0;
                                        $set('price', $price);
                                    })
                                    ->required(),
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->live()
                                    ->suffix('No(s)')
                                    ->numeric()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {

                                        $price1 = \App\Models\Dish::find($get('dish_id'))?->price ?? 0;
                                        // Update total price with a delay of 2 seconds
                                        $price = $price1 * $state; // Adjusting the price based on quantity

                                        // Implement delay
                                        sleep(0.5);

                                        $set('price', $price);
                                    })
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Price')
                                    ->prefix('₹.')
                                    ->suffix('/-')
                                    ->live()
                                    ->default(0)
                                    ->required(),
                            ])
                            ->createItemButtonLabel(' (+)    Add Dish')
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_id')->label('Invoice ID')->sortable(),
                TextColumn::make('customer')->sortable(),
                BadgeColumn::make('status')->sortable()
                    ->getStateUsing(function ($record) {
                        $status = [
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            'ready' => 'Ready',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ];
                        return $status[$record->status] ?? $record->status;
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'Pending' => 'warning',
                            'In Progress' => 'warning',
                            'Ready' => 'success',
                            'Delivered' => 'success',
                            'Cancelled' => 'danger',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('created_at')->sortable()->dateTime('d M,Y h:i A'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewInvoice')
                    ->label('Invoice')
                    ->url(fn(Order $record) => route('invoice.generate', $record->id))
                    ->color('success')
                    ->icon('heroicon-o-document-currency-rupee')
                    ->openUrlInNewTab(),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('organization_id', Auth::user()->organization_id);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
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
