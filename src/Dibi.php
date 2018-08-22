<?php

namespace Cuonggt\Dibi;

class Dibi
{
    public static function service()
    {
        return (new DatabaseProviderFactory)->make(
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
