<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlogShowLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! in_array($request->ip(), ['192.168.255.255'], true)) {
            abort(403, 'Your IP is not valid.');
        }
        return $next($request);
    }
}
