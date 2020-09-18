<?php

namespace Cuonggt\Dibi\Tests;

use Orchestra\Testbench\TestCase;
use Cuonggt\Dibi\DibiServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test case.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
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

    protected function resolveApplicationCore($app)
    {
        parent::resolveApplicationCore($app);

        $app->detectEnvironment(function () {
            return 'self-testing';
        });
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');

        $config->set('database.default', 'testing');

        $config->set('database.connections.testing', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'testdibi'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
        ]);
    }
}
