<?php

namespace Cuonggt\Dibi;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dibi
{
    /**
     * The callback that should be used to authenticate Horizon users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * Database Connections.
     *
     * @var array<string>
     */
    public static $databaseConnections = [];

    /**
     * Database Connection Name.
     *
     * @var string
     */
    public static $databaseConnectionName = 'mysql';

    /**
     * Database Repository.
     *
     * @var \Cuonggt\Dibi\Contracts\DatabaseRepository
     */
    public static $databaseRepository;

    /**
     * Get the URI path prefix utilized by Dibi.
     *
     * @return string
     */
    public static function path()
    {
        return config('dibi.path', '/dibi');
    }

    /**
     * Determine if the given request can access the Horizon dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate Horizon users.
     *
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }

    /**
     * Register available database connections.
     *
     * @return static
     */
    public static function registerDatabaseConnections(array $databaseConnections)
    {
        static::$databaseConnections = $databaseConnections;

        return new static;
    }

    /**
     * Get the list of available database connections.
     *
     * @return array
     */
    public static function databaseConnections()
    {
        return empty(static::$databaseConnections) ? [static::$databaseConnectionName] : static::$databaseConnections;
    }

    /**
     * Get the current database connection name.
     *
     * @return string
     */
    public static function currentDatabaseConnection()
    {
        return Cache::get('dibiConnection') ?? Arr::first(static::databaseConnections());
    }

    /**
     * Specify the database connection name that should be used by Dibi.
     *
     * @param  string  $databaseConnectionName
     * @return static
     */
    public static function useDatabaseConnectionName($databaseConnectionName)
    {
        static::$databaseConnectionName = $databaseConnectionName;

        return new static;
    }

    /**
     * Get the database connection.
     *
     * @return \Illuminate\Database\Connection
     */
    public static function databaseConnection()
    {
        return DB::connection(static::currentDatabaseConnection());
    }

    /**
     * Get the database repository.
     *
     * @return \Cuonggt\Dibi\Contracts\DatabaseRepository
     */
    public static function databaseRepository()
    {
        return static::$databaseRepository ?: static::$databaseRepository = DatabaseRepositoryFactory::make(static::databaseConnection());
    }

    /**
     * Get the default JavaScript variables for Dibi.
     *
     * @param  array  $options
     * @return array
     */
    public static function scriptVariables($options = [])
    {
        return array_merge([
            'path' => static::path(),
        ], $options);
    }
}
