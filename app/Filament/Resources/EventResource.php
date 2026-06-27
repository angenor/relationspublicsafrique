<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Category;
use App\Models\Event;
use App\Models\Pays;
use App\Models\User;
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
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Actualités';

    protected static ?string $label = 'Événements';

    protected static ?string $pluralLabel = 'Événements';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité & card')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre de l\'événement')
                            ->minLength(2)
                            ->maxLength(255)
                            ->required()
                            ->live(500)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug((string) $old)) {
                                    return;
                                }
                                $set('slug', Str::slug((string) $state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required(),
                        Forms\Components\Textarea::make('resume')
                            ->label('Résumé (card)')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Visuel de l\'événement')
                            ->image()
                            ->imageEditor()
                            ->directory('events')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Contenu détaillé')
                    ->columns(2)
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Présentation complète')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('objectifs')
                            ->label('Objectifs')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('programme')
                            ->label('Programme / agenda')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('public_cible')
                            ->label('Public cible')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Dates')
                    ->columns(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Date de début')
                            ->required()
                            ->native(),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Date de fin')
                            ->required()
                            ->native()
                            ->after('start_date'),
                        Forms\Components\DateTimePicker::make('registration_deadline')
                            ->label('Date limite d\'inscription')
                            ->native()
                            ->before('start_date'),
                        Forms\Components\Placeholder::make('temporal_status_display')
                            ->label('Statut temporel (calculé)')
                            ->content(fn (?Event $record): string => $record?->temporal_status_label ?? '— (selon les dates)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Format & lieu')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('online')
                            ->label('En ligne (format)')
                            ->helperText('Active si l\'événement se déroule en ligne ; sinon, indiquez le lieu.')
                            ->default(false)
                            ->live(),
                        Forms\Components\TextInput::make('location')
                            ->label('Lieu / Ville')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => ! $get('online')),
                    ]),

                Forms\Components\Section::make('Inscription')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('registration_mode')
                            ->label('Mode d\'inscription')
                            ->options(Event::$registrationModes)
                            ->default('internal')
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('registration_url')
                            ->label('Lien d\'inscription externe')
                            ->url()
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => $get('registration_mode') === 'external')
                            ->required(fn (Get $get): bool => $get('registration_mode') === 'external'),
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Nombre maximum de participants')
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('current_participants')
                            ->label('Participants actuels')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('price')
                            ->label('Prix')
                            ->numeric()
                            ->prefix('€')
                            ->default(0),
                    ]),

                Forms\Components\Section::make('Publication & classement')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut éditorial')
                            ->options(Event::$statuses)
                            ->required()
                            ->default('draft'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Mis en avant'),
                        Forms\Components\Select::make('category_id')
                            ->label('Type d\'événement')
                            ->options(Category::where('type', 'event')->where('online', 1)->orderBy('position')->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('pays_id')
                            ->label('Pays')
                            ->options(Pays::where('online', 1)->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('user_id')
                            ->label('Organisateur')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Compte rendu (post-événement)')
                    ->collapsed()
                    ->schema([
                        Forms\Components\RichEditor::make('compte_rendu')
                            ->label('Compte rendu')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->striped()
            ->emptyStateHeading('Aucun événement enregistré')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Visuel')
                    ->size(60)
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('temporal_status_label')
                    ->label('Statut temporel')
                    ->badge()
                    ->getStateUsing(fn (Event $record): string => $record->temporal_status_label)
                    ->color(fn (Event $record): string => match ($record->temporal_status) {
                        'ongoing' => 'success',
                        'upcoming' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lieu')
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\TextColumn::make('pays.name')
                    ->label('Pays')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Éditorial')
                    ->formatStateUsing(fn (?string $state): string => Event::$statuses[$state] ?? (string) $state)
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'draft',
                        'success' => 'published',
                        'gray' => 'completed',
                    ]),

                Tables\Columns\TextColumn::make('registration_mode')
                    ->label('Inscription')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Event::$registrationModes[$state] ?? (string) $state)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('online')
                    ->label('En ligne')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('À la une')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_participants')
                    ->label('Participants')
                    ->suffix(fn (Event $record): string => $record->max_participants ? " / {$record->max_participants}" : '')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Event::$statuses)
                    ->label('Statut éditorial'),

                SelectFilter::make('category_id')
                    ->label('Type')
                    ->options(Category::where('type', 'event')->orderBy('position')->pluck('name', 'id')),

                SelectFilter::make('pays_id')
                    ->label('Pays')
                    ->options(Pays::where('online', 1)->pluck('name', 'id'))
                    ->searchable(),

                Filter::make('En ligne')
                    ->query(fn (Builder $query) => $query->where('online', true)),

                Filter::make('Mis en avant')
                    ->query(fn (Builder $query) => $query->where('is_featured', true)),

                Filter::make('À venir')
                    ->query(fn (Builder $query) => $query->where('start_date', '>', now())),

                Filter::make('En cours')
                    ->query(fn (Builder $query) => $query->where('start_date', '<=', now())->where('end_date', '>=', now())),

                Filter::make('Clos')
                    ->query(fn (Builder $query) => $query->where('end_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('publish')
                        ->label('Publier')
                        ->action(fn (Event $record) => $record->publish())
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->visible(fn (Event $record) => $record->status !== 'published'),
                    Tables\Actions\Action::make('unpublish')
                        ->label('Dépublier')
                        ->action(fn (Event $record) => $record->unpublish())
                        ->requiresConfirmation()
                        ->color('gray')
                        ->icon('heroicon-o-eye-slash')
                        ->visible(fn (Event $record) => $record->status === 'published'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publier la sélection')
                        ->action(fn ($records) => $records->each->publish())
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check-circle'),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Dépublier la sélection')
                        ->action(fn ($records) => $records->each->unpublish())
                        ->requiresConfirmation()
                        ->color('gray')
                        ->icon('heroicon-o-eye-slash'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            EventResource\RelationManagers\RegistrationsRelationManager::class,
            EventResource\RelationManagers\SpeakersRelationManager::class,
            EventResource\RelationManagers\MediasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
