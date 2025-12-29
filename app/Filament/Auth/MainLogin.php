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

class MainLogin extends BaseAuth
{
    /**
     * Get the form for the resource.
     */
    protected static string $layout = 'filament-panels::components.layout.base';
    protected static string $view = 'filament-panels::pages.auth.mainlogin';
    protected array $extraBodyAttributes = ['class' => 'login-page'];

    public function getHeading(): string
    {
        return __('Sign In');
    }


    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill([
            'email' => 'admin@test.com',
            'password' => 'admin1234',
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


    public function hasLogo(): bool
    {
        return true;
    }


    protected function getFormActions(): array
    {
        return [

            Action::make('Back')
                ->action(function($livewire){
                    $livewire->redirect(url('/'), navigate:false);
                })
                // ->url(url('/'))
                ->extraAttributes(['wire:navigate' => 'false', 'style' => 'width:30%;', 'class' => 'bg-gray-400']),
            $this->getAuthenticateFormAction()
                ->extraAttributes(['style' => 'width:60%;']),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
