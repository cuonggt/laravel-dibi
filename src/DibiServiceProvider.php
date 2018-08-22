<?php

namespace Cuonggt\Dibi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DibiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerResources();
        $this->defineAssetPublishing();
    }

    /**
     * Register the Dibi routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('dibi.uri', 'dibi'),
            'namespace' => 'Cuonggt\Dibi\Http\Controllers',
            'middleware' => config('dibi.middleware', 'web'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Register the Dibi resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dibi');
    }

    /**
     * Define the asset publishing configuration.
     *
     * @return void
     */
    public function defineAssetPublishing()
    {
        $this->publishes([
            DIBI_PATH.'/public' => public_path('vendor/dibi'),
        ], 'dibi-assets');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! defined('DIBI_PATH')) {
            define('DIBI_PATH', realpath(__DIR__.'/../'));
        }

        $this->configure();
    }

    /**
     * Setup the configuration for Dibi.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dibi.php', 'dibi'
        );
    }
}
