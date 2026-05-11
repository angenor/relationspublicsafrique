<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProfilResource\Pages;
use App\Filament\Resources\ProfilResource\RelationManagers;
use App\Models\DomaineExpertise;
use App\Models\Pays;
use App\Models\Profil;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProfilResource extends Resource
{
    protected static ?string $model = Profil::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Annuaire';

    protected static ?string $label = 'Profil';

    protected static ?string $pluralLabel = 'Profils';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $prenom = $get('prenom') ?? '';
                                $set('slug', Str::slug(trim($prenom.' '.($state ?? ''))));
                            }),
                        Forms\Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $nom = $get('nom') ?? '';
                                $set('slug', Str::slug(trim(($state ?? '').' '.$nom)));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('fonction')->label('Fonction'),
                        Forms\Components\TextInput::make('organisation')->label('Organisation'),
                        Forms\Components\Select::make('type_profil')
                            ->label('Type de profil')
                            ->required()
                            ->options([
                                'expert' => 'Expert',
                                'etudiant' => 'Étudiant',
                                'alumni' => 'Alumni',
                                'partenaire' => 'Partenaire',
                                'autre' => 'Autre',
                            ])
                            ->default('autre'),
                    ]),

                Forms\Components\Section::make('Localisation')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('pays_id')
                            ->label('Pays')
                            ->options(fn () => Pays::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\TextInput::make('nationalite')->label('Nationalité'),
                        Forms\Components\TextInput::make('ville')->label('Ville'),
                    ]),

                Forms\Components\Section::make('Coordonnées et visibilité')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('email')->label('Email')->email(),
                        Forms\Components\Toggle::make('masquer_email')
                            ->label('Masquer l\'email publiquement')
                            ->default(false),
                        Forms\Components\TextInput::make('tel')->label('Téléphone'),
                        Forms\Components\Toggle::make('masquer_tel')
                            ->label('Masquer le téléphone publiquement')
                            ->default(false),
                    ]),

                Forms\Components\Section::make('Présentation')
                    ->schema([
                        Forms\Components\TextInput::make('bio_courte')->label('Bio courte')->maxLength(300),
                        Forms\Components\RichEditor::make('bio_longue')->label('Bio longue'),
                    ]),

                Forms\Components\Section::make('Expertise et tags')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('domainesExpertise')
                            ->label('Domaines d\'expertise')
                            ->multiple()
                            ->relationship('domainesExpertise', 'libelle')
                            ->options(fn () => DomaineExpertise::query()->orderBy('libelle')->pluck('libelle', 'id'))
                            ->preload(),
                        Forms\Components\Select::make('tags')
                            ->label('Tags')
                            ->multiple()
                            ->relationship('tags', 'libelle')
                            ->options(fn () => Tag::query()->orderBy('libelle')->pluck('libelle', 'id'))
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('libelle')->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Médias')
                    ->columns(2)
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Photo (JPEG, PNG ou WebP, min. 400×400, max. 2 Mo)')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('profils')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('cv')
                            ->label('CV (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('profils/cv'),
                    ]),

                Forms\Components\Section::make('Publication & RGPD')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('etat_publication')
                            ->label('État de publication')
                            ->options([
                                'en_attente' => 'En attente',
                                'publie' => 'Publié',
                                'archive' => 'Archivé',
                            ])
                            ->default('en_attente')
                            ->dehydrated(true),
                        Forms\Components\Checkbox::make('consentement_atteste')
                            ->label('Je certifie avoir recueilli le consentement RGPD écrit ou oral de la personne référencée')
                            ->helperText('Obligatoire avant toute publication (FR-026). Une notification sera envoyée à la personne lors de la publication.')
                            ->dehydrated(false),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Photo')->circular(),
                Tables\Columns\TextColumn::make('fullname')->label('Nom complet')->searchable(['nom', 'prenom']),
                Tables\Columns\TextColumn::make('organisation')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('pays.name')->label('Pays')->toggleable(),
                Tables\Columns\TextColumn::make('type_profil')->label('Type')->badge(),
                Tables\Columns\TextColumn::make('etat_publication')
                    ->label('État')
                    ->badge()
                    ->colors([
                        'warning' => 'en_attente',
                        'success' => 'publie',
                        'danger' => 'archive',
                    ]),
                Tables\Columns\IconColumn::make('legacy_sans_consentement')
                    ->label('Sans consentement')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Modifié')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('etat_publication')
                    ->label('État')
                    ->options([
                        'en_attente' => 'En attente',
                        'publie' => 'Publié',
                        'archive' => 'Archivé',
                    ]),
                Tables\Filters\SelectFilter::make('type_profil')
                    ->label('Type')
                    ->options([
                        'expert' => 'Expert',
                        'etudiant' => 'Étudiant',
                        'alumni' => 'Alumni',
                        'partenaire' => 'Partenaire',
                        'autre' => 'Autre',
                    ]),
                Tables\Filters\SelectFilter::make('pays_id')
                    ->label('Pays')
                    ->options(fn () => Pays::query()->orderBy('name')->pluck('name', 'id')),
                Tables\Filters\Filter::make('legacy_sans_consentement')
                    ->label('Sans consentement (à régulariser)')
                    ->toggle()
                    ->query(fn (Builder $q) => $q->where('legacy_sans_consentement', true))
                    ->indicator('Profils legacy à régulariser RGPD'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([\Illuminate\Database\Eloquent\SoftDeletingScope::class]);

        $user = auth()->user();
        if ($user && ($user->type ?? null) !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LiensExternesRelationManager::class,
            RelationManagers\DemandesModerationRelationManager::class,
            RelationManagers\HistoriqueRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfils::route('/'),
            'create' => Pages\CreateProfil::route('/create'),
            'view' => Pages\ViewProfil::route('/{record}'),
            'edit' => Pages\EditProfil::route('/{record}/edit'),
        ];
    }
}
