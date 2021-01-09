<?php

namespace App\Http\Middleware;

use Closure;

use Session;

class LoginAuth
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

        if(!Session::get('user')){
            return redirect()->route('homepage.login')->with(['message' => 'Login First', 'icon' => 'warning']);
        }

        return $next($request);
    }
}
