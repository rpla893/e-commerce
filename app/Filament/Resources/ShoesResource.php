<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Shoes\Pages;
use App\Models\Shoes;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Brand;
use App\Models\ShoesPhoto;
use App\Models\Category;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\Action;

class ShoesResource extends Resource
{
    protected static ?string $model = Shoes::class; // Perbaikan dari Shoe::class ke Shoes::class

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $label = 'Product';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('IDR'),
                        Forms\Components\FileUpload::make('thumbnail')
                            ->required()
                            ->image()
                            ->imageEditor()
                            ->directory('shoes/thumbnails'),
                        Forms\Components\Repeater::make('photos')
                            ->relationship('photos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->required()
                                    ->image()
                                    ->imageEditor()
                                    ->directory('shoes/photos'),
                            ]),
                        Forms\Components\Repeater::make('sizes')
                            ->relationship('sizes')
                            ->schema([
                                Forms\Components\TextInput::make('size')
                                    ->required()
                                    ->numeric()
                                    ->minValue(30)
                                    ->maxValue(50)
                                    ->suffix('EU')
                                    ->label('Size (EU)'),
                            ]),
                    ]),
                Fieldset::make('Additional')
                    ->schema([
                        Forms\Components\TextArea::make('about')
                            ->required(),
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle')
                            ->default(false),
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->required()
                            ->searchable()
                            ->relationship('category', 'name')
                            ->preload(),
                        Forms\Components\Select::make('brand_id')
                            ->label('Brand')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->prefix('Qty'),
                        Forms\Components\TextInput::make('diskon')
                            ->label('Discount (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),


                            Forms\Components\Select::make('status')
    ->label('Status')
    ->options([
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
    ])
    ->default('active')
    ->required()

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('custom_id')->label('Custom ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand'),
                Tables\Columns\TextColumn::make('stock'),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->label('Popular'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])

                 ])

            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name'),

                    SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->label('Filter Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('toggleStatus')
                ->label(fn (Shoes $record) => $record->status === 'active' ? 'Nonaktifkan' : 'Aktifkan')
                ->icon(fn (Shoes $record) => $record->status === 'active' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn (Shoes $record) => $record->status === 'active' ? 'danger' : 'success')
                ->action(function (Shoes $record) {
                    $newStatus = $record->status === 'active' ? 'inactive' : 'active';
                    $record->update(['status' => $newStatus]);

                    // Notifikasi saat status berubah
                    \Filament\Notifications\Notification::make()
                        ->title('Status Produk Diperbarui')
                        ->body("Produk **{$record->name}** sekarang **" . ucfirst($newStatus) . "**.")
                        ->success()
                        ->send();
                })
                    ->requiresConfirmation()
                    ->visible(fn(Shoes $record) => auth()->user()->hasRole('super_admin') && !$record->is_verified)
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
            // Define any additional relations here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShoes::route('/'),
            'create' => Pages\CreateShoes::route('/create'),
            'edit' => Pages\EditShoes::route('/{record}/edit'),
        ];
    }
}
