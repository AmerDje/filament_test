<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    //to change the shown label
    protected static ?string $modelLabel = 'All Posts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //here section acts as a card and also it can be given a title, description, and custom spaces
                Section::make('Add Post')
                    //->collapsible() //make it can collapse
                    //->aside()//takes the title and description to the side
                    ->description('create a post here')
                    ->schema([
                        //we can use Grid too check it out
                        //can group fields and give them design alone
                        //Groups cam have sections inside it too
                        Group::make()->schema([
                            //rules are for validation, we use values like validate in laravel
                            //|in:it,hi,he| is like telling that the title should be one of these words, same as ->in(['hello', 'hi'])
                            TextInput::make('title')->rules('min:2|max:20')->required()->validationMessages([
                                'required' => 'The :attribute should not be empty.',
                            ]),
                            //ignore record means ignore in case of update
                            //we can use custom validation too using rules
                            TextInput::make('slug')->required()->unique(ignoreRecord: true)->rules([
                                fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                    if ($get('title') === 'amer' && $value === 'amigo') {
                                        $fail("The " . str_split($attribute, 5)[1] . " is invalid.");
                                    }
                                },
                            ])
                        ])->columns(1)->columnSpanFull(),

                        Select::make('category_id')
                            ->label('Category')
                            //here ('here we define the model name, to make it selectable many make it categories', 'name of column we wanna show')
                            ->relationship('category', 'name')
                            ->searchable()
                            // ->options(
                            //     Category::all()->pluck('name', 'id')
                            // )
                            ->required(),

                        ColorPicker::make('color')->required(),
                        MarkdownEditor::make('content')->required()->columnSpanFull(), //this make it takes the full with, also we can make it act like a flex and take two times of normal space using columnSpan(N)

                        //Textarea::make('slug'),
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
                //see later what is disk
                // FileUpload::make('thumbnail')->disk('public')->directory('thumbnails'),
                // TagsInput::make('tags'),
                // Checkbox::make('published'),
            ])->columns(3); //here we are applying the column for all from so its arranges the form 3 on every row
        //responsiveness is already defined in filament
        //->columns(['default' => 1, 'md' => 2, 'lg' => 3, 'xl' => 3]); //here we are applying responsiveness md:medium, lg:large, xl:extraLarge
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('ID'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->searchable()->sortable(),
                ColorColumn::make('color')
                    ->toggleable(),
                ImageColumn::make('thumbnail')
                    ->toggleable(),
                TextColumn::make('tags')
                    ->searchable()
                    ->sortable(),
                CheckboxColumn::make('published'),
                TextColumn::make('created_at')
                    ->label('Published On')
                    ->date()
                    ->sortable()
                    ->toggleable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
