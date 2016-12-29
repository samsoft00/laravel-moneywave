<?php

namespace Samsoft\Moneywave;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class MoneywaveServiceProvider extends ServiceProvider
{

    protected $defer = false;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $config = realpath(__DIR__.'/../resources/config/moneywave.php');

        $this->publishes([
            $config => config_path('moneywave.php')
        ], 'config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'moneywave');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('Samsoft\Moneywave\PaymentController');

        $this->app->bind('laravel-moneywave', function () {
            return new Moneywave;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-moneywave'];
    }


}
