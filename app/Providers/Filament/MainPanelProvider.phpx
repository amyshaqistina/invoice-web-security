<?php

namespace App\Providers\Filament;

use App\Filament\Auth\EditProfile;
use Filament\Pages;
use Filament\Panel;
use App\Models\Team;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use App\Filament\Auth\Register;
use Filament\Support\Assets\Js;
use App\Filament\Auth\MainLogin;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use App\Filament\Auth\MainRegister;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Filament\Pages\Tenancy\EditTeamProfile;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class MainPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->topNavigation()
            ->id('main')
            ->path('main')
            ->tenant(Team::class, slugAttribute: 'slug')
            // ->tenantRegistration(RegisterTeam::class)
            // ->tenantProfile(EditTeamProfile::class)
            ->login(MainLogin::class)
            ->registration(MainRegister::class)
            ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class)
            ->spa()
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(asset('assets/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('assets/logo.png'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->viteTheme('resources/css/filament/main/theme.css')
            ->assets([
                Css::make('custom-stylesheet', asset('css/custom.css')),
                Js::make('custom-script', asset('js/custom.js')),
            ])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn(): string => Blade::render('
                   <div class="flex justify-center font-bold">{{ config("app.name") }}</div>
                ')
            )
            ->userMenuItems([
                'logout' => \Filament\Navigation\MenuItem::make()->label('Log out')
                    ->url(fn() => url('logout?team='.filament()->getTenant()?->slug)),
                // ...
            ])
            ->navigationItems([])
            ->navigationGroups([
                // NavigationGroup::make()
                //     ->label('Maintenance')
                //     ->icon('heroicon-o-wrench'),
            ])
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
                Authenticate::class,
            ])
            ->plugins([
                // \Hasnayeen\Themes\ThemesPlugin::make()
                //     ->registerTheme([
                //         Neumorphism::getName() => Neumorphism::class
                //     ])

            ]);
    }
}
