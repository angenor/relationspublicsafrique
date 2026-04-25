<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChapitreResource\Pages;
use App\Filament\Resources\ChapitreResource\RelationManagers;
use App\Models\Chapitre;
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

class ChapitreResource extends Resource
{
    protected static ?string $model = Chapitre::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Formation';
    protected static ?string $label ="Chapitre";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->minLength(2)
                    ->maxLength(255)
                    ->required()
                    ->live(5000)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
//                                if (($get('slug') ?? '') !== Str::slug($old)) {
//                                    return;
//                                }
                        $set('slug', Str::slug($state));
                    }),

                Forms\Components\TextInput::make('slug')
                    ->required(),

                Forms\Components\RichEditor::make('content'),


                Forms\Components\FileUpload::make('image'),
                Forms\Components\Toggle::make('online'),
                Forms\Components\TextInput::make('speakers'),




                Forms\Components\DateTimePicker::make('created_at')

//
//                         request()->route()->getName() !='filament.admin.resources.posts.edit'?     Forms\Components\DateTimePicker::make('created_at')
//                             ->label('Date de publication') :Forms\Components\Card::make()



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

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
            'index' => Pages\ListChapitres::route('/'),
            'create' => Pages\CreateChapitre::route('/create'),
            'edit' => Pages\EditChapitre::route('/{record}/edit'),
        ];
    }
}
