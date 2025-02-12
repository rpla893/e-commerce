<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductTransactionResource\Pages;
use App\Filament\Resources\ProductTransactionResource\RelationManagers;
use App\Models\ProductTransaction;
use Filament\Forms;
use App\Models\PromoCode;
use App\Models\Shoes;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductTransactionResource extends Resource
{
    protected static ?string $model = ProductTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $label = 'Transaction';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Wizard::make([
                Forms\Components\Wizard\Step::make('Product and Price')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Select::make('shoes_id')
                                ->label('Product')
                                ->relationship('shoes', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $shoes = Shoes::find($state);
                                    $price =$shoes ? $shoes->price : 0;
                                    $quantity = $get('quantity') ?? 1;
                                    $subTotalAmount = $price * $quantity;

                                    $set('price', $price);
                                    $set('sub_total_amount', $subTotalAmount);

                                    $discount = $get('discount_amount') ?? 0;
                                    $grandTotalAmount = $subTotalAmount - $discount;
                                    $set('grand_total_amount', $grandTotalAmount);

                                    $sizes = $shoes ? $shoes->sizes->pluck('size', 'id')->toArray() : [];
                                    $set('shoes_sizes', $sizes);
                                    })
                                ->afterStateHydrated(function (callable $get, callable $set, $state) {
                                    $shoesId = $state;
                                    if ($shoesId) {
                                    $shoes = Shoes::find($shoesId);
                                    $sizes = $shoes ? $shoes->sizes->pluck('size', 'id')->toArray() : [];
                                    $set('shoes_sizes', $sizes);
                                    }
                                }),
                            Forms\Components\Select::make('shoes_size')
                                ->label('Size')
                                ->options(function (callable $get) {
                                    $sizes = $get('shoes_sizes');
                                    return is_array($sizes) ? $sizes : [];
                                })
                                ->required()
                                ->reactive(),
                            Forms\Components\TextInput::make('quantity')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->prefix('qty')
                                ->live()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $price = $get('price');
                                    $quantity = $state;
                                    $subTotalAmount = $price * $quantity;
                                    $set('sub_total_amount', $subTotalAmount);
                                    $discount = $get('discount_amount') ?? 0;
                                    $grandTotalAmount = $subTotalAmount - $discount;
                                    $set('grand_total_amount', $grandTotalAmount);

                                }),
                                Forms\Components\Select::make('promo_code_id')
                                ->relationship('promoCode', 'code')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $subTotalAmount = $get('sub_total_amount');
                                    $promoCode = PromoCode::find($state);

                                    if (!$promoCode || !$promoCode->isValid()) {
                                        Notification::make()
                                            ->title('Promo Code Expired')
                                            ->danger()
                                            ->body('The promo code is invalid or expired.')
                                            ->send();

                                        $set('promo_code_id', null);
                                        $set('discount_amount', 0);
                                    } else {
                                        $discount = $promoCode->discount_amount;
                                        $grandTotalAmount = $subTotalAmount - $discount;

                                        $set('discount_amount', $discount);
                                        $set('grand_total_amount', max(0, $grandTotalAmount));
                                    }
                                }),

                            Forms\Components\TextInput::make('sub_total_amount')
                                ->required()
                                ->readOnly()
                                ->numeric()
                                ->prefix('IDR'),
                            Forms\Components\TextInput::make('grand_total_amount')
                                ->required()
                                ->readOnly()
                                ->numeric()
                                ->prefix('IDR'),
                            Forms\Components\TextInput::make('discount_amount')
                                ->required()
                                ->numeric()
                                ->prefix('IDR'),
                            Forms\Components\TextInput::make('barcode')
                                ->label('Barcode')
                                ->default(fn () => strtoupper(uniqid('RPL-')))
                                ->disabled()
                                ->dehydrated(),

                            ]),
                        ]),

                Forms\Components\Wizard\Step::make('Customer Information')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')->required()->maxLength(255),
                            Forms\Components\TextInput::make('phone')->required()->maxLength(255),
                            Forms\Components\TextInput::make('email')->required()->maxLength(255),
                            Forms\Components\TextArea::make('address')->rows(5)->required(),
                            Forms\Components\TextInput::make('city')->required()->maxLength(255),
                            Forms\Components\TextInput::make('post_code')->required()->maxLength(255),
                        ]),
                    ]),

                Forms\Components\Wizard\Step::make('Payment Information')
                        ->schema([
                            Forms\Components\TextInput::make('booking_trx_id')->required()->maxLength(255)
                            ->default(fn () => strtoupper(uniqid('SS-')))
                                ->disabled()
                                ->dehydrated(),
                                Forms\Components\Select::make('payment_method')
                                ->label('Payment Method')
                                ->options([
                                    'e_wallet' => 'E-Wallet',
                                    'credit_card' => 'Credit Card / Paylater',
                                    'cod' => 'Cash on Delivery (COD)',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($set) => $set('payment_provider', null)),

                            Forms\Components\Select::make('payment_provider')
                                ->label('Provider')
                                ->options(fn (callable $get) => match ($get('payment_method')) {
                                    'e_wallet' => [
                                        'bri' => 'BRI',
                                        'bca' => 'BCA',
                                        'permata' => 'Permata',
                                        'mandiri' => 'Mandiri',
                                        'bni' => 'BNI',
                                        'dana' => 'DANA',
                                        'ovo' => 'OVO',
                                        'gopay' => 'GoPay',
                                        'alfamart' => 'Alfamart',
                                        'indomaret' => 'Indomaret',
                                    ],
                                    'credit_card' => [
                                        'kredivo' => 'Kredivo',
                                        'ovo_paylater' => 'OVO Paylater',
                                        'akulaku' => 'Akulaku',
                                    ],
                                    default => [],
                                })
                                ->hidden(fn (callable $get) => $get('payment_method') === 'cod')
                                ->required(),
                                Forms\Components\Select::make('shipping_method')
    ->label('Shipping Method')
    ->options([
        'jne' => 'JNE',
        'jnt' => 'J&T',
        'gosend' => 'GoSend',
    ])
    ->required(),
                            ToggleButtons::make('is_verified')
                                ->label('Verification')
                                ->visible(fn() => auth()->user()->hasRole('admin')),
                        ]),
                ])->columnSpanFull('full')->skippable(),
            ]);

    }

    public static function updatePricing($shoesId, callable $get, callable $set)
    {
        $shoes = Shoes::find($shoesId);
        $price = $shoes ? $shoes->price : 0;
        $quantity = intval($get('quantity')) ?: 1;
        $subTotalAmount = $price * $quantity;

        $discount = $get('discount_amount') ?? 0;
        $grandTotalAmount = max(0, $subTotalAmount - $discount);

        $set('price', $price);
        $set('sub_total_amount', $subTotalAmount);
        $set('grand_total_amount', $grandTotalAmount);
    }

    public static function applyPromoCode($promoCodeId, callable $get, callable $set)
    {
        $subTotalAmount = $get('sub_total_amount') ?? 0;
        $promoCode = PromoCode::find($promoCodeId);
        $discount = $promoCode ? $promoCode->discount_amount : 0;
        $grandTotalAmount = max(0, $subTotalAmount - $discount);

        $set('discount_amount', $discount);
        $set('grand_total_amount', $grandTotalAmount);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('custom_id')->label('Custom ID')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('shoes.thumbnail')
                    ->label('product'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Total Barang'),
                Tables\Columns\TextColumn::make('grand_total_amount')
                    ->money('IDR', locale: 'id')
                    ->label('Total'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('booking_trx_id')->searchable()->label('Booking Id'),
                Tables\Columns\TextColumn::make('payment_method')
                ->label('Payment Status')
                ->formatStateUsing(fn ($record) =>
                    strtoupper($record->payment_provider) .
                    ($record->payment_provider ? ' - ' . strtoupper($record->payment_provider) : '')
                )
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('shipping_method')
                    ->label('Shipping Status'),

                Tables\Columns\BadgeColumn::make('is_verified')
                    ->label('Verification')
                    ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Pending')
                    ->colors([
                        'success' => fn ($state) => $state == 1,
                        'warning' => fn ($state) => $state == 0,
                    ]),

                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode'),
                    //->formatStateUsing(fn ($state) => "<img src='data:image/png;base64," . base64_encode(DNS1D::getBarcodePNG($state, 'C128')) . "' width='150'/>")

                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('shoes_id')->label('Shoes')->relationship('shoes', 'name'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn(ProductTransaction $record) => $record->update(['is_verified' => true]))
                    ->visible(fn(ProductTransaction $record) => auth()->user()->hasRole('super_admin') && !$record->is_verified)
            ])
            ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductTransactions::route('/'),
            'create' => Pages\CreateProductTransaction::route('/create'),
            'edit' => Pages\EditProductTransaction::route('/{record}/edit'),
        ];
    }
}
