<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
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
        if (\Auth::guard($guard)->check() && \Auth::guard($guard)->user()->admin == 1) {
            return $next($request);
        }

        $msg = "Bạn không có quyền truy cập";
        return redirect()->route('admin.403')->with(['msg' => $msg]);
    }
}
