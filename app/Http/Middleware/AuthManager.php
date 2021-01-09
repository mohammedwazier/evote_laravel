<?php

namespace App\Http\Middleware;

use Closure;

use Session;
use App\Models\AuthUser;

class AuthManager
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
        $data = Session::get('user');
        if(count(AuthUser::where(['id' => $data->id, 'is_manager' => 'false'])->get()) === 1){
            return redirect()->route('dashboard.index');
        }
        return $next($request);
    }
}
