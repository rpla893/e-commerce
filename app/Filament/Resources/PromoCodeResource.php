<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(PromoCode::class),

                Forms\Components\TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                    //->mask(fn (Forms\Components\TextInput\Mask $mask) =>
                       // $mask->numeric()->decimalPlaces(2)->thousandsSeparator(',')
                    //),

                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Expiry Date')
                    ->nullable()
                    ->default(now()->addDays(30))
                    ->helperText('Leave empty for no expiry date.')
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('custom_id')->label('Custom ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('discount')
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('discount_amount')
                    ->options([
                        '50000' => 'IDR 50,000',
                        '100000' => 'IDR 100,000',
                        '150000' => 'IDR 150,000',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
