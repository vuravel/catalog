<?php

namespace Vuravel\Catalog;

use Illuminate\Support\ServiceProvider;

class VuravelCatalogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
        if (file_exists($file = __DIR__.'/helpers.php'))
            require_once $file;
        
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadJSONTranslationsFrom(__DIR__.'/../resources/lang');
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeCatalog::class
            ]);
        }
         
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
