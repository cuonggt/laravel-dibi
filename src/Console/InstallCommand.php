<?php

namespace Cuonggt\Dibi\Console;

use Illuminate\Console\Command;
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
     * Register the Dibi service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerDibiServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

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

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\RouteServiceProvider::class,".$eol,
            "{$namespace}\\Providers\RouteServiceProvider::class,".$eol."        {$namespace}\Providers\DibiServiceProvider::class,".$eol,
            $appConfig
        ));

        file_put_contents(app_path('Providers/DibiServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/DibiServiceProvider.php'))
        ));
    }
}
