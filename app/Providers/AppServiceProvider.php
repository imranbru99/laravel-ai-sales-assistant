<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Core\Tenancy\TenantContext::class, function () {
            return new \App\Core\Tenancy\TenantContext();
        });

        $this->app->singleton(\App\Core\Modules\ModuleManager::class, function () {
            return new \App\Core\Modules\ModuleManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
