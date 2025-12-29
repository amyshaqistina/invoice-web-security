<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Team;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Actions\PasswordProtectedDeleteAction;
use App\Filament\Actions\PasswordProtectedDeleteBulkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected static ?string $navigationGroup = 'Maintenance';
    // protected static ?string $navigationLabel = 'Maintenance';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?int $navigationSort = 4;


    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole(['superadmin', 'admin'])) {
            return true;
        }
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Hidden::make('id'),
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->required()
                            ->numeric()
                            ->minLength(10) // Adjust based on your country's phone number length
                            ->maxLength(15) // Prevent excessively long numbers
                            ->placeholder('Enter phone number: 0123456789'),


                        \Filament\Forms\Components\TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn($state): bool => filled($state))
                            // ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),
                        \Filament\Forms\Components\TextInput::make('passwordConfirmation')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->visible(fn(Get $get): bool => filled($get('password')))
                            ->dehydrated(false),
                        Forms\Components\CheckboxList::make('roles')
                            ->required()
                            ->relationship(name: 'roles', titleAttribute: 'name')
                            ->saveRelationshipsUsing(function (Model $record, $state) {
                                $newRole = Role::whereIn('id', $state)->get();
                                $record->syncRoles([]);
                                $record->assignRole($newRole);
                            })
                            ->columns(2),
                    ])->columns(2),
            ])->columns(1);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('email_verified_at')
                //     ->dateTime()
                //     ->sortable(),
                Tables\Columns\IconColumn::make('ban')
                    ->icon(fn(string $state): string => match ($state) {
                        '' => 'heroicon-o-x-mark',
                        '0' => 'heroicon-o-x-mark',
                        '1' => 'heroicon-o-check',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '' => 'success',
                        '0' => 'success',
                        '1' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('ban user')
                        ->icon('heroicon-m-lock-closed')
                        ->color('danger')
                        ->label('Ban user')
                        ->hidden(fn($record) => auth()->user()->id == $record->id || $record->ban == true)
                        ->action(fn($record) => $record->update([
                            'ban' => true
                        ]))
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make('unban user')
                        ->icon('heroicon-m-lock-open')
                        ->color('success')
                        ->label('Unbanned user')
                        ->hidden(fn($record) => auth()->user()->id == $record->id || $record->ban != true)
                        ->action(fn($record) => $record->update([
                            'ban' => false
                        ]))
                        ->requiresConfirmation(),
                    PasswordProtectedDeleteAction::make()
                        ->hidden(fn($record) => auth()->user()->id == $record->id),
                    Tables\Actions\ForceDeleteAction::make()
                        ->hidden(fn($record) => auth()->user()->id == $record->id),
                    Tables\Actions\RestoreAction::make()
                        ->hidden(fn($record) => auth()->user()->id == $record->id),

                ])
                    ->label('More actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    PasswordProtectedDeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->recordUrl(
                fn(Model $record): string => UserResource::getUrl('edit', ['record' => $record->id])
            )
            ->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
