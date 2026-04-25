<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
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

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Formation';
    protected static ?string $label ="Cours";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


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
                ]) ,

                Forms\Components\RichEditor::make('description')->columnSpan(2) ,
                Forms\Components\Grid::make(2)->schema([

                    Forms\Components\TextInput::make('duration') ,
                    Forms\Components\TextInput::make('price') ,


                ]),
                Forms\Components\Grid::make(2)->schema([

                    Forms\Components\FileUpload::make('image') ,
                    Forms\Components\FileUpload::make('video') ,





                ]),
                Forms\Components\Group::make()->schema([

                    Forms\Components\Select::make('user_id')->required()
                        ->exists('users', 'id')
                        ->preload()
                        ->relationship('user', 'name' ),


                ]),




                /*Forms\Components\Grid::make()->schema([
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

                ]) ,

               Forms\Components\Grid::make()->schema([
                   Forms\Components\RichEditor::make('content') ,
                   Forms\Components\FileUpload::make('image') ,
                   Forms\Components\FileUpload::make('video') ,
               ]) ,*/

            ]);
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
//                Tables\Columns\TextColumn::make('categories.name')->limit(10),
//                Tables\Columns\TextColumn::make('post_id'),

//                Tables\Columns\CheckboxColumn::make('online')->sortable() ,
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i'),

            ])
            ->filters([
                Filter::make('En ligne')
                    ->query(fn (Builder $query) => $query->where('online', true)),
                SelectFilter::make('type')
                    ->options(Post::$types)
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

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
