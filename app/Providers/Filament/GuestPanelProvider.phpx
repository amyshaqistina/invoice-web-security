<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use App\Filament\Guest\Pages\GuestLogin;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Guest\Pages\GuestRegistration;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class GuestPanelProvider extends PanelProvider
{

    public $team ;
    public function __construct($app)
    {
        parent::__construct($app);
        if(request()->input('team')){
            $this->team =request()->input('team') ;
        }elseif(request()->segment(2) && !in_array(request()->segment(2), ['login', 'registration', 'password-reset'])){
            $this->team = request()->segment(2) ;
        }
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('guest')
            ->path('guest')
            ->topNavigation()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->spa()
           
            ->navigationItems($this->submenu())
            ->brandName($this->team)
            ->viteTheme('resources/css/filament/guest/theme.css')
            ->assets([
                Css::make('custom-stylesheet', asset('css/guest/custom.css')),
                Js::make('custom-script', asset('js/guest/custom.js')),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn(): string => Blade::render('
                   @assets
                        <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
                        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
                        <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
                    @endassets
                    ')
            )
            ->discoverResources(in: app_path('Filament/Guest/Resources'), for: 'App\\Filament\\Guest\\Resources')
            ->discoverPages(in: app_path('Filament/Guest/Pages'), for: 'App\\Filament\\Guest\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Guest/Widgets'), for: 'App\\Filament\\Guest\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                // Authenticate::class,
            ]);
    }

    function submenu()
    {
        $menu = [
            \Filament\Navigation\NavigationItem::make('home')
                ->label('Home')
                ->url(fn() => url(request()->segment(1) ?? '/'))
                ->icon('heroicon-o-home-modern')
                ->sort(1),
            \Filament\Navigation\NavigationItem::make('login')
                ->label('Login')
                // ->url(fn() => filament()->getPanel('main')->getLoginUrl())
                ->url(fn() => GuestLogin::getUrl())
                // ->url('#" onclick="Livewire.dispatch(\'open-modal\', { id: \'choose-login\' })')
                ->icon('heroicon-o-arrow-right-start-on-rectangle')
                ->sort(1)
                ->visible(
                    fn() => (!request()->segment(2) || in_array(request()->segment(2), ['login', 'registration', 'password-reset']))
                        && !auth()->check()
                ),

            \Filament\Navigation\NavigationItem::make('register')
                ->label('Register')
                ->url(fn() => GuestRegistration::getUrl())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->sort(1)
                ->visible(fn() => (!request()->segment(2) || in_array(request()->segment(2), ['login', 'registration', 'password-reset']))
                    && !auth()->check()),
        ];
        if ((request()->segment(2) && !in_array(request()->segment(2), ['login', 'registration', 'password-reset']))
        || $this->team) {

            $menu = [
                \Filament\Navigation\NavigationItem::make('home')
                    ->label('Home')
                    ->url(fn() => url('/guest/'.$this->team))
                    ->icon('heroicon-o-home-modern')
                    ->sort(1),
                \Filament\Navigation\NavigationItem::make('admin')
                    ->url(fn() => GuestLogin::getUrl(['role' => 'admin', 'team'=> $this->team]))
                    ->icon('heroicon-o-building-library')
                    ->sort(1)
                    ->visible(
                        fn() => $this->team
                    ),
                \Filament\Navigation\NavigationItem::make('customer')
                    ->url(fn() => GuestLogin::getUrl(['role' => 'customer', 'team'=> $this->team]))
                    ->icon('heroicon-o-computer-desktop')
                    ->sort(1)
                    ->visible(
                        fn() => $this->team
                    ),
            ];
        }


        return $menu;
    }
}
