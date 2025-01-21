<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostUserResource\Pages;
use App\Filament\Resources\PostUserResource\RelationManagers;
use App\Models\Post_User;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostUserResource extends Resource
{
    protected static ?string $model = Post_User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Post User';

    protected static ?string $navigationGroup = "Blog";

    //to sort taps in navigation
    protected static ?int $navigationSort = 2;

    // public static function getNavigationLabel(): string
    // {
    //     return 'Post User';
    // }

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('post.title'),
                TextColumn::make('user.name'),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->dateTime(),

            ])
            ->filters([
                //
            ]);
        // ->actions([
        //     Tables\Actions\EditAction::make(),
        // ])
        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
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
            'index' => Pages\ListPostUsers::route('/'),
            // 'create' => Pages\CreatePostUser::route('/create'),
            // 'edit' => Pages\EditPostUser::route('/{record}/edit'),
        ];
    }
}
