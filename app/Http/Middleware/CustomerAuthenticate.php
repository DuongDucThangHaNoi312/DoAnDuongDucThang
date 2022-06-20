<?php

namespace App\Http\Middleware;

use Closure;

class CustomerAuthenticate
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @param  string|null  $guard
    * @return mixed
    */

    public function handle($request, Closure $next, $guard = 'customer')
    {
        config(['auth.defaults.guard'       => 'customer']);
        config(['auth.defaults.passwords'   => 'customers']);
        if (!\Auth::guard($guard)->check()) {
            if ($request->expectsJson()) return response()->json(['error' => 'Unauthenticated.'], 401);
            \Session::put('login_redirect_' . $guard, \Request::url());
            return redirect()->route('home.get-login');
        }
        return $next($request);
    }
}
