<?php

namespace App\Filament\Resources\Shoes\Pages;

use App\Filament\Resources\ShoesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShoes extends ListRecords
{
    protected static string $resource = ShoesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
