<?php

namespace App\Filament\Shop\Resources\ShopOrderResource\Pages;

use App\Filament\Shop\Resources\ShopOrderResource;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;

class ViewOrders extends ViewRecord
{
    protected static string $resource = ShopOrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Customer Details')
                    ->schema([
                        ImageEntry::make('organization.logo')
                            ->label('Logo')
                            ->circular()
                            ->defaultImageUrl(url('https://t3.ftcdn.net/jpg/04/60/01/36/360_F_460013622_6xF8uN6ubMvLx0tAJECBHfKPoNOR5cRa.jpg')),
                        TextEntry::make('organization.name')
                            ->label('Customer'),
                        TextEntry::make('organization.address')
                            ->label('Address'),
                        TextEntry::make('organization.contact_number')
                            ->label('Phone')
                            ->prefix('+91-'),
                        TextEntry::make('organization.email')
                            ->label('Email'),
                    ])
                    ->columns(3),
                Section::make('Order Status')
                    ->schema([
                        TextEntry::make('supplier_order_id')
                            ->label('Supplier Order-ID'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->icon(fn(string $state): string => match ($state) {
                                'pending' => 'heroicon-o-clock',
                                'accepted' => 'heroicon-o-clipboard-document-check',
                                'shipped' => 'heroicon-o-truck',
                                'completed' => 'heroicon-o-check-badge',
                                'rejected' => 'heroicon-o-x-circle',
                                'cancelled' => 'heroicon-o-archive-box-x-mark',
                            })
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'info',
                                'accepted' => 'secondary',
                                'shipped' => 'warning',
                                'completed' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'danger'
                            }),
                    ])
                    ->columns(2),
                Section::make('Order Details')
                    ->schema([
                        RepeatableEntry::make('details')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Item')
                                    ->formatStateUsing(function ($state) {
                                        return ucwords($state);
                                    }),
                                TextEntry::make('quantity')
                                    ->label('Quantity')
                                    ->suffix(function ($record) {
                                        return ' (' . $record->product->unit . ')';
                                    })

                            ])
                            ->columns(2)
                            ->grid(2),

                    ]),
            ]);
    }
}
