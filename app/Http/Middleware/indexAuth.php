<?php

namespace App\Http\Middleware;

use Closure;

use Session;

class indexAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Session::get('user')){
            return redirect()->route('dashboard.index');
        }
        return $next($request);
    }
}
