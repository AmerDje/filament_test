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
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
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
use Illuminate\Database\Eloquent\Model;
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() < 10 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        //this for getting value of next filed based on the previous
        /*  Forms\Components\Select::make('country_id')
        ->relationship(name: 'country', titleAttribute: 'name')
        ->searchable()
        ->preload()
        ->live()
        ->afterStateUpdated(function (Set $set) {
            $set('state_id', null);
            $set('city_id', null);
        })
        ->required(),
    Forms\Components\Select::make('state_id')
        ->options(fn (Get $get): Collection => State::query()
            ->where('country_id', $get('country_id'))
            ->pluck('name', 'id'))
        ->searchable()
        ->preload()
        ->live()
        ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
        ->required(),
    Forms\Components\Select::make('city_id')
        ->options(fn (Get $get): Collection => City::query()
            ->where('state_id', $get('state_id'))
            ->pluck('name', 'id'))
        ->searchable()
        ->preload()
        ->required(),
         Forms\Components\Select::make('relative_id')
                            ->label('Relative')
                            ->searchable()
                            ->required()
                            ->placeholder('Select a relative')
                            ->visible(function (Forms\Get $get): ?bool {
                                return $get('employee_id');
                            })
                            ->options(function (Forms\Get $get) {
                                $employeeId = $get('employee_id');
                                if ($employeeId) {
                                    return EmployeeRelative::where('employee_id', $employeeId)->pluck('name', 'id');
                                }
                                return [];
                            }),     
                        ]),
                        Forms\Components\Fieldset::make('Medical Service Information')
                    ->schema([
                    Forms\Components\Select::make('type_care')
                    ->required()
                    ->placeholder('Select a type care')
                    ->options([
                    'In-Patient' => 'In-Patient',
                    'Out-Patient' => 'Out-Patient',
                ])->live(), 
                Forms\Components\TextInput::make('approval_number')
                    ->required()
                    ->visible(function (Forms\Get $get): ?bool {
                              return $get('type_care') === 'In-Patient';
                    })
                    ->maxLength(255),
        */
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
                Tables\Actions\ViewAction::make()->label(''),
                // Tables\Actions\Action::make('assignRoom')
                //     //->label('Assign Room')
                //     ->icon('heroicon-s-home')
                //     ->form([
                //         Select::make('room_id')
                //             ->label('Room')
                //             ->options(Room::all()->pluck('number', 'id'))
                //             ->required(),
                //     ])
                //     ->action(function (Resident $resident, array $data): void {
                //         $roomId = $data['room_id'];

                //         // End the current assignment if exists
                //         $currentAssignment = RoomAssignment::where('resident_id', $resident->id)
                //             ->whereNull('end_date')
                //             ->first();

                //         if ($currentAssignment) {
                //             $currentAssignment->update(['end_date' => now()]);
                //         }

                //         // Create a new assignment
                //         RoomAssignment::create([
                //             'resident_id' => $resident->id,
                //             'room_id' => $roomId,
                //             'start_date' => now(),
                //         ]);

                //         Notification::make()
                //             ->title('Room assigned successfully!')
                //             ->success()
                //             ->send();
                //     }),

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
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Relationships')
                    ->schema([
                        TextEntry::make('category.name'),
                        // TextEntry::make(
                        //     'state.name'
                        // ),
                        // TextEntry::make(
                        //     'city.name'
                        // ),
                        // TextEntry::make('department.name'),
                    ])->columns(2),
                InfolistSection::make('Name')
                    ->schema([
                        TextEntry::make('title'),
                        // TextEntry::make(
                        //     'middle_name'
                        // ),
                        // TextEntry::make(
                        //     'last_name'
                        // ),
                        TextEntry::make('id'),
                        TextEntry::make('title'),
                        TextEntry::make('slug'),
                        TextEntry::make('category.name'),
                        ColorEntry::make('color'),
                        ImageEntry::make('thumbnail'),
                        TextEntry::make('tags'),
                        TextEntry::make('Is Published')->state(function (Model $record): string {
                            return $record->published ? 'Yes' : 'No';
                        }),
                        TextEntry::make('created_at'),
                    ])->columns(3),
                InfolistSection::make('Comments Info')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('comments_count')
                            ->state(function (Model $record): int {
                                return $record->comments()->count();
                            }),
                    ])->columns(2)
                //InfolistSection::make('Address')
                //     ->schema([
                //         TextEntry::make('address'),
                //         TextEntry::make(
                //             'zip_code'
                //         ),
                //     ])->columns(2)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'activities' => Pages\LogPostActivities::route('/{record}/activities'),
        ];
    }
}
