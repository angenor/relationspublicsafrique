<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstructorsResource\Pages;
use App\Filament\Resources\InstructorsResource\RelationManagers;
use App\Models\Instructors;
use App\Models\Post;
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

class InstructorsResource extends Resource
{
    protected static ?string $model = Instructors::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Formation';
    protected static ?string $label ="Formateurs";

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->minLength(2)
                        ->maxLength(255)
                        ->required(),

                    Forms\Components\TextInput::make('competance')
                        ->minLength(2)
                        ->maxLength(255)
                        ->required(),


                    Forms\Components\Select::make('user_id')
                        ->label('Utilisateur Instructeur')

                        ->required()
                        ->exists('users', 'id')
                        ->preload()
                        ->relationship('user', 'email' ),

                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('competance')->sortable()  ->searchable(),


            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
//                    Tables\Actions\Action::make('online')
//                        ->action(fn (Post $record) => $record->activate())
//                        ->requiresConfirmation()
//                        ->color('success'),
                ]),
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
            'index' => Pages\ListInstructors::route('/'),
            'create' => Pages\CreateInstructors::route('/create'),
            'edit' => Pages\EditInstructors::route('/{record}/edit'),
        ];
    }
}
