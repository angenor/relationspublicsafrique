<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MediaSerieResource\Pages;
use App\Filament\Resources\MediaSerieResource\RelationManagers\EpisodesRelationManager;
use App\Models\MediaSerie;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MediaSerieResource extends Resource
{
    protected static ?string $model = MediaSerie::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Média';

    protected static ?string $label = 'Série';

    protected static ?string $pluralLabel = 'Séries';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
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
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(MediaSerie::$types),
                    Forms\Components\TextInput::make('position')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('cover_image')
                        ->label('Couverture')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('media/series')
                        ->visibility('public'),
                    Forms\Components\Toggle::make('online')
                        ->label('En ligne')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->label('Couverture')->disk('public')->size(50),
                Tables\Columns\TextColumn::make('titre')->label('Titre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge(),
                Tables\Columns\TextColumn::make('medias_count')->label('Épisodes')->counts('medias'),
                Tables\Columns\IconColumn::make('online')->label('En ligne')->boolean(),
                Tables\Columns\TextColumn::make('position')->label('Ordre')->sortable(),
            ])
            ->defaultSort('position')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            EpisodesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaSeries::route('/'),
            'create' => Pages\CreateMediaSerie::route('/create'),
            'edit' => Pages\EditMediaSerie::route('/{record}/edit'),
        ];
    }
}
