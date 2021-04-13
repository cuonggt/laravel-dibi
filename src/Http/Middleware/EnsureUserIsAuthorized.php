<?php

namespace Cuonggt\Dibi\Http\Middleware;

use Illuminate\Support\Facades\Gate;

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
        $allowed = app()->environment('local')
            || Gate::allows('viewDibi', [$request->user()]);

        abort_unless($allowed, 403);

        return $next($request);
    }
}
