<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\CategoryResource\RelationManagers\PostsRelationManager;
use App\Models\Category;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    //a line is added if you don't want the section to be controlled by the polices
    //?protected static bool $shouldSkipAuthorization = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()
                    ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set, Forms\Get $get, Category $category) {
                        // this shows a alert screen as terminal dump("hi");
                        // dump($operation) // return which operation we are using (create or edit)
                        // dump($state) // return the value written in the field
                        // $set // gives us the ability to change other fields value
                        // $get // gets the other values field $get('slug');
                        // $category // gets all the values of the category model and it returns
                        //   Log::debug('State value:', ['state' => $state]);

                        if ($operation == 'edit') {
                            return;
                        } elseif (is_string($state) && !empty($state)) {
                            $set('slug', Str::slug($state)); //str($state)->slug()); //converts string to slug
                        } else {
                            return; // $set('slug', '');
                        }
                    })->live(onBlur: true) // gets called when typing has onBlur  when true it waits until focus is in other place also ->reactive() do same
                ,
                Gate::allows('editPanel', User::class)
                    ? TextInput::make('slug')
                    ->label('Admin Slug')
                    ->required()
                    : TextInput::make('slug')
                    ->label('Editor Slug')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug')
            ])
            ->filters([
                //
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
            PostsRelationManager::class
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
