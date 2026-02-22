<?php

namespace Cuonggt\Dibi\Tests;

use Cuonggt\Dibi\Dibi;
use Cuonggt\Dibi\DibiServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase;

class FeatureTestCase extends TestCase
{
    use DatabaseMigrations;

    /**
     * Setup the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached database repository so each test gets a fresh instance.
        Dibi::$databaseRepository = null;

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

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        Dibi::auth(function () {
            return true;
        });

        Dibi::useDatabaseConnectionName('sqlite');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
