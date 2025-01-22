<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Models\Category;
use App\Models\Post;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    //to change the shown label
    protected static ?string $modelLabel = 'Posts';

    //name of group should be the same in other models 
    protected static ?string $navigationGroup = "Blog";

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //we can use taps for long forms
                // Tabs::make('Create New Post')
                //     ->tabs([
                //         Tab::make('Tab 1s')
                //             ->icon('heroicon-o-rectangle-stack')
                //             ->iconPosition(IconPosition::After)
                //             ->badge('Hi')
                //             ->schema([]),
                //         Tab::make('Tab 1')
                //             ->icon('heroicon-o-rectangle-stack')
                //             ->iconPosition(IconPosition::After)
                //             ->badge('Hi')
                //             ->schema([])
                //     ])->activeTab(2)->persistTabInQueryString(),
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
                            //to preload options before searching
                            ->preload()
                            // ->options(
                            //pluck makes it like that id=>name
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
                    ]),

                    //  Section::make('Authors')->schema([
                    //?we handled the cemented below using relationship manager
                    //authors is relationship name in the model
                    // Select::make('Authors')
                    //     ->label('Co Authors')
                    //     ->multiple()
                    //     ->relationship('authors', 'name')
                    // CheckboxList::make('Authors')
                    //     ->label('Co Authors')
                    //     ->searchable()
                    //     ->relationship('authors', 'name')
                    //  ]),
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
                // Filter::make('Published')->query(
                //     function (Builder $query): Builder {
                //         return $query->where('published', true);
                //     }
                // ),
                // Filter::make('Un Published')->query(
                //     function (Builder $query): Builder {
                //         return $query->where('published', false);
                //     }
                // ),
                TernaryFilter::make('published'),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                //  ->multiple()
                //->options(
                // Category::all()
                //     ->pluck('name', 'id')
                //)

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
            //to view the others on the page add this
            //whenever add relation manager in this resource you should add it here
            AuthorsRelationManager::class,
            CommentsRelationManager::class
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
