<?php

namespace App\Filament\Guest\Pages;

use Livewire\Component;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;

use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Forms\Components\MarkdownEditor;

use Illuminate\Validation\ValidationException;
use Filament\Forms\Concerns\InteractsWithForms;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class GuestLogin extends Page implements HasForms
{
    use WithRateLimiting;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guest.pages.guest-login';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = '/login';
    public function getTitle(): string | Htmlable
    {
        return __('');
    }

    use InteractsWithForms;

    public ?array $data = [];


    public function mount()
    {
        if (Filament::auth()->check()) {
            // $this->redirect(filament()->getPanel('main')->getLoginUrl(), navigate: false);
            $this->dispatch('reload-page', url('/main'));

            // return  redirect(filament()->getPanel('main')->getLoginUrl());
            // redirect()->intended(filament()->getPanel('main')->getLoginUrl());
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Login')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label(__('filament-panels::pages/auth/login.form.email.label'))
                            ->email()
                            ->required()
                            ->autocomplete()
                            ->autofocus()
                            ->extraInputAttributes(['tabindex' => 1]),
                        \Filament\Forms\Components\TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/login.form.password.label'))
                            ->hint(new HtmlString(Blade::render('<x-filament::link href="' . url('/guest/password-reset') . '" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->autocomplete('current-password')
                            ->required()
                            ->extraInputAttributes(['tabindex' => 2]),
                        \Filament\Forms\Components\Checkbox::make('remember')
                            ->label(__('filament-panels::pages/auth/login.form.remember.label')),
                    ])
                    ->footerActions([
                        \Filament\Forms\Components\Actions\Action::make('login')
                            ->action(function () {
                                $this->authenticate();
                            }),

                        // \Filament\Forms\Components\Actions\Action::make('loginWithGoogle')
                        //     ->label('Login with Google')
                        //     ->url(route('auth.google.redirect'))
                        //     ->icon(fn() => new HtmlString('<svg width="20px" height="20px" viewBox="-3 0 262 262" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid"><path d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027" fill="#4285F4"/><path d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1" fill="#34A853"/><path d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782" fill="#FBBC05"/><path d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251" fill="#EB4335"/></svg>')) // Font Awesome brand icon
                        //     ->iconPosition(IconPosition::Before)
                        //     ->extraAttributes([
                        //         'class' => 'bg-gray-700 text-white hover:bg-gray-800',
                        //     ]),

                        \Filament\Forms\Components\Actions\Action::make('loginWithGitHub')
                            ->label('Login with GitHub')
                            // ->url(route('auth.github.redirect'))
                            ->action(function ($livewire) {
                                $livewire->redirect(route('auth.github.redirect'), navigate:false);
                                // return redirect()->away(route('auth.github.redirect'));
                            })
                            ->icon(fn() => new HtmlString('<svg width="20px" height="20px" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 .5a12 12 0 0 0-3.8 23.4c.6.1.8-.2.8-.5v-2c-3.3.7-4-1.6-4-1.6-.5-1.2-1.2-1.5-1.2-1.5-1-.6.1-.6.1-.6 1.1.1 1.6 1.1 1.6 1.1 1 .1 1.5-.9 1.5-.9.4-1 .7-1.2.5-1.3-2.6-.3-5.2-1.3-5.2-5.9 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.6.1-3.2 0 0 1-.3 3.3 1.3A11.4 11.4 0 0 1 12 6.8a11.4 11.4 0 0 1 3 .4c2.3-1.6 3.3-1.3 3.3-1.3.6 1.6.2 2.9.1 3.2.7.8 1.2 1.9 1.2 3.2 0 4.6-2.7 5.6-5.3 5.9.5.5.8 1.2.8 2.3v3.3c0 .3.2.6.8.5A12 12 0 0 0 12 .5"/>
                                    </svg>'))
                            ->iconPosition(\Filament\Support\Enums\IconPosition::Before)
                            ->extraAttributes([
                                'class' => 'bg-gray-700 text-white hover:bg-gray-800',
                            ]),

                    ])


            ])

            ->statePath('data');
    }

    public function authenticate()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();


        return redirect(filament()->getPanel('main')->getLoginUrl());
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
