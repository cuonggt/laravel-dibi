<?php

namespace Cuonggt\Dibi;

use RuntimeException;
use Illuminate\Support\Facades\File;

class Dibi
{
    use AuthorizesRequests;

    /**
     * Get the database driver.
     *
     * @return \Cuonggt\Dibi\AbstractDatabase
     */
    public static function driver()
    {
        return config('database.connections.'.config('database.default').'.driver');
    }

    /**
     * Get the database name.
     *
     * @return \Cuonggt\Dibi\AbstractDatabase
     */
    public static function databaseName()
    {
        return config('database.connections.'.config('database.default').'.database');
    }

    /**
     * Get the default JavaScript variables for Dibi.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'path' => config('dibi.path'),
        ];
    }

    /**
     * Check if assets are up-to-date.
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    public static function assetsAreCurrent()
    {
        $publishedPath = public_path('vendor/dibi/mix-manifest.json');

        if (! File::exists($publishedPath)) {
            throw new RuntimeException('The Dibi assets are not published. Please run: php artisan dibi:publish');
        }

        return File::get($publishedPath) === File::get(__DIR__.'/../public/mix-manifest.json');
    }
}
