<?php

namespace App\Filament\Actions;

use App\Traits\RequiresPasswordConfirmation;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

class PasswordProtectedDeleteBulkAction extends DeleteBulkAction
{
    use RequiresPasswordConfirmation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading('Confirm Bulk Deletion');

        $this->modalDescription('This is a sensitive action that will delete multiple records. Please enter your password to confirm.');

        // Ensure confirmation is required
        $this->requiresConfirmation();

        // Add password field to the confirmation modal
        $this->form(function () {
            // If password already confirmed within 30 minutes, skip password field
            if ($this->isPasswordConfirmed()) {
                return [];
            }

            return [
                TextInput::make('password_confirmation')
                    ->label('Your Password')
                    ->password()
                    ->required()
                    ->revealable()
                    ->autocomplete('current-password')
                    ->helperText('Enter your current password to proceed with bulk deletion.')
                    ->validationAttribute('password')
                    ->rules([
                        function () {
                            return function (string $attribute, $value, $fail) {
                                if (!Hash::check($value, auth()->user()->password)) {
                                    $fail('The password is incorrect.');
                                }
                            };
                        },
                    ]),
            ];
        });

        // Before the action executes
        $this->before(function ($action, $data) {
            // If password was just confirmed in the form, mark it as confirmed
            if (isset($data['password_confirmation'])) {
                $action->markPasswordConfirmed();
            }
        });
    }
}
