<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Post created.')
            ->body('The Post created successfully.');
    }
}
