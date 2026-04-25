<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Blog;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Publication';
    protected static ?string $label ="Blog";


    public static function form(Form $form): Form
    {

        return $form


            ->schema([
                Forms\Components\Card::make()->schema([

                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->minLength(2)
                            ->maxLength(255)
                            ->required()
                            ->live(5000)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
//                                if (($get('slug') ?? '') !== Str::slug($old)) {
//                                    return;
//                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required(),

                    ])->columns(2),

                    Forms\Components\MarkdownEditor::make('resume'),
                    Forms\Components\RichEditor::make('content') ,
                    Forms\Components\Textarea::make('video'),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('position')->numeric(),
                        Forms\Components\Select::make('type',)
                            ->label('Type de post')
                            ->required()
                            ->searchable()
                            ->placeholder('Sélectionner un type de post')
                            ->options(Post::$typeblogs),
                    ]),
                ])->columnSpan(8),


                Forms\Components\Card::make()->schema([
                    Forms\Components\FileUpload::make('image')->imageEditor(),
                    Forms\Components\Toggle::make('online'),
                    Forms\Components\TextInput::make('speakers'),

                    Forms\Components\Select::make('category_id')
                        ->multiple()
                        ->searchable()
                        ->required()
                        ->preload()
                        ->relationship(name:'categories', titleAttribute:'name'),

                    Forms\Components\Select::make('user_id')
                        ->required()
                        ->exists('users', 'id')
                        ->preload()
                        ->relationship('user', 'email' ),

                    Forms\Components\DateTimePicker::make('created_at')

//
//                         request()->route()->getName() !='filament.admin.resources.posts.edit'?     Forms\Components\DateTimePicker::make('created_at')
//                             ->label('Date de publication') :Forms\Components\Card::make()


                ])->columnSpan(4),

            ])->columns(12);


    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->striped()
            ->emptyStateHeading("Pas d'articles enregistrés")
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('image')->size(fn()=>88),


                Tables\Columns\TextColumn::make('name')
                    ->limit(20)
                    ->label('Titre article')
                    ->searchable(),

//                Tables\Columns\TextColumn::make('slug'),
//                Tables\Columns\TextColumn::make('content'),
//                Tables\Columns\TextColumn::make('resume'),
//                Tables\Columns\TextColumn::make('categories.0.name')->limit(10),
                Tables\Columns\TextColumn::make('type')->sortable()  ->searchable(),
                Tables\Columns\TextColumn::make('view')->sortable(),
                Tables\Columns\TextColumn::make('categories.name')->limit(10),
//                Tables\Columns\TextColumn::make('post_id'),

                Tables\Columns\CheckboxColumn::make('online')->sortable() ,
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i'),

            ])
            ->filters([
                Filter::make('En ligne')
                    ->query(fn (Builder $query) => $query->where('online', true)),
                SelectFilter::make('type')
                    ->options(Post::$typeblogs)
                    ->label("Type d'article"),
            ])
            ->actions([

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('online')
                        ->action(fn (Post $record) => $record->activate())
                        ->requiresConfirmation()
                        ->color('success'),
                ]),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('activate')
                    ->action(fn (Post $records) => $records->each->activate())
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
            ]);


    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('type', array_keys(Post::$typeblogs));
    }

}
