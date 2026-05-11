<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfilResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class LiensExternesRelationManager extends RelationManager
{
    protected static string $relationship = 'liensExternes';

    protected static ?string $title = 'Liens externes';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->options([
                    'site' => 'Site web',
                    'facebook' => 'Facebook',
                    'twitter' => 'Twitter / X',
                    'youtube' => 'YouTube',
                    'linkedin' => 'LinkedIn',
                    'instagram' => 'Instagram',
                    'autre' => 'Autre',
                ])
                ->required(),
            Forms\Components\TextInput::make('url')
                ->url()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('libelle')->label('Libellé')->maxLength(120),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('url')->limit(60),
                Tables\Columns\TextColumn::make('libelle')->label('Libellé'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
