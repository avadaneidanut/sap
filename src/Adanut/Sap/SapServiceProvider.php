<?php

namespace Adanut\Sap;


use Adanut\Sap\Sap;
use Adanut\Sap\Helpers\Arr;
use Adanut\Sap\Helpers\Guid;
use Illuminate\Support\ServiceProvider;

class SapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/sap.php' => config_path('sap.php'),
        ]);
        
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sap.php', 'sap'
        );
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register the sap signleton.
        $this->app->singleton('sap', function ($app) {
            return new Sap();
        });

        // Register the guid signleton.
        $this->app->singleton('guid', function ($app) {
            return new Guid();
        });

        // Register the arr signleton.
        $this->app->singleton('sarr', function ($app) {
            return new Arr();
        });
    }
}
