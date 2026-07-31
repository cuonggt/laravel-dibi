<?php

namespace Cuonggt\Dibi\Tests\Console;

use Cuonggt\Dibi\DibiServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase;

class InstallCommandTest extends TestCase
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The path to the throwaway application the command is run against.
     *
     * @var string
     */
    protected $appPath;

    /**
     * Setup the test case.
     */
    protected function setUp(): void
    {
        $this->files = new Filesystem;

        $this->appPath = sys_get_temp_dir().'/dibi-install-'.uniqid();

        parent::setUp();
    }

    /**
     * Clean up the test case.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->files->deleteDirectory($this->appPath);
    }

    /**
     * Get the service providers for the package.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            DibiServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // The command publishes files into the application and rewrites its
        // provider registration, so point the application at a throwaway
        // copy of the skeleton shipped with Testbench.
        $this->files->copyDirectory($app->basePath(), $this->appPath);

        $app->setBasePath($this->appPath);
    }

    public function test_it_registers_the_service_provider_in_the_bootstrap_providers_file(): void
    {
        $this->skipWithoutBootstrapProvidersFileSupport();

        $this->useBootstrapProvidersFile();

        $this->artisan('dibi:install')->assertSuccessful();

        $providers = $this->files->get($this->appPath.'/bootstrap/providers.php');

        $this->assertStringContainsString('App\Providers\AppServiceProvider::class', $providers);
        $this->assertStringContainsString('App\Providers\DibiServiceProvider::class', $providers);
        $this->assertFileExists($this->appPath.'/app/Providers/DibiServiceProvider.php');
    }

    public function test_it_does_not_duplicate_the_service_provider_in_the_bootstrap_providers_file(): void
    {
        $this->skipWithoutBootstrapProvidersFileSupport();

        $this->useBootstrapProvidersFile();

        $this->artisan('dibi:install')->assertSuccessful();
        $this->artisan('dibi:install')->assertSuccessful();

        $this->assertSame(1, substr_count(
            $this->files->get($this->appPath.'/bootstrap/providers.php'),
            'DibiServiceProvider::class'
        ));
    }

    public function test_it_registers_the_service_provider_in_the_app_config_without_a_bootstrap_providers_file(): void
    {
        $this->useLegacyAppConfigFile();

        $this->artisan('dibi:install')->assertSuccessful();

        $this->assertStringContainsString(
            'App\Providers\RouteServiceProvider::class,'."\n".'        App\Providers\DibiServiceProvider::class,',
            $this->files->get($this->appPath.'/config/app.php')
        );
        $this->assertFileExists($this->appPath.'/app/Providers/DibiServiceProvider.php');
    }

    public function test_it_does_not_duplicate_the_service_provider_in_the_app_config(): void
    {
        $this->useLegacyAppConfigFile();

        $this->artisan('dibi:install')->assertSuccessful();
        $this->artisan('dibi:install')->assertSuccessful();

        $this->assertSame(1, substr_count(
            $this->files->get($this->appPath.'/config/app.php'),
            'DibiServiceProvider::class'
        ));
    }

    public function test_it_replaces_the_application_namespace_on_the_published_service_provider(): void
    {
        $this->useLegacyAppConfigFile();

        $this->files->put($this->appPath.'/composer.json', json_encode([
            'autoload' => ['psr-4' => ['Acme\\' => 'app/']],
        ]));

        $this->artisan('dibi:install')->assertSuccessful();

        $this->assertStringContainsString(
            'namespace Acme\Providers;',
            $this->files->get($this->appPath.'/app/Providers/DibiServiceProvider.php')
        );
    }

    public function test_it_publishes_the_dibi_assets(): void
    {
        $this->artisan('dibi:install')->assertSuccessful();

        $this->assertFileExists($this->appPath.'/public/vendor/dibi/.vite/manifest.json');
    }

    /**
     * Skip the test on Laravel versions that do not use a bootstrap providers file.
     *
     * @return void
     */
    protected function skipWithoutBootstrapProvidersFileSupport()
    {
        if (! method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')) {
            $this->markTestSkipped('The installed Laravel version does not use a "bootstrap/providers.php" file.');
        }
    }

    /**
     * Shape the throwaway application like a Laravel 11 and above skeleton.
     *
     * @return void
     */
    protected function useBootstrapProvidersFile()
    {
        $this->files->delete($this->appPath.'/config/app.php');

        $this->files->put($this->appPath.'/bootstrap/providers.php', <<<'PHP'
            <?php

            return [
                App\Providers\AppServiceProvider::class,
            ];

            PHP);
    }

    /**
     * Shape the throwaway application like a Laravel 10 and below skeleton.
     *
     * @return void
     */
    protected function useLegacyAppConfigFile()
    {
        $this->files->delete($this->appPath.'/bootstrap/providers.php');

        $this->files->put($this->appPath.'/config/app.php', <<<'PHP'
            <?php

            return [
                'providers' => [
                    App\Providers\AppServiceProvider::class,
                    App\Providers\RouteServiceProvider::class,
                ],
            ];

            PHP);
    }
}
