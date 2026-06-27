<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjetResource\RelationManagers;

use App\Helper\VideoEmbed;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class GalerieMediasRelationManager extends RelationManager
{
    protected static string $relationship = 'medias';

    protected static ?string $title = 'Galerie';

    protected static ?string $modelLabel = 'média';

    protected static ?string $pluralModelLabel = 'médias';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label('Type')
                ->options(['image' => 'Image', 'video' => 'Vidéo'])
                ->default('image')
                ->required()
                ->live(),
            Forms\Components\FileUpload::make('chemin')
                ->label('Fichier image')
                ->image()
                ->imageEditor()
                ->disk('public')
                ->directory('projets/galerie')
                ->visibility('public')
                ->visible(fn (Get $get): bool => $get('type') === 'image')
                ->required(fn (Get $get): bool => $get('type') === 'image'),
            Forms\Components\TextInput::make('url_embed')
                ->label('URL YouTube / Vimeo')
                ->url()
                ->visible(fn (Get $get): bool => $get('type') === 'video')
                ->required(fn (Get $get): bool => $get('type') === 'video')
                ->rules([
                    static function () {
                        return static function (string $attribute, $value, \Closure $fail): void {
                            if (! empty($value) && VideoEmbed::parse($value) === null) {
                                $fail('Renseignez une URL YouTube ou Vimeo valide.');
                            }
                        };
                    },
                ]),
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
                Tables\Columns\BadgeColumn::make('type')->label('Type'),
                Tables\Columns\TextColumn::make('legende')->label('Légende')->limit(40),
                Tables\Columns\TextColumn::make('url_embed')->label('Embed')->limit(30)->toggleable(),
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
