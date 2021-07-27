<?php

namespace Cuonggt\Dibi;

class Dibi
{
    /**
     * Get the database connection name.
     *
     * @return string
     */
    public static function connectionName()
    {
        return config('dibi.db_connection') ?? config('database.default');
    }

    /**
     * Get the database setup.
     *
     * @return array
     */
    public static function connectionSetup()
    {
        return config('database.connections.'.static::connectionName());
    }

    /**
     * Get the database driver.
     *
     * @return string
     */
    public static function driver()
    {
        return static::connectionSetup()['driver'] ?? null;
    }

    /**
     * Get the database name.
     *
     * @return string
     */
    public static function databaseName()
    {
        return static::connectionSetup()['database'] ?? null;
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
            'path' => config('dibi.path'),
        ], $options);
    }
}
