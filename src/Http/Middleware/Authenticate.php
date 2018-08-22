<?php

namespace Cuonggt\Dibi\Http\Middleware;

use Cuonggt\Dibi\Dibi;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, $next)
    {
        return Dibi::check($request) ? $next($request) : abort(403);
    }
}
