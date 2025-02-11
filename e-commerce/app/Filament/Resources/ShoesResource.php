<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShoesResource\Pages;
use App\Filament\Resources\ShoesResource\RelationManagers;
use App\Models\Shoes;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShoesResource extends Resource
{
    protected static ?string $model = Shoes::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $label = 'Product';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Details')

            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('brand')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                Forms\Components\FileUpload::make('thumbnail')
                    ->required()
                    ->image(),
                Forms\Components\Repeater::make('photos')
                    ->relationship('photos')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                        ->required(),
                    ]),
                Forms\Components\Repeater::make('sizes')
                ->relationship('sizes')
                ->schema([
                    Forms\Components\TextInput::make('size')
                    ->required(),
                ]),

            ]),

                Fieldset::make('Additional')

                ->schema([
                    Forms\Components\TextArea::make('about')
                        ->required(),
                    Forms\Components\Select::make('is_popular')
                        ->options([
                            true => 'Popular',
                            false => 'Not Popular',
                        ])
                        ->required(),
                    Forms\Components\Select::make('category_id')
                        ->required()
                        ->searchable()
                        ->relationship('category', 'name')
                        ->preload(),
                    Forms\Components\Select::make('brand_id')
                        ->relationship('brand', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('stock')
                        ->required()
                        ->numeric()
                        ->prefix('Qty'),
                 ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\ImageColumn::make('thumbnail')
                ->circular(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->label('Popular'),

            ])
            ->filters([
                SelectFilter::make('category_id')
                ->label('category')
                    ->relationship('category', 'name'),

                SelectFilter::make('brand_id')
                ->label('brand')
                    ->relationship('brand', 'name'),
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
            'index' => Pages\ListShoes::route('/'),
            'create' => Pages\CreateShoes::route('/create'),
            'edit' => Pages\EditShoes::route('/{record}/edit'),
        ];
    }
}
