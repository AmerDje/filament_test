<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // this is the tap icon
    protected static ?string $navigationIcon = 'heroicon-o-users'; //Hero Icon / Outline / Users icon


    protected static ?string $navigationGroup = "Users";
    //to sort navigation taps
    protected static ?int $navigationSort = 3;

    // this for defining what should be shown in the create page user forms
    public static function form(Form $form): Form
    {
        //here we are creating the fields of create user form
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->required()->email(),

                Select::make('role')
                    ->options(User::ROLES)
                    ->required(),
                //in read only we are saying make it read only in this specific page
                TextInput::make('password')->required()->password()->minLength(8)->readOnlyOn('edit')->visibleOn(['create']),/*visible(false) */
                // This can be a normal list as follows ['test', 'admin'] or the next format, in both the stored value are the key which is 'name'
                // Select::make('name')->options([
                //     'test' => 'test as name',
                //     'admin' => 'admin as name'
                // ])
                // KeyValue::make('name')

                //Textarea::make('name')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            //the columns section is for defining what columns should be shown in the table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')
                    ->badge()
                    ->color(function (string $state): string {
                        // if ($state == 'ADMIN') return 'danger';
                        // elseif ($state == 'EDITOR') return 'info';
                        // else return 'gray';
                        return match ($state) {
                            'ADMIN' => 'danger',
                            'EDITOR' => 'info',
                            'USER' => 'success',
                        };
                    })
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->headerActions([
                // should import from tables/export
                ExportAction::make()
                    ->exporter(UserExporter::class)
                    //to select which format to export
                    ->formats([ExportFormat::Csv]),
                ImportAction::make()->importer(UserImporter::class),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportAction::make()->exporter(UserExporter::class),

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
