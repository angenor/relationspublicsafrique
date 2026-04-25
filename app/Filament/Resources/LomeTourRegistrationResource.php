<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LomeTourRegistrationResource\Pages;
use App\Models\LomeTourRegistration;
use App\Notifications\LomeTourAcceptanceNotification;
use App\Notifications\LomeTourRejectionNotification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class LomeTourRegistrationResource extends Resource
{
    protected static ?string $model = LomeTourRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('prenom')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nom')
                    ->required()
                    ->maxLength(255),
                Select::make('statut')
                    ->options([
                        'etudiant' => 'Étudiant',
                        'professionnel' => 'Professionnel',
                    ])
                    ->native(false)
                    ->required(),
                TextInput::make('fonction')
                    ->required()
                    ->maxLength(255),
                TextInput::make('telephone_whatsapp')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                FileUpload::make('photo_professionnelle')
                    ->image()
                    ->directory('lome-tour')
                    ->disk('public')
                    ->imageEditor()
                    ->imageCropAspectRatio('1:1')
                    ->imagePreviewHeight('150')
                    ->downloadable()
                    ->openable(),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmé',
                        'rejected' => 'Rejeté',
                    ])
                    ->native(false)
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('registered_at')
                    ->disabled()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),
                TextColumn::make('prenom')
                    ->searchable(),
                TextColumn::make('nom')
                    ->searchable(),
                BadgeColumn::make('statut')
                    ->label('Statut')
                    ->colors([
                        'primary' => 'etudiant',
                        'warning' => 'professionnel',
                    ])
                    ->formatStateUsing(fn(string $state) => $state === 'etudiant' ? 'Étudiant' : 'Professionnel'),
                TextColumn::make('fonction')
                    ->searchable(),
                TextColumn::make('telephone_whatsapp')
                    ->searchable(),
                ImageColumn::make('photo_professionnelle')
                    ->disk('public')
                    ->circular()
                    ->height(40)
                    ->width(40),
                TextColumn::make('email')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(function (string $state) {
                        return match ($state) {
                            'pending' => 'En attente',
                            'confirmed' => 'Confirmé',
                            'rejected' => 'Rejeté',
                            default => $state,
                        };
                    }),
                TextColumn::make('registered_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmé',
                        'rejected' => 'Rejeté',
                    ]),
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'etudiant' => 'Étudiant',
                        'professionnel' => 'Professionnel',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('accept')
                    ->label('Accepter')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(LomeTourRegistration $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (LomeTourRegistration $record) {
                        $record->update(['status' => 'confirmed']);

                        if ($record->email) {
                            $record->notify(new LomeTourAcceptanceNotification($record));

                            Notification::make()
                                ->title('Inscription acceptée')
                                ->body('L\'inscription a été acceptée et un email a été envoyé à ' . $record->email)
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Inscription acceptée')
                                ->body('L\'inscription a été acceptée mais aucun email n\'a été envoyé (pas d\'adresse email)')
                                ->warning()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(LomeTourRegistration $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (LomeTourRegistration $record) {
                        $record->update(['status' => 'rejected']);

                        if ($record->email) {
                            $record->notify(new LomeTourRejectionNotification($record));

                            Notification::make()
                                ->title('Inscription rejetée')
                                ->body('L\'inscription a été rejetée et un email a été envoyé à ' . $record->email)
                                ->warning()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Inscription rejetée')
                                ->body('L\'inscription a été rejetée mais aucun email n\'a été envoyé (pas d\'adresse email)')
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListLomeTourRegistrations::route('/'),
            'create' => Pages\CreateLomeTourRegistration::route('/create'),
            'edit' => Pages\EditLomeTourRegistration::route('/{record}/edit'),
        ];
    }
}
