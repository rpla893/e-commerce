<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductTransactionResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\ToggleButtons;
use App\Models\Shoes;
use App\Models\PromoCode;
use Filament\Notifications\Notification;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(ProductTransactionResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')

            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('custom_id')->label('Custom ID')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('shoes.thumbnail')->label('Product'),
                Tables\Columns\TextColumn::make('name')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('booking_trx_id')->label('Booking ID')->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                ->label('Payment Status')
                ->formatStateUsing(fn ($record) =>
                    strtoupper($record->payment_provider) .
                    ($record->payment_provider ? ' - ' . strtoupper($record->payment_provider) : '')
                )
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('shipping_method')->label('Shipping'),
                Tables\Columns\BadgeColumn::make('is_verified')
                    ->label('Verification')
                    ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Pending')
                    ->colors([
                        'success' => fn ($state) => $state == 1,
                        'warning' => fn ($state) => $state == 0,
                    ]),
                Tables\Columns\TextColumn::make('barcode')->label('Barcode'),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ]);
    }
}
