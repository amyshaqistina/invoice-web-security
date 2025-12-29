<?php

namespace App\Filament\Auth;

use Filament\Pages\Page;
use Filament\Pages\Auth\EditProfile as oriEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use App\Traits\RequiresPasswordConfirmation;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class EditProfile extends oriEditProfile
{
    use RequiresPasswordConfirmation;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $layout = 'filament-panels::components.layout.simple';
    protected static string $view = 'filament.pages.edit-profile';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent()
                            ->maxLength(255)
                            ->rules([
                                'string',
                                'max:255',
                                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
                            ])
                            ->validationMessages([
                                'regex' => 'The name may only contain letters and spaces.',
                            ]),

                        $this->getEmailFormComponent()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->rules([
                                'email:rfc,dns', // Strict email validation with DNS check
                                Rule::unique('users', 'email')->ignore(auth()->id()),
                            ]),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->required()
                            ->tel()
                            ->minLength(10)
                            ->maxLength(15)
                            ->placeholder('Enter phone number: 0123456789')
                            ->rules([
                                'required',
                                'string',
                                'regex:/^([0-9\s\-\+\(\)]*)$/', // Phone number format
                                'min:10',
                                'max:15',
                            ])
                            ->validationMessages([
                                'regex' => 'Please enter a valid phone number.',
                            ]),

                        Section::make('Change Password')
                            ->description('To change your password, you must first verify your current password for security.')
                            ->schema([
                                TextInput::make('current_password')
                                    ->label('Current Password')
                                    ->password()
                                    ->revealable()
                                    ->autocomplete('current-password')
                                    ->requiredWith('password')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        // Store in a way we can access it later
                                        $set('_current_password_value', $state);
                                    })
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, $fail) {
                                                if (filled($value) && !Hash::check($value, auth()->user()->password)) {
                                                    $fail('The current password is incorrect.');
                                                }
                                            };
                                        },
                                    ])
                                    ->helperText('Required to change your password for security verification.'),

                                TextInput::make('_current_password_value')
                                    ->hidden()
                                    ->dehydrated(true),

                                $this->getPasswordFormComponent()
                                    ->label('New Password')
                                    ->revealable()
                                    ->requiredWith('current_password')
                                    ->rules([
                                        'nullable',
                                        'confirmed',
                                        Password::min(8)
                                            ->letters()
                                            ->mixedCase()
                                            ->numbers()
                                            ->symbols()
                                            ->uncompromised(),
                                    ])
                                    ->validationMessages([
                                        'min' => 'Password must be at least 8 characters.',
                                    ])
                                    ->helperText('Leave blank to keep current password. Must contain uppercase, lowercase, number, and special character.'),

                                $this->getPasswordConfirmationFormComponent()
                                    ->label('Confirm New Password')
                                    ->revealable()
                                    ->requiredWith('password'),
                            ])
                            ->collapsed()
                            ->compact(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If password is being changed, current password MUST be provided and correct
        if (filled($data['password'] ?? null)) {
            // Check if current_password was provided (check both fields)
            $currentPassword = $data['_current_password_value'] ?? $data['current_password'] ?? null;

            if (empty($currentPassword)) {
                Notification::make()
                    ->title('Current Password Required')
                    ->body('You must enter your current password to change your password.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'data.current_password' => 'Current password is required to change your password.',
                ]);
            }

            // Verify current password is correct
            if (!Hash::check($currentPassword, auth()->user()->password)) {
                Notification::make()
                    ->title('Incorrect Password')
                    ->body('The current password you entered is incorrect.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'data.current_password' => 'The current password is incorrect.',
                ]);
            }

            // Mark password as confirmed for future sensitive actions
            $this->markPasswordConfirmed();

            // Remove current_password fields from data as they're not model fields
            unset($data['current_password'], $data['_current_password_value']);
        }

        return parent::mutateFormDataBeforeSave($data);
    }
}

