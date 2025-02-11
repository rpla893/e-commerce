<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))), // Slug otomatis

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(Category::class, 'slug', ignoreRecord: true),

                Forms\Components\FileUpload::make('icon')
                    ->label('Icon Image')
                    ->image()
                    ->directory('categories') // Simpan di folder "categories/"
                    ->required(),

                Forms\Components\Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'wanita' => 'Wanita',
                        'pria' => 'Pria',
                        'anak-anak' => 'Anak-anak',
                        'pria-wanita' => 'Pria & Wanita',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('subcategory')
                    ->label('Subcategory')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('brand')
                    ->label('Brand (Optional)')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('custom_id')->label('Custom ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('icon')
                    ->label('Icon Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subcategory')
                    ->label('Subcategory')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Brand')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(), // Waktu dalam format relatif (misal: "2 jam yang lalu")
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'wanita' => 'Wanita',
                        'pria' => 'Pria',
                        'anak-anak' => 'Anak-anak',
                        'pria-wanita' => 'Pria & Wanita',
                    ]),
                Tables\Filters\TrashedFilter::make(), // Filter data yang dihapus (soft delete)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation() // Konfirmasi sebelum menghapus
                    ->successNotificationTitle('Category berhasil dihapus!'),
                Tables\Actions\RestoreAction::make(), // Restore data yang dihapus
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(), // Restore massal
                ]),
            ])
            ->defaultSort('created_at', 'desc'); // Data terbaru muncul pertama
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class, // Menampilkan semua data termasuk yang sudah dihapus
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
