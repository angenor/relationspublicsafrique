<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProjetResource\Pages;
use App\Models\Projet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProjetResource extends Resource
{
    protected static ?string $model = Projet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Projets';

    protected static ?string $label = 'Projet';

    protected static ?string $pluralLabel = 'Projets';

    protected static ?string $recordTitleAttribute = 'titre';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identité & card')
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
                    Forms\Components\Textarea::make('resume')
                        ->label('Résumé (card)')
                        ->helperText('Brève description affichée sur la card (max 280 caractères).')
                        ->required()
                        ->rows(2)
                        ->maxLength(280)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('visuel_card')
                        ->label('Visuel de card')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('projets/cards')
                        ->visibility('public'),
                    Forms\Components\Group::make([
                        Forms\Components\Toggle::make('featured')->label('Mettre en avant (à la une)'),
                        Forms\Components\TextInput::make('position')
                            ->label('Position (ordre d\'affichage)')
                            ->numeric()
                            ->default(0),
                    ]),
                ]),

            Forms\Components\Section::make('Contenu détaillé')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('contexte')
                        ->label('Contexte / problématique')
                        ->rows(4)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('objectifs')
                        ->label('Objectifs')
                        ->rows(4)
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')
                        ->label('Description détaillée')
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('activites')
                        ->label('Activités réalisées')
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('visuel_principal')
                        ->label('Visuel principal (détail)')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('projets/principal')
                        ->visibility('public')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Classification')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('categories')
                        ->label('Thématiques')
                        ->relationship('categories', 'name', fn (Builder $query) => $query->where('type', 'projet'))
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->columnSpanFull(),
                    Forms\Components\Select::make('portee')
                        ->label('Portée géographique')
                        ->options(Projet::$portees)
                        ->default('pays')
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('pays_id')
                        ->label('Pays')
                        ->relationship('pays', 'name')
                        ->searchable()
                        ->preload()
                        ->visible(fn (Get $get): bool => $get('portee') === 'pays')
                        ->required(fn (Get $get): bool => $get('portee') === 'pays'),
                    Forms\Components\TextInput::make('zone_libelle')
                        ->label('Libellé de la zone')
                        ->helperText('Ex. « Afrique de l\'Ouest » pour une portée régionale/continentale.')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('portee') !== 'pays')
                        ->required(fn (Get $get): bool => $get('portee') !== 'pays'),
                ]),

            Forms\Components\Section::make('Statut & publication')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('statut')
                        ->label('Statut du projet')
                        ->options(Projet::$statuts)
                        ->default('en_developpement')
                        ->required(),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Date de publication')
                        ->helperText('Une date future programme la mise en ligne ; vide = maintenant si publié.'),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Publié (visible sur le site)')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('SEO (optionnel)')
                ->columns(2)
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('meta_titre')
                        ->label('Meta title')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta description')
                        ->rows(2)
                        ->maxLength(500),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('pays'))
            ->columns([
                Tables\Columns\ImageColumn::make('visuel_card')
                    ->label('Visuel')
                    ->disk('public')
                    ->size(60),
                Tables\Columns\TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->limit(40)
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('statut')
                    ->label('Statut')
                    ->formatStateUsing(fn (?string $state): string => Projet::$statuts[$state] ?? (string) $state)
                    ->colors([
                        'success' => 'actif',
                        'primary' => 'realise',
                        'warning' => 'en_developpement',
                    ]),
                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),
                Tables\Columns\IconColumn::make('featured')->label('À la une')->boolean(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Thématiques')
                    ->badge()
                    ->limitList(2),
                Tables\Columns\TextColumn::make('zone_label')
                    ->label('Zone')
                    ->getStateUsing(fn (Projet $record): ?string => $record->zone_label)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('published_at')->label('Publié le')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('position')->label('Position')->sortable(),
            ])
            ->filters([
                SelectFilter::make('statut')->label('Statut')->options(Projet::$statuts),
                TernaryFilter::make('is_published')->label('Publié'),
                SelectFilter::make('portee')->label('Portée')->options(Projet::$portees),
                SelectFilter::make('categories')->label('Thématique')
                    ->relationship('categories', 'name', fn (Builder $query) => $query->where('type', 'projet')),
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
                        ->action(fn ($records) => $records->each(fn (Projet $p) => $p->publish())),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Dépublier')
                        ->icon('heroicon-o-eye-slash')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn (Projet $p) => $p->unpublish())),
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Mettre à la une')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn (Projet $p) => $p->feature())),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ProjetResource\RelationManagers\ResultatsRelationManager::class,
            ProjetResource\RelationManagers\GalerieMediasRelationManager::class,
            ProjetResource\RelationManagers\TemoignagesRelationManager::class,
            ProjetResource\RelationManagers\PartenairesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjets::route('/'),
            'create' => Pages\CreateProjet::route('/create'),
            'edit' => Pages\EditProjet::route('/{record}/edit'),
        ];
    }
}
