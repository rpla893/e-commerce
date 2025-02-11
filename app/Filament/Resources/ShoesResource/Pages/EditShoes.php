<?php

namespace App\Filament\Resources\Shoes\Pages;

use App\Filament\Resources\ShoesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShoes extends EditRecord
{
    protected static string $resource = ShoesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
