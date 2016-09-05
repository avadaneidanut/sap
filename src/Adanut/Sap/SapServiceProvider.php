<?php

namespace Adanut\Sap\SapServiceProvider;


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
}
