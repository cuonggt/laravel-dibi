<?php

use Cuonggt\Dibi\Http\Middleware\EnsureUpToDateAssets;
use Cuonggt\Dibi\Http\Middleware\EnsureUserIsAuthorized;

return [

    /*
    |--------------------------------------------------------------------------
    | Dibi Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Dibi will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that aren't exposed to users.
    |
    */

    'path' => env('DIBI_PATH', '/dibi'),

    /*
    |--------------------------------------------------------------------------
    | Dibi Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every Dibi route - giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    'middleware' => [
        'web',
        EnsureUserIsAuthorized::class,
        EnsureUpToDateAssets::class,
    ],

];
