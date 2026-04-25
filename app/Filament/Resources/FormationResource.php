<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormationResource\Pages;
use App\Filament\Resources\FormationResource\RelationManagers;
use App\Models\Formation;
use App\Models\Instructors;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;

class FormationResource extends Resource
{
    protected static ?string $model = Formation::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Formation';
    protected static ?string $label = 'Formations';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom de la formation')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Description courte')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Contenu détaillé')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Médias')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image de couverture')
                                    ->image()
                                    ->directory('formations/images')
                                    ->visibility('public'),
                                Forms\Components\FileUpload::make('video')
                                    ->label('Vidéo de présentation')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                                    ->directory('formations/videos')
                                    ->visibility('public'),
                            ]),
                    ]),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('duration')
                                    ->label('Durée (minutes)')
                                    ->numeric()
                                    ->suffix('min'),

                                Forms\Components\TextInput::make('price')
                                    ->label('Prix')
                                    ->numeric()
                                    ->prefix('FCFA')
                                    ->default(0),

                                Forms\Components\TextInput::make('max_students')
                                    ->label('Nombre max d\'étudiants')
                                    ->numeric()
                                    ->default(null),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('level_name')
                                    ->label('Niveau')
                                    ->options([
                                        'débutant' => 'Débutant',
                                        'intermédiaire' => 'Intermédiaire',
                                        'avancé' => 'Avancé',
                                        'expert' => 'Expert',
                                    ])
                                    ->default('débutant')
                                    ->required(),

                                Forms\Components\Select::make('language')
                                    ->label('Langue')
                                    ->options([
                                        'fr' => 'Français',
                                        'en' => 'Anglais',
                                        'es' => 'Espagnol',
                                    ])
                                    ->default('fr')
                                    ->required(),

                                Forms\Components\TextInput::make('certificate')
                                    ->label('Type de certificat')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('instructor_id')
                                    ->label('Formateur')
                                    ->relationship('instructor', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('category_id')
                                    ->label('Catégorie')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),

                Forms\Components\Section::make('Publication')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publié')
                                    ->default(false),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Mise en avant')
                                    ->default(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Ordre d\'affichage')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Date de publication'),

                                Forms\Components\DateTimePicker::make('start_date')
                                    ->label('Date de début'),
                            ]),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Date de fin'),
                    ]),

                Forms\Components\Section::make('Tags')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Mots-clés')
                            ->placeholder('Ajouter un tag')
                            ->separator(','),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->size(60)
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('instructor.name')
                    ->label('Formateur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('level_name')
                    ->label('Niveau')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'débutant' => 'success',
                        'intermédiaire' => 'warning',
                        'avancé' => 'danger',
                        'expert' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('XOF')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée')
                    ->formatStateUsing(fn($state) => $state ? $state . ' min' : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('enrollment_count')
                    ->label('Inscriptions')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 1) . '/5' : '-')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Mise en avant')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_published')
                    ->label('Statut de publication')
                    ->options([
                        1 => 'Publié',
                        0 => 'Non publié',
                    ]),

                Tables\Filters\SelectFilter::make('is_featured')
                    ->label('Mise en avant')
                    ->options([
                        1 => 'Oui',
                        0 => 'Non',
                    ]),

                Tables\Filters\SelectFilter::make('level_name')
                    ->label('Niveau')
                    ->options([
                        'débutant' => 'Débutant',
                        'intermédiaire' => 'Intermédiaire',
                        'avancé' => 'Avancé',
                        'expert' => 'Expert',
                    ]),

                Tables\Filters\SelectFilter::make('instructor_id')
                    ->label('Formateur')
                    ->relationship('instructor', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publier')
                        ->icon('heroicon-o-eye')
                        ->action(fn($records) => $records->each->update(['is_published' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Dépublier')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn($records) => $records->each->update(['is_published' => false]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListFormations::route('/'),
            'create' => Pages\CreateFormation::route('/create'),
            'edit' => Pages\EditFormation::route('/{record}/edit'),
        ];
    }
}
