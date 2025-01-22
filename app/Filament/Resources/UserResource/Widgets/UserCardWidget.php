<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class UserCardWidget extends BaseWidget
{

    //access name of user we accessed, works only in edit
    public ?User $record;

    protected function getStats(): array
    {
        return [
            Stat::make('Number of Posts', $this->record->posts()->count()),
        ];
    }
}
