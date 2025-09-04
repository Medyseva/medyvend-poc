<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\VendTrailsService;

class VendingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(VendTrailsService::class, function ($app) {
            return new VendTrailsService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
