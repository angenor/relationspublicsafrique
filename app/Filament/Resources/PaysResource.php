<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaysResource\Pages;
use App\Filament\Resources\PaysResource\RelationManagers;
use App\Models\Pays;
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

class PaysResource extends Resource
{
    protected static ?string $model = Pays::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?string $label ="Gestion des pays";

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([

                    Forms\Components\Grid::make()->schema([
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

                    ])->columns(2),



                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('indicatif')->numeric(),

                    ]),
                ])->columnSpan(8),


                Forms\Components\Card::make()->schema([

                    Forms\Components\Toggle::make('online'),




                    Forms\Components\DateTimePicker::make('created_at')

//
//                         request()->route()->getName() !='filament.admin.resources.posts.edit'?     Forms\Components\DateTimePicker::make('created_at')
//                             ->label('Date de publication') :Forms\Components\Card::make()


                ])->columnSpan(4),

            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->size(fn()=>88),


                Tables\Columns\TextColumn::make('indicatif')
                    ->limit(10)
                    ->label('Indicatif')
                    ->searchable(),
                Tables\Columns\CheckboxColumn::make('online')->sortable() ,

            ])
            ->filters([
                //
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
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePays::route('/'),
        ];
    }
}
