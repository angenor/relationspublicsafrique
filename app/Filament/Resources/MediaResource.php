<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Helper\VideoEmbed;
use App\Models\Media;
use App\Models\MediaAuthor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Média';

    protected static ?string $label = 'Contenu média';

    protected static ?string $pluralLabel = 'Contenus média';

    protected static ?string $recordTitleAttribute = 'titre';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contenu')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('titre')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            if (($get('slug') ?? '') !== Str::slug((string) $old)) {
                                return;
                            }
                            $set('slug', Str::slug((string) $state));
                        }),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('type')
                        ->label('Type de contenu')
                        ->options(Media::$types)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            if (filled($get('media_kind'))) {
                                return;
                            }
                            $set('media_kind', match ($state) {
                                'podcast' => 'audio',
                                'video' => 'youtube',
                                default => null,
                            });
                        }),
                    Forms\Components\Textarea::make('chapo')
                        ->label('Chapô')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('content')
                        ->label('Corps / transcription')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Média (audio / vidéo)')
                ->description('Disponible pour tout type : une interview ou un reportage peut porter audio ou vidéo.')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('media_kind')
                        ->label('Nature du média')
                        ->options(Media::$kinds)
                        ->placeholder('Aucun (texte seul)')
                        ->live(),
                    Forms\Components\TextInput::make('embed_url')
                        ->label('URL YouTube / Vimeo')
                        ->url()
                        ->visible(fn (Get $get): bool => in_array($get('media_kind'), ['youtube', 'vimeo'], true))
                        ->required(fn (Get $get): bool => in_array($get('media_kind'), ['youtube', 'vimeo'], true))
                        ->rules([
                            static function () {
                                return static function (string $attribute, $value, \Closure $fail): void {
                                    if (! empty($value) && VideoEmbed::parse($value) === null) {
                                        $fail('Renseignez une URL YouTube ou Vimeo valide.');
                                    }
                                };
                            },
                        ]),
                    Forms\Components\FileUpload::make('audio_file')
                        ->label('Fichier audio (MP3)')
                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-m4a', 'audio/ogg'])
                        ->disk('public')
                        ->directory('media/audio')
                        ->visible(fn (Get $get): bool => $get('media_kind') === 'audio')
                        ->required(fn (Get $get): bool => $get('media_kind') === 'audio'),
                    Forms\Components\Toggle::make('audio_downloadable')
                        ->label('Autoriser le téléchargement de l\'audio')
                        ->visible(fn (Get $get): bool => $get('media_kind') === 'audio'),
                    Forms\Components\FileUpload::make('subtitles_path')
                        ->label('Sous-titres (.vtt)')
                        ->acceptedFileTypes(['text/vtt'])
                        ->disk('public')
                        ->directory('media/subtitles')
                        ->visible(fn (Get $get): bool => filled($get('media_kind'))),
                    Forms\Components\TextInput::make('duration')
                        ->label('Durée (secondes)')
                        ->numeric()
                        ->minValue(0)
                        ->visible(fn (Get $get): bool => filled($get('media_kind'))),
                ]),

            Forms\Components\Section::make('Couverture & SEO')
                ->columns(2)
                ->schema([
                    Forms\Components\FileUpload::make('cover_image')
                        ->label('Image de couverture')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('media/covers')
                        ->visibility('public'),
                    Forms\Components\FileUpload::make('og_image')
                        ->label('Image de partage (Open Graph)')
                        ->image()
                        ->disk('public')
                        ->directory('media/og')
                        ->visibility('public'),
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Meta title (SEO)')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta description (SEO)')
                        ->rows(2)
                        ->maxLength(500),
                ]),

            Forms\Components\Section::make('Classement')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('categories')
                        ->label('Thématiques')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Forms\Components\Select::make('tags')
                        ->label('Mots-clés')
                        ->relationship('tags', 'libelle')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('libelle')->required(),
                        ]),
                    Forms\Components\Select::make('pays_id')
                        ->label('Pays')
                        ->relationship('pays', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('user_id')
                        ->label('Auteur principal')
                        ->relationship('auteurPrincipal', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Repeater::make('contributions')
                        ->label('Co-auteurs / intervenants')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->label('Personne')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Forms\Components\Select::make('role')
                                ->label('Rôle')
                                ->options(MediaAuthor::$roles)
                                ->default('auteur')
                                ->required(),
                            Forms\Components\TextInput::make('position')
                                ->label('Ordre')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(3)
                        ->orderColumn('position')
                        ->collapsible()
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Série / playlist')
                ->columns(4)
                ->visible(fn (Get $get): bool => in_array($get('type'), ['podcast', 'video'], true))
                ->schema([
                    Forms\Components\Select::make('serie_id')
                        ->label('Série')
                        ->relationship('serie', 'titre')
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('saison')->label('Saison')->numeric(),
                    Forms\Components\TextInput::make('episode')->label('Épisode')->numeric(),
                    Forms\Components\TextInput::make('serie_position')->label('Ordre dans la série')->numeric(),
                ]),

            Forms\Components\Section::make('Publication & mise en avant')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(Media::$statuses)
                        ->default('draft')
                        ->required()
                        ->live(),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Date de publication')
                        ->helperText('Requis pour les statuts « programmé » et « publié ».')
                        ->required(fn (Get $get): bool => in_array($get('status'), ['scheduled', 'published'], true)),
                    Forms\Components\Toggle::make('featured')->label('À la une'),
                    Forms\Components\Toggle::make('is_pinned')->label('Épinglé (sélection éditoriale)')->live(),
                    Forms\Components\TextInput::make('pinned_position')
                        ->label('Position d\'épinglage')
                        ->numeric()
                        ->visible(fn (Get $get): bool => (bool) $get('is_pinned')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Couverture')
                    ->disk('public')
                    ->size(60),
                Tables\Columns\TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->limit(40)
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (?string $state): string => Media::$types[$state] ?? (string) $state)
                    ->colors([
                        'primary' => 'article',
                        'info' => 'interview',
                        'warning' => 'podcast',
                        'danger' => 'video',
                        'success' => 'reportage',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn (?string $state): string => Media::$statuses[$state] ?? (string) $state)
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'scheduled',
                        'success' => 'published',
                        'danger' => 'archived',
                    ]),
                Tables\Columns\IconColumn::make('featured')->label('À la une')->boolean(),
                Tables\Columns\IconColumn::make('is_pinned')->label('Épinglé')->boolean(),
                Tables\Columns\TextColumn::make('view')->label('Vues')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('published_at')->label('Publié le')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Type')->options(Media::$types),
                SelectFilter::make('status')->label('Statut')->options(Media::$statuses),
                SelectFilter::make('serie')->label('Série')->relationship('serie', 'titre'),
                SelectFilter::make('categories')->label('Thématique')->relationship('categories', 'name'),
                TernaryFilter::make('featured')->label('À la une'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publier')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn (Media $m) => $m->publish())),
                    Tables\Actions\BulkAction::make('archive')
                        ->label('Archiver')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn (Media $m) => $m->archive())),
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Mettre à la une')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn (Media $m) => $m->feature())),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            MediaResource\RelationManagers\CommentairesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
