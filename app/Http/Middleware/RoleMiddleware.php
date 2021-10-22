<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param mixed                    ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if(!auth()->user()?->hasRole($roles)){
            abort(Response::HTTP_FORBIDDEN, 'This action is unauthorized.');
        }

        return $next($request);

    }
}
