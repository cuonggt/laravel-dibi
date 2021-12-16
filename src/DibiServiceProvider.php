<?php

namespace Cuonggt\Dibi;

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
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dibi');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dibi.php' => config_path('dibi.php'),
            ], 'dibi-config');

            $this->publishes([
                __DIR__.'/../public' => public_path('vendor/dibi'),
            ], 'dibi-assets');

            $this->publishes([
                __DIR__.'/../stubs/DibiServiceProvider.stub' => app_path('Providers/DibiServiceProvider.php'),
            ], 'dibi-provider');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dibi.php', 'dibi'
        );

        $this->commands([
            Console\InstallCommand::class,
            Console\PublishCommand::class,
        ]);
    }
}
