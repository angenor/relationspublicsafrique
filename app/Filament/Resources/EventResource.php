<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use App\Models\Category;
use App\Models\User;
use App\Models\Pays;
use Closure;
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
                Forms\Components\Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre de l\'événement')
                            ->minLength(2)
                            ->maxLength(255)
                            ->required()
                            ->live(500)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required(),
                    ])->columns(2),

                    Forms\Components\Textarea::make('resume')
                        ->label('Résumé')
                        ->rows(3)
                        ->maxLength(500),

                    Forms\Components\RichEditor::make('description')
                        ->label('Description complète')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(3)->schema([
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
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('location')
                            ->label('Lieu')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('price')
                            ->label('Prix')
                            ->numeric()
                            ->prefix('€')
                            ->default(0),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Nombre maximum de participants')
                            ->numeric()
                            ->minValue(1),

                        Forms\Components\TextInput::make('current_participants')
                            ->label('Participants actuels')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ]),
                ])->columnSpan(8),

                Forms\Components\Card::make()->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Image de l\'événement')
                        ->image()
                        ->imageEditor()
                        ->directory('events'),

                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(Event::$statuses)
                        ->required()
                        ->default('draft'),

                    Forms\Components\Toggle::make('online')
                        ->label('En ligne')
                        ->default(false),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('Mis en avant')
                        ->default(false),

                    Forms\Components\Select::make('category_id')
                        ->label('Catégorie')
                        ->options(Category::pluck('name', 'id'))
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
                ])->columnSpan(4),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->striped()
            ->emptyStateHeading("Aucun événement enregistré")
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->size(60)
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Date de début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lieu')
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\TextColumn::make('pays.name')
                    ->label('Pays')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'draft',
                        'success' => 'published',
                        'gray' => 'completed',
                    ]),

                Tables\Columns\IconColumn::make('online')
                    ->label('En ligne')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Mis en avant')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_participants')
                    ->label('Participants')
                    ->suffix(fn(Event $record): string => $record->max_participants ? " / {$record->max_participants}" : '')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Event::$statuses)
                    ->label('Statut'),

                SelectFilter::make('pays_id')
                    ->label('Pays')
                    ->options(Pays::where('online', 1)->pluck('name', 'id'))
                    ->searchable(),

                Filter::make('En ligne')
                    ->query(fn(Builder $query) => $query->where('online', true)),

                Filter::make('Mis en avant')
                    ->query(fn(Builder $query) => $query->where('is_featured', true)),

                Filter::make('À venir')
                    ->query(fn(Builder $query) => $query->where('start_date', '>', now())),

                Filter::make('En cours')
                    ->query(fn(Builder $query) => $query->where('start_date', '<=', now())->where('end_date', '>=', now())),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('activate')
                        ->label('Activer')
                        ->action(fn(Event $record) => $record->activate())
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->visible(fn(Event $record) => !$record->online),
                    Tables\Actions\Action::make('deactivate')
                        ->label('Désactiver')
                        ->action(fn(Event $record) => $record->deactivate())
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->visible(fn(Event $record) => $record->online),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('activate')
                    ->label('Activer la sélection')
                    ->action(fn($records) => $records->each->activate())
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Tables\Actions\BulkAction::make('deactivate')
                    ->label('Désactiver la sélection')
                    ->action(fn($records) => $records->each->deactivate())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\EventResource\RelationManagers\RegistrationsRelationManager::class,
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
