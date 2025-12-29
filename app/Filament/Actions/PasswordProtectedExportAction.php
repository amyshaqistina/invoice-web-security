<?php

namespace App\Filament\Actions;

use App\Traits\RequiresPasswordConfirmation;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class PasswordProtectedExportAction extends ExportAction
{
    use RequiresPasswordConfirmation;

    protected function setUp(): void
    {
        parent::setUp();

        // Use action method to check password before executing
        $this->action(function ($action, $data) {
            // Check if password was confirmed in the last 30 minutes
            if (!$action->isPasswordConfirmed()) {
                // Check if password_confirmation was provided in data
                if (!isset($data['password_confirmation']) || empty($data['password_confirmation'])) {
                    Notification::make()
                        ->title('Password Required')
                        ->body('Please enter your password to proceed with data export.')
                        ->danger()
                        ->send();
                    return;
                }

                // Verify password
                if (!Hash::check($data['password_confirmation'], auth()->user()->password)) {
                    Notification::make()
                        ->title('Incorrect Password')
                        ->body('The password you entered is incorrect.')
                        ->danger()
                        ->send();
                    return;
                }

                // Mark password as confirmed
                $action->markPasswordConfirmed();
            }

            // Proceed with the export by calling parent action
            return $action->call(parent::class . '@action', ['data' => $data]);
        });
    }
}
