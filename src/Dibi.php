<?php

namespace Cuonggt\Dibi;

class Dibi
{
    /**
     * The Dibi's database instance.
     *
     * @var \Cuonggt\Dibi\AbstractDatabase
     */
    protected static $db;

    /**
     * Get the Dibi's database instance.
     *
     * @return \Cuonggt\Dibi\AbstractDatabase
     */
    public static function service()
    {
        return static::$db ?: static::$db = (new DatabaseProviderFactory)->make(
            config('dibi.type'),
            config('database.connections.'.config('database.default').'.database')
        );
    }

    /**
     * Determine if the given request can access the Dibi management.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function check($request)
    {
        return true;
    }
}
