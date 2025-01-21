<?php

namespace App\Filament\Resources\PostUserResource\Pages;

use App\Filament\Resources\PostUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPostUsers extends ListRecords
{
    protected static string $resource = PostUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //remove create button
            // Actions\CreateAction::make(),
        ];
    }
}
