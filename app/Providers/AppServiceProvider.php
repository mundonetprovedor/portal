<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            \App\Services\IxcService::class,
            fn($app) => new \App\Services\IxcService()
        );
    }

    public function boot(): void
    {
        //
    }
}
