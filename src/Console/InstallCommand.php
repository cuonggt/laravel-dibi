<?php

namespace Cuonggt\Dibi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dibi:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Dibi resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Dibi Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'dibi-provider']);

        $this->comment('Publishing Dibi Assets...');
        $this->callSilent('vendor:publish', ['--tag' => 'dibi-assets']);

        $this->registerDibiServiceProvider();

        $this->info('Dibi scaffolding installed successfully.');
    }

    /**
     * Register the Dibi service provider in the application.
     *
     * @return void
     */
    protected function registerDibiServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $this->replaceServiceProviderNamespace($namespace);

        // Laravel 11 and above register application providers in the
        // "bootstrap/providers.php" file rather than in the "providers"
        // array of the application's "config/app.php" configuration file.
        if (method_exists(ServiceProvider::class, 'addProviderToBootstrapFile') &&
            ServiceProvider::addProviderToBootstrapFile($namespace.'\\Providers\\DibiServiceProvider')) {
            return;
        }

        $this->registerDibiServiceProviderInAppConfig($namespace);
    }

    /**
     * Set the application namespace on the published service provider.
     *
     * @param  string  $namespace
     * @return void
     */
    protected function replaceServiceProviderNamespace($namespace)
    {
        $path = app_path('Providers/DibiServiceProvider.php');

        if (! file_exists($path)) {
            return;
        }

        file_put_contents($path, str_replace(
            'namespace App\Providers;',
            "namespace {$namespace}\Providers;",
            file_get_contents($path)
        ));
    }

    /**
     * Register the Dibi service provider in the application configuration file.
     *
     * @param  string  $namespace
     * @return void
     */
    protected function registerDibiServiceProviderInAppConfig($namespace)
    {
        $path = config_path('app.php');

        if (! file_exists($path)) {
            return;
        }

        $appConfig = file_get_contents($path);

        if (! $appConfig) {
            return;
        }

        if (Str::contains($appConfig, $namespace.'\\Providers\\DibiServiceProvider::class')) {
            return;
        }

        $lineEndingCount = [
            "\r\n" => substr_count($appConfig, "\r\n"),
            "\r" => substr_count($appConfig, "\r"),
            "\n" => substr_count($appConfig, "\n"),
        ];

        $eol = array_keys($lineEndingCount, max($lineEndingCount))[0];

        file_put_contents($path, str_replace(
            "{$namespace}\\Providers\RouteServiceProvider::class,".$eol,
            "{$namespace}\\Providers\RouteServiceProvider::class,".$eol."        {$namespace}\Providers\DibiServiceProvider::class,".$eol,
            $appConfig
        ));
    }
}
