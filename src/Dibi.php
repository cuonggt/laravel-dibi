<?php

namespace Cuonggt\Dibi;

use Closure;
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
     * @param  \Closure  $callback
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
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
        return DB::connection(static::$databaseConnectionName);
    }

    /**
     * Get the database repository.
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
