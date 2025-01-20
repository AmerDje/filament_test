<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
            //show many variables as you want
            ->schema([
                Section::make('Add Post')
                    ->description('create a post here')
                    ->schema([
                        Group::make()->schema([
                            TextInput::make('title')->minLength(2)->maxLength(20)->required()->validationMessages([
                                'required' => 'The :attribute should not be empty.',
                            ]),
                        ])->columns(1)->columnSpanFull(),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        ColorPicker::make('color')->required(),
                        MarkdownEditor::make('content')->required()->columnSpanFull(),

                    ])->columnSpan(2)->columns(2),

                //Meta
                Group::make()->schema([
                    Section::make('Image')->collapsible()->description('create the meta of a post')->schema([
                        FileUpload::make('thumbnail')->disk('public')->directory('thumbnails'),
                    ]),

                    Section::make('Meta')->description('create the meta of a post')->schema([
                        TagsInput::make('tags'),
                        Checkbox::make('published'),
                    ])
                ])->columnSpan(1)
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                //show many columns you want
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('tags'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            //here when the table is empty show this
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
