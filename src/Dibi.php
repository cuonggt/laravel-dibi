<?php

namespace Cuonggt\Dibi;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class Dibi
{
    /**
     * The callback that should be used to authenticate Horizon users.
     *
     * @var Closure
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
     * @var Contracts\DatabaseRepository
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
     * Determine whether Dibi can use the given database connection.
     *
     * A connection has to be one Dibi was told about and one the application
     * actually configures. A name registered with no matching entry under
     * `database.connections` cannot be connected to, so accepting it would
     * leave every request failing.
     *
     * @param  string|null  $connection
     * @return bool
     */
    public static function hasDatabaseConnection($connection)
    {
        return in_array($connection, static::databaseConnections(), true)
            && array_key_exists($connection, config('database.connections', []));
    }

    /**
     * Get the current database connection name.
     *
     * A selection is remembered in the cache indefinitely, so one that can no
     * longer be used — because it was never configured, or its configuration
     * has since been removed — is discarded rather than left to fail on every
     * request. Dibi falls back to the first available connection.
     *
     * @return string
     */
    public static function currentDatabaseConnection()
    {
        $selected = Cache::get('dibiConnection');

        if (static::hasDatabaseConnection($selected)) {
            return $selected;
        }

        if (! is_null($selected)) {
            Cache::forget('dibiConnection');
        }

        return Arr::first(static::databaseConnections());
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
     * @return Contracts\DatabaseRepository
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

    /**
     * Generate Vite asset tags from the manifest.
     *
     * @return HtmlString
     */
    public static function viteAssets()
    {
        $manifestPath = __DIR__.'/../public/.vite/manifest.json';

        if (! file_exists($manifestPath)) {
            return new HtmlString('');
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        $tags = '';
        $basePath = '/vendor/dibi/';

        // CSS entry
        if (isset($manifest['resources/css/app.css'])) {
            $cssFile = $manifest['resources/css/app.css']['file'];
            $tags .= '<link rel="stylesheet" href="'.$basePath.$cssFile.'" />';
        }

        // JS entry and its CSS imports
        if (isset($manifest['resources/js/app.js'])) {
            $entry = $manifest['resources/js/app.js'];

            if (isset($entry['css'])) {
                foreach ($entry['css'] as $cssFile) {
                    $tags .= '<link rel="stylesheet" href="'.$basePath.$cssFile.'" />';
                }
            }

            $tags .= '<script type="module" src="'.$basePath.$entry['file'].'"></script>';
        }

        return new HtmlString($tags);
    }
}
