<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WidgetTextResource\Pages;
use App\Filament\Resources\WidgetTextResource\RelationManagers;
use App\Models\WidgetText;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class WidgetTextResource extends Resource
{
    protected static ?string $model = WidgetText::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?string $label ="Gestion des blocs";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\FileUpload::make('image'),

                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('slug')
                            ->required(),
                    ]),

                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('key')
                            ->required()
//                            ->unique(callback: function (Unique $rule, callable $get) { // $get callable is used
//                                return $rule
//                                    ->where('id', $get('id')) ;// get the current value in the 'school_id' field
//
//                            }, ignoreRecord: true) //
                        ,
                        Forms\Components\TextInput::make('link')

                    ]),
                    Forms\Components\RichEditor::make('resume'),
                    Forms\Components\RichEditor::make('content'),
                    Forms\Components\TextInput::make('position')->numeric(),

                    Forms\Components\Toggle::make('online'),
                ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('key'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('position'),
                Tables\Columns\IconColumn::make('online')->boolean(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('online')
                        ->action(fn (WidgetText $record) => $record->activate())
                        ->requiresConfirmation()
                        ->color('success'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListWidgetTexts::route('/'),
            'create' => Pages\CreateWidgetText::route('/create'),
            'edit' => Pages\EditWidgetText::route('/{record}/edit'),
        ];
    }
}
