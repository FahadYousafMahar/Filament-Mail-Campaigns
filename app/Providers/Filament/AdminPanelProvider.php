<?php

namespace App\Providers\Filament;

use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Forms\Components\FileUpload;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use GeoSot\FilamentEnvEditor\FilamentEnvEditorPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->defaultAvatarProvider(UiAvatarsProvider::class)
            ->colors([
                'primary' => Color::Purple,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn() => view('footer')
            )
            ->brandLogo( new HtmlString('<img src="' . asset('images/logo.png') . '" alt="logo">'.config('app.name'). '&nbsp;<h1>Mailer Panel</h1>'))
            ->favicon(asset('images/logo.png'))
            ->plugin(FilamentMediaManagerPlugin::make()->allowSubFolders()->allowUserAccess())
            ->plugin(FilamentEnvEditorPlugin::make()->navigationLabel('Configuration')->navigationIcon('heroicon-o-cog')->navigationSort(1000)->navigationGroup('System'))
            ->plugin(
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterNavigation: true, hasAvatars: true, navigationGroup: 'System'
                    )
                    ->enableTwoFactorAuthentication()
                    ->avatarUploadComponent(fn($fileUpload) => $fileUpload->disk('profile-photos'))
            )
            ->navigationGroups([
                'Content','System'
            ]);
    }
}
