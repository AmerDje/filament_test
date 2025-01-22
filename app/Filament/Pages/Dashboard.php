<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form->schema(
            [
                Section::make('Filters')
                    ->schema([
                        TextInput::make('name'),
                        DateTimePicker::make('startDate'),
                        DateTimePicker::make('endDate'),
                        // Toggle::make('published')
                        //     ->label('Published')
                        //     ->default(true)
                        //     ->helperText('Is this post published?')
                        //     ->inline(),
                    ])->columns(3),
            ]
        );
    }
}
