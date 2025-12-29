<?php

namespace App\Filament\Pages\Settings;

use App\Models\User;
use Filament\Forms\Get;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class AccountInformation extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Settings';
    protected static string $view = 'filament.pages.settings.account-information';

    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        // dd(auth()->user()->toArray());
        $this->form->fill(auth()->user()->toArray());
    }

    public static function canAccess(): bool
    {
          if(!auth()->user()->hasRole('superadmin')){
            return true;
        }
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('')
                    ->id('Account-information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        TextInput::make('email')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
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
                            ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),
                        \Filament\Forms\Components\TextInput::make('passwordConfirmation')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->visible(fn(Get $get): bool => filled($get('password')))
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->footerActions([
                        \Filament\Forms\Components\Actions\Action::make('Save Changes')
                            ->action(function () {
                                $data = $this->form->getState();
                                $user = User::find(auth()->user()->id);
                                $user->update($data);

                                Notification::make()
                                    ->title(__('Successfully updated account information'))
                                    ->success()
                                    ->send();
                            }),
                    ])

            ])
            ->statePath('data');
    }
}
