<?php

declare(strict_types=1);

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Helper\VideoEmbed;
use App\Models\EventMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Médias post-événement (research R6) — replay (embed YouTube/Vimeo), photos,
 * documents. Upload via Filament (FR-022). Réordonnable par position.
 */
class MediasRelationManager extends RelationManager
{
    protected static string $relationship = 'medias';

    protected static ?string $title = 'Médias post-événement';

    protected static ?string $modelLabel = 'média';

    protected static ?string $pluralModelLabel = 'médias';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label('Type')
                ->options(EventMedia::$types)
                ->default('image')
                ->required()
                ->live(),
            Forms\Components\TextInput::make('url_embed')
                ->label('URL YouTube / Vimeo (replay)')
                ->url()
                ->visible(fn (Get $get): bool => $get('type') === 'replay')
                ->required(fn (Get $get): bool => $get('type') === 'replay')
                ->rules([
                    static function () {
                        return static function (string $attribute, $value, \Closure $fail): void {
                            if (! empty($value) && VideoEmbed::parse($value) === null) {
                                $fail('Renseignez une URL YouTube ou Vimeo valide.');
                            }
                        };
                    },
                ]),
            Forms\Components\FileUpload::make('chemin')
                ->label('Fichier (photo / document)')
                ->disk('public')
                ->directory('events/medias')
                ->visibility('public')
                ->visible(fn (Get $get): bool => in_array($get('type'), ['image', 'document'], true))
                ->required(fn (Get $get): bool => in_array($get('type'), ['image', 'document'], true)),
            Forms\Components\TextInput::make('legende')->label('Légende')->maxLength(255),
            Forms\Components\TextInput::make('position')->label('Ordre')->numeric()->default(0),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('legende')
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\ImageColumn::make('chemin')->label('Aperçu')->disk('public')->size(50),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => EventMedia::$types[$state] ?? (string) $state),
                Tables\Columns\TextColumn::make('legende')->label('Légende')->limit(40),
                Tables\Columns\TextColumn::make('url_embed')->label('Embed')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('position')->label('Ordre'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Aucun média');
    }
}
