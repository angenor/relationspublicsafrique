<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NousResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class NousResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $label = 'Pages Nous';

    protected static ?string $pluralModelLabel = 'Pages Nous';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Titre')
                            ->minLength(2)
                            ->maxLength(255)
                            ->required()
                            ->live(5000)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                    Forms\Components\Textarea::make('resume')
                        ->label('Résumé')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('content')
                        ->label('Contenu')
                        ->columnSpanFull()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'link',
                            'bulletList',
                            'orderedList',
                            'h2',
                            'h3',
                            'h4',
                            'blockquote',
                            'codeBlock',
                        ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('type')
                            ->label('Type de page')
                            ->required()
                            ->native()
                            ->options([
                                'mission' => 'Mission',
                                'vision' => 'Vision',
                                'historique' => 'Historique',
                            ])
                            ->disabled(),

                        Forms\Components\TextInput::make('position')
                            ->label('Position')
                            ->numeric()
                            ->default(1),
                    ]),
                ])->columnSpan(8),

                Forms\Components\Card::make()->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Image à la une')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->directory('nous')
                        ->visibility('public')
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('online')
                        ->label('En ligne')
                        ->default(true),

                    Forms\Components\Select::make('user_id')
                        ->label('Auteur')
                        ->required()
                        ->exists('users', 'id')
                        ->preload()
                        ->relationship('user', 'name')
                        ->default(fn() => auth()->id()),

                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Date de création')
                        ->default(now()),

                    Forms\Components\TextInput::make('view')
                        ->label('Nombre de vues')
                        ->numeric()
                        ->default(0)
                        ->disabled(),
                ])->columnSpan(4),

            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->striped()
            ->emptyStateHeading("Aucune page 'Nous' enregistrée")
            ->emptyStateDescription("Créez votre première page Mission, Vision ou Historique")
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->size(88)
                    ->defaultImageUrl(asset('front/assets/img/blog/blog-1-1.jpg')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Titre')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'mission' => 'success',
                        'vision' => 'info',
                        'historique' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'mission' => 'Mission',
                        'vision' => 'Vision',
                        'historique' => 'Historique',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('online')
                    ->label('En ligne')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('view')
                    ->label('Vues')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Auteur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('online')
                    ->label('En ligne')
                    ->boolean()
                    ->trueLabel('En ligne uniquement')
                    ->falseLabel('Hors ligne uniquement')
                    ->native(false),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Type de page')
                    ->options([
                        'mission' => 'Mission',
                        'vision' => 'Vision',
                        'historique' => 'Historique',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_online')
                        ->label(fn(Post $record): string => $record->online ? 'Mettre hors ligne' : 'Mettre en ligne')
                        ->icon(fn(Post $record): string => $record->online ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn(Post $record): string => $record->online ? 'warning' : 'success')
                        ->action(fn(Post $record) => $record->update(['online' => !$record->online]))
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('activate')
                    ->label('Mettre en ligne')
                    ->action(fn($records) => $records->each->update(['online' => true]))
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\BulkAction::make('deactivate')
                    ->label('Mettre hors ligne')
                    ->action(fn($records) => $records->each->update(['online' => false]))
                    ->requiresConfirmation()
                    ->color('warning')
                    ->icon('heroicon-o-eye-slash'),
            ])
            ->defaultSort('type', 'asc');
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
            'index' => Pages\ListNous::route('/'),
            'create' => Pages\CreateNous::route('/create'),
            'view' => Pages\ViewNous::route('/{record}'),
            'edit' => Pages\EditNous::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('type', ['mission', 'vision', 'historique']);
    }
}
