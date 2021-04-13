<?php

namespace Cuonggt\Dibi;

class Dibi
{
    /**
     * Get the database driver.
     *
     * @return string
     */
    public static function driver()
    {
        return config('database.connections.'.config('dibi.db_connection').'.driver');
    }

    /**
     * Get the database name.
     *
     * @return string
     */
    public static function databaseName()
    {
        return config('database.connections.'.config('dibi.db_connection').'.database');
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
