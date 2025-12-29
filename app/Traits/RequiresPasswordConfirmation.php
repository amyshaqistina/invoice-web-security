<?php

namespace App\Traits;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

trait RequiresPasswordConfirmation
{
    /**
     * Check if password confirmation is still valid (30 minutes)
     */
    protected function isPasswordConfirmed(): bool
    {
        $confirmedAt = session('auth.password_confirmed_at');

        if (!$confirmedAt) {
            return false;
        }

        // Password confirmation valid for 30 minutes (1800 seconds)
        return (time() - $confirmedAt) < 1800;
    }

    /**
     * Verify the provided password
     */
    protected function verifyPassword(string $password): bool
    {
        return Hash::check($password, auth()->user()->password);
    }

    /**
     * Mark password as confirmed
     */
    protected function markPasswordConfirmed(): void
    {
        session(['auth.password_confirmed_at' => time()]);
    }

    /**
     * Show error notification for invalid password
     */
    protected function sendInvalidPasswordNotification(): void
    {
        Notification::make()
            ->title('Invalid Password')
            ->body('The password you entered is incorrect. Please try again.')
            ->danger()
            ->send();
    }

    /**
     * Show notification that password confirmation is required
     */
    protected function sendPasswordRequiredNotification(): void
    {
        Notification::make()
            ->title('Password Confirmation Required')
            ->body('Please enter your password to confirm this sensitive action.')
            ->warning()
            ->send();
    }
}
