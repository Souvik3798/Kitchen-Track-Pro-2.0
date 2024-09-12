<?php

namespace App\Filament\Shop\Resources;

use App\Filament\Shop\Resources\ShopOrderResource\Pages;
use App\Filament\Shop\Resources\ShopOrderResource\RelationManagers;
use App\Models\OrderTracking;
use App\Models\ShopOrder;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShopOrderResource extends Resource
{
    protected static ?string $model = ShopOrder::class;

    protected static ?string $navigationIcon = 'heroicon-s-swatch';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Orders';

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
                Stack::make([
                    TextColumn::make('organization.name')
                        ->label('Customer')
                        ->icon('heroicon-s-building-office')
                        ->iconColor('primary')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('organization.contact_number')
                        ->label('Phone')
                        ->icon('heroicon-s-phone')
                        ->iconColor('info')
                        ->prefix('+91-')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('details')
                        ->label('Total Items')
                        ->icon('heroicon-s-clipboard-document-list')
                        ->iconColor('success')
                        ->searchable()
                        ->formatStateUsing(function ($record) {
                            return count($record->details) . ' item(s)';
                        })
                        ->sortable(),
                    TextColumn::make('status')
                        ->label('Status')
                        ->alignment(Alignment::End)
                        ->badge()
                        ->icon(fn(string $state): string => match ($state) {
                            'pending' => 'heroicon-s-clock',
                            'accepted' => 'heroicon-s-clipboard-document-check',
                            'shipped' => 'heroicon-s-truck',
                            'completed' => 'heroicon-s-check-badge',
                            'rejected' => 'heroicon-s-x-circle',
                            'cancelled' => 'heroicon-s-archive-box-x-mark',
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'pending' => 'info',
                            'accepted' => 'secondary',
                            'shipped' => 'warning',
                            'completed' => 'success',
                            'rejected' => 'danger',
                            'cancelled' => 'danger',
                        }),
                    TextColumn::make('created_at')
                        ->label('Time')
                        ->icon('heroicon-s-clock')
                        ->iconColor('warning')
                        ->sortable()
                        ->since()
                        ->dateTimeTooltip(),
                ])

            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('view')
                    ->label('Details')
                    ->icon('heroicon-s-information-circle')
                    ->url(fn($record) => ShopOrderResource::geturl('view', ['record' => $record->id]))
                    ->color('info'),
                Action::make('action')
                    ->label('Action')
                    ->icon('heroicon-s-check-circle')
                    ->modalHeading('Accept or Reject Order')
                    ->modalSubmitActionLabel('Apply')
                    ->modalDescription('PLease Choose an Option to continue')
                    ->form([
                        ToggleButtons::make('status')
                            ->label('Action')
                            ->options([
                                'accepted' => 'Accept',
                                'rejected' => 'Reject',
                            ])
                            ->icons([
                                'accepted' => 'heroicon-s-check-circle',
                                'rejected' => 'heroicon-s-x-circle',
                            ])
                            ->colors([
                                'accepted' => 'success',
                                'rejected' => 'danger',
                            ])
                            ->inline()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                // Show review field only when rejecting
                                if ($state === 'rejected') {
                                    $set('reviewVisible', true);
                                } else {
                                    $set('reviewVisible', false);
                                }
                            }),

                        MarkdownEditor::make('review')
                            ->label('Reason to Reject')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'heading',
                                'italic',
                                'strike',
                                'undo',
                            ])
                            ->required(fn($get) => $get('status') === 'rejected')
                            ->visible(fn($get) => $get('status') === 'rejected')
                            ->hint('Please provide a reason for rejection'),

                    ])
                    ->modalWidth(MaxWidth::Large)
                    ->modalAlignment(Alignment::Center)
                    ->modalIcon('heroicon-s-pencil-square')
                    ->modalIconColor('warning')
                    ->action(function (array $data, ShopOrder $record) {
                        if ($data['status'] === 'accepted') {
                            $insufficientStockItems = [];

                            foreach ($record->details as $detail) {
                                $shopStock = $detail->shopstock;
                                if ($shopStock && $shopStock->quantity < $detail->quantity) {
                                    $insufficientStockItems[] = $detail->product->name;
                                }
                            }

                            if (!empty($insufficientStockItems)) {
                                Notification::make()
                                    ->title('Insufficient Stock')
                                    ->body('The following items do not have enough stock to accept the order: ' . implode(', ', $insufficientStockItems))
                                    ->danger()
                                    ->send();
                                return;
                            }
                        }

                        // Update the order status and review reason if applicable
                        $record->update([
                            'status' => $data['status'],
                            'review' => $data['status'] === 'rejected' ? $data['review'] : null,
                        ]);

                        OrderTracking::create([
                            'order_id' => $record->id,
                            'status' => 'accepted',
                            'description' => 'Order Accepted',
                        ]);

                        Notification::make()
                            ->title('Order Updated')
                            ->body('The order status has been updated successfully.')
                            ->success()
                            ->send();
                    })
                    ->hidden(fn($record) => in_array($record->status, ['rejected', 'cancelled', 'accepted', 'shipped', 'completed'])),
                Action::make('shipped')
                    ->label('Mark as Shipped')
                    ->icon('heroicon-s-truck')
                    ->color('warning')
                    ->action(function (ShopOrder $record) {

                        // Update the order status to shipped
                        $record->update([
                            'status' => 'shipped',
                        ]);

                        OrderTracking::create([
                            'order_id' => $record->id,
                            'status' => 'shipped',
                            'description' => 'Order Shipped',
                        ]);

                        Notification::make()
                            ->title('Order Status Updated')
                            ->body('The order has been shipped.')
                            ->success()
                            ->send();
                    })
                    ->hidden(fn($record) => $record->status !== 'accepted' || in_array($record->status, ['rejected', 'cancelled', 'shipped'])),
                Action::make('completed')
                    ->label('Mark as Delivered')
                    ->icon('heroicon-s-check-badge')
                    ->color('success')
                    ->action(function (ShopOrder $record) {

                        // Deduct quantities from the shop's stock
                        foreach ($record->details as $detail) {
                            $shopStock = $detail->shopstock;
                            if ($shopStock) {
                                $shopStock->quantity -= $detail->quantity;
                                $shopStock->save();
                            }
                        }

                        // Update the order status to completed
                        $record->update([
                            'status' => 'completed',
                        ]);

                        OrderTracking::create([
                            'order_id' => $record->id,
                            'status' => 'completed',
                            'description' => 'Order Delivered',
                        ]);
                    })
                    ->hidden(fn($record) => $record->status !== 'shipped' || in_array($record->status, ['rejected', 'cancelled', 'completed'])),


            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('supplier_id', auth()->user()->organization_id);
            })
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListShopOrders::route('/'),
            // 'create' => Pages\CreateShopOrder::route('/create'),
            // 'edit' => Pages\EditShopOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrders::route('/{record}/details')
        ];
    }
}
