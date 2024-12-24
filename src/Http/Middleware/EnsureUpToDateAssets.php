<?php

namespace Cuonggt\Dibi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use RuntimeException;

class EnsureUpToDateAssets
{
    /**
     * Ensures assets are up to date.
     *
     * @param  Request  $request
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (! app()->runningInConsole()) {
            $publishedPath = public_path('vendor/dibi/mix-manifest.json');

            if (! File::exists($publishedPath)) {
                throw new RuntimeException('The Dibi assets are not published. Please run: `php artisan dibi:install`.');
            }

            if (File::get($publishedPath) !== File::get(__DIR__.'/../../../public/mix-manifest.json')) {
                throw new RuntimeException('The published Dibi assets are not up-to-date with the installed version.  Please run: `php artisan dibi:publish`.');
            }
        }

        return $next($request);
    }
}
