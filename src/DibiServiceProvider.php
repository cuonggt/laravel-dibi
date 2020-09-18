<?php

namespace Cuonggt\Dibi;

use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Cuonggt\Dibi\Contracts\DatabaseRepository;
use Cuonggt\Dibi\Repositories\MysqlDatabaseRepository;

class DibiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! config('dibi.enabled')) {
            return;
        }

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
            'domain' => config('dibi.domain', null),
            'namespace' => 'Cuonggt\Dibi\Http\Controllers',
            'prefix' => config('dibi.path'),
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
            __DIR__.'/../public' => public_path('vendor/dibi'),
        ], 'dibi-assets');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->registerPublishing();
        $this->registerServices();
        $this->registerCommands();
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

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            // $this->publishes([
            //     __DIR__.'/../public' => public_path('vendor/dibi'),
            // ], 'dibi-assets');

            $this->publishes([
                __DIR__.'/../config/dibi.php' => config_path('dibi.php'),
            ], 'dibi-config');

            $this->publishes([
                __DIR__.'/../stubs/DibiServiceProvider.stub' => app_path('Providers/DibiServiceProvider.php'),
            ], 'dibi-provider');
        }
    }

    /**
     * Register Dibi's services in the container.
     *
     * @return void
     */
    protected function registerServices()
    {
        $class = 'Cuonggt\\Dibi\\Repositories\\'.ucfirst(Dibi::driver()).'DatabaseRepository';

        if (! class_exists($class)) {
            throw new Exception('Database driver ['.Dibi::driver().'] is not supported.');
        }

        $this->app->bind(DatabaseRepository::class, $class);

        $this->app->when(MysqlDatabaseRepository::class)
            ->needs('$name')
            ->give(Dibi::databaseName());
    }

    /**
     * Register the Dibi Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\PublishCommand::class,
            ]);
        }
    }
}
