<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Filament\Forms\Components\RichEditor;
use App\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends BaseAuth
{
    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::pages.auth.login';

    public function getHeading(): string | Htmlable
    {
        return __('Sign in');
    }


    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill([
            'email' => 'superadmin@test.com',
            'password' => 'superadmin1234',
        ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        // $this->getUsernameFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label(__('Username'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * Authenticate the user.
     */
    public function authenticate(): ?LoginResponse
    {

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $check = $this->loginProcess($data);
        if (!$check) {
            return null;
        }
        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (!$user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function loginProcess($data)
    {
        // $user = User::where("username", $data['username'])->first();
        $user = User::where("email", $data['email'])->first();
        if ($user && Hash::check($data['password'], $user->password)) {
            if ($user->ban == 1) {
                Notification::make()
                    ->title(__('User banned'))
                    ->danger()
                    ->send();
                return false;
            }

            Auth::login($user);
            return true;
        }
        Notification::make()
            ->title(__('Wrong Username or Password'))
            ->danger()
            ->send();

        return false;
    }


    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label(__('filament-panels::pages/auth/login.actions.register.label'))
            ->url(filament()->getRegistrationUrl());
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
