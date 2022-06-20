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
            //echo $request->user()->id;
            // dd(\Auth::guard($guard));
            // if (substr($request->path(), 0, 16) == "admin/log-viewer" || $request->path() == "admin/elfinder/?CKEditor=content&CKEditorFuncNum=1&langCode=vi") {
            //     return $next($request);
            // }
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            \Session::put('loginRedirect_' . $guard, \Request::url());
            return redirect()->guest($guard . '/login');
        }

        if (substr($request->path(), 0, 16) == "admin/log-viewer" || $request->path() == "admin/elfinder") {
            return $next($request);
        }
        $user = \Auth::guard($guard)->user();
        if ($request->getRequestUri() != '/admin/change-password' && $request->getRequestUri() != '/admin/logout') {
            if (Hash::check('123@123', $user->password)) {
                Session::flash('message', 'Đổi mật khẩu mặc định để sử dụng hệ thống');
                Session::flash('alert-class', 'warning');
                return redirect()->route('admin.change-password');
            }
        }

        $action = $request->route()->getAction();
        if (!isset($action['as']) || $user->hasRole(['system', 'admin', 'administrator'])) return $next($request);

        if (isset($action['role'])) {
            $data = explode('.', $action['role']);
            if (!isset($data[2])) return $next($request);
            if (\Auth::guard($guard)->user()->can(str_replace('-', '_', $data[1]) . '.' . $data[2]))
                return $next($request);
        }

        $data = explode('.', $action['as']);
        if (count($data) <> 3) return $next($request);
        if ($data[2] == 'index' || $data[2] == 'show')
            $action = 'read';
        elseif ($data[2] == 'edit' || $data[2] == 'update')
            $action = 'update';
        elseif ($data[2] == 'create' || $data[2] == 'store')
            $action = 'create';
        elseif ($data[2] == 'destroy')
            $action = 'delete';
        else
            $action = $data[2];
        if (!$user->can(str_replace('-', '_', $data[1]) . '.' . $action)) {

            // phan quyen tao moi khoan giam tru
            $currentRoute = \Request::route()->getName();
            if ($currentRoute == 'admin.deductions.index') {
                // get more permission
                $morePermissions = DB::table('permission_user')->where('user_id', auth()->id())->pluck('permission_id')->toArray();
                $moreActions = Permission::whereIn('id', $morePermissions)->where('module', 'deductions')->pluck('action')->toArray();

                if (in_array("read", $moreActions) || in_array("create", $moreActions)) {
                    return $next($request);
                }
            }

            Session::flash('message', trans('system.access_errors'));
            Session::flash('alert-class', 'danger');
            return redirect()->back();
        }
        return $next($request);
    }
}
