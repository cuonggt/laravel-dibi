<?php

namespace Cuonggt\Dibi;

use Closure;

class Dibi
{
    /**
     * The callback that should be used to authenticate Horizon users.
     *
     * @var \Closure
     */
    public static $authUsing;

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
}
