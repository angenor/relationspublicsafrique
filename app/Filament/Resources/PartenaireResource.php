<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PartenaireResource\Pages;
use App\Models\Partenaire;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PartenaireResource extends Resource
{
    protected static ?string $model = Partenaire::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Projets';

    protected static ?string $label = 'Partenaire';

    protected static ?string $pluralLabel = 'Partenaires';

    protected static ?string $recordTitleAttribute = 'nom';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nom')
                ->label('Nom')
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
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\FileUpload::make('logo')
                ->label('Logo')
                ->image()
                ->imageEditor()
                ->disk('public')
                ->directory('projets/partenaires')
                ->visibility('public'),
            Forms\Components\TextInput::make('url')
                ->label('Site web')
                ->url()
                ->maxLength(255),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->label('Logo')->disk('public')->size(50),
                Tables\Columns\TextColumn::make('nom')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('url')->label('Site')->limit(40),
                Tables\Columns\TextColumn::make('projets_count')->label('Projets')->counts('projets'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nom');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartenaires::route('/'),
            'create' => Pages\CreatePartenaire::route('/create'),
            'edit' => Pages\EditPartenaire::route('/{record}/edit'),
        ];
    }
}
