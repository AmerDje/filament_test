<?php

namespace App\Providers\Filament;

use App\Filament\Resources\UserResource;
use App\Filament\Widgets\TestChartWidget;
use App\Filament\Widgets\TestWidget;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('hello')
            ->login()
            ->profile()
            // ->userMenuItems([
            //     MenuItem::make('profile')
            //         ->label('Profile')
            //         ->icon('heroicon-o-user-circle')
            //         ->url(fn() => Auth::check() ? "/hello/users/" . Auth::user()->id . "/edit" : "#")

            // ])
            ->colors([
                'primary' => Color::Amber,
                'secondary' => Color::Gray,
                'danger' => Color::Red,
                'warning' => Color::Yellow,
                'success' => Color::Green,
                'info' => Color::Blue,
            ])
            ->databaseNotifications() //enables notifications
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            //remove default dashboard 
            // ->pages([
            //     Pages\Dashboard::class,
            // ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            // disables default widgets
            ->widgets([
                // we can add widgets here if they are not detected and also we can control the order from here
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                TestWidget::class,
                TestChartWidget::class
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
            ])->plugins([
                ActivitylogPlugin::make()->resource(UserResource::class)->label('Log')
                    ->pluralLabel('Logs')
                    ->navigationItem(true)
                    ->navigationGroup('Activity Log')
                    ->navigationIcon('heroicon-o-shield-check')
                    ->navigationCountBadge(true)
                    ->navigationSort(2)
                // ->authorize(
                //     fn() => Auth::user()->role === User::ROLE_ADMIN
                // ),
            ]);
    }
}
