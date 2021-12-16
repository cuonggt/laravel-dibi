<?php

namespace Cuonggt\Dibi\Http\Middleware;

use Cuonggt\Dibi\Dibi;

class EnsureUserIsAuthorized
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return Dibi::check($request) ? $next($request) : abort(403);
    }
}
