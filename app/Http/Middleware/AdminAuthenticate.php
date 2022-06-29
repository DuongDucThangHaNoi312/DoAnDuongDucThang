<?php

namespace App\Http\Middleware;

use App\Permission;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */

    public function handle($request, Closure $next, $guard = 'admin')
    {
        config(['auth.defaults.guard' => 'admin']);
        config(['auth.defaults.passwords' => 'users']);

        if (!\Auth::guard($guard)->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            \Session::put('loginRedirect_' . $guard, \Request::url());
            return redirect()->guest($guard . '/login');
        }

        return $next($request);
    }
}
