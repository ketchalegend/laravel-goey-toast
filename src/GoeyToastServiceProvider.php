<?php

declare(strict_types=1);

namespace Ketchalegend\LaravelGoeyToast;

use Ketchalegend\LaravelGoeyToast\View\Components\GoeyToastStack;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GoeyToastServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('goey-toast')
            ->hasConfigFile()
            ->hasViews()
            ->hasViewComponent('goey-toast-stack', GoeyToastStack::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(GoeyToastManager::class, function ($app): GoeyToastManager {
            return new GoeyToastManager($app['session.store']);
        });
    }
}
