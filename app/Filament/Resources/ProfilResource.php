<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfilResource\Pages;
use App\Filament\Resources\ProfilResource\RelationManagers;
use App\Models\Profil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProfilResource extends Resource
{
    protected static ?string $model = Profil::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?string $label ="Communicants";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('title')
                    ->label("Titre ")
                    ->placeholder('Sélectionner un type')
                    ->options(['Entreprise' => 'Entreprise', 'Personne' => 'Personne'])
                    ->required(),






                Forms\Components\TextInput::make('nom')
                    ->label('Nom')
                    ->live(500)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
//                                if (($get('slug') ?? '') !== Str::slug($old)) {
//                                    return;
//                                }
                        $set('slug', Str::slug($state));
                    })
                   ->required(),


                Forms\Components\TextInput::make('slug')
                    ->required(),


                Forms\Components\TextInput::make('prenom')
                    ->label('Prenom')

                    ->required(),

                Forms\Components\TextInput::make('fonction')
                    ->label('Fonction *')
                     ,
                Forms\Components\TextInput::make('contact')
                     ,
                Forms\Components\TextInput::make('adresse')
                    ->label('Adresse du commercant')
                    ,
                Forms\Components\TextInput::make('tel')
                    ->label('Téléphone')
                    ->numeric()
                     ,

                Forms\Components\Select::make('pays_id')
                    ->placeholder('Sélectionner un type')
                    ->options(\App\Models\Pays::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->label('Pays')
                    ->required(),

                Forms\Components\TextInput::make('domaine')
                    ->label('Domaine de compétances')
                     ,
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                     ,

                Forms\Components\TextInput::make('facebook')
                    ->label('Lien Facebook')
                    ->placeholder('https://www.facebook.com')
                    ->url(),

                Forms\Components\TextInput::make('twitter')
                    ->label('Lien Twitter')
                    ->placeholder('https://www.twitter.com')
                    ->url(),

                Forms\Components\TextInput::make('youtube')
                    ->label('Lien Youtube')
                    ->placeholder('https://www.youtube.com')
                    ->url() ,
                Forms\Components\TextInput::make('linkding')
                    ->label('Lien LinkedIn')
                    ->placeholder('https://www.linkedin.com')
                    ->url()   ,
                Forms\Components\TextInput::make('site')
                    ->label('Lien Site internet')
                    ->placeholder('https://www.site.com')
                    ->url(),


                Forms\Components\RichEditor::make('bio')

                    ->columnSpan(3),
                Forms\Components\Toggle::make('online')
                    ->required() ->columnSpan(3),

                Forms\Components\FileUpload::make('image')->imageEditor()->required()->columnSpan(2) ,
                Forms\Components\FileUpload::make('cv')->label('CV')
                    ->acceptedFileTypes(['application/pdf'])
                    ->columnSpan(1) ,
            ])->columns(3) ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('id', 'DESC')
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('image')->size(fn()=>40),

                Tables\Columns\TextColumn::make('name')
                    ->hidden()
                    ->searchable(),

                Tables\Columns\TextColumn::make('prenom')
                    ->hidden()
                    ->searchable(),

                Tables\Columns\TextColumn::make('fullname')
                    ->limit(20)
                    ->label('Nom et prénom')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.profil.email')
                    ->limit(20)
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pays.name')
                    ->limit(20)
                    ->label('Pays')
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('online')->sortable() ,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfils::route('/'),
            'create' => Pages\CreateProfil::route('/create'),
            'edit' => Pages\EditProfil::route('/{record}/edit'),
        ];
    }
}
