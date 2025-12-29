<?php

namespace App\Filament\Guest\Pages;

use Exception;

use App\Models\Team;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Forms\Concerns\InteractsWithForms;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;


class GuestRegistration extends Page implements HasForms
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;
    use WithRateLimiting;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guest.pages.guest-registration';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = '/registration';
    protected string $userModel;
    public function getTitle(): string | Htmlable
    {
        return __('');
    }

    use InteractsWithForms;

    public ?array $data = [];

    public function mount()
    {
        if (auth()->check()) {
            $this->dispatch('reload-page', url('/main'));
        }

        $this->form->fill();
    }

    protected function getUserModel(): string
    {
        if (isset($this->userModel)) {
            return $this->userModel;
        }

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        /** @var EloquentUserProvider $provider */
        $provider = $authGuard->getProvider();

        return $this->userModel = $provider->getModel();
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Registration')
                    
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-panels::pages/auth/register.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        TextInput::make('email')
                            ->label(__('filament-panels::pages/auth/register.form.email.label'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique($this->getUserModel()),

                        TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/register.form.password.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->rule(Password::default())
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->same('passwordConfirmation')
                            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),

                        TextInput::make('passwordConfirmation')
                            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->dehydrated(false),
                        TextInput::make('slug')
                            ->live()
                            ->hint('Choose your URL address')
                            ->helperText(fn($get) => url('/') . '/' . filament()->getCurrentPanel()->getPath() . '/' . $get('slug'))
                            ->required()
                            ->unique(table: Team::class, ignoreRecord: true),

                    ])
                    ->footerActions([
                        \Filament\Forms\Components\Actions\Action::make('register')
                            ->action(function () {
                                $this->register();
                            }),
                    ])


            ])
            ->statePath('data');
    }

    public function register()
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return redirect(filament()->getPanel('main')->getRegistrationUrl());

        // return app(RegistrationResponse::class);
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    protected function handleRegistration(array $data): Model
    {
        $team = Team::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);
        $user = $this->getUserModel()::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $team->members()->syncWithoutDetaching([$user->id]);
        return $user;
    }

    protected function sendEmailVerificationNotification(Model $user): void
    {
        if (! $user instanceof MustVerifyEmail) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = app(VerifyEmail::class);
        $notification->url = Filament::getVerifyEmailUrl($user);

        $user->notify($notification);
    }

    
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        return $data;
    }
}
