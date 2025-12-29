<?php

namespace App\Filament\Auth;

use Exception;
use App\Models\Team;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\SimplePage;
use Filament\Actions\ActionGroup;
use Illuminate\Auth\SessionGuard;
use Filament\Events\Auth\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Notifications\Auth\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

/**
 * @property Form $form
 */
class Register extends SimplePage
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::pages.auth.register';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected string $userModel;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(5, 60); // 5 attempts per minute
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

        return app(RegistrationResponse::class);
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

    /**
     * @param  array<string, mixed>  $data
     */
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

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        TextInput::make('slug')
                            ->live()
                            ->label('Team Slug')
                            ->placeholder('my-team-name')
                            ->hint('Choose your URL address')
                            ->helperText(fn($get) => url('/') . '/' . filament()->getCurrentPanel()->getPath() . '/' . $get('slug'))
                            ->required()
                            ->minLength(3)
                            ->maxLength(50)
                            ->regex('/^[a-zA-Z0-9\-]+$/')
                            ->dehydrateStateUsing(fn ($state) => trim($state))
                            ->unique(table: Team::class, ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'Slug is required.',
                                'min' => 'Slug must be at least 3 characters.',
                                'max' => 'Slug must not exceed 50 characters.',
                                'regex' => 'Slug can only contain letters, numbers, and hyphens.',
                                'unique' => 'This slug is already taken.',
                            ]),
                    ])
                    ->columns(1)
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Full Name')
            ->required()
            ->minLength(2)
            ->maxLength(255)
            ->live(onBlur: true)
            ->dehydrateStateUsing(fn ($state) => trim($state))
            ->afterStateUpdated(function ($state) {
                if (empty($state)) {
                    return;
                }

                if (strlen($state) < 2) {
                    Notification::make()
                        ->title('Invalid Name')
                        ->body('Name must be at least 2 characters.')
                        ->danger()
                        ->send();
                } elseif (strlen($state) > 255) {
                    Notification::make()
                        ->title('Invalid Name')
                        ->body('Name must not exceed 255 characters.')
                        ->danger()
                        ->send();
                }
            })
            ->validationMessages([
                'required' => 'Name is required.',
                'min' => 'Name must be at least 2 characters.',
                'max' => 'Name must not exceed 255 characters.',
            ])
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Gmail Address')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->regex('/^[a-zA-Z0-9._%+-]+@gmail\.com$/')
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state) {
                if (empty($state)) {
                    return;
                }

                if (!filter_var($state, FILTER_VALIDATE_EMAIL)) {
                    Notification::make()
                        ->title('Invalid Email')
                        ->body('Please enter a valid email address.')
                        ->danger()
                        ->send();
                    return;
                }

                if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $state)) {
                    Notification::make()
                        ->title('Gmail Required')
                        ->body('Please use a Gmail address (@gmail.com).')
                        ->warning()
                        ->send();
                }
            })
            ->validationMessages([
                'required' => 'Email is required.',
                'email' => 'Please enter a valid email address.',
                'max' => 'Email must not exceed 255 characters.',
                'unique' => 'This Gmail address is already registered.',
                'regex' => 'Please use a Gmail address (@gmail.com).',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->minLength(8)
            ->maxLength(255)
            ->regex('/[a-z]/', 'Password must contain at least one lowercase letter')
            ->regex('/[A-Z]/', 'Password must contain at least one uppercase letter')
            ->regex('/[0-9]/', 'Password must contain at least one digit')
            ->regex('/[!@#$%^&*()\-_=+\[\]{};:\'",.< >?\\|`~]/', 'Password must contain at least one special character')
            ->dehydrateStateUsing(fn($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state) {
                if (empty($state) || strlen($state) < 3) {
                    return;
                }

                $errors = [];
                if (strlen($state) < 8) {
                    $errors[] = 'minimum 8 characters';
                }
                if (!preg_match('/[a-z]/', $state)) {
                    $errors[] = 'lowercase letter';
                }
                if (!preg_match('/[A-Z]/', $state)) {
                    $errors[] = 'uppercase letter';
                }
                if (!preg_match('/[0-9]/', $state)) {
                    $errors[] = 'digit';
                }
                if (!preg_match('/[!@#$%^&*()\-_=+\[\]{};:\'",.< >?\\|`~]/', $state)) {
                    $errors[] = 'special character';
                }

                if (!empty($errors)) {
                    Notification::make()
                        ->title('Weak Password')
                        ->body('Missing: ' . implode(', ', $errors))
                        ->warning()
                        ->send();
                }
            })
            ->validationMessages([
                'required' => 'Password is required.',
                'min' => 'Password must be at least 8 characters long.',
                'max' => 'Password must not exceed 255 characters.',
                'same' => 'Passwords do not match.',
            ])
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirm Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false)
            ->live(onBlur: true)
            ->hint('Re-enter your password')
            ->afterStateUpdated(function ($state, Get $get) {
                if (empty($state) || empty($get('password'))) {
                    return;
                }

                if ($state !== $get('password')) {
                    Notification::make()
                        ->title('Password Mismatch')
                        ->body('Passwords do not match.')
                        ->danger()
                        ->send();
                }
            })
            ->validationMessages([
                'required' => 'Password confirmation is required.',
            ]);
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label(__('filament-panels::pages/auth/register.actions.login.label'))
            ->url(filament()->getLoginUrl());
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

    public function getTitle(): string | Htmlable
    {
        return __('filament-panels::pages/auth/register.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament-panels::pages/auth/register.heading');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
        ];
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label(__('filament-panels::pages/auth/register.form.actions.register.label'))
            ->submit('register');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        return $data;
    }
}


